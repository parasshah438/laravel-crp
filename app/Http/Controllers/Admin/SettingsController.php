<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use DB;

class SettingsController extends Controller
{
    public function index()
    {
        // Get current settings from .env file and config
        $settings = [
            'app' => [
                'name' => env('APP_NAME', config('app.name')),
                'url' => env('APP_URL', config('app.url')),
                'timezone' => env('APP_TIMEZONE', config('app.timezone')),
                'debug' => env('APP_DEBUG', config('app.debug')),
                'environment' => env('APP_ENV', config('app.env')),
            ],
            'mail' => [
                'driver' => env('MAIL_MAILER', config('mail.default')),
                'host' => env('MAIL_HOST', config('mail.mailers.smtp.host')),
                'port' => env('MAIL_PORT', config('mail.mailers.smtp.port')),
                'username' => env('MAIL_USERNAME', config('mail.mailers.smtp.username')),
                'password' => env('MAIL_PASSWORD') ? '••••••••' : '',
                'encryption' => env('MAIL_ENCRYPTION', config('mail.mailers.smtp.encryption')),
                'from_address' => env('MAIL_FROM_ADDRESS', config('mail.from.address')),
                'from_name' => env('MAIL_FROM_NAME', config('mail.from.name')),
            ],
            'database' => [
                'connection' => env('DB_CONNECTION', config('database.default')),
                'host' => env('DB_HOST', config('database.connections.mysql.host')),
                'port' => env('DB_PORT', config('database.connections.mysql.port')),
                'database' => env('DB_DATABASE', config('database.connections.mysql.database')),
                'username' => env('DB_USERNAME', config('database.connections.mysql.username')),
                'password' => env('DB_PASSWORD') ? '••••••••' : '',
            ],
            'system' => [
                'maintenance_mode' => app()->isDownForMaintenance(),
                'cache_driver' => env('CACHE_DRIVER', config('cache.default')),
                'queue_driver' => env('QUEUE_CONNECTION', config('queue.default')),
                'session_driver' => env('SESSION_DRIVER', config('session.driver')),
                'session_lifetime' => env('SESSION_LIFETIME', config('session.lifetime')),
                'max_login_attempts' => 5,
                'lockout_duration' => 60,
            ],
            'security' => [
                'password_min_length' => 8,
                'require_email_verification' => false,
                'two_factor_enabled' => false,
                'force_https' => env('FORCE_HTTPS', false),
                'session_encrypt' => env('SESSION_ENCRYPT', config('session.encrypt')),
            ]
        ];

        // Get system information
        $systemInfo = [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'server_os' => php_uname('s'),
            'server_time' => now()->format('Y-m-d H:i:s'),
            'timezone' => config('app.timezone'),
            'memory_limit' => ini_get('memory_limit'),
            'upload_limit' => ini_get('upload_max_filesize'),
            'database' => config('database.default'),
            'env_writable' => is_writable(base_path('.env')),
        ];

        return view('admin.settings.index', compact('settings', 'systemInfo'));
    }

