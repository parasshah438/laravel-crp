<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        
        $data = [
            'users_count' => User::where('status', true)->count(),
            'admins_count' => Admin::where('status', true)->count(),
            'total_users' => User::count(),
            'total_admins' => Admin::count(),
            'admin' => $admin,
        ];

        // Add additional data for SuperAdmin
        if ($admin->isSuperAdmin()) {
            $data['roles_count'] = Role::where('status', true)->count();
            $data['permissions_count'] = Permission::where('status', true)->count();
            $data['recent_users'] = User::with('role')->latest()->take(5)->get();
            $data['recent_admins'] = Admin::with('role')->latest()->take(5)->get();
        }

        return view('admin.dashboard', compact('data'));
    }

    public function reports()
    {
        $admin = Auth::guard('admin')->user();
        
        $data = [
            'users_by_role' => User::selectRaw('roles.name as role_name, COUNT(*) as count')
                ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
                ->groupBy('roles.name')
                ->get(),
            'admins_by_role' => Admin::selectRaw('roles.name as role_name, COUNT(*) as count')
                ->join('roles', 'admins.role_id', '=', 'roles.id')
                ->groupBy('roles.name')
                ->get(),
            'monthly_registrations' => User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->take(12)
                ->get(),
        ];

        return view('admin.reports.index', compact('data'));
    }
}