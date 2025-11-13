<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckProductionReadiness extends Command
{
    protected $signature = 'app:check-production';
    protected $description = 'Check if the application is ready for production deployment';

    public function handle()
    {
        $this->info('Laravel Production Readiness Check');
        $this->info('=====================================');
        
        $issues = [];
        $warnings = [];
        $good = [];
        
        // Check environment
        if (app()->environment('production')) {
            $good[] = '✅ Environment set to production';
        } else {
            $warnings[] = '⚠️  Environment is not set to production (current: ' . app()->environment() . ')';
        }
        
        // Check debug mode
        if (!config('app.debug')) {
            $good[] = '✅ Debug mode is disabled';
        } else {
            $issues[] = '❌ Debug mode is enabled (security risk in production)';
        }
        
        // Check APP_KEY
        if (config('app.key')) {
            $good[] = '✅ Application key is set';
        } else {
            $issues[] = '❌ Application key is not set (run: php artisan key:generate)';
        }
        
        // Check database connection
        try {
            DB::connection()->getPdo();
            $good[] = '✅ Database connection is working';
        } catch (\Exception $e) {
            $issues[] = '❌ Database connection failed: ' . $e->getMessage();
        }
        
        // Check web server
        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            if (strpos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false || 
                strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false) {
                $good[] = '✅ Using production web server: ' . $_SERVER['SERVER_SOFTWARE'];
            } else {
                $warnings[] = '⚠️  Web server: ' . $_SERVER['SERVER_SOFTWARE'];
            }
        } else {
            $warnings[] = '⚠️  Unable to detect web server (might be using artisan serve)';
        }
        
        // Check file permissions
        if (is_writable(storage_path())) {
            $good[] = '✅ Storage directory is writable';
        } else {
            $issues[] = '❌ Storage directory is not writable';
        }
        
        if (is_writable(base_path('bootstrap/cache'))) {
            $good[] = '✅ Bootstrap cache directory is writable';
        } else {
            $issues[] = '❌ Bootstrap cache directory is not writable';
        }
        
        // Check .env file
        if (file_exists(base_path('.env'))) {
            if (is_writable(base_path('.env'))) {
                $good[] = '✅ .env file exists and is writable';
            } else {
                $warnings[] = '⚠️  .env file exists but is not writable (admin settings won\'t work)';
            }
        } else {
            $issues[] = '❌ .env file does not exist';
        }
        
        // Check cache configuration
        $cacheDriver = config('cache.default');
        if (in_array($cacheDriver, ['redis', 'memcached'])) {
            $good[] = '✅ Using efficient cache driver: ' . $cacheDriver;
        } else {
            $warnings[] = '⚠️  Using basic cache driver: ' . $cacheDriver . ' (consider Redis for better performance)';
        }
        
        // Check session configuration
        $sessionDriver = config('session.driver');
        if (in_array($sessionDriver, ['redis', 'database'])) {
            $good[] = '✅ Using scalable session driver: ' . $sessionDriver;
        } else {
            $warnings[] = '⚠️  Using basic session driver: ' . $sessionDriver;
        }
        
        // Check HTTPS
        $appUrl = config('app.url', '');
        if (str_starts_with($appUrl, 'https://')) {
            $good[] = '✅ HTTPS is configured';
        } else {
            $warnings[] = '⚠️  HTTPS is not configured (recommended for production)';
        }
        
        // Display results
        if (!empty($good)) {
            $this->info("\n✅ What's Working Well:");
            foreach ($good as $item) {
                $this->line("   $item");
            }
        }
        
        if (!empty($warnings)) {
            $this->warn("\n⚠️  Warnings (Recommended Improvements):");
            foreach ($warnings as $warning) {
                $this->line("   $warning");
            }
        }
        
        if (!empty($issues)) {
            $this->error("\n❌ Critical Issues (Must Fix):");
            foreach ($issues as $issue) {
                $this->line("   $issue");
            }
        }
        
        // Overall assessment
        $this->info("\n" . str_repeat('=', 50));
        
        if (empty($issues)) {
            if (empty($warnings)) {
                $this->info('🎉 EXCELLENT! Your application is production-ready!');
            } else {
                $this->info('✅ GOOD! Your application is ready for production with minor optimizations available.');
            }
        } else {
            $this->error('❌ NOT READY! Please fix the critical issues before deploying to production.');
            return 1;
        }
        
        // Environment change safety info
        $this->info("\n📋 Environment Changes Information:");
        if (app()->environment('production')) {
            $this->info('   • Environment changes via admin panel will be applied safely');
            $this->info('   • No downtime expected during configuration updates');
            $this->info('   • Changes take effect gradually as PHP processes reload');
        } else {
            $this->warn('   • Current environment will cause server restarts during admin changes');
            $this->warn('   • Switch to production environment for zero-downtime updates');
        }
        
        return 0;
    }
}