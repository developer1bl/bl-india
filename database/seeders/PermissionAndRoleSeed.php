<?php

namespace Database\Seeders;

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
        $restoreDataPermission = Permission::create(['name' => 'restore_data', 'permissions_description' => 'this permission is for restore data permission']);
        $viewProductPermission = Permission::create(['name' => 'view_product', 'permissions_description' => 'this permission is for view Product permission']);
        $createProductPermission = Permission::create(['name' => 'create_product', 'permissions_description' => 'this permission is for create Product permission']);
        $editProductPermission = Permission::create(['name' => 'edit_product', 'permissions_description' => 'this permission is for edit Product permission']);
        $deleteProductPermission = Permission::create(['name' => 'delete_product', 'permissions_description' => 'this permission is for delete Product permission']);
        $viewProductCategoryPermission = Permission::create(['name' => 'view_productCategory', 'permissions_description' => 'this permission is for view Product Category permission']);
        $createProductCategoryPermission = Permission::create(['name' => 'create_productCategory', 'permissions_description' => 'this permission is for create Product Category permission']);
        $editProductCategoryPermission = Permission::create(['name' => 'edit_ProductCategory', 'permissions_description' => 'this permission is for edit Product Category permission']);
        $deleteProductCategoryPermission = Permission::create(['name' => 'delete_productCategory', 'permissions_description' => 'this permission is for delete Product Category permission']);
        $viewNoticePermission = Permission::create(['name' => 'view_notice', 'permissions_description' => 'this permission is for view Notice permission']);
        $createNoticePermission = Permission::create(['name' => 'create_notice', 'permissions_description' => 'this permission is for create Notice permission']);
        $editNoticePermission = Permission::create(['name' => 'edit_notice', 'permissions_description' => 'this permission is for edit Notice permission']);
        $deleteNoticePermission = Permission::create(['name' => 'delete_notice', 'permissions_description' => 'this permission is for delete Notice permission']);
        $viewDownloadCategoryPermission = Permission::create(['name' => 'view_download_category', 'permissions_description' => 'this permission is for view Download Category permission']);
        $createDownloadCategoryPermission = Permission::create(['name' => 'create_download_category', 'permissions_description' => 'this permission is for create Download Category permission']);
        $editDownloadCategoryPermission = Permission::create(['name' => 'edit_download_category', 'permissions_description' => 'this permission is for edit Download Category permission']);
        $deleteDownloadCategoryPermission = Permission::create(['name' => 'delete_download_category', 'permissions_description' => 'this permission is for delete Download Category permission']);
        $viewDownloadPermission = Permission::create(['name' => 'view_download', 'permissions_description' => 'this permission is for view Download permission']);
        $createDownloadPermission = Permission::create(['name' => 'create_download', 'permissions_description' => 'this permission is for create Download permission']);
        $editDownloadPermission = Permission::create(['name' => 'edit_download', 'permissions_description' => 'this permission is for edit Download permission']);
        $deleteDownloadPermission = Permission::create(['name' => 'delete_download', 'permissions_description' => 'this permission is for delete Download permission']);
        $viewBlogCategoryPermission = Permission::create(['name' => 'view_blog_category', 'permissions_description' => 'this permission is for view Blog Category permission']);
        $createBlogCategoryPermission = Permission::create(['name' => 'create_blog_category', 'permissions_description' => 'this permission is for create Blog Category permission']);
        $editBlogCategoryPermission = Permission::create(['name' => 'edit_blog_category', 'permissions_description' => 'this permission is for edit Blog Category permission']);
        $deleteBlogCategoryPermission = Permission::create(['name' => 'delete_blog_category', 'permissions_description' => 'this permission is for delete Blog Category permission']);
        $viewBlogPermission = Permission::create(['name' => 'view_blog', 'permissions_description' => 'this permission is for view Blog permission']);
        $createBlogPermission = Permission::create(['name' => 'create_blog', 'permissions_description' => 'this permission is for create Blog permission']);
        $editBlogPermission = Permission::create(['name' => 'edit_blog', 'permissions_description' => 'this permission is for edit Blog permission']);
        $deleteBlogPermission = Permission::create(['name' => 'delete_blog', 'permissions_description' => 'this permission is for delete Blog permission']);
        $viewServiceSectionPermission = Permission::create(['name' => 'vie_service_section', 'permissions_description' => 'this permission is for view Service Section permission']);
        $createServiceSectionPermission = Permission::create(['name' => 'create_service_section', 'permissions_description' => 'this permission is for create Service Section permission']);
        $editServiceSectionPermission = Permission::create(['name' => 'edit_service_section', 'permissions_description' => 'this permission is for edit Service Section permission']);
        $deleteServiceSectionPermission = Permission::create(['name' => 'delete_service_section', 'permissions_description' => 'this permission is for delete Service Section permission']);
        
        // Assign permissions to roles
        $adminRole->permissions()->sync([$creaUserPermission->id, $editUserPermission->id, $deleteUserPermission->id,
                                        $viewUserPermission->id, $approveUserPermission->id, $createRolePermission->id, 
                                        $editRolePermission->id, $deleteRolePermission->id, $viewRolePermission->id, 
                                        $viewPermissionsPermission->id, $createPermissionsPermission->id, $editPermissionsPermission->id,
                                        $deletePermissionPermission->id, $viewContentPermission->id, $createContentPermission->id,
                                        $editContentPermission->id, $deleteContentPermission->id, $approveContentPermission->id,
                                        $viewClientPermission->id, $viewServicePermission->id, $createServicePermission->id,
                                        $editServicePermission->id, $deleteServicePermission->id, $restoreDataPermission->id,
                                        $viewProductPermission->id, $createProductPermission->id, $editProductPermission->id,
                                        $deleteProductPermission->id, $viewProductCategoryPermission->id, $createProductCategoryPermission->id,
                                        $editProductCategoryPermission->id, $deleteProductCategoryPermission->id, $viewNoticePermission->id,
                                        $createNoticePermission->id, $editNoticePermission->id, $deleteNoticePermission->id,
                                        $viewDownloadCategoryPermission->id, $createDownloadCategoryPermission->id, $editDownloadCategoryPermission->id,
                                        $deleteDownloadCategoryPermission->id, $viewDownloadPermission->id, $editDownloadPermission->id,
                                        $createDownloadPermission->id, $deleteDownloadPermission->id, $viewBlogCategoryPermission->id,
                                        $createBlogCategoryPermission->id, $editBlogCategoryPermission->id, $deleteBlogCategoryPermission->id,
                                        $viewBlogPermission->id, $editBlogPermission->id, $deleteBlogPermission->id, 
                                        $createBlogPermission->id, $viewServiceSectionPermission->id, $createServiceSectionPermission->id,
                                        $editServiceSectionPermission->id, $deleteServiceSectionPermission->id ]);

        $moderatorRole->permissions()->sync([$viewUserPermission->id, $approveUserPermission->id, $approveContentPermission->id,
                                            $assignUserPermission->id, $viewClientPermission->id ]);

        $contributorRole->permissions()->sync([$viewContentPermission->id, $createContentPermission->id, $editContentPermission->id ]);

    }
}
