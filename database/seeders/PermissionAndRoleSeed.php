<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionAndRoleSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin', 'role_description' => 'this role is for admin user']);
        $moderatorRole = Role::create(['name' => 'moderator', 'role_description' => 'this role is for manager user']);
        $contributorRole = Role::create(['name' => 'contributor', 'role_description' => 'this role is for contributor user']);
 
        // Create permissions
        $creaUserPermission = Permission::create(['name' => 'create_user', 'permissions_description' => 'this permission is for create user']);
        $assignUserPermission = Permission::create(['name' => 'assign_user', 'permissions_description' => 'this permission is for assigne role to user']);
        $editUserPermission = Permission::create(['name' => 'edit_user', 'permissions_description' => 'this permission is for edit user']);
        $deleteUserPermission = Permission::create(['name' => 'delete_user', 'permissions_description' => 'this permission is for delete user']);
        $viewUserPermission = Permission::create(['name' => 'view_user', 'permissions_description' => 'this permission is for view user']);
        $approveUserPermission = Permission::create(['name' => 'approve_user', 'permissions_description' => 'this permission is for user user']);
        $createRolePermission = Permission::create(['name' => 'create_role', 'permissions_description' => 'this permission is for create role']);
        $editRolePermission = Permission::create(['name' => 'edit_role', 'permissions_description' => 'this permission is for edit role']);
        $deleteRolePermission = Permission::create(['name' => 'delete_role', 'permissions_description' => 'this permission is for delete role']);
        $viewRolePermission = Permission::create(['name' => 'view_role', 'permissions_description' => 'this permission is for view role']);
        $viewPermissionsPermission = Permission::create(['name' => 'view_permission', 'permissions_description' => 'this permission is for view permission']);
        $createPermissionsPermission = Permission::create(['name' => 'create_permission', 'permissions_description' => 'this permission is for create permission']);
        $editPermissionsPermission = Permission::create(['name' => 'edit_permission', 'permissions_description' => 'this permission is for edit permission']);
        $deletePermissionPermission = Permission::create(['name' => 'delete_permission', 'permissions_description' => 'this permission is for delete permission']);
        $viewContentPermission = Permission::create(['name' => 'view_content', 'permissions_description' => 'this permission is for view content permission']);
        $createContentPermission = Permission::create(['name' => 'create_content', 'permissions_description' => 'this permission is for create content permission']);
        $editContentPermission = Permission::create(['name' => 'edit_content', 'permissions_description' => 'this permission is for edit content permission']);
        $deleteContentPermission = Permission::create(['name' => 'delete_content', 'permissions_description' => 'this permission is for delete content permission']);
        $approveContentPermission = Permission::create(['name' => 'approve_content', 'permissions_description' => 'this permission is for approve content permission']);
        $viewClientPermission = Permission::create(['name' => 'view_client', 'permissions_description' => 'this permission is for view client permission']);
        $viewServicePermission = Permission::create(['name' => 'view_service', 'permissions_description' => 'this permission is for view service permission']);
        $createServicePermission = Permission::create(['name' => 'create_service', 'permissions_description' => 'this permission is for create Service permission']);
        $editServicePermission = Permission::create(['name' => 'edit_service', 'permissions_description' => 'this permission is for edit Service permission']);
        $deleteServicePermission = Permission::create(['name' => 'delete_service', 'permissions_description' => 'this permission is for delete Service permission']);

        // Assign permissions to roles
        $adminRole->permissions()->sync([$creaUserPermission->id, $editUserPermission->id, $deleteUserPermission->id,
                                        $viewUserPermission->id, $approveUserPermission->id, $createRolePermission->id, 
                                        $editRolePermission->id, $deleteRolePermission->id, $viewRolePermission->id, 
                                        $viewPermissionsPermission->id, $createPermissionsPermission->id, $editPermissionsPermission->id,
                                        $deletePermissionPermission->id, $viewContentPermission->id, $createContentPermission->id,
                                        $editContentPermission->id, $deleteContentPermission->id, $approveContentPermission->id,
                                        $viewClientPermission->id, $viewServicePermission->id, $createServicePermission->id,
                                        $editServicePermission->id, $deleteServicePermission->id ]);

        $moderatorRole->permissions()->sync([$viewUserPermission->id, $approveUserPermission->id, $approveContentPermission->id,
                                            $assignUserPermission->id, $viewClientPermission->id ]);

        $contributorRole->permissions()->sync([$viewContentPermission->id, $createContentPermission->id, $editContentPermission->id ]);

    }
}
