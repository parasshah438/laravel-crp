<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            $hasAccess = $this->checkPermissions($user, $permissions);
            
            if (!$hasAccess) {
                // Log unauthorized access attempt
                Log::warning('Unauthorized access attempt', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'route' => $request->route()->getName(),
                    'permissions_required' => $permissions,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'You do not have permission to access this resource.',
                        'required_permissions' => $permissions
                    ], 403);
                }
                
                abort(403, 'You do not have permission to access this resource.');
            }
        }

        return $next($request);
    }

    /**
     * Check permissions with support for AND/OR logic
     * Format: 'permission1,permission2' = OR logic
     * Format: 'permission1&permission2' = AND logic  
     * Format: 'permission1|permission2' = OR logic (alternative)
     */
    private function checkPermissions($user, array $permissions)
    {
        foreach ($permissions as $permissionString) {
            // Handle AND logic (&)
            if (strpos($permissionString, '&') !== false) {
                $andPermissions = explode('&', $permissionString);
                $andPermissions = array_map('trim', $andPermissions);
                
                if ($user->role && $user->role->hasAllPermissions($andPermissions)) {
                    return true;
                }
            }
            // Handle OR logic (| or ,)
            elseif (strpos($permissionString, '|') !== false || strpos($permissionString, ',') !== false) {
                $orSeparator = strpos($permissionString, '|') !== false ? '|' : ',';
                $orPermissions = explode($orSeparator, $permissionString);
                $orPermissions = array_map('trim', $orPermissions);
                
                if ($user->role && $user->role->hasAnyPermission($orPermissions)) {
                    return true;
                }
            }
            // Single permission check
            else {
                if ($user->hasPermission(trim($permissionString))) {
                    return true;
                }
            }
        }
        
        return false;
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