@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-calendar me-1"></i>
                {{ now()->format('M d, Y') }}
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="alert alert-info border-0" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle-fill me-3" style="font-size: 1.5rem;"></i>
                <div>
                    <h5 class="alert-heading mb-1">Welcome back, {{ $user->name }}!</h5>
                    <p class="mb-0">You are successfully logged in to your dashboard. 
                        @if($user->role)
                            Your current role is: <strong>{{ $user->role->name }}</strong>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Profile Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary rounded-circle p-3 me-3">
                        <i class="bi bi-person text-white"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">Profile Information</h5>
                        <small class="text-muted">Your account details</small>
                    </div>
                </div>
                
                <hr>
                
                <div class="mb-2">
                    <small class="text-muted">Name:</small>
                    <div class="fw-bold">{{ $user->name }}</div>
                </div>
                
                <div class="mb-2">
                    <small class="text-muted">Email:</small>
                    <div class="fw-bold">{{ $user->email }}</div>
                </div>
                
                <div class="mb-2">
                    <small class="text-muted">Role:</small>
                    <div>
                        @if($user->role)
                            <span class="badge bg-primary">{{ $user->role->name }}</span>
                        @else
                            <span class="badge bg-secondary">No Role Assigned</span>
                        @endif
                    </div>
                </div>
                
                <div class="mb-2">
                    <small class="text-muted">Status:</small>
                    <div>
                        <span class="badge bg-success">Active</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted">Member Since:</small>
                    <div class="fw-bold">{{ $user->created_at->format('M d, Y') }}</div>
                </div>
                
                <button class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-pencil me-1"></i>Edit Profile
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="bi bi-lightning-charge me-2"></i>Quick Actions
                </h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center">
                                <i class="bi bi-person-gear text-primary mb-2" style="font-size: 2rem;"></i>
                                <h6 class="card-title">Account Settings</h6>
                                <p class="card-text small text-muted">Update your account information and preferences.</p>
                                <button class="btn btn-primary btn-sm">
                                    <i class="bi bi-gear me-1"></i>Settings
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center">
                                <i class="bi bi-shield-check text-success mb-2" style="font-size: 2rem;"></i>
                                <h6 class="card-title">Security</h6>
                                <p class="card-text small text-muted">Manage your password and security settings.</p>
                                <button class="btn btn-success btn-sm">
                                    <i class="bi bi-shield-lock me-1"></i>Security
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                @if($user->role && $user->role->permissions->count() > 0)
                <hr class="my-4">
                
                <h6 class="mb-3">
                    <i class="bi bi-key me-2"></i>Your Permissions
                </h6>
                
                <div class="row">
                    @foreach($user->role->permissions->take(6) as $permission)
                        <div class="col-md-4 col-sm-6 mb-2">
                            <span class="badge bg-info text-dark w-100">
                                <i class="bi bi-check-circle me-1"></i>{{ $permission->name }}
                            </span>
                        </div>
                    @endforeach
                    @if($user->role->permissions->count() > 6)
                        <div class="col-12">
                            <small class="text-muted">
                                ... and {{ $user->role->permissions->count() - 6 }} more permissions
                            </small>
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-graph-up me-2"></i>Activity Overview
                </h5>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-graph-up" style="font-size: 4rem; opacity: 0.3;"></i>
                    <p class="mt-3">Activity tracking will be displayed here.</p>
                    <small>This section will show your recent activity and usage statistics.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection