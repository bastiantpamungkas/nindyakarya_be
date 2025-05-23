<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Exception;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function list(Request $request){
        $keyword = $request->input('keyword');
        $sort = $request->input('sort', 'created_at');
        $sortDirection = $request->input('type', 'desc');

        $data = Project::orderBy($sort, $sortDirection)
        ->when($keyword, function ($query) use ($keyword) {
            $query->where('name', 'ilike', '%'.$keyword.'%');
        });

        $projects = Helper::pagination($data, $request, [
            'name'
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $projects,
        ]);
    }
    
    public function detail($id){
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => "Project Not Found",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $project
        ], 200);
    }

    public function store(Request $request){
        $valid = Helper::validator($request->all(), [
            'name' => 'required',
            'description' => 'required',
        ]);

        if($valid === true){
            try {
                $project = Project::create($request->all());

                return response()->json([
                    'success' => true,
                    'message' => 'Success Added Project',
                    'data' => $project
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
            'message' => "Failed Added Project",
        ], 422);
    }
    
    public function update(Request $request, $id){
        $valid = Helper::validator($request->all(), [
            'name' => 'required',
            'description' => 'required',
        ]);

        if($valid === true){
            try {
                $project = Project::find($id);

                $project->update($request->all());

                return response()->json([
                    'success' => true,
                    'message' => 'Success Updated Project',
                    'data' => $project
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
            'message' => "Failed Updated Project",
        ], 422);
    }

    public function delete(Request $request, $id){
        try {
            $project = Project::find($id);
            $project->delete();

            return response()->json([
                'success' => true,
                'message' => 'Success Deleted Project',
            ],200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ],422);
        }
    }
}
