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
        // Create Permissions with better organization
        $permissions = [
            // User Management
            ['name' => 'View Users', 'slug' => 'view-users', 'description' => 'Can view all users'],
            ['name' => 'Manage Users', 'slug' => 'manage-users', 'description' => 'Can create, edit, and manage users'],
            ['name' => 'Delete Users', 'slug' => 'delete-users', 'description' => 'Can delete users'],
            
            // Admin Management (SuperAdmin only)
            ['name' => 'Manage Admins', 'slug' => 'manage-admins', 'description' => 'Can manage admin accounts'],
            
            // Role & Permission Management (SuperAdmin only)
            ['name' => 'Manage Roles', 'slug' => 'manage-roles', 'description' => 'Can manage roles and permissions'],
            
            // System Management (SuperAdmin only)
            ['name' => 'System Settings', 'slug' => 'system-settings', 'description' => 'Can access system settings'],
            ['name' => 'Backup System', 'slug' => 'backup-system', 'description' => 'Can backup system data'],
            
            // Reporting (Both Admin and SuperAdmin)
            ['name' => 'View Reports', 'slug' => 'view-reports', 'description' => 'Can view system reports'],
            
            // Special Permissions
            ['name' => 'Full System Access', 'slug' => 'full-access', 'description' => 'Complete system access (SuperAdmin only)'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        // Create Roles
        $superAdminRole = Role::firstOrCreate(
            ['slug' => 'superadmin'],
            [
                'name' => 'Super Administrator',
                'description' => 'Complete system access with all permissions',
                'status' => true,
            ]
        );

        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrator',
                'description' => 'Basic administrative access with user management',
                'status' => true,
            ]
        );

        $userRole = Role::firstOrCreate(
            ['slug' => 'user'],
            [
                'name' => 'User',
                'description' => 'Standard user access',
                'status' => true,
            ]
        );

        // Assign ALL permissions to Super Admin
        $allPermissions = Permission::all();
        $superAdminRole->permissions()->sync($allPermissions->pluck('id'));

        // Assign limited permissions to Admin
        $adminPermissions = Permission::whereIn('slug', [
            'view-users',
            'manage-users',
            'view-reports'
        ])->get();
        $adminRole->permissions()->sync($adminPermissions->pluck('id'));

        // No permissions for regular users (they get basic access through authentication)
        $userRole->permissions()->sync([]);
    }
}