<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create Permission Groups first
        $groups = [
            ['name' => 'User Management', 'slug' => 'user-management', 'description' => 'User related permissions', 'icon' => 'fas fa-users', 'sort_order' => 1],
            ['name' => 'Admin Management', 'slug' => 'admin-management', 'description' => 'Admin account permissions', 'icon' => 'fas fa-user-shield', 'sort_order' => 2],
            ['name' => 'Role & Permissions', 'slug' => 'role-permissions', 'description' => 'Role and permission management', 'icon' => 'fas fa-key', 'sort_order' => 3],
            ['name' => 'System Management', 'slug' => 'system-management', 'description' => 'System settings and maintenance', 'icon' => 'fas fa-cogs', 'sort_order' => 4],
            ['name' => 'Activity & Logs', 'slug' => 'activity-logs', 'description' => 'Activity monitoring and logs', 'icon' => 'fas fa-history', 'sort_order' => 5],
            ['name' => 'Reports & Analytics', 'slug' => 'reports-analytics', 'description' => 'Reporting and analytics', 'icon' => 'fas fa-chart-bar', 'sort_order' => 6],
        ];

        foreach ($groups as $group) {
            \App\Models\PermissionGroup::firstOrCreate(
                ['slug' => $group['slug']],
                $group
            );
        }

        // Create Permissions with groups and modern organization
        $permissions = [
            // User Management Group
            ['name' => 'View Users', 'slug' => 'view-users', 'description' => 'Can view all users', 'group_slug' => 'user-management', 'sort_order' => 1],
            ['name' => 'Manage Users', 'slug' => 'manage-users', 'description' => 'Can create and edit users', 'group_slug' => 'user-management', 'sort_order' => 2],
            ['name' => 'Delete Users', 'slug' => 'delete-users', 'description' => 'Can delete users', 'group_slug' => 'user-management', 'sort_order' => 3, 'dependencies' => ['manage-users']],
            ['name' => 'Export Users', 'slug' => 'export-users', 'description' => 'Can export user data', 'group_slug' => 'user-management', 'sort_order' => 4, 'dependencies' => ['view-users']],
            
            // Admin Management Group
            ['name' => 'View Admins', 'slug' => 'view-admins', 'description' => 'Can view admin accounts', 'group_slug' => 'admin-management', 'sort_order' => 1],
            ['name' => 'Manage Admins', 'slug' => 'manage-admins', 'description' => 'Can manage admin accounts', 'group_slug' => 'admin-management', 'sort_order' => 2, 'is_system' => true],
            ['name' => 'Delete Admins', 'slug' => 'delete-admins', 'description' => 'Can delete admin accounts', 'group_slug' => 'admin-management', 'sort_order' => 3, 'dependencies' => ['manage-admins'], 'is_system' => true],
            
            // Role & Permission Management Group
            ['name' => 'View Roles', 'slug' => 'view-roles', 'description' => 'Can view roles', 'group_slug' => 'role-permissions', 'sort_order' => 1],
            ['name' => 'Manage Roles', 'slug' => 'manage-roles', 'description' => 'Can manage roles', 'group_slug' => 'role-permissions', 'sort_order' => 2, 'is_system' => true],
            ['name' => 'View Permissions', 'slug' => 'view-permissions', 'description' => 'Can view permissions', 'group_slug' => 'role-permissions', 'sort_order' => 3],
            ['name' => 'Manage Permissions', 'slug' => 'manage-permissions', 'description' => 'Can manage permissions', 'group_slug' => 'role-permissions', 'sort_order' => 4, 'is_system' => true],
            
            // System Management Group
            ['name' => 'System Settings', 'slug' => 'system-settings', 'description' => 'Can access system settings', 'group_slug' => 'system-management', 'sort_order' => 1, 'is_system' => true],
            ['name' => 'Backup System', 'slug' => 'backup-system', 'description' => 'Can backup system data', 'group_slug' => 'system-management', 'sort_order' => 2, 'is_system' => true],
            ['name' => 'Maintenance Mode', 'slug' => 'maintenance-mode', 'description' => 'Can enable/disable maintenance mode', 'group_slug' => 'system-management', 'sort_order' => 3, 'is_system' => true],
            ['name' => 'Clear Cache', 'slug' => 'clear-cache', 'description' => 'Can clear system cache', 'group_slug' => 'system-management', 'sort_order' => 4],
            
            // Activity & Logs Group
            ['name' => 'View Activity Logs', 'slug' => 'view-activity-logs', 'description' => 'Can view activity logs', 'group_slug' => 'activity-logs', 'sort_order' => 1],
            ['name' => 'Manage Activity Logs', 'slug' => 'manage-activity-logs', 'description' => 'Can export and delete activity logs', 'group_slug' => 'activity-logs', 'sort_order' => 2, 'dependencies' => ['view-activity-logs']],
            
            // Reports & Analytics Group
            ['name' => 'View Reports', 'slug' => 'view-reports', 'description' => 'Can view system reports', 'group_slug' => 'reports-analytics', 'sort_order' => 1],
            ['name' => 'Advanced Reports', 'slug' => 'advanced-reports', 'description' => 'Can view advanced analytics', 'group_slug' => 'reports-analytics', 'sort_order' => 2, 'dependencies' => ['view-reports']],
            ['name' => 'Export Reports', 'slug' => 'export-reports', 'description' => 'Can export report data', 'group_slug' => 'reports-analytics', 'sort_order' => 3, 'dependencies' => ['view-reports']],
        ];

        foreach ($permissions as $permissionData) {
            $group = \App\Models\PermissionGroup::where('slug', $permissionData['group_slug'])->first();
            
            $permission = Permission::firstOrCreate(
                ['slug' => $permissionData['slug']],
                [
                    'name' => $permissionData['name'],
                    'slug' => $permissionData['slug'],
                    'description' => $permissionData['description'],
                    'permission_group_id' => $group ? $group->id : null,
                    'sort_order' => $permissionData['sort_order'] ?? 1,
                    'is_system' => $permissionData['is_system'] ?? false,
                    'dependencies' => $permissionData['dependencies'] ?? [],
                    'status' => true
                ]
            );
        }

        // Create Roles with hierarchy
        $superAdminRole = Role::firstOrCreate(
            ['slug' => 'superadmin'],
            [
                'name' => 'Super Administrator',
                'description' => 'Complete system access with all permissions',
                'status' => true,
                'level' => Role::LEVEL_SUPER_ADMIN,
                'is_system' => true
            ]
        );

        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrator', 
                'description' => 'Administrative access with user management',
                'status' => true,
                'level' => Role::LEVEL_ADMIN,
                'is_system' => true
            ]
        );

        $moderatorRole = Role::firstOrCreate(
            ['slug' => 'moderator'],
            [
                'name' => 'Moderator',
                'description' => 'Limited administrative access',
                'status' => true,
                'level' => Role::LEVEL_MODERATOR,
                'is_system' => false
            ]
        );

        $userRole = Role::firstOrCreate(
            ['slug' => 'user'],
            [
                'name' => 'User',
                'description' => 'Standard user access',
                'status' => true,
                'level' => Role::LEVEL_USER,
                'is_system' => true
            ]
        );

        // Assign ALL permissions to Super Admin
        $allPermissions = Permission::all();
        $superAdminRole->permissions()->sync($allPermissions->pluck('id'));

        // Assign permissions to Admin
        $adminPermissions = Permission::whereIn('slug', [
            'view-users',
            'manage-users',
            'export-users',
            'view-activity-logs',
            'view-reports',
            'export-reports',
            'clear-cache'
        ])->get();
        $adminRole->permissions()->sync($adminPermissions->pluck('id'));

        // Assign limited permissions to Moderator
        $moderatorPermissions = Permission::whereIn('slug', [
            'view-users',
            'view-activity-logs',
            'view-reports'
        ])->get();
        $moderatorRole->permissions()->sync($moderatorPermissions->pluck('id'));

        // No permissions for regular users
        $userRole->permissions()->sync([]);
    }
}