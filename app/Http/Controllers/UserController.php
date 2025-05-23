<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\User;
use App\Models\UserConfig;
use Illuminate\Support\Str;
use App\Models\PersPerson;
use Illuminate\Http\Request;
use App\Jobs\JobPersPerson;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function list(Request $request)
    {
        $project_id = $request->input('project_id') ?? null;
        $keyword = $request->input('keyword') ?? null;
        $sort = $request->input('sort', 'created_at');
        $sortDirection = $request->input('type', 'desc');

        $query = User::with('user_config', 'user_config.project', 'roles')->orderBy($sort, $sortDirection)
        ->when($keyword, function ($query) use ($keyword) {
            $query->where( function ($q_group) use ($keyword) {
                $q_group->where('name', 'ilike', '%'.$keyword.'%');
                $q_group->orWhere('email', 'ilike', '%'.$keyword.'%');
            });
        })
        ->when($project_id, function ($query) use ($project_id) {
            $query->whereHas('user_config', function ($q) use ($project_id) {
                $q->where('project_id', $project_id);
            });
        });

        $user = Helper::pagination($query, $request, ['name', 'email']);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function store(Request $request)
    {
        $valid = Helper::validator($request->all(), [
            'name' => 'required',
            'email' => 'email|required|unique:users,email',
            'password' => 'required',
            'pin' => 'required',
        ]);

        if ($valid === true) {
            try {
                $data = $request->all();
                $data['pin'] = Hash::make($request->input('pin'));
                DB::beginTransaction(); 

                $user = User::create($data);

                if ($request->role) {
                    $user->assignRole($request->role);
                }
                if ($request->project_id) {
                    $user_config = UserConfig::where('user_id', $user->id)->first();
                    if (!$user_config) {
                        $user_config = new UserConfig();
                        $user_config->user_id = $user->id;
                    }
                    $user_config->project_id = $request->project_id;
                    $user_config->save();
                }
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Success Added User",
                    'data' => $user
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
        }

        return response()->json([
            'success' => false,
            'message' => "Failed Added User",
        ], 422);
    }

    public function detail($id)
    {
        $user = User::with('user_config')->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "User Not Found",
            ], 404);
        }

        $user->getRoleNames();
        
        if ($user->user_config) {
            $user->user_config->project;
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => "User Not Found",
                ], 404);
            }

            $valid = Helper::validator($request->all(), [
                'name' => 'required',
                'email' => 'email|required|unique:users,email,'.$user->id,
            ]);

            if ($valid !== true) {
                return response()->json([
                    'success' => false,
                    'message' => "Failed Updated User",
                ], 422);
            }
            
            $data = $request->all();
            if ($request->input('pin')) {
                $data['pin'] = Hash::make($request->input('pin'));
            }
            if ($request->input('password')) {
                $data['password'] = Hash::make($request->input('password'));
            }
            DB::beginTransaction(); 

            $user->update($data);

            $user->syncRoles([]);

            if ($request->role) {
                $user->assignRole($request->role);
            }
            if ($request->project_id) {
                $user_config = UserConfig::where('user_id', $user->id)->first();
                if (!$user_config) {
                    $user_config = new UserConfig();
                    $user_config->user_id = $user->id;
                }
                $user_config->project_id = $request->project_id;
                $user_config->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Success Updated User",
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function delete($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => true,
                    'message' => "User Not Found",
                ], 200);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => "Success Deleted User",
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
