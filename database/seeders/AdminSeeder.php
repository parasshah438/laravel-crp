<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $superAdminRole = Role::where('slug', 'superadmin')->first();
        $adminRole = Role::where('slug', 'admin')->first();

        // Create Super Admin
        Admin::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $superAdminRole->id,
            'status' => true,
        ]);

        // Create Regular Admin
        Admin::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'status' => true,
        ]);
    }
}