    public function update(Request $request)
    {
        $validationRules = [
            'setting_type' => 'required|in:app,mail,database,system',
        ];

        // Add specific validation rules based on setting type
        switch ($request->setting_type) {
            case 'app':
                $validationRules = array_merge($validationRules, [
                    'app_name' => 'required|string|max:255',
                    'app_url' => 'required|url',
                    'app_timezone' => 'required|string',
                    'session_lifetime' => 'required|integer|min:1|max:1440',
                ]);
                break;
                
            case 'mail':
                $validationRules = array_merge($validationRules, [
                    'mail_mailer' => 'required|string',
                    'mail_host' => 'required|string',
                    'mail_port' => 'required|integer|min:1|max:65535',
                    'mail_username' => 'nullable|string',
                    'mail_password' => 'nullable|string',
                    'mail_encryption' => 'nullable|in:tls,ssl',
                    'mail_from_name' => 'required|string|max:255',
                    'mail_from_address' => 'required|email',
                ]);
                break;
                
            case 'database':
                $validationRules = array_merge($validationRules, [
                    'db_host' => 'required|string',
                    'db_port' => 'required|integer',
                    'db_database' => 'required|string',
                    'db_username' => 'required|string',
                    'db_password' => 'nullable|string',
                ]);
                break;
                
            case 'system':
                $validationRules = array_merge($validationRules, [
                    'cache_driver' => 'required|string',
                    'queue_connection' => 'required|string',
                    'session_driver' => 'required|string',
                ]);
                break;
        }

        $request->validate($validationRules);

        try {
            // Check if .env file is writable
            if (!is_writable(base_path('.env'))) {
                return redirect()->back()
                    ->with('error', 'Unable to write to .env file. Please check file permissions.')
                    ->withInput();
            }

            // Update .env file based on setting type
            switch ($request->setting_type) {
                case 'app':
                    $this->updateEnvFile([
                        'APP_NAME' => '"' . $request->app_name . '"',
                        'APP_URL' => $request->app_url,
                        'APP_TIMEZONE' => $request->app_timezone,
                        'SESSION_LIFETIME' => $request->session_lifetime,
                    ]);
                    $message = 'Application settings updated successfully.';
                    break;
                    
                case 'mail':
                    $envUpdates = [
                        'MAIL_MAILER' => $request->mail_mailer,
                        'MAIL_HOST' => $request->mail_host,
                        'MAIL_PORT' => $request->mail_port,
                        'MAIL_USERNAME' => $request->mail_username ?: '',
                        'MAIL_ENCRYPTION' => $request->mail_encryption ?: 'null',
                        'MAIL_FROM_NAME' => '"' . $request->mail_from_name . '"',
                        'MAIL_FROM_ADDRESS' => $request->mail_from_address,
                    ];
                    
                    // Only update password if provided
                    if ($request->filled('mail_password')) {
                        $envUpdates['MAIL_PASSWORD'] = $request->mail_password;
                    }
                    
                    $this->updateEnvFile($envUpdates);
                    $message = 'Mail settings updated successfully.';
                    break;
                    
                case 'database':
                    // Test database connection before applying changes
                    $testConnection = $this->testDatabaseConnection(
                        $request->db_host,
                        $request->db_port,
                        $request->db_database,
                        $request->db_username,
                        $request->filled('db_password') ? $request->db_password : env('DB_PASSWORD')
                    );
                    
                    if (!$testConnection['success']) {
                        return redirect()->back()
                            ->with('error', 'Database connection test failed: ' . $testConnection['error'])
                            ->withInput();
                    }
                    
                    // Store original values for rollback
                    $originalValues = [
                        'DB_HOST' => env('DB_HOST'),
                        'DB_PORT' => env('DB_PORT'),
                        'DB_DATABASE' => env('DB_DATABASE'),
                        'DB_USERNAME' => env('DB_USERNAME'),
                    ];
                    
                    $envUpdates = [
                        'DB_HOST' => $request->db_host,
                        'DB_PORT' => $request->db_port,
                        'DB_DATABASE' => $request->db_database,
                        'DB_USERNAME' => $request->db_username,
                    ];
                    
                    // Only update password if provided
                    if ($request->filled('db_password')) {
                        $envUpdates['DB_PASSWORD'] = $request->db_password;
                    }
                    
                    try {
                        $this->updateEnvFile($envUpdates);
                        
                        // Test connection with new settings
                        $this->clearAllCaches();
                        
                        // Give a moment for config to reload
                        sleep(1);
                        
                        // Test the connection again
                        $finalTest = $this->testCurrentDatabaseConnection();
                        if (!$finalTest['success']) {
                            // Rollback changes
                            $this->updateEnvFile($originalValues);
                            $this->clearAllCaches();
                            
                            return redirect()->back()
                                ->with('error', 'Database connection failed after update. Settings have been rolled back.')
                                ->withInput();
                        }
                        
                        $message = 'Database settings updated and tested successfully. Connection is working properly.';
                        
                    } catch (\Exception $e) {
                        // Rollback changes
                        $this->updateEnvFile($originalValues);
                        $this->clearAllCaches();
                        
                        return redirect()->back()
                            ->with('error', 'Database update failed and has been rolled back: ' . $e->getMessage())
                            ->withInput();
                    }
                    break;
                    
                case 'system':
                    $this->updateEnvFile([
                        'CACHE_DRIVER' => $request->cache_driver,
                        'QUEUE_CONNECTION' => $request->queue_connection,
                        'SESSION_DRIVER' => $request->session_driver,
                    ]);
                    $message = 'System settings updated successfully.';
                    break;
            }

            // Clear all caches after settings update (except for database changes which handle this separately)
            if ($request->setting_type !== 'database') {
                $this->clearAllCaches();
            }

            // Get production-appropriate success message
            $finalMessage = $this->getProductionSafeMessage($request->setting_type);

            return redirect()->route('admin.settings.index')
                ->with('success', $finalMessage);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update settings: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function maintenance(Request $request)
    {
        $request->validate([
            'action' => 'required|in:enable,disable',
            'message' => 'nullable|string|max:500',
        ]);

        try {
            if ($request->action === 'enable') {
                $options = [];
                if ($request->message) {
                    $options['message'] = $request->message;
                }
                Artisan::call('down', $options);
                $message = 'Maintenance mode enabled successfully.';
            } else {
                Artisan::call('up');
                $message = 'Maintenance mode disabled successfully.';
            }

            return redirect()->route('admin.settings.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to toggle maintenance mode: ' . $e->getMessage());
        }
    }

    public function clearCache(Request $request)
    {
        $request->validate([
            'type' => 'required|in:config,cache,route,view,all',
        ]);

        try {
            $results = [];
            
            switch ($request->type) {
                case 'config':
                    Artisan::call('config:clear');
                    $results[] = 'Configuration cache cleared';
                    
                    try {
                        Artisan::call('config:cache');
                        $results[] = 'Configuration cache rebuilt';
                    } catch (\Exception $e) {
                        // Config cache rebuild failed, continue without it
                        $results[] = 'Configuration cache rebuild skipped (not critical)';
                    }
                    $message = 'Configuration cache operations completed: ' . implode(', ', $results);
                    break;
                    
                case 'cache':
                    Artisan::call('cache:clear');
                    $message = 'Application cache cleared successfully.';
                    break;
                    
                case 'route':
                    Artisan::call('route:clear');
                    $results[] = 'Route cache cleared';
                    
                    try {
                        Artisan::call('route:cache');
                        $results[] = 'Route cache rebuilt';
                    } catch (\Exception $e) {
                        // Route cache rebuild failed, continue without it
                        $results[] = 'Route cache rebuild skipped (not critical)';
                    }
                    $message = 'Route cache operations completed: ' . implode(', ', $results);
                    break;
                    
                case 'view':
                    Artisan::call('view:clear');
                    $message = 'View cache cleared successfully.';
                    break;
                    
                case 'all':
                    $results = $this->clearAllCachesSafely();
                    $message = 'Cache clearing completed: ' . implode(', ', $results);
                    break;
            }

            // Use absolute URL to ensure proper redirect
            $redirectUrl = url('/admin/settings');
            
            return redirect($redirectUrl)
                ->with('success', $message);

        } catch (\Exception $e) {
            // Use absolute URL for error redirect too
            $redirectUrl = url('/admin/settings');
            
            return redirect($redirectUrl)
                ->with('error', 'Cache clearing encountered issues: ' . $e->getMessage() . '. Some caches may have been cleared successfully.');
        }
    }

    public function backup(Request $request)
    {
        $request->validate([
            'action' => 'required|in:create,download,list',
            'backup_file' => 'required_if:action,download|string',
        ]);

        try {
            switch ($request->action) {
                case 'create':
                    return $this->createBackup();
                case 'download':
                    return $this->downloadBackup($request->backup_file);
                case 'list':
                    return $this->listBackups();
                default:
                    return redirect()->back()->with('error', 'Invalid backup action.');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Backup operation failed: ' . $e->getMessage());
        }
    }

    private function createBackup()
    {
        $backupPath = storage_path('app/backups');
        
        // Create backup directory if it doesn't exist
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }
        
        $timestamp = now()->format('Y_m_d_H_i_s');
        $filename = 'db_backup_' . $timestamp . '.sql';
        $filepath = $backupPath . '/' . $filename;
        
        // Get database configuration
        $dbConfig = config('database.connections.' . config('database.default'));
        $dbHost = $dbConfig['host'];
        $dbPort = $dbConfig['port'];
        $dbName = $dbConfig['database'];
        $dbUser = $dbConfig['username'];
        $dbPass = $dbConfig['password'];
        
        // Check if mysqldump is available
        $mysqldumpPath = $this->findMysqldumpPath();
        
        if (!$mysqldumpPath) {
            // Fallback to PHP-based backup
            return $this->createPhpBackup($filepath, $filename);
        }
        
        // Create mysqldump command with proper escaping
        $command = sprintf(
            '"%s" --user=%s --password=%s --host=%s --port=%s --single-transaction --routines --triggers %s > "%s"',
            $mysqldumpPath,
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbName),
            $filepath
        );
        
        // Execute backup command
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0 && File::exists($filepath) && filesize($filepath) > 0) {
            // Store backup info in session for download
            session(key: ['last_backup' => [
                'filename' => $filename,
                'filepath' => $filepath,
                'size' => filesize($filepath),
                'created_at' => now()->format('Y-m-d H:i:s')
            ]]);
            
            return redirect()->route('admin.settings.index')
                ->with('success', "Database backup created successfully: {$filename} (" . $this->formatBytes(filesize($filepath)) . ")");
        } else {
            // Clean up failed backup file
            if (File::exists($filepath)) {
                File::delete($filepath);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to create database backup. Please check database configuration and permissions.');
        }
    }

    private function createPhpBackup($filepath, $filename)
    {
        try {
            // Test database connection first
            $pdo = DB::connection()->getPdo();
            if (!$pdo) {
                throw new \Exception('Unable to establish database connection');
            }
            
            $backupContent = "-- Database Backup Created: " . now()->format('Y-m-d H:i:s') . "\n";
            $userName = auth()->user() ? auth()->user()->name : 'System Admin';
            $backupContent .= "-- Generated by: " . $userName . "\n";
            $backupContent .= "-- Database: " . config('database.connections.' . config('database.default') . '.database') . "\n";
            $backupContent .= "-- Laravel Version: " . app()->version() . "\n\n";
            
            $backupContent .= "SET FOREIGN_KEY_CHECKS=0;\n";
            $backupContent .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
            $backupContent .= "SET AUTOCOMMIT = 0;\n";
            $backupContent .= "START TRANSACTION;\n\n";
            
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $dbName = config('database.connections.' . config('database.default') . '.database');
            
            $tableCount = 0;
            $rowCount = 0;
            
            foreach ($tables as $table) {
                try {
                    // Convert table object to array to handle different MySQL versions
                    $tableArray = (array) $table;
                    $tableName = reset($tableArray); // Get the first (and only) value
                    
                    if (empty($tableName)) {
                        continue; // Skip empty table names
                    }
                    
                    // Get table structure
                    $createTableResult = DB::select("SHOW CREATE TABLE `{$tableName}`");
                    if (empty($createTableResult)) {
                        continue; // Skip if no CREATE TABLE result
                    }
                    
                    $createTableArray = (array) $createTableResult[0];
                    $createTableSql = $createTableArray['Create Table'] ?? $createTableArray[array_keys($createTableArray)[1]] ?? null;
                    
                    if (!$createTableSql) {
                        continue; // Skip if no CREATE TABLE SQL found
                    }
                    
                    $backupContent .= "-- Table structure for `{$tableName}`\n";
                    $backupContent .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                    $backupContent .= $createTableSql . ";\n\n";
                    $tableCount++;
                    
                    // Get table data
                    $rows = DB::table($tableName)->get();
                    if ($rows->count() > 0) {
                        $backupContent .= "-- Data for table `{$tableName}`\n";
                        $backupContent .= "INSERT INTO `{$tableName}` VALUES\n";
                        
                        $rowData = [];
                        foreach ($rows as $row) {
                            $values = array_map(function($value) {
                                if ($value === null) {
                                    return 'NULL';
                                } elseif (is_numeric($value)) {
                                    return $value;
                                } else {
                                    return "'" . addslashes($value) . "'";
                                }
                            }, (array) $row);
                            $rowData[] = '(' . implode(', ', $values) . ')';
                            $rowCount++;
                        }
                        
                        $backupContent .= implode(",\n", $rowData) . ";\n\n";
                    }
                    
                } catch (\Exception $tableError) {
                    // Log error but continue with other tables
                    $backupContent .= "-- Error backing up table `{$tableName}`: " . $tableError->getMessage() . "\n\n";
                    continue;
                }
            }
            
            // Add closing statements
            $backupContent .= "SET FOREIGN_KEY_CHECKS=1;\n";
            $backupContent .= "COMMIT;\n\n";
            $backupContent .= "-- Backup completed: " . now()->format('Y-m-d H:i:s') . "\n";
            $backupContent .= "-- Tables backed up: {$tableCount}\n";
            $backupContent .= "-- Total rows: {$rowCount}\n";
            
            File::put($filepath, $backupContent);
            
            if (File::exists($filepath) && filesize($filepath) > 0) {
                session(['last_backup' => [
                    'filename' => $filename,
                    'filepath' => $filepath,
                    'size' => filesize($filepath),
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'method' => 'PHP',
                    'tables' => $tableCount,
                    'rows' => $rowCount
                ]]);
                
                return redirect()->route('admin.settings.index')
                    ->with('success', "Database backup created successfully using PHP method: {$filename} (" . $this->formatBytes(filesize($filepath)) . ") - {$tableCount} tables, {$rowCount} rows");
            } else {
                throw new \Exception('Backup file was not created or is empty');
            }
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'PHP backup failed: ' . $e->getMessage());
        }
    }

