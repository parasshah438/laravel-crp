<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('user_type')) {
            $query->byUserType($request->user_type);
        }

        if ($request->filled('log_type')) {
            $query->byLogType($request->log_type);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        if ($request->filled('ip_address')) {
            $query->byIpAddress($request->ip_address);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 50);
        $logs = $query->paginate($perPage)->withQueryString();

        // Get filter options
        $userTypes = ActivityLog::distinct()->pluck('user_type')->filter();
        $logTypes = ActivityLog::distinct()->pluck('log_type');
        $statuses = ActivityLog::distinct()->pluck('status');

        return view('admin.activity-logs.index', compact(
            'logs', 
            'userTypes', 
            'logTypes', 
            'statuses'
        ));
    }

    public function show(ActivityLog $activityLog)
    {
        return view('admin.activity-logs.show', compact('activityLog'));
    }

    public function export(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Apply same filters as index
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('user_type')) {
            $query->byUserType($request->user_type);
        }

        if ($request->filled('log_type')) {
            $query->byLogType($request->log_type);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        if ($request->filled('ip_address')) {
            $query->byIpAddress($request->ip_address);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->limit(10000)->get(); // Limit to prevent memory issues

        $format = $request->get('format', 'csv');

        if ($format === 'json') {
            return $this->exportJson($logs);
        }

        return $this->exportCsv($logs);
    }

    protected function exportCsv($logs)
    {
        $filename = 'activity_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'ID',
                'Date Time',
                'Log Type',
                'User Type',
                'User Email',
                'Activity',
                'Description',
                'Method',
                'URL',
                'IP Address',
                'Location',
                'Browser Info',
                'Status'
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->log_type,
                    $log->user_type,
                    $log->user_email,
                    $log->activity,
                    $log->description,
                    $log->method,
                    $log->url,
                    $log->ip_address,
                    $log->location,
                    $log->browser_info,
                    $log->status
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    protected function exportJson($logs)
    {
        $filename = 'activity_logs_' . now()->format('Y-m-d_H-i-s') . '.json';
        
        $data = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'datetime' => $log->created_at->format('Y-m-d H:i:s'),
                'log_type' => $log->log_type,
                'user_type' => $log->user_type,
                'user_email' => $log->user_email,
                'activity' => $log->activity,
                'description' => $log->description,
                'method' => $log->method,
                'url' => $log->url,
                'ip_address' => $log->ip_address,
                'location' => $log->location,
                'browser_info' => $log->browser_info,
                'properties' => $log->properties,
                'status' => $log->status,
                'geolocation' => [
                    'country' => $log->country,
                    'region' => $log->region,
                    'city' => $log->city,
                    'latitude' => $log->latitude,
                    'longitude' => $log->longitude,
                    'timezone' => $log->timezone
                ]
            ];
        });

        return response()->json($data, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function destroy(ActivityLog $activityLog)
    {
        $activityLog->delete();
        
        return redirect()->route('admin.activity-logs.index')
            ->with('success', 'Activity log deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:activity_logs,id'
        ]);

        ActivityLog::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Selected activity logs deleted successfully.'
        ]);
    }

    public function stats()
    {
        $stats = [
            'total_logs' => ActivityLog::count(),
            'today_logs' => ActivityLog::whereDate('created_at', today())->count(),
            'user_logs' => ActivityLog::where('user_type', 'user')->count(),
            'admin_logs' => ActivityLog::where('user_type', 'admin')->count(),
            'login_logs' => ActivityLog::where('log_type', 'login')->count(),
            'error_logs' => ActivityLog::where('log_type', 'error')->count(),
            'unique_users' => ActivityLog::distinct('user_id')->whereNotNull('user_id')->count(),
            'unique_ips' => ActivityLog::distinct('ip_address')->count(),
        ];

        // Recent activity by day (last 7 days)
        $recentActivity = ActivityLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.activity-logs.stats', compact('stats', 'recentActivity'));
    }
}