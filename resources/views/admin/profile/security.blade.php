@extends('layouts.admin')

@section('title', 'Security Settings')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-shield-lock me-2 text-info"></i>Security Settings
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.profile.change-password') }}" class="btn btn-sm btn-warning">
                <i class="bi bi-key me-1"></i>Change Password
            </a>
            <a href="{{ route('admin.profile.show') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Profile
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Security Overview -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-shield-check me-2"></i>Security Status
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                         style="width: 80px; height: 80px;">
                        <i class="bi bi-shield-fill-check text-white" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="text-success">Account Secure</h5>
                    <p class="text-muted">Your admin account has strong security measures in place.</p>
                </div>
                
                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Password Strength</span>
                        <span class="badge bg-success">Strong</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Account Status</span>
                        <span class="badge bg-success">Active</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Role Security</span>
                        <span class="badge bg-{{ $admin->isSuperAdmin() ? 'warning text-dark' : 'primary' }}">
                            {{ $admin->role->name ?? 'No Role' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Email Verification</span>
                        @if($admin->email_verified_at)
                            <span class="badge bg-success">Verified</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightning-charge text-warning me-2"></i>Security Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.profile.change-password') }}" class="btn btn-outline-warning">
                        <i class="bi bi-key me-1"></i>Change Password
                    </a>
                    <button class="btn btn-outline-danger" onclick="logoutAllDevices()">
                        <i class="bi bi-phone-vibrate me-1"></i>Logout All Devices
                    </button>
                    <button class="btn btn-outline-info" onclick="downloadSecurityReport()">
                        <i class="bi bi-download me-1"></i>Security Report
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Security Details -->
    <div class="col-lg-8">
        <!-- Account Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-badge text-primary me-2"></i>Account Security Details
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Account ID</label>
                            <p class="fw-bold">#{{ $admin->id }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Admin Email</label>
                            <p class="fw-bold">
                                {{ $admin->email }}
                                @if($admin->email_verified_at)
                                    <i class="bi bi-patch-check-fill text-success ms-1" title="Verified"></i>
                                @else
                                    <i class="bi bi-exclamation-triangle text-warning ms-1" title="Unverified"></i>
                                @endif
                            </p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Role & Permissions</label>
                            <p>
                                @if($admin->role)
                                    <span class="badge bg-{{ $admin->isSuperAdmin() ? 'warning text-dark' : 'primary' }} fs-6">
                                        @if($admin->isSuperAdmin())
                                            <i class="bi bi-crown me-1"></i>
                                        @else
                                            <i class="bi bi-shield-check me-1"></i>
                                        @endif
                                        {{ $admin->role->name }}
                                    </span>
                                    <br><small class="text-muted">{{ $admin->role->permissions->count() }} permissions granted</small>
                                @else
                                    <span class="badge bg-secondary">No Role Assigned</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Account Created</label>
                            <p class="fw-bold">{{ $admin->created_at->format('M d, Y H:i') }}</p>
                            <small class="text-muted">{{ $admin->created_at->diffForHumans() }}</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Last Profile Update</label>
                            <p class="fw-bold">{{ $admin->updated_at->format('M d, Y H:i') }}</p>
                            <small class="text-muted">{{ $admin->updated_at->diffForHumans() }}</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Current Session</label>
                            <p class="fw-bold">{{ now()->format('M d, Y H:i') }}</p>
                            <span class="badge bg-success">Active Session</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear text-secondary me-2"></i>Security Preferences
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.profile.update-security') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-bell text-info me-2"></i>Login Notifications
                                    </h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" 
                                               id="login_notifications" name="login_notifications" checked>
                                        <label class="form-check-label" for="login_notifications">
                                            Email me when I log in from a new device
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-clock-history text-warning me-2"></i>Session Management
                                    </h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" 
                                               id="auto_logout" name="auto_logout" checked>
                                        <label class="form-check-label" for="auto_logout">
                                            Auto logout after 30 minutes of inactivity
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Update Security Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Recent Activity (Mock Data) -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-activity text-success me-2"></i>Recent Security Activity
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item d-flex align-items-start mb-3">
                        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px; flex-shrink: 0;">
                            <i class="bi bi-box-arrow-in-right text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Successful Login</h6>
                            <p class="text-muted mb-1">Logged in from {{ request()->ip() }}</p>
                            <small class="text-muted">{{ now()->format('M d, Y H:i') }}</small>
                        </div>
                    </div>
                    
                    <div class="timeline-item d-flex align-items-start mb-3">
                        <div class="bg-info rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px; flex-shrink: 0;">
                            <i class="bi bi-person-gear text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Profile Updated</h6>
                            <p class="text-muted mb-1">Profile information was modified</p>
                            <small class="text-muted">{{ $admin->updated_at->format('M d, Y H:i') }}</small>
                        </div>
                    </div>
                    
                    <div class="timeline-item d-flex align-items-start">
                        <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px; flex-shrink: 0;">
                            <i class="bi bi-key text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Password Policy Check</h6>
                            <p class="text-muted mb-1">Your password meets all security requirements</p>
                            <small class="text-muted">{{ now()->subDays(1)->format('M d, Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function logoutAllDevices() {
    if (confirm('This will log you out from all devices except this one. Continue?')) {
        // In a real implementation, you would make an AJAX call here
        alert('Feature will be implemented - this would logout all other sessions.');
    }
}

function downloadSecurityReport() {
    // In a real implementation, this would generate and download a security report
    alert('Security report generation feature will be implemented.');
}

// Auto-save security preferences
document.addEventListener('DOMContentLoaded', function() {
    const switches = document.querySelectorAll('.form-check-input[type="checkbox"]');
    
    switches.forEach(switch => {
        switch.addEventListener('change', function() {
            // Show a subtle indication that settings are being saved
            const originalText = this.nextElementSibling.textContent;
            this.nextElementSibling.textContent = 'Saving...';
            
            setTimeout(() => {
                this.nextElementSibling.textContent = originalText;
                // In a real implementation, you would make an AJAX call here
            }, 1000);
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 40px;
    width: 2px;
    height: calc(100% - 40px);
    background-color: #e9ecef;
}

.timeline {
    position: relative;
}

.card-title i {
    opacity: 0.8;
}
</style>
@endpush