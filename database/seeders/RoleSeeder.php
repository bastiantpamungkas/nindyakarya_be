<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'guard_name' => 'api',
            ],
            [
                'name' => 'Officer',
                'guard_name' => 'api',
            ],
            [
                'name' => 'PM',
                'guard_name' => 'api',
            ],
            [
                'name' => 'VP QHSE',
                'guard_name' => 'api',
            ],
        ];

        $permissions = [
            "Admin" => [
                [
                    'name' => 'dashboard',
                    'guard_name' => 'api',
                    'category' => 'dashboard',
                ],
                [
                    'name' => 'projects',
                    'guard_name' => 'api',
                    'category' => 'projects',
                ],
                [
                    'name' => 'projects-create',
                    'guard_name' => 'api',
                    'category' => 'projects',
                ],
                [
                    'name' => 'projects-update',
                    'guard_name' => 'api',
                    'category' => 'projects',
                ],
                [
                    'name' => 'projects-delete',
                    'guard_name' => 'api',
                    'category' => 'projects',
                ],
                [
                    'name' => 'progress',
                    'guard_name' => 'api',
                    'category' => 'progress',
                ],
                [
                    'name' => 'role',
                    'guard_name' => 'api',
                    'category' => 'role',
                ],
                [
                    'name' => 'role-create',
                    'guard_name' => 'api',
                    'category' => 'role',
                ],
                [
                    'name' => 'role-update',
                    'guard_name' => 'api',
                    'category' => 'role',
                ],
                [
                    'name' => 'role-delete',
                    'guard_name' => 'api',
                    'category' => 'role',
                ],
                [
                    'name' => 'users',
                    'guard_name' => 'api',
                    'category' => 'users',
                ],
                [
                    'name' => 'users-create',
                    'guard_name' => 'api',
                    'category' => 'users',
                ],
                [
                    'name' => 'users-update',
                    'guard_name' => 'api',
                    'category' => 'users',
                ],
                [
                    'name' => 'users-delete',
                    'guard_name' => 'api',
                    'category' => 'users',
                ],
            ],
            "Officer" => [
                [
                    'name' => 'dashboard',
                    'guard_name' => 'api',
                    'category' => 'dashboard',
                ],
                [
                    'name' => 'progress',
                    'guard_name' => 'api',
                    'category' => 'progress',
                ],
                [
                    'name' => 'progress-create',
                    'guard_name' => 'api',
                    'category' => 'progress',
                ],
                [
                    'name' => 'progress-update',
                    'guard_name' => 'api',
                    'category' => 'progress',
                ],
                [
                    'name' => 'progress-delete',
                    'guard_name' => 'api',
                    'category' => 'progress',
                ],
            ],
            "PM" => [
                [
                    'name' => 'dashboard',
                    'guard_name' => 'api',
                    'category' => 'dashboard',
                ],
                [
                    'name' => 'progress',
                    'guard_name' => 'api',
                    'category' => 'progress',
                ],
                [
                    'name' => 'progress-approve',
                    'guard_name' => 'api',
                    'category' => 'progress',
                ],
            ],
            "VP QHSE" => [
                [
                    'name' => 'dashboard',
                    'guard_name' => 'api',
                    'category' => 'dashboard',
                ],
                [
                    'name' => 'progress',
                    'guard_name' => 'api',
                    'category' => 'progress',
                ],
                [
                    'name' => 'progress-approve',
                    'guard_name' => 'api',
                    'category' => 'progress',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            // Create or fetch the role
            $role = Role::firstOrCreate(['name' => $roleData['name'], 'guard_name' => $roleData['guard_name']]);

            // Assign permissions to role if available
            if (isset($permissions[$role->name])) {
                foreach ($permissions[$role->name] as $permissionData) {
                    // Create or fetch the permission
                    $permission = Permission::firstOrCreate(
                        [   
                            'name' => $permissionData['name'],
                            'guard_name' => $permissionData['guard_name'],
                            'category' => $permissionData['category']
                        ]
                    );

                    // Assign the permission to the role
                    $role->givePermissionTo($permission);
                }
            }
        }
    }
}
