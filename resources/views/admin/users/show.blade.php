@extends('layouts.admin')

@section('title', 'User Details - ' . $user->name)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-person me-2 text-info"></i>User Details
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-pencil me-1"></i>Edit User
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Users
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- User Profile Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-4">
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 100px; height: 100px;">
                    <i class="bi bi-person text-white" style="font-size: 3rem;"></i>
                </div>
                
                <h4 class="card-title">{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->email }}</p>
                
                <div class="mb-3">
                    @if($user->status)
                        <span class="badge bg-success fs-6">
                            <i class="bi bi-check-circle me-1"></i>Active
                        </span>
                    @else
                        <span class="badge bg-danger fs-6">
                            <i class="bi bi-x-circle me-1"></i>Inactive
                        </span>
                    @endif
                    
                    @if($user->email_verified_at)
                        <span class="badge bg-info fs-6">
                            <i class="bi bi-patch-check me-1"></i>Verified
                        </span>
                    @else
                        <span class="badge bg-warning fs-6">
                            <i class="bi bi-exclamation-triangle me-1"></i>Unverified
                        </span>
                    @endif
                </div>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Edit User
                    </a>
                    @if(Auth::guard('admin')->user()->id !== $user->id)
                        <button class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="bi bi-trash me-1"></i>Delete User
                        </button>
                        
                        <form id="delete-form" action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Role & Permissions Card -->
        @if($user->role)
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-light border-0">
                <h6 class="card-title mb-0">
                    <i class="bi bi-diagram-3 text-primary me-2"></i>Role & Permissions
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-muted mb-2">Current Role:</h6>
                    <span class="badge bg-primary fs-6">{{ $user->role->name }}</span>
                    <p class="text-muted mt-2 mb-0">{{ $user->role->description }}</p>
                </div>
                
                @if($user->role->permissions->count() > 0)
                    <div>
                        <h6 class="text-muted mb-2">Permissions:</h6>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($user->role->permissions as $permission)
                                <span class="badge bg-info">{{ $permission->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="text-muted mb-0">No specific permissions assigned to this role.</p>
                @endif
            </div>
        </div>
        @endif
    </div>
    
    <!-- User Information Details -->
    <div class="col-lg-8">
        <!-- Basic Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle text-primary me-2"></i>Basic Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">User ID</label>
                            <p class="fw-bold">#{{ $user->id }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Full Name</label>
                            <p class="fw-bold">{{ $user->name }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Email Address</label>
                            <p class="fw-bold">
                                {{ $user->email }}
                                @if($user->email_verified_at)
                                    <i class="bi bi-patch-check-fill text-success ms-1" title="Verified"></i>
                                @else
                                    <i class="bi bi-exclamation-triangle text-warning ms-1" title="Unverified"></i>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Account Status</label>
                            <p>
                                @if($user->status)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Active
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>Inactive
                                    </span>
                                @endif
                            </p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Email Verification</label>
                            <p>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Verified
                                    </span>
                                    <br><small class="text-muted">{{ $user->email_verified_at->format('M d, Y H:i') }}</small>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Pending
                                    </span>
                                @endif
                            </p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Assigned Role</label>
                            <p>
                                @if($user->role)
                                    <span class="badge bg-info">{{ $user->role->name }}</span>
                                @else
                                    <span class="badge bg-secondary">No Role Assigned</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline/Activity -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history text-success me-2"></i>Account Timeline
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item d-flex align-items-start mb-3">
                        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px; flex-shrink: 0;">
                            <i class="bi bi-person-plus text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Account Created</h6>
                            <p class="text-muted mb-1">User account was created in the system</p>
                            <small class="text-muted">{{ $user->created_at->format('M d, Y H:i') }} ({{ $user->created_at->diffForHumans() }})</small>
                        </div>
                    </div>
                    
                    @if($user->email_verified_at)
                        <div class="timeline-item d-flex align-items-start mb-3">
                            <div class="bg-info rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 40px; height: 40px; flex-shrink: 0;">
                                <i class="bi bi-patch-check text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Email Verified</h6>
                                <p class="text-muted mb-1">Email address was verified</p>
                                <small class="text-muted">{{ $user->email_verified_at->format('M d, Y H:i') }} ({{ $user->email_verified_at->diffForHumans() }})</small>
                            </div>
                        </div>
                    @endif
                    
                    @if($user->updated_at != $user->created_at)
                        <div class="timeline-item d-flex align-items-start">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 40px; height: 40px; flex-shrink: 0;">
                                <i class="bi bi-pencil text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Last Updated</h6>
                                <p class="text-muted mb-1">Account information was last modified</p>
                                <small class="text-muted">{{ $user->updated_at->format('M d, Y H:i') }} ({{ $user->updated_at->diffForHumans() }})</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning-charge text-warning me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="d-grid">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary">
                                <i class="bi bi-pencil me-1"></i>Edit User
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid">
                            @if($user->status)
                                <button class="btn btn-outline-warning" onclick="toggleStatus('deactivate')">
                                    <i class="bi bi-pause-circle me-1"></i>Deactivate
                                </button>
                            @else
                                <button class="btn btn-outline-success" onclick="toggleStatus('activate')">
                                    <i class="bi bi-play-circle me-1"></i>Activate
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid">
                            @if(!$user->email_verified_at)
                                <button class="btn btn-outline-info">
                                    <i class="bi bi-envelope me-1"></i>Send Verification
                                </button>
                            @else
                                <button class="btn btn-outline-secondary" disabled>
                                    <i class="bi bi-check-circle me-1"></i>Verified
                                </button>
                            @endif
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
function confirmDelete() {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        document.getElementById('delete-form').submit();
    }
}

function toggleStatus(action) {
    const message = action === 'activate' 
        ? 'Are you sure you want to activate this user account?' 
        : 'Are you sure you want to deactivate this user account?';
        
    if (confirm(message)) {
        // In a real application, you would submit a form to update the status
        // For now, redirect to edit page
        window.location.href = '{{ route("admin.users.edit", $user) }}';
    }
}
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
</style>
@endpush