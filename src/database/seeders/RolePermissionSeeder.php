<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $admin = Role::firstOrCreate([
            'name' => 'admin',
        ]);

        $manager = Role::firstOrCreate([
            'name' => 'manager',
        ]);

        $member = Role::firstOrCreate([
            'name' => 'member',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [
            // Projects
            'create_project',
            'view_project',
            'update_project',
            'delete_project',

            // Project members
            'invite_member',
            'remove_member',

            // Tasks
            'create_task',
            'view_task',
            'update_task',
            'delete_task',
            'assign_task',

            // Comments
            'create_comment',
            'update_comment',
            'delete_comment',

            // Attachments
            'upload_attachment',
            'delete_attachment',

            // Notifications
            'view_notifications',

            // Users
            'manage_users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        |
        | Admin gets every permission.
        |
        */

        $admin->syncPermissions(
            Permission::all()
        );

        /*
        |--------------------------------------------------------------------------
        | Manager
        |--------------------------------------------------------------------------
        */

        $manager->syncPermissions([
            'create_project',
            'view_project',
            'update_project',
            'delete_project',
            'invite_member',
            'remove_member',
            'create_task',
            'view_task',
            'update_task',
            'delete_task',
            'assign_task',
            'create_comment',
            'update_comment',
            'delete_comment',
            'upload_attachment',
            'delete_attachment',
            'view_notifications',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Member
        |--------------------------------------------------------------------------
        */

        $member->syncPermissions([
            'view_project',
            'view_task',
            'update_task',
            'create_comment',
            'update_comment',
            'upload_attachment',
            'view_notifications',
        ]);
    }
}
