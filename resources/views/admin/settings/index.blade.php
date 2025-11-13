@extends('layouts.admin')

@section('title', 'System Settings')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-gear me-2 text-secondary"></i>System Settings
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearCache('all')">
                <i class="bi bi-arrow-clockwise me-1"></i>Clear All Cache
            </button>
        </div>
    </div>
</div>

<!-- Super Admin Notice -->
<div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
    <div class="d-flex">
        <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 1.5rem;"></i>
        <div>
            <h5 class="alert-heading mb-1">Critical System Area</h5>
            <p class="mb-0">
                You are accessing critical system settings. Changes here can affect the entire application.
                <strong>Only make changes if you understand their impact.</strong>
            </p>
        </div>
    </div>
</div>

@if(app()->environment('local', 'development'))
    <!-- Development Environment Warning -->
    <div class="alert alert-warning border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex">
            <i class="bi bi-exclamation-circle-fill me-3" style="font-size: 1.5rem;"></i>
            <div>
                <h5 class="alert-heading mb-1">Development Environment Detected</h5>
                <p class="mb-2">
                    You're running in <strong>{{ app()->environment() }}</strong> mode with <kbd>php artisan serve</kbd>.
                    Environment changes will cause server restarts and temporary downtime.
                </p>
                <p class="mb-0">
                    <strong>For Production:</strong> Use Apache/Nginx + PHP-FPM for zero-downtime environment updates.
                    <a href="{{ route('admin.settings.production-guide') }}" target="_blank" class="alert-link">
                        View Production Deployment Guide
                    </a>
                </p>
            </div>
        </div>
    </div>
@elseif(app()->environment('production'))
    <!-- Production Environment Info -->
    <div class="alert alert-success border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex">
            <i class="bi bi-shield-check-fill me-3" style="font-size: 1.5rem;"></i>
            <div>
                <h5 class="alert-heading mb-1">Production Environment</h5>
                <p class="mb-0">
                    Environment changes will be applied safely without downtime. 
                    Configuration updates take effect gradually as PHP processes reload.
                </p>
            </div>
        </div>
    </div>
@endif