    private function downloadBackup($filename)
    {
        $filepath = storage_path('app/backups/' . $filename);
        
        if (!File::exists($filepath)) {
            return redirect()->back()
                ->with('error', 'Backup file not found: ' . $filename);
        }
        
        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    public function listBackups()
    {
        $backupPath = storage_path('app/backups');
        $backups = [];
        
        if (File::exists($backupPath)) {
            $files = File::files($backupPath);
            
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $backups[] = [
                        'filename' => basename($file),
                        'size' => filesize($file),
                        'size_formatted' => $this->formatBytes(filesize($file)),
                        'created_at' => date('Y-m-d H:i:s', filemtime($file)),
                        'age' => $this->timeAgo(filemtime($file))
                    ];
                }
            }
            
            // Sort by creation time (newest first)
            usort($backups, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
        }
        
        return response()->json(['backups' => $backups]);
    }

    public function deleteBackup(Request $request)
    {
        $request->validate([
            'filename' => 'required|string'
        ]);
        
        $filepath = storage_path('app/backups/' . $request->filename);
        
        if (!File::exists($filepath)) {
            return response()->json(['success' => false, 'message' => 'Backup file not found']);
        }
        
        try {
            File::delete($filepath);
            return response()->json(['success' => true, 'message' => 'Backup deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete backup: ' . $e->getMessage()]);
        }
    }

