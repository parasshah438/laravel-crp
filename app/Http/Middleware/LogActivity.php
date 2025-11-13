<?php

namespace App\Http\Middleware;

use App\Services\LoggingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    protected $loggingService;
    
    // Routes to skip logging
    protected $skipRoutes = [
        'activity-logs*',
        'logs*',
        '_debugbar*',
        'telescope*',
    ];

    // Methods to skip logging
    protected $skipMethods = [
        'HEAD',
        'OPTIONS'
    ];

    public function __construct(LoggingService $loggingService)
    {
        $this->loggingService = $loggingService;
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Skip logging for certain routes and methods
        if ($this->shouldSkipLogging($request)) {
            return $response;
        }

        try {
            $this->logActivity($request, $response);
        } catch (\Exception $e) {
            // Don't let logging errors break the application
            \Log::error('Activity logging failed: ' . $e->getMessage());
        }

        return $response;
    }

    protected function shouldSkipLogging(Request $request)
    {
        // Skip certain HTTP methods
        if (in_array($request->method(), $this->skipMethods)) {
            return true;
        }

        // Skip certain routes
        foreach ($this->skipRoutes as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        // Skip AJAX requests for assets
        if ($request->ajax() && (
            $request->is('api/*') && $request->method() === 'GET'
        )) {
            return true;
        }

        return false;
    }

    protected function logActivity(Request $request, $response)
    {
        $user = Auth::user();
        $routeName = $request->route() ? $request->route()->getName() : null;
        $action = $this->getActionName($request);
        $status = $response->getStatusCode() >= 400 ? 'failed' : 'success';

        $description = $this->getDescription($request, $response);
        $properties = $this->getProperties($request, $response);

        $this->loggingService->logActivity(
            $action,
            $description,
            $properties,
            'activity',
            $status
        );
    }

    protected function getActionName(Request $request)
    {
        $method = $request->method();
        $path = $request->path();
        $routeName = $request->route() ? $request->route()->getName() : null;

        // Try to get a meaningful action name from route
        if ($routeName) {
            $parts = explode('.', $routeName);
            $action = end($parts);
            
            switch ($action) {
                case 'index':
                    return 'View ' . ucwords(str_replace(['-', '_'], ' ', $parts[0] ?? 'Page'));
                case 'show':
                    return 'View Details';
                case 'create':
                    return 'Create Form';
                case 'store':
                    return 'Create Record';
                case 'edit':
                    return 'Edit Form';
                case 'update':
                    return 'Update Record';
                case 'destroy':
                    return 'Delete Record';
                default:
                    return ucwords(str_replace(['-', '_'], ' ', $action));
            }
        }

        // Fallback to method and path
        return $method . ' ' . $path;
    }

    protected function getDescription(Request $request, $response)
    {
        $user = Auth::user();
        $userInfo = $user ? "User {$user->email}" : 'Guest user';
        $action = strtolower($this->getActionName($request));
        $path = $request->path();
        $status = $response->getStatusCode();

        return "{$userInfo} {$action} on {$path} (HTTP {$status})";
    }

    protected function getProperties(Request $request, $response)
    {
        $properties = [
            'route_name' => $request->route() ? $request->route()->getName() : null,
            'status_code' => $response->getStatusCode(),
            'response_size' => strlen($response->getContent()),
        ];

        // Add request data for POST/PUT/PATCH requests (excluding sensitive data)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $requestData = $request->except([
                'password',
                'password_confirmation',
                'token',
                '_token',
                'api_token',
                'current_password'
            ]);

            if (!empty($requestData)) {
                $properties['request_data'] = $requestData;
            }
        }

        return $properties;
    }
}