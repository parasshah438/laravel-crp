<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Jenssegers\Agent\Agent;

class LoggingService
{
    protected $agent;

    public function __construct()
    {
        $this->agent = new Agent();
    }

    public function logActivity($activity, $description = null, $properties = null, $logType = 'activity', $status = 'success')
    {
        $request = request();
        $user = Auth::user();

        $logData = [
            'log_type' => $logType,
            'user_type' => $this->getUserType($user),
            'user_id' => $user ? $user->id : null,
            'user_email' => $user ? $user->email : null,
            'activity' => $activity,
            'description' => $description,
            'properties' => $properties,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'ip_address' => $this->getClientIp($request),
            'browser' => $this->agent->browser(),
            'platform' => $this->agent->platform(),
            'device' => $this->getDeviceType(),
            'status' => $status
        ];

        // Get geolocation data
        $geoData = $this->getGeolocationData($logData['ip_address']);
        $logData = array_merge($logData, $geoData);

        return ActivityLog::create($logData);
    }

    public function logLogin($user)
    {
        return $this->logActivity(
            'User Login',
            "User {$user->email} logged in successfully",
            ['user_id' => $user->id, 'login_time' => now()],
            'login'
        );
    }

    public function logLogout($user)
    {
        return $this->logActivity(
            'User Logout',
            "User {$user->email} logged out",
            ['user_id' => $user->id, 'logout_time' => now()],
            'logout'
        );
    }

    public function logError($error, $context = [])
    {
        return $this->logActivity(
            'Error',
            $error,
            $context,
            'error',
            'failed'
        );
    }

    protected function getUserType($user)
    {
        if (!$user) {
            return null;
        }

        // Check if user has admin role or is_admin field
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('admin') ? 'admin' : 'user';
        }

        if (isset($user->is_admin)) {
            return $user->is_admin ? 'admin' : 'user';
        }

        if (isset($user->role)) {
            return $user->role === 'admin' ? 'admin' : 'user';
        }

        return 'user';
    }

    protected function getClientIp($request)
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) && !empty($_SERVER[$key])) {
                $ip = trim($_SERVER[$key]);
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $request->ip();
    }

    protected function getGeolocationData($ipAddress)
    {
        if (!$ipAddress || $ipAddress === '127.0.0.1' || $ipAddress === '::1') {
            return [
                'country' => 'Local',
                'region' => 'Local',
                'city' => 'Localhost',
                'latitude' => null,
                'longitude' => null,
                'timezone' => config('app.timezone')
            ];
        }

        try {
            // Using ip-api.com (free tier allows 1000 requests per month)
            $response = Http::timeout(5)->get("http://ip-api.com/json/{$ipAddress}");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'success') {
                    return [
                        'country' => $data['country'] ?? null,
                        'region' => $data['regionName'] ?? null,
                        'city' => $data['city'] ?? null,
                        'latitude' => $data['lat'] ?? null,
                        'longitude' => $data['lon'] ?? null,
                        'timezone' => $data['timezone'] ?? null
                    ];
                }
            }
        } catch (\Exception $e) {
            // Fallback silently if geolocation fails
        }

        return [
            'country' => null,
            'region' => null,
            'city' => null,
            'latitude' => null,
            'longitude' => null,
            'timezone' => null
        ];
    }

    protected function getDeviceType()
    {
        if ($this->agent->isMobile()) {
            return 'Mobile';
        } elseif ($this->agent->isTablet()) {
            return 'Tablet';
        } elseif ($this->agent->isDesktop()) {
            return 'Desktop';
        }

        return 'Unknown';
    }
}