<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\Helper;

class RolePermissionController extends Controller
{
    public function role(Request $request)
    {
        $keyword = $request->input('keyword');
        $sort = $request->input('sort', 'id');
        $sortDirection = $request->input('type', 'asc');

        $role = Role::when($keyword, function ($query) use ($keyword) {
            $query->where("name", "ilike", '%' . $keyword . '%');
        });

        $role = Helper::pagination($role->orderBy($sort, $sortDirection), $request, [
            'name'
        ]);

        return response()->json([
            'message' => 'Successfully get all roles',
            'roles' => $role,
        ], 200);
    }

    public function permission()
    {
        // $permission = Permission::all()->groupBy('category');
        $permission = Permission::get();

        return response()->json([
            'message' => 'Successfully get all permissions',
            'permissions' => $permission,
        ], 200);
    }


    public function permission_category()
    {
        $permissionCategory = Permission::select('category')->groupBy('category')->orderBy('category', 'asc')->get();
        return response()->json([
            'message' => 'Successfully get all category',
            'permissionCategory' => $permissionCategory,
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'permissions' => 'array'
            ]);

            $permissions = [];
            $permissionsData = Permission::get();
            $category = Permission::select('category')->groupBy('category')->orderBy('category', 'asc')->get();
            if ($category) {
                foreach ($category as $row_category) {
                    if ($request->has($row_category->category)) {
                        foreach ($permissionsData as $row) {
                            foreach ($request->input($row_category->category) as $row_request) {
                                if (isset($row_request[$row->name])) {
                                    if ($row_request[$row->name]) {
                                        $permissions[] = $row->name;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $role = Role::create(['name' => $request->name]);
            $role->syncPermissions($permissions);            

            return response()->json([
                'message' => 'Role created successfully',
                'role' => $role,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all()->groupBy('category');
        $rolePermission = $role->permissions->pluck('id')->toArray();

        return response()->json([
            'status' => 'Success',
            'role' => $role,
        ], 200);
    }

    public function updateRole(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string'
            ]);

    
            $role = Role::findOrFail($id);
            $role->name = $request->name;
            $role->save();

            $permissions = [];
            $permissionsData = Permission::get();
            $category = Permission::select('category')->groupBy('category')->orderBy('category', 'asc')->get();
            if ($category) {
                foreach ($category as $row_category) {
                    if ($request->has($row_category->category)) {
                        foreach ($permissionsData as $row) {
                            foreach ($request->input($row_category->category) as $row_request) {
                                if (isset($row_request[$row->name])) {
                                    if ($row_request[$row->name]) {
                                        $permissions[] = $row->name;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $role->syncPermissions($permissions);
            $role->permissions;
            
            return response()->json([
                'message' => 'Role updated successfully',
                'role' => $role,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function delete(Request $request, $id){
        try {
            $project = Role::find($id);
            $project->delete();

            return response()->json([
                'success' => true,
                'message' => 'Success Deleted Role',
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ],422);
        }
    }
}