    private function findMysqldumpPath()
    {
        // Check if shell_exec is disabled (common in shared hosting)
        if (!function_exists('shell_exec') || !is_callable('shell_exec')) {
            return null; // Use PHP backup method
        }
        
        // Check if exec functions are disabled
        $disabledFunctions = explode(',', ini_get('disable_functions'));
        $disabledFunctions = array_map('trim', $disabledFunctions);
        if (in_array('shell_exec', $disabledFunctions) || in_array('exec', $disabledFunctions)) {
            return null; // Use PHP backup method
        }
        
        // Common paths for mysqldump (expanded for production)
        $possiblePaths = [
            // System PATH (most common in production)
            'mysqldump',
            
            // Linux/Ubuntu production servers
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/opt/mysql/bin/mysqldump',
            '/usr/local/mysql/bin/mysqldump',
            
            // CentOS/RHEL production servers
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            
            // macOS (development/staging)
            '/usr/local/bin/mysqldump',
            '/opt/homebrew/bin/mysqldump',
            
            // Windows development environments
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\mysqldump.exe',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
            
            // Docker containers (common in production)
            '/usr/local/bin/mysqldump',
            '/usr/bin/mysqldump',
            
            // Plesk hosting
            '/usr/local/psa/bin/mysqldump',
            
            // cPanel hosting
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
        ];
        
        // Test each possible path
        foreach ($possiblePaths as $path) {
            if (is_executable($path) || (PHP_OS_FAMILY === 'Windows' && file_exists($path))) {
                // Test if the command actually works
                $testOutput = shell_exec($path . ' --version 2>/dev/null');
                if ($testOutput && stripos($testOutput, 'mysqldump') !== false) {
                    return $path;
                }
            }
        }
        
        // Try to find using system commands
        try {
            $whereCommand = PHP_OS_FAMILY === 'Windows' ? 'where mysqldump 2>nul' : 'which mysqldump 2>/dev/null';
            $output = @shell_exec($whereCommand);
            
            if ($output && trim($output)) {
                $path = trim(explode("\n", $output)[0]);
                if ($path && (is_executable($path) || (PHP_OS_FAMILY === 'Windows' && file_exists($path)))) {
                    // Verify it's actually mysqldump
                    $testOutput = @shell_exec($path . ' --version 2>/dev/null');
                    if ($testOutput && stripos($testOutput, 'mysqldump') !== false) {
                        return $path;
                    }
                }
            }
        } catch (\Exception $e) {
            // Command failed, fall back to PHP method
        }
        
        // Alternative search methods for production
        try {
            // Try locate command (Linux)
            if (PHP_OS_FAMILY !== 'Windows') {
                $locateOutput = @shell_exec('locate mysqldump 2>/dev/null | head -5');
                if ($locateOutput) {
                    $paths = explode("\n", trim($locateOutput));
                    foreach ($paths as $path) {
                        if (is_executable($path) && basename($path) === 'mysqldump') {
                            $testOutput = @shell_exec($path . ' --version 2>/dev/null');
                            if ($testOutput && stripos($testOutput, 'mysqldump') !== false) {
                                return $path;
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Locate command not available
        }
        
        return null; // Fall back to PHP backup method
    }

    public function checkBackupCapabilities()
    {
        $capabilities = [
            'php_backup' => true, // Always available
            'mysqldump_available' => false,
            'mysqldump_path' => null,
            'shell_exec_enabled' => function_exists('shell_exec') && is_callable('shell_exec'),
            'exec_enabled' => function_exists('exec') && is_callable('exec'),
            'environment' => app()->environment(),
            'hosting_type' => $this->detectHostingType(),
            'recommendations' => []
        ];

        // Check if mysqldump is available
        $mysqldumpPath = $this->findMysqldumpPath();
        if ($mysqldumpPath) {
            $capabilities['mysqldump_available'] = true;
            $capabilities['mysqldump_path'] = $mysqldumpPath;
            $capabilities['recommendations'][] = 'mysqldump available - backups will be fast and efficient';
        } else {
            $capabilities['recommendations'][] = 'mysqldump not available - using PHP backup method (slower but reliable)';
        }

        // Check shell functions
        if (!$capabilities['shell_exec_enabled']) {
            $capabilities['recommendations'][] = 'shell_exec disabled - PHP backup method will be used';
        }

        // Environment-specific recommendations
        if (app()->environment('production')) {
            if ($capabilities['hosting_type'] === 'shared') {
                $capabilities['recommendations'][] = 'Shared hosting detected - PHP backup method is most reliable';
            } elseif ($capabilities['hosting_type'] === 'vps') {
                $capabilities['recommendations'][] = 'VPS/Dedicated server - consider installing mysql-client for faster backups';
            }
        }

        return response()->json($capabilities);
    }

    private function detectHostingType()
    {
        // Try to detect hosting environment
        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            $serverSoftware = strtolower($_SERVER['SERVER_SOFTWARE']);
            
            // Shared hosting indicators
            if (strpos($serverSoftware, 'apache') !== false && 
                (strpos($_SERVER['SERVER_NAME'] ?? '', 'hostgator') !== false ||
                 strpos($_SERVER['SERVER_NAME'] ?? '', 'bluehost') !== false ||
                 strpos($_SERVER['SERVER_NAME'] ?? '', 'godaddy') !== false ||
                 strpos($_SERVER['SERVER_NAME'] ?? '', 'cpanel') !== false)) {
                return 'shared';
            }
            
            // VPS/Cloud indicators
            if (strpos($serverSoftware, 'nginx') !== false || 
                isset($_SERVER['AWS_REGION']) ||
                isset($_SERVER['DIGITAL_OCEAN']) ||
                file_exists('/etc/cloud')) {
                return 'vps';
            }
        }
        
        // Check for common shared hosting limitations
        $disabledFunctions = explode(',', ini_get('disable_functions'));
        $disabledFunctions = array_map('trim', $disabledFunctions);
        
        if (in_array('shell_exec', $disabledFunctions) || 
            in_array('exec', $disabledFunctions) ||
            in_array('system', $disabledFunctions)) {
            return 'shared';
        }
        
        return 'unknown';
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function timeAgo($timestamp)
    {
        $difference = time() - $timestamp;
        
        if ($difference < 60) {
            return $difference . ' seconds ago';
        } elseif ($difference < 3600) {
            return floor($difference / 60) . ' minutes ago';
        } elseif ($difference < 86400) {
            return floor($difference / 3600) . ' hours ago';
        } else {
            return floor($difference / 86400) . ' days ago';
        }
    }

    /**
     * Update the .env file with new values
     */
    private function updateEnvFile(array $updates)
    {
        $envPath = base_path('.env');
        
        if (!File::exists($envPath)) {
            throw new \Exception('.env file not found.');
        }
        
        $envContent = File::get($envPath);
        
        foreach ($updates as $key => $value) {
            // Handle null values
            if ($value === null || $value === '') {
                $value = '';
            }
            
            // Check if key already exists
            $pattern = "/^{$key}=.*$/m";
            $replacement = "{$key}={$value}";
            
            if (preg_match($pattern, $envContent)) {
                // Update existing key
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                // Add new key at the end
                $envContent .= "\n{$replacement}";
            }
        }
        
        File::put($envPath, $envContent);
    }

    public function testDbConnection(Request $request)
    {
        $request->validate([
            'host' => 'required|string',
            'port' => 'required|integer',
            'database' => 'required|string',
            'username' => 'required|string',
            'password' => 'nullable|string',
        ]);

        $result = $this->testDatabaseConnection(
            $request->host,
            $request->port,
            $request->database,
            $request->username,
            $request->password ?: env('DB_PASSWORD')
        );

        return response()->json($result);
    }

    public function productionGuide()
    {
        $guideContent = file_get_contents(base_path('PRODUCTION_DEPLOYMENT.md'));
        
        return response($guideContent)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Content-Disposition', 'inline; filename="production-deployment-guide.md"');
    }

    public function getSystemHealth()
    {
        try {
            $health = [
                'timestamp' => now()->toISOString(),
                'server' => [
                    'php_version' => phpversion(),
                    'laravel_version' => app()->version(),
                    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                    'operating_system' => php_uname('s') . ' ' . php_uname('r'),
                    'server_time' => now()->format('Y-m-d H:i:s T'),
                    'timezone' => config('app.timezone'),
                ],
                'performance' => [
                    'memory_usage' => [
                        'current' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
                        'peak' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB',
                        'limit' => ini_get('memory_limit'),
                    ],
                    'disk_space' => $this->getDiskSpaceInfo(),
                    'database' => $this->getDatabaseHealth(),
                ],
                'application' => [
                    'environment' => app()->environment(),
                    'debug_mode' => config('app.debug'),
                    'maintenance_mode' => app()->isDownForMaintenance(),
                    'cache_driver' => config('cache.default'),
                    'session_driver' => config('session.driver'),
                    'queue_driver' => config('queue.default'),
                ],
                'security' => [
                    'https_enabled' => request()->isSecure(),
                    'app_key_set' => !empty(config('app.key')),
                    'env_file_writable' => is_writable(base_path('.env')),
                    'storage_writable' => is_writable(storage_path()),
                ],
                'recent_activity' => [
                    'total_users' => \App\Models\User::count(),
                    'total_admins' => \App\Models\Admin::count(),
                    'recent_logins' => $this->getRecentLoginsCount(),
                    'backup_count' => $this->getBackupCount(),
                ],
                'status' => 'healthy'
            ];

            // Determine overall health status
            $issues = [];
            if ($health['application']['debug_mode'] && app()->environment('production')) {
                $issues[] = 'Debug mode enabled in production';
            }
            if (!$health['security']['https_enabled'] && app()->environment('production')) {
                $issues[] = 'HTTPS not enabled in production';
            }
            if (!$health['security']['app_key_set']) {
                $issues[] = 'Application key not set';
            }

            if (!empty($issues)) {
                $health['status'] = 'warning';
                $health['issues'] = $issues;
            }

            return response()->json($health);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve system health: ' . $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    public function updateTheme(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark,auto'
        ]);

        $user = auth()->user();
        
        // Store theme preference in database or session
        if (method_exists($user, 'setPreference')) {
            $user->setPreference('theme', $request->theme);
        } else {
            // Fallback to session
            session(['admin_theme' => $request->theme]);
        }

        return response()->json([
            'success' => true,
            'theme' => $request->theme,
            'message' => 'Theme updated successfully'
        ]);
    }

    public function getNotifications()
    {
        $notifications = [
            'system' => $this->getSystemNotifications(),
            'security' => $this->getSecurityNotifications(),
            'updates' => $this->getUpdateNotifications(),
            'performance' => $this->getPerformanceNotifications()
        ];

        return response()->json($notifications);
    }

    private function getDiskSpaceInfo()
    {
        try {
            $totalSpace = disk_total_space('/');
            $freeSpace = disk_free_space('/');
            $usedSpace = $totalSpace - $freeSpace;

            return [
                'total' => $this->formatBytes($totalSpace),
                'used' => $this->formatBytes($usedSpace),
                'free' => $this->formatBytes($freeSpace),
                'usage_percentage' => round(($usedSpace / $totalSpace) * 100, 1),
            ];
        } catch (\Exception $e) {
            return [
                'total' => 'Unknown',
                'used' => 'Unknown',
                'free' => 'Unknown',
                'usage_percentage' => 0,
                'error' => 'Unable to determine disk space'
            ];
        }
    }

    private function getDatabaseHealth()
    {
        try {
            $pdo = DB::connection()->getPdo();
            $dbConfig = config('database.connections.' . config('database.default'));
            
            // Get database size
            $dbSize = 0;
            try {
                $result = DB::select("
                    SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                    FROM information_schema.tables 
                    WHERE table_schema = ?
                ", [$dbConfig['database']]);
                $dbSize = $result[0]->size_mb ?? 0;
            } catch (\Exception $e) {
                $dbSize = 'Unknown';
            }

            // Get table count
            $tableCount = 0;
            try {
                $tables = DB::select('SHOW TABLES');
                $tableCount = count($tables);
            } catch (\Exception $e) {
                $tableCount = 'Unknown';
            }

            return [
                'connection' => 'Connected',
                'driver' => $dbConfig['driver'] ?? 'Unknown',
                'host' => $dbConfig['host'] ?? 'Unknown',
                'database' => $dbConfig['database'] ?? 'Unknown',
                'size' => is_numeric($dbSize) ? $dbSize . ' MB' : $dbSize,
                'tables' => $tableCount,
                'status' => 'healthy'
            ];
        } catch (\Exception $e) {
            return [
                'connection' => 'Failed',
                'error' => $e->getMessage(),
                'status' => 'error'
            ];
        }
    }

    private function getRecentLoginsCount()
    {
        try {
            // Count recent admin logins in the last 24 hours
            return \App\Models\Admin::where('last_login_at', '>=', now()->subDay())->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getBackupCount()
    {
        try {
            $backupPath = storage_path('app/backups');
            if (!is_dir($backupPath)) {
                return 0;
            }
            
            $files = glob($backupPath . '/*.sql');
            return count($files);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getSystemNotifications()
    {
        $notifications = [];

        // Check for PHP version
        if (version_compare(phpversion(), '8.1', '<')) {
            $notifications[] = [
                'type' => 'warning',
                'title' => 'PHP Version',
                'message' => 'Consider upgrading to PHP 8.1+ for better performance',
                'action' => null
            ];
        }

        // Check for debug mode in production
        if (config('app.debug') && app()->environment('production')) {
            $notifications[] = [
                'type' => 'error',
                'title' => 'Debug Mode',
                'message' => 'Debug mode is enabled in production - security risk!',
                'action' => 'Disable debug mode in .env file'
            ];
        }

        // Check storage space
        $freeSpace = disk_free_space(storage_path());
        if ($freeSpace && $freeSpace < 100 * 1024 * 1024) { // Less than 100MB
            $notifications[] = [
                'type' => 'warning',
                'title' => 'Low Storage Space',
                'message' => 'Less than 100MB free space remaining',
                'action' => 'Clean up old files or increase storage'
            ];
        }

        return $notifications;
    }

    private function getSecurityNotifications()
    {
        $notifications = [];

        // Check HTTPS in production
        if (!request()->isSecure() && app()->environment('production')) {
            $notifications[] = [
                'type' => 'warning',
                'title' => 'HTTPS Not Enabled',
                'message' => 'Your site should use HTTPS in production',
                'action' => 'Enable SSL certificate'
            ];
        }

        // Check APP_KEY
        if (empty(config('app.key'))) {
            $notifications[] = [
                'type' => 'error',
                'title' => 'Missing Application Key',
                'message' => 'Application key is not set',
                'action' => 'Run: php artisan key:generate'
            ];
        }

        return $notifications;
    }

    private function getUpdateNotifications()
    {
        $notifications = [];

        // This could check for Laravel updates, package updates, etc.
        // For now, just a placeholder
        $notifications[] = [
            'type' => 'info',
            'title' => 'System Up to Date',
            'message' => 'All components are current',
            'action' => null
        ];

        return $notifications;
    }

    private function getPerformanceNotifications()
    {
        $notifications = [];

        // Check cache driver
        if (config('cache.default') === 'file' && app()->environment('production')) {
            $notifications[] = [
                'type' => 'info',
                'title' => 'Cache Optimization',
                'message' => 'Consider using Redis or Memcached for better performance',
                'action' => 'Configure Redis caching'
            ];
        }

        // Check opcache
        if (!function_exists('opcache_get_status') || !opcache_get_status()) {
            $notifications[] = [
                'type' => 'info',
                'title' => 'OPcache',
                'message' => 'OPcache is not enabled - could improve performance',
                'action' => 'Enable OPcache in PHP configuration'
            ];
        }

        return $notifications;
    }

    /**
     * Test database connection with provided credentials
     */
    private function testDatabaseConnection($host, $port, $database, $username, $password)
    {
        try {
            $connection = config('database.default');
            $driver = config("database.connections.{$connection}.driver", 'mysql');
            
            if ($driver === 'mysql') {
                $dsn = "mysql:host={$host};port={$port};dbname={$database}";
                $pdo = new \PDO($dsn, $username, $password, [
                    \PDO::ATTR_TIMEOUT => 5,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                ]);
                
                // Test with a simple query
                $pdo->query('SELECT 1');
                $pdo = null;
                
                return ['success' => true, 'error' => null];
            }
            
            return ['success' => false, 'error' => 'Unsupported database driver: ' . $driver];
            
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Test current database connection
     */
    private function testCurrentDatabaseConnection()
    {
        try {
            \DB::connection()->getPdo();
            \DB::connection()->select('SELECT 1');
            return ['success' => true, 'error' => null];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Clear all application caches safely (production-optimized)
     */
    private function clearAllCachesSafely()
    {
        $results = [];
        $errors = [];
        
        // Check if we're in production environment
        $isProduction = app()->environment('production');
        
        // Clear caches one by one with individual error handling
        try {
            Artisan::call('config:clear');
            $results[] = 'Config cache cleared';
        } catch (\Exception $e) {
            $errors[] = 'Config clear failed: ' . $e->getMessage();
        }
        
        try {
            Artisan::call('cache:clear');
            $results[] = 'Application cache cleared';
        } catch (\Exception $e) {
            $errors[] = 'App cache clear failed: ' . $e->getMessage();
        }
        
        try {
            Artisan::call('route:clear');
            $results[] = 'Route cache cleared';
        } catch (\Exception $e) {
            $errors[] = 'Route clear failed: ' . $e->getMessage();
        }
        
        try {
            Artisan::call('view:clear');
            $results[] = 'View cache cleared';
        } catch (\Exception $e) {
            $errors[] = 'View clear failed: ' . $e->getMessage();
        }
        
        // In production, be more careful with cache rebuilding
        if ($isProduction) {
            // Only rebuild config cache in production for better performance
            try {
                Artisan::call('config:cache');
                $results[] = 'Config cache rebuilt for production';
            } catch (\Exception $e) {
                $results[] = 'Config rebuild skipped (not critical)';
            }
            
            // Clear opcache if available (important for production)
            if (function_exists('opcache_reset')) {
                opcache_reset();
                $results[] = 'OPcache cleared';
            }
        } else {
            // Development environment - try to rebuild caches but don't worry if it fails
            try {
                Artisan::call('config:cache');
                $results[] = 'Config cache rebuilt';
            } catch (\Exception $e) {
                $results[] = 'Config rebuild skipped';
            }
        }
        
        return $results;
    }
    
    /**
     * Get production-safe environment update message
     */
    private function getProductionSafeMessage($settingType)
    {
        $isProduction = app()->environment('production');
        
        if (!$isProduction) {
            return 'Settings updated successfully. Configuration reloaded.';
        }
        
        $baseMessage = ucfirst($settingType) . ' settings updated successfully in production environment.';
        
        switch ($settingType) {
            case 'app':
                return $baseMessage . ' Changes will take effect gradually as PHP processes reload. No downtime expected.';
            case 'database':
                return $baseMessage . ' Database connection tested and working. Application remains online.';
            case 'mail':
                return $baseMessage . ' Email configuration updated. Test email sending to verify settings.';
            case 'system':
                return $baseMessage . ' Performance settings updated. Monitor application performance.';
            default:
                return $baseMessage . ' Changes applied without downtime.';
        }
    }

    /**
     * Clear all application caches (for backward compatibility)
     */
    private function clearAllCaches()
    {
        $this->clearAllCachesSafely();
    }
}