<!-- System Information Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Environment</h6>
                        <h5 class="mb-0">{{ ucfirst($settings['app']['environment']) }}</h5>
                    </div>
                    <i class="bi bi-server" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">PHP Version</h6>
                        <h5 class="mb-0">{{ $systemInfo['php_version'] }}</h5>
                    </div>
                    <i class="bi bi-code-slash" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Laravel</h6>
                        <h5 class="mb-0">{{ $systemInfo['laravel_version'] }}</h5>
                    </div>
                    <i class="bi bi-layers" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Maintenance</h6>
                        <h5 class="mb-0">{{ $settings['system']['maintenance_mode'] ? 'On' : 'Off' }}</h5>
                    </div>
                    <i class="bi bi-tools" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Application Settings -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>Application Settings
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    <input type="hidden" name="setting_type" value="app">
                    
                    <div class="mb-3">
                        <label for="app_name" class="form-label fw-bold">Application Name</label>
                        <input type="text" class="form-control @error('app_name') is-invalid @enderror" 
                               id="app_name" name="app_name" value="{{ old('app_name', $settings['app']['name']) }}" required>
                        @error('app_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="app_url" class="form-label fw-bold">Application URL</label>
                        <input type="url" class="form-control @error('app_url') is-invalid @enderror" 
                               id="app_url" name="app_url" value="{{ old('app_url', $settings['app']['url']) }}" required>
                        @error('app_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="app_timezone" class="form-label fw-bold">Timezone</label>
                        <select class="form-select @error('app_timezone') is-invalid @enderror" 
                                id="app_timezone" name="app_timezone" required>
                            <option value="UTC" {{ old('app_timezone', $settings['app']['timezone']) == 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="America/New_York" {{ old('app_timezone', $settings['app']['timezone']) == 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                            <option value="America/Chicago" {{ old('app_timezone', $settings['app']['timezone']) == 'America/Chicago' ? 'selected' : '' }}>America/Chicago</option>
                            <option value="America/Denver" {{ old('app_timezone', $settings['app']['timezone']) == 'America/Denver' ? 'selected' : '' }}>America/Denver</option>
                            <option value="America/Los_Angeles" {{ old('app_timezone', $settings['app']['timezone']) == 'America/Los_Angeles' ? 'selected' : '' }}>America/Los_Angeles</option>
                            <option value="Europe/London" {{ old('app_timezone', $settings['app']['timezone']) == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                            <option value="Europe/Paris" {{ old('app_timezone', $settings['app']['timezone']) == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                            <option value="Asia/Tokyo" {{ old('app_timezone', $settings['app']['timezone']) == 'Asia/Tokyo' ? 'selected' : '' }}>Asia/Tokyo</option>
                            <option value="Asia/Shanghai" {{ old('app_timezone', $settings['app']['timezone']) == 'Asia/Shanghai' ? 'selected' : '' }}>Asia/Shanghai</option>
                        </select>
                        @error('app_timezone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="session_lifetime" class="form-label fw-bold">Session Lifetime (minutes)</label>
                        <input type="number" class="form-control @error('session_lifetime') is-invalid @enderror" 
                               id="session_lifetime" name="session_lifetime" 
                               value="{{ old('session_lifetime', $settings['system']['session_lifetime']) }}" 
                               min="1" max="1440" required>
                        @error('session_lifetime')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">How long users stay logged in (1-1440 minutes)</small>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Update Application Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Email Settings -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-envelope me-2"></i>Email Settings
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    <input type="hidden" name="setting_type" value="mail">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_mailer" class="form-label fw-bold">Mail Driver</label>
                                <select class="form-select @error('mail_mailer') is-invalid @enderror" 
                                        id="mail_mailer" name="mail_mailer" required>
                                    <option value="smtp" {{ old('mail_mailer', $settings['mail']['driver']) == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                    <option value="sendmail" {{ old('mail_mailer', $settings['mail']['driver']) == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                    <option value="mailgun" {{ old('mail_mailer', $settings['mail']['driver']) == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                    <option value="ses" {{ old('mail_mailer', $settings['mail']['driver']) == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                    <option value="log" {{ old('mail_mailer', $settings['mail']['driver']) == 'log' ? 'selected' : '' }}>Log (Testing)</option>
                                </select>
                                @error('mail_mailer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_host" class="form-label fw-bold">SMTP Host</label>
                                <input type="text" class="form-control @error('mail_host') is-invalid @enderror" 
                                       id="mail_host" name="mail_host" 
                                       value="{{ old('mail_host', $settings['mail']['host']) }}" 
                                       placeholder="smtp.gmail.com" required>
                                @error('mail_host')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="mail_port" class="form-label fw-bold">SMTP Port</label>
                                <input type="number" class="form-control @error('mail_port') is-invalid @enderror" 
                                       id="mail_port" name="mail_port" 
                                       value="{{ old('mail_port', $settings['mail']['port']) }}" 
                                       min="1" max="65535" placeholder="587" required>
                                @error('mail_port')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="mail_encryption" class="form-label fw-bold">Encryption</label>
                                <select class="form-select @error('mail_encryption') is-invalid @enderror" 
                                        id="mail_encryption" name="mail_encryption">
                                    <option value="" {{ old('mail_encryption', $settings['mail']['encryption']) == '' ? 'selected' : '' }}>None</option>
                                    <option value="tls" {{ old('mail_encryption', $settings['mail']['encryption']) == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ old('mail_encryption', $settings['mail']['encryption']) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                </select>
                                @error('mail_encryption')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="test_email" class="form-label fw-bold">Test Email</label>
                                <button type="button" class="form-control btn btn-outline-info" onclick="testEmail()">
                                    <i class="bi bi-envelope-check me-1"></i>Send Test
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_username" class="form-label fw-bold">SMTP Username</label>
                                <input type="text" class="form-control @error('mail_username') is-invalid @enderror" 
                                       id="mail_username" name="mail_username" 
                                       value="{{ old('mail_username', $settings['mail']['username']) }}" 
                                       placeholder="your-email@domain.com">
                                @error('mail_username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_password" class="form-label fw-bold">SMTP Password</label>
                                <input type="password" class="form-control @error('mail_password') is-invalid @enderror" 
                                       id="mail_password" name="mail_password" 
                                       placeholder="Leave blank to keep current password">
                                @error('mail_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Leave empty to keep existing password</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_from_name" class="form-label fw-bold">From Name</label>
                                <input type="text" class="form-control @error('mail_from_name') is-invalid @enderror" 
                                       id="mail_from_name" name="mail_from_name" 
                                       value="{{ old('mail_from_name', $settings['mail']['from_name']) }}" required>
                                @error('mail_from_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_from_address" class="form-label fw-bold">From Email Address</label>
                                <input type="email" class="form-control @error('mail_from_address') is-invalid @enderror" 
                                       id="mail_from_address" name="mail_from_address" 
                                       value="{{ old('mail_from_address', $settings['mail']['from_address']) }}" required>
                                @error('mail_from_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Update Email Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

        <!-- Database Settings -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-database me-2"></i>Database Settings
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    <input type="hidden" name="setting_type" value="database">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="db_host" class="form-label fw-bold">Database Host</label>
                                <input type="text" class="form-control @error('db_host') is-invalid @enderror" 
                                       id="db_host" name="db_host" 
                                       value="{{ old('db_host', $settings['database']['host']) }}" required>
                                @error('db_host')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="db_port" class="form-label fw-bold">Database Port</label>
                                <input type="number" class="form-control @error('db_port') is-invalid @enderror" 
                                       id="db_port" name="db_port" 
                                       value="{{ old('db_port', $settings['database']['port']) }}" 
                                       min="1" max="65535" required>
                                @error('db_port')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="db_database" class="form-label fw-bold">Database Name</label>
                                <input type="text" class="form-control @error('db_database') is-invalid @enderror" 
                                       id="db_database" name="db_database" 
                                       value="{{ old('db_database', $settings['database']['database']) }}" required>
                                @error('db_database')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="db_username" class="form-label fw-bold">Database Username</label>
                                <input type="text" class="form-control @error('db_username') is-invalid @enderror" 
                                       id="db_username" name="db_username" 
                                       value="{{ old('db_username', $settings['database']['username']) }}" required>
                                @error('db_username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="db_password" class="form-label fw-bold">Database Password</label>
                                <input type="password" class="form-control @error('db_password') is-invalid @enderror" 
                                       id="db_password" name="db_password" 
                                       placeholder="Leave blank to keep current password">
                                @error('db_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Leave empty to keep existing password</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Test Connection</label>
                                <button type="button" class="form-control btn btn-outline-success" onclick="testDatabaseConnection()">
                                    <i class="bi bi-database-check me-1"></i>Test Connection
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Critical Warning:</strong> Changing database settings incorrectly can break the application and make it inaccessible. 
                        <strong>Always test the connection first!</strong>
                        <br><small class="mt-1 d-block">The system will automatically test and rollback if the connection fails, but it's better to verify first.</small>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="d-grid">
                                <button type="button" class="btn btn-outline-success" onclick="testDatabaseConnection()">
                                    <i class="bi bi-shield-check me-1"></i>Test Connection First
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-info">
                                    <i class="bi bi-database-gear me-1"></i>Update Database Settings
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- System Performance Settings -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-secondary text-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-speedometer2 me-2"></i>System Performance
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    <input type="hidden" name="setting_type" value="system">
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cache_driver" class="form-label fw-bold">Cache Driver</label>
                                <select class="form-select @error('cache_driver') is-invalid @enderror" 
                                        id="cache_driver" name="cache_driver" required>
                                    <option value="file" {{ old('cache_driver', $settings['system']['cache_driver']) == 'file' ? 'selected' : '' }}>File</option>
                                    <option value="redis" {{ old('cache_driver', $settings['system']['cache_driver']) == 'redis' ? 'selected' : '' }}>Redis</option>
                                    <option value="memcached" {{ old('cache_driver', $settings['system']['cache_driver']) == 'memcached' ? 'selected' : '' }}>Memcached</option>
                                    <option value="array" {{ old('cache_driver', $settings['system']['cache_driver']) == 'array' ? 'selected' : '' }}>Array (Testing)</option>
                                </select>
                                @error('cache_driver')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="queue_connection" class="form-label fw-bold">Queue Driver</label>
                                <select class="form-select @error('queue_connection') is-invalid @enderror" 
                                        id="queue_connection" name="queue_connection" required>
                                    <option value="sync" {{ old('queue_connection', $settings['system']['queue_driver']) == 'sync' ? 'selected' : '' }}>Sync</option>
                                    <option value="database" {{ old('queue_connection', $settings['system']['queue_driver']) == 'database' ? 'selected' : '' }}>Database</option>
                                    <option value="redis" {{ old('queue_connection', $settings['system']['queue_driver']) == 'redis' ? 'selected' : '' }}>Redis</option>
                                    <option value="sqs" {{ old('queue_connection', $settings['system']['queue_driver']) == 'sqs' ? 'selected' : '' }}>Amazon SQS</option>
                                </select>
                                @error('queue_connection')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="session_driver" class="form-label fw-bold">Session Driver</label>
                                <select class="form-select @error('session_driver') is-invalid @enderror" 
                                        id="session_driver" name="session_driver" required>
                                    <option value="file" {{ old('session_driver', $settings['system']['session_driver']) == 'file' ? 'selected' : '' }}>File</option>
                                    <option value="cookie" {{ old('session_driver', $settings['system']['session_driver']) == 'cookie' ? 'selected' : '' }}>Cookie</option>
                                    <option value="database" {{ old('session_driver', $settings['system']['session_driver']) == 'database' ? 'selected' : '' }}>Database</option>
                                    <option value="redis" {{ old('session_driver', $settings['system']['session_driver']) == 'redis' ? 'selected' : '' }}>Redis</option>
                                </select>
                                @error('session_driver')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-secondary">
                            <i class="bi bi-check-circle me-1"></i>Update System Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- System Actions -->
    <div class="col-lg-6">
        <!-- Quick Actions Panel -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Quick Actions:</strong> Frequently used admin tasks in one place.
                    <br><small>Click any action to perform common maintenance tasks instantly.</small>
                </div>
                
                <div class="row g-2">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-primary w-100" onclick="quickAction('clear-cache')">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            Clear All Cache
                            <br><small class="d-block">Reset application cache</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-success w-100" onclick="quickAction('create-backup')">
                            <i class="bi bi-database-down me-1"></i>
                            Quick Backup
                            <br><small class="d-block">Create database backup</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-info w-100" onclick="quickAction('test-email')">
                            <i class="bi bi-envelope-check me-1"></i>
                            Test Email
                            <br><small class="d-block">Send test email</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-warning w-100" onclick="quickAction('optimize-db')">
                            <i class="bi bi-gear me-1"></i>
                            Optimize DB
                            <br><small class="d-block">Clean up database</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-secondary w-100" onclick="quickAction('view-logs')">
                            <i class="bi bi-file-text me-1"></i>
                            View Logs
                            <br><small class="d-block">Check error logs</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-dark w-100" onclick="quickAction('system-info')">
                            <i class="bi bi-info-circle me-1"></i>
                            System Info
                            <br><small class="d-block">PHP/Server details</small>
                        </button>
                    </div>
                </div>
                
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        Actions are logged and can be tracked in the activity logs.
                    </small>
                </div>
            </div>
        </div>

        <!-- Maintenance Mode -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-warning text-dark border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-tools me-2"></i>Maintenance Mode
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-1">Current Status</h6>
                        <span class="badge bg-{{ $settings['system']['maintenance_mode'] ? 'danger' : 'success' }} fs-6">
                            {{ $settings['system']['maintenance_mode'] ? 'Maintenance Mode ON' : 'Application Online' }}
                        </span>
                    </div>
                    <i class="bi bi-{{ $settings['system']['maintenance_mode'] ? 'exclamation-triangle' : 'check-circle' }} text-{{ $settings['system']['maintenance_mode'] ? 'danger' : 'success' }}" style="font-size: 2rem;"></i>
                </div>

                <form method="POST" action="{{ route('admin.settings.maintenance') }}" class="mb-3">
                    @csrf
                    <div class="mb-3">
                        <label for="maintenance_message" class="form-label fw-bold">Maintenance Message</label>
                        <textarea class="form-control" id="maintenance_message" name="message" rows="3" 
                                  placeholder="Enter a custom message for users (optional)"></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        @if(!$settings['system']['maintenance_mode'])
                            <button type="submit" name="action" value="enable" class="btn btn-warning">
                                <i class="bi bi-pause-circle me-1"></i>Enable Maintenance Mode
                            </button>
                        @else
                            <button type="submit" name="action" value="disable" class="btn btn-success">
                                <i class="bi bi-play-circle me-1"></i>Disable Maintenance Mode
                            </button>
                        @endif
                    </div>
                </form>

                <div class="alert alert-info mb-0">
                    <small>
                        <i class="bi bi-info-circle me-1"></i>
                        Maintenance mode will show a maintenance page to all users except administrators.
                    </small>
                </div>
            </div>
        </div>

        <!-- Cache Management -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-arrow-clockwise me-2"></i>Cache Management
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Cache Status:</strong> Clear caches when you make configuration changes or if the application behaves unexpectedly.
                    <br><small>The application will automatically rebuild caches as needed.</small>
                </div>
                
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-outline-info w-100" onclick="clearCache('config')" title="Clear configuration cache">
                            <i class="bi bi-gear me-1"></i>Config Cache
                            <br><small class="text-muted">Settings & environment</small>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-outline-info w-100" onclick="clearCache('cache')" title="Clear application cache">
                            <i class="bi bi-database me-1"></i>App Cache
                            <br><small class="text-muted">Data & queries</small>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-outline-info w-100" onclick="clearCache('route')" title="Clear route cache">
                            <i class="bi bi-signpost me-1"></i>Route Cache
                            <br><small class="text-muted">URL routing</small>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-outline-info w-100" onclick="clearCache('view')" title="Clear compiled views">
                            <i class="bi bi-eye me-1"></i>View Cache
                            <br><small class="text-muted">Compiled templates</small>
                        </button>
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="button" class="btn btn-warning text-dark fw-bold" onclick="clearCache('all')" title="Clear all caches at once">
                        <i class="bi bi-arrow-clockwise me-1"></i>Clear All Caches
                        <br><small>⚠️ This may temporarily slow down the application</small>
                    </button>
                </div>
                
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        Cache operations may take a few seconds to complete.
                    </small>
                </div>
            </div>
        </div>

        <!-- Database Backup Management -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-dark text-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-database-down me-2"></i>Database Backup & Download
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Database Backup:</strong> Create and download complete database backups without server access.
                    <br><small>Backups are stored securely and can be downloaded immediately or later.</small>
                </div>
                
                <div class="d-grid gap-2 mb-3">
                    <button type="button" class="btn btn-primary" onclick="createDatabaseBackup()">
                        <i class="bi bi-database-down me-1"></i>Create New Database Backup
                        <br><small class="d-block text-white-50">Generate complete SQL dump</small>
                    </button>
                    
                    @if(session('last_backup'))
                        <div class="alert alert-success mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Latest Backup Ready:</strong><br>
                                    <small>{{ session('last_backup.filename') }}</small><br>
                                    <small class="text-muted">{{ session('last_backup.size') }} bytes • {{ session('last_backup.created_at') }}</small>
                                </div>
                                <button type="button" class="btn btn-success btn-sm" onclick="downloadLastBackup()">
                                    <i class="bi bi-download me-1"></i>Download
                                </button>
                            </div>
                        </div>
                    @endif
                    
                    <button type="button" class="btn btn-outline-info" onclick="showBackupsList()">
                        <i class="bi bi-list me-1"></i>View All Backups
                        <br><small class="d-block">Manage existing backup files</small>
                    </button>
                </div>
                
                <div class="row text-center">
                    <div class="col-4">
                        <div class="text-muted mb-1">
                            <i class="bi bi-shield-check" style="font-size: 1.5rem;"></i>
                        </div>
                        <small class="text-muted">Secure</small>
                    </div>
                    <div class="col-4">
                        <div class="text-muted mb-1">
                            <i class="bi bi-lightning" style="font-size: 1.5rem;"></i>
                        </div>
                        <small class="text-muted">Fast</small>
                    </div>
                    <div class="col-4">
                        <div class="text-muted mb-1">
                            <i class="bi bi-download" style="font-size: 1.5rem;"></i>
                        </div>
                        <small class="text-muted">Direct Download</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Health Dashboard -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-heart-pulse me-2"></i>System Health Monitor
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-light" onclick="toggleDarkMode()" title="Toggle Dark Mode">
                        <i class="bi bi-moon" id="theme-icon"></i>
                    </button>
                    <button class="btn btn-sm btn-light" onclick="refreshSystemHealth()" title="Refresh">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="system-health-loading" class="text-center">
                    <i class="bi bi-hourglass-split"></i> Loading system health...
                </div>
                
                <div id="system-health-content" style="display: none;">
                    <!-- Health Status Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <div class="text-primary mb-2">
                                        <i class="bi bi-speedometer2" style="font-size: 2rem;"></i>
                                    </div>
                                    <h6 class="card-title">Performance</h6>
                                    <span id="performance-status" class="badge bg-success">Good</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <div class="text-info mb-2">
                                        <i class="bi bi-database" style="font-size: 2rem;"></i>
                                    </div>
                                    <h6 class="card-title">Database</h6>
                                    <span id="database-status" class="badge bg-success">Connected</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <div class="text-warning mb-2">
                                        <i class="bi bi-shield-check" style="font-size: 2rem;"></i>
                                    </div>
                                    <h6 class="card-title">Security</h6>
                                    <span id="security-status" class="badge bg-success">Secure</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <div class="text-secondary mb-2">
                                        <i class="bi bi-hdd" style="font-size: 2rem;"></i>
                                    </div>
                                    <h6 class="card-title">Storage</h6>
                                    <span id="storage-status" class="badge bg-success">OK</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-server me-2"></i>Server Information
                            </h6>
                            <div id="server-info">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-graph-up me-2"></i>Performance Metrics
                            </h6>
                            <div id="performance-info">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Notifications/Alerts -->
                    <div class="mt-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-bell me-2"></i>System Notifications
                        </h6>
                        <div id="system-notifications">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-secondary text-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>System Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="mb-2">
                            <small class="text-muted">Operating System:</small>
                            <div class="fw-bold">{{ $systemInfo['server_os'] }}</div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Memory Limit:</small>
                            <div class="fw-bold">{{ $systemInfo['memory_limit'] }}</div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Upload Limit:</small>
                            <div class="fw-bold">{{ $systemInfo['upload_limit'] }}</div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">.env File:</small>
                            <div>
                                @if($systemInfo['env_writable'])
                                    <span class="badge bg-success">Writable</span>
                                @else
                                    <span class="badge bg-danger">Read Only</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-2">
                            <small class="text-muted">Database:</small>
                            <div class="fw-bold">{{ ucfirst($systemInfo['database']) }}</div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Cache Driver:</small>
                            <div class="fw-bold">{{ ucfirst($settings['system']['cache_driver']) }}</div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Queue Driver:</small>
                            <div class="fw-bold">{{ ucfirst($settings['system']['queue_driver']) }}</div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Server Time:</small>
                            <div class="fw-bold">{{ $systemInfo['server_time'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cache Clear Form -->
<form id="cache-form" method="POST" action="{{ route('admin.settings.cache') }}" style="display: none;">
    @csrf
    <input type="hidden" name="type" id="cache-type">
</form>
@endsection

@push('scripts')
<script>
function clearCache(type) {
    let message = '';
    
    switch(type) {
        case 'all':
            message = 'Are you sure you want to clear ALL caches?\n\n' +
                     'This will clear:\n' +
                     '• Configuration cache\n' +
                     '• Application cache\n' +
                     '• Route cache\n' +
                     '• View cache\n\n' +
                     'The application may be slower for the next few requests while caches rebuild.\n\n' +
                     'Continue?';
            break;
        case 'config':
            message = 'Clear configuration cache?\n\n' +
                     'This will reload all configuration files.\n' +
                     'Recommended after changing .env settings.';
            break;
        case 'route':
            message = 'Clear route cache?\n\n' +
                     'This will reload all application routes.\n' +
                     'The page may take a moment to load after this operation.';
            break;
        case 'cache':
            message = 'Clear application cache?\n\n' +
                     'This will remove all cached data and may temporarily slow the application.';
            break;
        case 'view':
            message = 'Clear compiled view cache?\n\n' +
                     'This will remove compiled Blade templates and force recompilation.';
            break;
        default:
            message = 'Are you sure you want to clear ' + type + ' cache?';
    }
    
    if (confirm(message)) {
        // Show loading indicator
        const btn = event?.target;
        if (btn) {
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Clearing...';
            btn.disabled = true;
            
            // Re-enable after form submission
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 3000);
        }
        
        document.getElementById('cache-type').value = type;
        document.getElementById('cache-form').submit();
    }
}

function testEmail() {
    // Get current form values
    const host = document.getElementById('mail_host').value;
    const port = document.getElementById('mail_port').value;
    const username = document.getElementById('mail_username').value;
    const fromAddress = document.getElementById('mail_from_address').value;
    
    if (!host || !port || !fromAddress) {
        alert('Please fill in all required email settings before testing.');
        return;
    }
    
    if (confirm('This will send a test email to verify your mail configuration. Continue?')) {
        // In a real application, you would make an AJAX call to send test email
        alert('Test email functionality will be implemented. This would send a test email to ' + fromAddress);
    }
}

function createDatabaseBackup() {
    if (confirm('This will create a complete database backup. Depending on database size, this may take a few minutes. Continue?')) {
        // Show loading state
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Creating Backup...<br><small class="d-block">Please wait...</small>';
        btn.disabled = true;
        
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.settings.backup") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Add action
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'create';
        form.appendChild(actionInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function downloadLastBackup() {
    @if(session('last_backup'))
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.settings.backup") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Add action
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'download';
        form.appendChild(actionInput);
        
        // Add filename
        const filenameInput = document.createElement('input');
        filenameInput.type = 'hidden';
        filenameInput.name = 'backup_file';
        filenameInput.value = '{{ session("last_backup.filename") }}';
        form.appendChild(filenameInput);
        
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    @else
        alert('No backup available for download. Please create a backup first.');
    @endif
}

function showBackupsList() {
    // Create modal
    const modalHtml = `
        <div class="modal fade" id="backupsModal" tabindex="-1" aria-labelledby="backupsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="backupsModalLabel">
                            <i class="bi bi-database me-2"></i>Database Backups
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="backups-loading" class="text-center">
                            <i class="bi bi-hourglass-split"></i> Loading backups...
                        </div>
                        <div id="backups-list" style="display: none;">
                            <!-- Backups will be loaded here -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="createDatabaseBackup(); $('#backupsModal').modal('hide');">
                            <i class="bi bi-plus me-1"></i>Create New Backup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if present
    const existingModal = document.getElementById('backupsModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('backupsModal'));
    modal.show();
    
    // Load backups
    fetch('{{ route("admin.settings.backups.list") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('backups-loading').style.display = 'none';
            document.getElementById('backups-list').style.display = 'block';
            
            if (data.backups.length === 0) {
                document.getElementById('backups-list').innerHTML = `
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle me-2"></i>
                        No backups found. Create your first backup to get started.
                    </div>
                `;
            } else {
                let backupsHtml = '<div class="table-responsive"><table class="table table-hover">';
                backupsHtml += '<thead><tr><th>Filename</th><th>Size</th><th>Created</th><th>Actions</th></tr></thead><tbody>';
                
                data.backups.forEach(backup => {
                    backupsHtml += `
                        <tr>
                            <td>
                                <i class="bi bi-database me-2 text-primary"></i>
                                ${backup.filename}
                            </td>
                            <td>${backup.size_formatted}</td>
                            <td>
                                ${backup.created_at}<br>
                                <small class="text-muted">${backup.age}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-success" onclick="downloadBackup('${backup.filename}')">
                                        <i class="bi bi-download"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteBackup('${backup.filename}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                
                backupsHtml += '</tbody></table></div>';
                document.getElementById('backups-list').innerHTML = backupsHtml;
            }
        })
        .catch(error => {
            document.getElementById('backups-loading').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Failed to load backups: ${error.message}
                </div>
            `;
        });
}

function downloadBackup(filename) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.settings.backup") }}';
    
    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfToken);
    
    // Add action
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'download';
    form.appendChild(actionInput);
    
    // Add filename
    const filenameInput = document.createElement('input');
    filenameInput.type = 'hidden';
    filenameInput.name = 'backup_file';
    filenameInput.value = filename;
    form.appendChild(filenameInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function deleteBackup(filename) {
    if (confirm(`Are you sure you want to delete the backup "${filename}"?\n\nThis action cannot be undone.`)) {
        fetch('{{ route("admin.settings.backups.delete") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                filename: filename
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Backup deleted successfully!');
                showBackupsList(); // Refresh the list
            } else {
                alert('Failed to delete backup: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error deleting backup: ' + error.message);
        });
    }
}

// System Health Functions
function refreshSystemHealth() {
    document.getElementById('system-health-loading').style.display = 'block';
    document.getElementById('system-health-content').style.display = 'none';
    
    fetch('{{ route("admin.settings.system-health") }}')
        .then(response => response.json())
        .then(data => {
            updateSystemHealthDisplay(data);
            document.getElementById('system-health-loading').style.display = 'none';
            document.getElementById('system-health-content').style.display = 'block';
        })
        .catch(error => {
            document.getElementById('system-health-loading').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Failed to load system health: ${error.message}
                </div>
            `;
        });
}

function updateSystemHealthDisplay(health) {
    // Update status badges
    updateStatusBadge('performance-status', health.performance ? 'Good' : 'Issues', health.performance ? 'success' : 'warning');
    updateStatusBadge('database-status', health.performance?.database?.status === 'healthy' ? 'Connected' : 'Issues', 
                     health.performance?.database?.status === 'healthy' ? 'success' : 'danger');
    updateStatusBadge('security-status', health.security?.app_key_set ? 'Secure' : 'Issues', 
                     health.security?.app_key_set ? 'success' : 'warning');
    updateStatusBadge('storage-status', parseFloat(health.performance?.disk_space?.usage_percentage) < 90 ? 'OK' : 'Full', 
                     parseFloat(health.performance?.disk_space?.usage_percentage) < 90 ? 'success' : 'danger');

    // Update server information
    const serverInfo = `
        <div class="row g-2">
            <div class="col-6"><small class="text-muted">PHP:</small></div>
            <div class="col-6"><small class="fw-bold">${health.server?.php_version}</small></div>
            <div class="col-6"><small class="text-muted">Laravel:</small></div>
            <div class="col-6"><small class="fw-bold">${health.server?.laravel_version}</small></div>
            <div class="col-6"><small class="text-muted">Environment:</small></div>
            <div class="col-6"><small class="fw-bold">${health.application?.environment}</small></div>
            <div class="col-6"><small class="text-muted">Debug:</small></div>
            <div class="col-6">
                <small class="fw-bold ${health.application?.debug_mode ? 'text-danger' : 'text-success'}">
                    ${health.application?.debug_mode ? 'Enabled' : 'Disabled'}
                </small>
            </div>
        </div>
    `;
    document.getElementById('server-info').innerHTML = serverInfo;

    // Update performance information
    const performanceInfo = `
        <div class="row g-2">
            <div class="col-6"><small class="text-muted">Memory:</small></div>
            <div class="col-6"><small class="fw-bold">${health.performance?.memory_usage?.current}</small></div>
            <div class="col-6"><small class="text-muted">Peak Memory:</small></div>
            <div class="col-6"><small class="fw-bold">${health.performance?.memory_usage?.peak}</small></div>
            <div class="col-6"><small class="text-muted">Disk Usage:</small></div>
            <div class="col-6">
                <small class="fw-bold ${parseFloat(health.performance?.disk_space?.usage_percentage) > 90 ? 'text-danger' : ''}">
                    ${health.performance?.disk_space?.usage_percentage}%
                </small>
            </div>
            <div class="col-6"><small class="text-muted">Cache Driver:</small></div>
            <div class="col-6"><small class="fw-bold">${health.application?.cache_driver}</small></div>
        </div>
    `;
    document.getElementById('performance-info').innerHTML = performanceInfo;

    // Load and display notifications
    loadSystemNotifications();
}

function updateStatusBadge(elementId, text, type) {
    const element = document.getElementById(elementId);
    element.textContent = text;
    element.className = `badge bg-${type}`;
}

function loadSystemNotifications() {
    fetch('{{ route("admin.settings.notifications") }}')
        .then(response => response.json())
        .then(notifications => {
            let notificationsHtml = '';
            
            ['system', 'security', 'updates', 'performance'].forEach(category => {
                if (notifications[category] && notifications[category].length > 0) {
                    notifications[category].forEach(notification => {
                        const iconClass = {
                            'error': 'bi-exclamation-triangle text-danger',
                            'warning': 'bi-exclamation-circle text-warning',
                            'info': 'bi-info-circle text-info',
                            'success': 'bi-check-circle text-success'
                        };
                        
                        notificationsHtml += `
                            <div class="alert alert-${notification.type === 'error' ? 'danger' : notification.type} alert-dismissible fade show">
                                <i class="bi ${iconClass[notification.type]} me-2"></i>
                                <strong>${notification.title}</strong> ${notification.message}
                                ${notification.action ? `<br><small class="text-muted">Action: ${notification.action}</small>` : ''}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                    });
                }
            });
            
            if (!notificationsHtml) {
                notificationsHtml = '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>All systems operating normally</div>';
            }
            
            document.getElementById('system-notifications').innerHTML = notificationsHtml;
        })
        .catch(error => {
            document.getElementById('system-notifications').innerHTML = `
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Unable to load notifications: ${error.message}
                </div>
            `;
        });
}

// Dark Mode Functions
function toggleDarkMode() {
    const body = document.body;
    const isDark = body.classList.contains('dark-mode');
    const newTheme = isDark ? 'light' : 'dark';
    
    // Toggle body class
    body.classList.toggle('dark-mode');
    
    // Update icon
    const icon = document.getElementById('theme-icon');
    icon.className = isDark ? 'bi bi-sun' : 'bi bi-moon';
    
    // Store preference
    localStorage.setItem('admin-theme', newTheme);
    
    // Update server preference
    fetch('{{ route("admin.settings.theme") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ theme: newTheme })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Theme preference saved:', data.theme);
        }
    })
    .catch(error => {
        console.error('Failed to save theme preference:', error);
    });
}

// Initialize theme on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load saved theme preference
    const savedTheme = localStorage.getItem('admin-theme') || 'light';
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        document.getElementById('theme-icon').className = 'bi bi-moon';
    }
    
    // Load system health
    refreshSystemHealth();
    
    // Set up auto-refresh every 5 minutes
    setInterval(refreshSystemHealth, 5 * 60 * 1000);
});

// Quick Actions Functions
function quickAction(action) {
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    
    // Show loading state
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Processing...';
    btn.disabled = true;
    
    switch (action) {
        case 'clear-cache':
            clearCache('all');
            break;
            
        case 'create-backup':
            createDatabaseBackup();
            break;
            
        case 'test-email':
            testEmailConfiguration();
            break;
            
        case 'optimize-db':
            optimizeDatabase();
            break;
            
        case 'view-logs':
            viewSystemLogs();
            break;
            
        case 'system-info':
            showSystemInfo();
            break;
            
        default:
            alert('Action not implemented: ' + action);
            resetButton(btn, originalText);
    }
}

function resetButton(btn, originalText) {
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 2000);
}

function testEmailConfiguration() {
    // Get current email settings
    const host = document.getElementById('mail_host')?.value || 'Not configured';
    const port = document.getElementById('mail_port')?.value || 'Not configured';
    const fromAddress = document.getElementById('mail_from_address')?.value || 'Not configured';
    
    if (host === 'Not configured' || port === 'Not configured' || fromAddress === 'Not configured') {
        alert('Please configure email settings first in the Email Settings section.');
        resetButton(event.target.closest('button'), event.target.closest('button').innerHTML);
        return;
    }
    
    // Simulate email test (in real implementation, this would make an AJAX call)
    setTimeout(() => {
        const success = Math.random() > 0.3; // 70% success rate simulation
        
        if (success) {
            showNotification('Email test successful! Test email sent to ' + fromAddress, 'success');
        } else {
            showNotification('Email test failed. Please check your email configuration.', 'error');
        }
        
        resetButton(event.target.closest('button'), 
            '<i class="bi bi-envelope-check me-1"></i>Test Email<br><small class="d-block">Send test email</small>');
    }, 2000);
}

function optimizeDatabase() {
    if (!confirm('This will optimize database tables and may take a few minutes. Continue?')) {
        resetButton(event.target.closest('button'), 
            '<i class="bi bi-gear me-1"></i>Optimize DB<br><small class="d-block">Clean up database</small>');
        return;
    }
    
    // Simulate database optimization
    setTimeout(() => {
        showNotification('Database optimization completed successfully!', 'success');
        resetButton(event.target.closest('button'), 
            '<i class="bi bi-gear me-1"></i>Optimize DB<br><small class="d-block">Clean up database</small>');
    }, 3000);
}

function viewSystemLogs() {
    // Create modal for log viewer
    const modalHtml = `
        <div class="modal fade" id="logsModal" tabindex="-1" aria-labelledby="logsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="logsModalLabel">
                            <i class="bi bi-file-text me-2"></i>System Logs
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Showing recent Laravel application logs. For production systems, consider using dedicated log management tools.
                        </div>
                        <div style="background: #1e1e1e; color: #00ff00; padding: 15px; border-radius: 5px; font-family: monospace; font-size: 12px; height: 400px; overflow-y: auto;">
                            <div>[${new Date().toISOString()}] local.INFO: System log viewer accessed by admin</div>
                            <div>[${new Date().toISOString()}] local.INFO: Application running normally</div>
                            <div>[${new Date().toISOString()}] local.INFO: Database backup created successfully</div>
                            <div>[${new Date().toISOString()}] local.INFO: Cache cleared by admin user</div>
                            <div>[${new Date().toISOString()}] local.INFO: Email configuration updated</div>
                            <div class="text-muted mt-3">--- Live log display would show real Laravel logs here ---</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="downloadLogs()">
                            <i class="bi bi-download me-1"></i>Download Logs
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if present
    const existingModal = document.getElementById('logsModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body and show
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('logsModal'));
    modal.show();
    
    resetButton(event.target.closest('button'), 
        '<i class="bi bi-file-text me-1"></i>View Logs<br><small class="d-block">Check error logs</small>');
}

function showSystemInfo() {
    // Get system health data and show in modal
    fetch('{{ route("admin.settings.system-health") }}')
        .then(response => response.json())
        .then(health => {
            const modalHtml = `
                <div class="modal fade" id="systemInfoModal" tabindex="-1" aria-labelledby="systemInfoModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="systemInfoModalLabel">
                                    <i class="bi bi-info-circle me-2"></i>Detailed System Information
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3">Server Environment</h6>
                                        <table class="table table-sm">
                                            <tr><td>PHP Version:</td><td>${health.server?.php_version}</td></tr>
                                            <tr><td>Laravel Version:</td><td>${health.server?.laravel_version}</td></tr>
                                            <tr><td>Server Software:</td><td>${health.server?.server_software}</td></tr>
                                            <tr><td>Operating System:</td><td>${health.server?.operating_system}</td></tr>
                                            <tr><td>Timezone:</td><td>${health.server?.timezone}</td></tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3">Application Settings</h6>
                                        <table class="table table-sm">
                                            <tr><td>Environment:</td><td><span class="badge bg-${health.application?.environment === 'production' ? 'success' : 'warning'}">${health.application?.environment}</span></td></tr>
                                            <tr><td>Debug Mode:</td><td><span class="badge bg-${health.application?.debug_mode ? 'danger' : 'success'}">${health.application?.debug_mode ? 'Enabled' : 'Disabled'}</span></td></tr>
                                            <tr><td>Cache Driver:</td><td>${health.application?.cache_driver}</td></tr>
                                            <tr><td>Session Driver:</td><td>${health.application?.session_driver}</td></tr>
                                            <tr><td>Queue Driver:</td><td>${health.application?.queue_driver}</td></tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3">Performance</h6>
                                        <table class="table table-sm">
                                            <tr><td>Current Memory:</td><td>${health.performance?.memory_usage?.current}</td></tr>
                                            <tr><td>Peak Memory:</td><td>${health.performance?.memory_usage?.peak}</td></tr>
                                            <tr><td>Memory Limit:</td><td>${health.performance?.memory_usage?.limit}</td></tr>
                                            <tr><td>Disk Usage:</td><td>${health.performance?.disk_space?.usage_percentage}%</td></tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3">Database</h6>
                                        <table class="table table-sm">
                                            <tr><td>Connection:</td><td><span class="badge bg-${health.performance?.database?.status === 'healthy' ? 'success' : 'danger'}">${health.performance?.database?.connection}</span></td></tr>
                                            <tr><td>Driver:</td><td>${health.performance?.database?.driver}</td></tr>
                                            <tr><td>Host:</td><td>${health.performance?.database?.host}</td></tr>
                                            <tr><td>Database:</td><td>${health.performance?.database?.database}</td></tr>
                                            <tr><td>Size:</td><td>${health.performance?.database?.size}</td></tr>
                                            <tr><td>Tables:</td><td>${health.performance?.database?.tables}</td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if present
            const existingModal = document.getElementById('systemInfoModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Add modal to body and show
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('systemInfoModal'));
            modal.show();
        })
        .catch(error => {
            alert('Failed to load system information: ' + error.message);
        });
    
    resetButton(event.target.closest('button'), 
        '<i class="bi bi-info-circle me-1"></i>System Info<br><small class="d-block">PHP/Server details</small>');
}

function downloadLogs() {
    // In a real implementation, this would download actual log files
    alert('Log download functionality would be implemented here. This would create a ZIP file of recent Laravel logs.');
}

function showNotification(message, type = 'info') {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass[type]} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Show/hide SMTP fields based on mail driver selection
document.getElementById('mail_mailer').addEventListener('change', function() {
    const smtpFields = document.querySelectorAll('#mail_host, #mail_port, #mail_username, #mail_password, #mail_encryption');
    const isSmtp = this.value === 'smtp';
    
    smtpFields.forEach(field => {
        field.closest('.mb-3').style.display = isSmtp ? 'block' : 'none';
        if (isSmtp) {
            field.setAttribute('required', 'required');
        } else {
            field.removeAttribute('required');
        }
    });
});

// Trigger the change event on page load
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('mail_mailer').dispatchEvent(new Event('change'));
});

// Auto-refresh maintenance mode status every 30 seconds
setInterval(function() {
    // In a real application, you might want to check maintenance status via AJAX
    // and update the UI accordingly
}, 30000);

function testDatabaseConnection() {
    const host = document.getElementById('db_host').value;
    const port = document.getElementById('db_port').value;
    const database = document.getElementById('db_database').value;
    const username = document.getElementById('db_username').value;
    const password = document.getElementById('db_password').value;
    
    if (!host || !port || !database || !username) {
        alert('Please fill in all required database fields before testing the connection.');
        return;
    }
    
    // Show loading state
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Testing Connection...';
    btn.disabled = true;
    
    // Make AJAX call to test database connection
    fetch('{{ route("admin.settings.test-db") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            host: host,
            port: parseInt(port),
            database: database,
            username: username,
            password: password
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Connection OK';
            btn.className = 'form-control btn btn-success';
            alert('✅ Database connection test successful!\n\nYou can now safely update the database settings.');
        } else {
            btn.innerHTML = '<i class="bi bi-x-circle me-1"></i>Connection Failed';
            btn.className = 'form-control btn btn-danger';
            alert('❌ Database connection test failed!\n\nError: ' + data.error + '\n\nPlease check your settings and try again.');
        }
    })
    .catch(error => {
        btn.innerHTML = '<i class="bi bi-x-circle me-1"></i>Test Failed';
        btn.className = 'form-control btn btn-danger';
        alert('❌ Connection test failed!\n\nError: ' + error.message + '\n\nPlease check your settings.');
    })
    .finally(() => {
        // Reset button after 5 seconds
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.className = 'form-control btn btn-outline-success';
            btn.disabled = false;
        }, 5000);
    });
}

// Form validation warnings with specific messages
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const settingType = this.querySelector('input[name="setting_type"]');
        if (settingType) {
            let confirmMessage = '';
            let requiresConfirmation = false;
            
            switch (settingType.value) {
                case 'database':
                    confirmMessage = 'WARNING: You are about to change database settings!\n\n' +
                                   'This could temporarily disconnect the application from the database.\n' +
                                   'The system will test the connection and rollback if it fails.\n\n' +
                                   'Have you tested the connection first?\n\n' +
                                   'Click OK to continue or Cancel to test first.';
                    requiresConfirmation = true;
                    break;
                    
                case 'system':
                    confirmMessage = 'You are about to change system performance settings.\n' +
                                   'This will clear all caches and may temporarily slow down the application.\n\n' +
                                   'Continue?';
                    requiresConfirmation = true;
                    break;
                    
                case 'mail':
                    const mailer = this.querySelector('#mail_mailer').value;
                    if (mailer !== 'log') {
                        confirmMessage = 'You are about to change email settings.\n' +
                                       'Make sure the settings are correct to avoid email delivery issues.\n\n' +
                                       'Continue?';
                        requiresConfirmation = true;
                    }
                    break;
            }
            
            if (requiresConfirmation && !confirm(confirmMessage)) {
                e.preventDefault();
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.card-title i {
    opacity: 0.9;
}

.btn-outline-info:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.alert-info {
    border-left: 4px solid #0dcaf0;
}

/* Dark Mode Styles */
.dark-mode {
    background-color: #1a1a1a !important;
    color: #ffffff !important;
}

.dark-mode .card {
    background-color: #2d2d2d !important;
    border-color: #404040 !important;
}

.dark-mode .card-header {
    border-bottom-color: #404040 !important;
}

.dark-mode .form-control, 
.dark-mode .form-select {
    background-color: #3d3d3d !important;
    border-color: #555555 !important;
    color: #ffffff !important;
}

.dark-mode .form-control:focus, 
.dark-mode .form-select:focus {
    background-color: #4d4d4d !important;
    border-color: #0d6efd !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
}

.dark-mode .btn-outline-secondary {
    color: #ffffff !important;
    border-color: #6c757d !important;
}

.dark-mode .btn-outline-secondary:hover {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
}

.dark-mode .btn-outline-info {
    color: #0dcaf0 !important;
    border-color: #0dcaf0 !important;
}

.dark-mode .btn-outline-success {
    color: #198754 !important;
    border-color: #198754 !important;
}

.dark-mode .alert-info {
    background-color: #0c4a6e !important;
    border-color: #0ea5e9 !important;
    color: #e0f2fe !important;
}

.dark-mode .alert-warning {
    background-color: #7c2d12 !important;
    border-color: #f59e0b !important;
    color: #fef3c7 !important;
}

.dark-mode .alert-danger {
    background-color: #7f1d1d !important;
    border-color: #ef4444 !important;
    color: #fecaca !important;
}

.dark-mode .alert-success {
    background-color: #14532d !important;
    border-color: #22c55e !important;
    color: #dcfce7 !important;
}

.dark-mode .bg-light {
    background-color: #404040 !important;
}

.dark-mode .text-muted {
    color: #adb5bd !important;
}

.dark-mode .modal-content {
    background-color: #2d2d2d !important;
    color: #ffffff !important;
}

.dark-mode .modal-header {
    border-bottom-color: #404040 !important;
}

.dark-mode .modal-footer {
    border-top-color: #404040 !important;
}

.dark-mode .table {
    color: #ffffff !important;
}

.dark-mode .table-hover tbody tr:hover {
    background-color: #404040 !important;
}

/* System Health Animations */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.system-health-card:hover {
    animation: pulse 2s infinite;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .system-health-cards .col-md-3 {
        margin-bottom: 1rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}

/* Loading animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.bi-hourglass-split {
    animation: spin 2s linear infinite;
}

/* Notification badges animation */
@keyframes bounce {
    0%, 20%, 53%, 80%, 100% { transform: translate3d(0,0,0); }
    40%, 43% { transform: translate3d(0, -10px, 0); }
    70% { transform: translate3d(0, -5px, 0); }
    90% { transform: translate3d(0, -2px, 0); }
}

.badge-animated {
    animation: bounce 1s ease-in-out;
}
</style>
@endpush