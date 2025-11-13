<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $guard = $this->getGuard($request);
        
        if (!Auth::guard($guard)->check()) {
            return $this->redirectToLogin($guard);
        }

        $user = Auth::guard($guard)->user();

        if (!$user->status) {
            Auth::guard($guard)->logout();
            return $this->redirectToLogin($guard)->with('error', 'Your account has been deactivated.');
        }

        // Check permissions if specified
        if (!empty($permissions)) {
            $hasPermission = false;
            
            // Check each permission (OR logic)
            foreach ($permissions as $permission) {
                if ($user->hasPermission($permission)) {
                    $hasPermission = true;
                    break;
                }
            }
            
            if (!$hasPermission) {
                abort(403, 'You do not have permission to access this resource.');
            }
        }

        return $next($request);
    }

    private function getGuard($request)
    {
        if ($request->is('admin/*')) {
            return 'admin';
        }
        
        return 'web';
    }

    private function redirectToLogin($guard)
    {
        if ($guard === 'admin') {
            return redirect()->route('admin.login');
        }
        
        return redirect()->route('login');
    }
}