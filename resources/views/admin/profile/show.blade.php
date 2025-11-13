@extends('layouts.admin')

@section('title', 'My Profile')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-person-circle me-2 text-primary"></i>My Profile
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.profile.edit') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-pencil me-1"></i>Edit Profile
            </a>
            <a href="{{ route('admin.profile.change-password') }}" class="btn btn-sm btn-outline-warning">
                <i class="bi bi-key me-1"></i>Change Password
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Profile Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-4">
                <div class="position-relative d-inline-block mb-3">
                    <div class="bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 120px; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="bi bi-person text-white" style="font-size: 4rem;"></i>
                    </div>
                    @if($admin->isSuperAdmin())
                        <div class="position-absolute top-0 end-0">
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-crown"></i>
                            </span>
                        </div>
                    @endif
                </div>
                
                <h4 class="card-title">{{ $admin->name }}</h4>
                <p class="text-muted mb-3">{{ $admin->email }}</p>
                
                <div class="mb-3">
                    @if($admin->isSuperAdmin())
                        <span class="badge bg-warning text-dark fs-6">
                            <i class="bi bi-crown me-1"></i>Super Administrator
                        </span>
                    @else
                        <span class="badge bg-primary fs-6">
                            <i class="bi bi-shield-check me-1"></i>Administrator
                        </span>
                    @endif
                    
                    @if($admin->status)
                        <span class="badge bg-success fs-6">
                            <i class="bi bi-check-circle me-1"></i>Active
                        </span>
                    @else
                        <span class="badge bg-danger fs-6">
                            <i class="bi bi-x-circle me-1"></i>Inactive
                        </span>
                    @endif
                </div>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Edit Profile
                    </a>
                    <a href="{{ route('admin.profile.change-password') }}" class="btn btn-outline-warning">
                        <i class="bi bi-key me-1"></i>Change Password
                    </a>
                    <a href="{{ route('admin.profile.security') }}" class="btn btn-outline-info">
                        <i class="bi bi-shield-lock me-1"></i>Security Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Profile Details -->
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
                            <label class="form-label text-muted">Admin ID</label>
                            <p class="fw-bold">#{{ $admin->id }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Full Name</label>
                            <p class="fw-bold">{{ $admin->name }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Email Address</label>
                            <p class="fw-bold">
                                {{ $admin->email }}
                                @if($admin->email_verified_at)
                                    <i class="bi bi-patch-check-fill text-success ms-1" title="Verified"></i>
                                @else
                                    <i class="bi bi-exclamation-triangle text-warning ms-1" title="Unverified"></i>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Role</label>
                            <p>
                                @if($admin->role)
                                    <span class="badge bg-{{ $admin->isSuperAdmin() ? 'warning text-dark' : 'primary' }}">
                                        @if($admin->isSuperAdmin())
                                            <i class="bi bi-crown me-1"></i>
                                        @else
                                            <i class="bi bi-shield-check me-1"></i>
                                        @endif
                                        {{ $admin->role->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">No Role</span>
                                @endif
                            </p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Account Status</label>
                            <p>
                                @if($admin->status)
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
                            <label class="form-label text-muted">Member Since</label>
                            <p class="fw-bold">{{ $admin->created_at->format('M d, Y') }}</p>
                            <small class="text-muted">{{ $admin->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role & Permissions -->
        @if($admin->role && $admin->role->permissions->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-diagram-3 text-success me-2"></i>Role & Permissions
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-muted mb-2">Current Role:</h6>
                    <span class="badge bg-{{ $admin->isSuperAdmin() ? 'warning text-dark' : 'primary' }} fs-6">
                        @if($admin->isSuperAdmin())
                            <i class="bi bi-crown me-1"></i>
                        @else
                            <i class="bi bi-shield-check me-1"></i>
                        @endif
                        {{ $admin->role->name }}
                    </span>
                    <p class="text-muted mt-2 mb-0">{{ $admin->role->description }}</p>
                </div>
                
                <div>
                    <h6 class="text-muted mb-3">Your Permissions:</h6>
                    <div class="row">
                        @foreach($admin->role->permissions->chunk(3) as $permissionChunk)
                            <div class="col-md-4">
                                @foreach($permissionChunk as $permission)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <span>{{ $permission->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Account Activity -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history text-info me-2"></i>Account Activity
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                                 style="width: 50px; height: 50px;">
                                <i class="bi bi-person-plus text-white"></i>
                            </div>
                            <h6 class="fw-bold">Account Created</h6>
                            <p class="mb-0">{{ $admin->created_at->format('M d, Y') }}</p>
                            <small class="text-muted">{{ $admin->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                                 style="width: 50px; height: 50px;">
                                <i class="bi bi-pencil text-white"></i>
                            </div>
                            <h6 class="fw-bold">Last Updated</h6>
                            <p class="mb-0">{{ $admin->updated_at->format('M d, Y') }}</p>
                            <small class="text-muted">{{ $admin->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                                 style="width: 50px; height: 50px;">
                                <i class="bi bi-clock text-white"></i>
                            </div>
                            <h6 class="fw-bold">Current Session</h6>
                            <p class="mb-0">{{ now()->format('H:i') }}</p>
                            <small class="text-muted">{{ now()->format('M d, Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection