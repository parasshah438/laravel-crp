<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $data = [
            'users_count' => User::where('status', true)->count(),
            'admins_count' => Admin::where('status', true)->count(),
            'roles_count' => Role::where('status', true)->count(),
            'permissions_count' => Permission::where('status', true)->count(),
            'total_users' => User::count(),
            'total_admins' => Admin::count(),
        ];

        return view('admin.superadmin.dashboard', compact('data'));
    }

    public function users()
    {
        $users = User::with('role')->paginate(10);
        return view('admin.superadmin.users.index', compact('users'));
    }

    public function admins()
    {
        $admins = Admin::with('role')->paginate(10);
        return view('admin.superadmin.admins.index', compact('admins'));
    }

    public function roles()
    {
        $roles = Role::with('permissions')->paginate(10);
        return view('admin.superadmin.roles.index', compact('roles'));
    }

    public function permissions()
    {
        $permissions = Permission::paginate(10);
        return view('admin.superadmin.permissions.index', compact('permissions'));
    }
}