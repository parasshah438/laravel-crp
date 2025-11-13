<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $admin = Auth::guard('admin')->user();
        $admin->load('role.permissions');
        
        return view('admin.profile.show', compact('admin'));
    }

    public function edit()
    {
        $admin = Auth::guard('admin')->user();
        
        return view('admin.profile.edit', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email,' . $admin->id],
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    public function showChangePasswordForm()
    {
        return view('admin.profile.change-password');
    }

    public function changePassword(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $request->validate([
            'current_password' => ['required', 'current_password:admin'],
            'password' => ['required', 'confirmed', Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()],
        ]);

        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        // Log out other sessions (optional security measure)
        Auth::guard('admin')->logoutOtherDevices($request->password);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Password changed successfully. For security, you have been logged out of other devices.');
    }

    public function security()
    {
        $admin = Auth::guard('admin')->user();
        $admin->load('role.permissions');
        
        // Get login history or security logs if you have them
        $recentActivity = []; // You can implement this based on your needs
        
        return view('admin.profile.security', compact('admin', 'recentActivity'));
    }

    public function updateSecuritySettings(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        // Here you can handle security settings like:
        // - Two-factor authentication
        // - Login notifications
        // - Session management
        // etc.
        
        return redirect()->route('admin.profile.security')
            ->with('success', 'Security settings updated successfully.');
    }
}