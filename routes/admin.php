<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\ActivityLogController;


// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Protected Admin Routes - All admin roles
    Route::middleware(['admin.auth', 'log.activity'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Activity Logs Routes - Proper permission checks
        Route::middleware(['permission:view-activity-logs'])->group(function () {
            Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
            Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
            Route::get('activity-logs/stats', [ActivityLogController::class, 'stats'])->name('activity-logs.stats');
        });
        
        Route::middleware(['permission:manage-activity-logs'])->group(function () {
            Route::get('activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
            Route::delete('activity-logs/{activityLog}', [ActivityLogController::class, 'destroy'])->name('activity-logs.destroy');
            Route::delete('activity-logs/bulk-delete', [ActivityLogController::class, 'bulkDelete'])->name('activity-logs.bulk-delete');
        });
        
        // Profile Management - Available to all admin users
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'show'])->name('show');
            Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
            Route::put('/update', [ProfileController::class, 'update'])->name('update');
            Route::get('/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('change-password');
            Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('update-password');
            Route::get('/security', [ProfileController::class, 'security'])->name('security');
            Route::put('/security', [ProfileController::class, 'updateSecuritySettings'])->name('update-security');
        });
        
        // User Management - Different permissions for different actions
        Route::middleware(['permission:view-users|manage-users'])->group(function () {
            Route::get('users', [UserController::class, 'index'])->name('users.index');
            Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        });
        
        Route::middleware(['permission:manage-users'])->group(function () {
            Route::get('users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('users', [UserController::class, 'store'])->name('users.store');
            Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        });
        
        Route::middleware(['permission:manage-users&delete-users'])->group(function () {
            Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });
        
        // Admin Management - SuperAdmin Only
        Route::middleware(['permission:manage-admins'])->group(function () {
            Route::resource('admins', AdminController::class);
        });
        
        // Role Management - SuperAdmin Only
        Route::middleware(['permission:manage-roles'])->group(function () {
            Route::resource('roles', RoleController::class);
        });
        
        // Permission Management - SuperAdmin Only  
        Route::middleware(['permission:manage-permissions'])->group(function () {
            Route::resource('permissions', PermissionController::class);
        });
        
        // System Settings - SuperAdmin Only
        Route::middleware(['permission:system-settings'])->group(function () {
            Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
            Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
            Route::post('/settings/maintenance', [SettingsController::class, 'maintenance'])->name('settings.maintenance');
            Route::post('/settings/cache', [SettingsController::class, 'clearCache'])->name('settings.cache');
            Route::post('/settings/backup', [SettingsController::class, 'backup'])->name('settings.backup');
            Route::get('/settings/backups', [SettingsController::class, 'listBackups'])->name('settings.backups.list');
            Route::delete('/settings/backups', [SettingsController::class, 'deleteBackup'])->name('settings.backups.delete');
            Route::get('/settings/backup-capabilities', [SettingsController::class, 'checkBackupCapabilities'])->name('settings.backup-capabilities');
            Route::get('/settings/system-health', [SettingsController::class, 'getSystemHealth'])->name('settings.system-health');
            Route::get('/settings/notifications', [SettingsController::class, 'getNotifications'])->name('settings.notifications');
            Route::post('/settings/theme', [SettingsController::class, 'updateTheme'])->name('settings.theme');
            Route::post('/settings/test-db', [SettingsController::class, 'testDbConnection'])->name('settings.test-db');
            Route::get('/settings/production-guide', [SettingsController::class, 'productionGuide'])->name('settings.production-guide');
        });
        
        // Reports - Available to both Admin and SuperAdmin
        Route::middleware(['permission:view-reports'])->group(function () {
            Route::get('/reports', [DashboardController::class, 'reports'])->name('reports.index');
        });
    });
});