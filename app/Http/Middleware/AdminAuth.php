<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $admin = Auth::guard('admin')->user();

        if (!$admin->status) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->with('error', 'Your account has been deactivated.');
        }

        if (!empty($roles)) {
            if (!$admin->hasRole($roles)) {
                abort(403, 'Unauthorized access.');
            }
        }

        return $next($request);
    }
}