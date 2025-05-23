<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Progres;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgresController extends Controller
{
    public function list(Request $request){
        $keyword = $request->input('keyword');
        $sort = $request->input('sort', 'created_at');
        $sortDirection = $request->input('type', 'desc');

        $data = Progres::with('project','user', 'media')->orderBy($sort, $sortDirection)
        ->when($keyword, function ($query) use ($keyword) {
            $query->whereHas('project', function ($q) use ($keyword) {
                $q->where('name', 'ilike', '%'.$keyword.'%');
            });
            $query->orWhereHas('user', function ($q) use ($keyword) {
                $q->where('name', 'ilike', '%'.$keyword.'%');
            });
        });

        $progres = Helper::pagination($data, $request, [
            'description'
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $progres,
        ]);
    }
    
    public function detail($id){
        $progres = Progres::with('project', 'user',  'media')->find($id);

        if (!$progres) {
            return response()->json([
                'success' => false,
                'message' => "Progres Not Found",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $progres
        ], 200);
    }

    public function store(Request $request){
        $valid = Helper::validator($request->all(), [
            'date' => 'required',
            'progress' => 'required'
        ]);

        $project_id = null;
        $user = Auth::user();
        if ($user && $user->user_config) {
            foreach ($user->user_config as $user_config) {
                $project_id = $user_config->project_id;
            }
        } 
        if (is_null($project_id)) {
            return response()->json([
                'success' => false,
                'message' => "You dont have project",
            ], 404);
        }

        if($valid === true){
            try {
                $data = $request->all();  
                $data['project_id'] = $project_id;
                $data['created_by'] = $user->id;
                DB::beginTransaction(); 

                $progres = Progres::create($data);

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $progres->addMedia($image)
                            ->usingFileName(uniqid() . '.' . $image->getClientOriginalExtension())
                            ->toMediaCollection('progress');
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Success Added Progres',
                    'data' => $progres
                ],200);

            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ],422);
            }
        }

        return response()->json([
            'success' => false,
            'message' => "Failed Added Progres",
        ], 422);
    }
    
    public function update(Request $request, $id){
        $valid = Helper::validator($request->all(), [
            'date' => 'required',
            'progress' => 'required'
        ]);

        if($valid === true){
            try {
                $user = Auth::user();
                $progres = Progres::find($id);
                $data = $request->all();
                $data['updated_by'] = $user->id;
                DB::beginTransaction(); 
                
                $progres->update($data);
                
                if ($request->hasFile('images')) {
                    if ($progres->media) {
                        foreach ($progres->media as $media) {
                            $media->delete();
                        }
                    }
                    
                    foreach ($request->file('images') as $image) {
                        $progres->addMedia($image)
                            ->usingFileName(uniqid() . '.' . $image->getClientOriginalExtension())
                            ->toMediaCollection('progress');
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Success Updated Progres',
                    'data' => $progres
                ],200);

            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ],422);
            }
        }

        return response()->json([
            'success' => false,
            'message' => "Failed Updated Progres",
        ], 422);
    }

    public function update_status(Request $request, $id){
        $valid = Helper::validator($request->all(), [
            'status' => 'required'
        ]);

        if($valid === true){
            try {
                $user = Auth::user();
                $progres = Progres::find($id);
                $data = $request->all();
                $data['updated_by'] = $user->id;
                DB::beginTransaction(); 
                
                $progres->update($data);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Success Updated Progres',
                    'data' => $progres
                ],200);

            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ],422);
            }
        }

        return response()->json([
            'success' => false,
            'message' => "Failed Updated Progres",
        ], 422);
    }

    public function delete(Request $request, $id){
        try {
            $progres = Progres::find($id);
            $progres->delete();

            return response()->json([
                'success' => true,
                'message' => 'Success Deleted Progres',
            ],200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ],422);
        }
    }
}
