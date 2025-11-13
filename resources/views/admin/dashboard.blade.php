@extends('layouts.admin')

@section('title', $data['admin']->isSuperAdmin() ? 'Super Admin Dashboard' : 'Admin Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        @if($data['admin']->isSuperAdmin())
            <i class="bi bi-crown me-2 text-warning"></i>Super Admin Dashboard
        @else
            <i class="bi bi-speedometer2 me-2 text-primary"></i>Admin Dashboard
        @endif
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-{{ $data['admin']->isSuperAdmin() ? 'warning' : 'primary' }}">
                <i class="bi bi-calendar me-1"></i>
                {{ now()->format('M d, Y') }}
            </button>
        </div>
    </div>
</div>

<!-- Role-Based Welcome Alert -->
<div class="row mb-4">
    <div class="col-12">
        @if($data['admin']->isSuperAdmin())
            <div class="alert alert-warning border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-crown text-warning me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Welcome, Super Administrator!</h5>
                        <p class="mb-0">
                            You have <strong>full system access</strong> with all administrative privileges.
                            <span class="badge bg-warning text-dark ms-2">{{ $data['admin']->role->name }}</span>
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-shield-check text-primary me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Welcome, {{ $data['admin']->name }}!</h5>
                        <p class="mb-0">
                            You are logged in as an <strong>Administrator</strong>. 
                            Your role: <span class="badge bg-primary">{{ $data['admin']->role->name ?? 'Admin' }}</span>
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Enhanced Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1 opacity-75">Total Users</h6>
                        <h2 class="mb-0">{{ $data['total_users'] }}</h2>
                        <small class="opacity-75">
                            <i class="bi bi-arrow-up me-1"></i>Active: {{ $data['users_count'] }}
                        </small>
                    </div>
                    <div class="opacity-75">
                        <i class="bi bi-people" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1 opacity-75">Total Admins</h6>
                        <h2 class="mb-0">{{ $data['total_admins'] }}</h2>
                        <small class="opacity-75">
                            <i class="bi bi-arrow-up me-1"></i>Active: {{ $data['admins_count'] }}
                        </small>
                    </div>
                    <div class="opacity-75">
                        <i class="bi bi-shield-check" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($data['admin']->isSuperAdmin())
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase mb-1 opacity-75">System Roles</h6>
                            <h2 class="mb-0">{{ $data['roles_count'] }}</h2>
                            <small class="opacity-75">
                                <i class="bi bi-diagram-3 me-1"></i>Total roles
                            </small>
                        </div>
                        <div class="opacity-75">
                            <i class="bi bi-diagram-3" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase mb-1 opacity-75">Permissions</h6>
                            <h2 class="mb-0">{{ $data['permissions_count'] }}</h2>
                            <small class="opacity-75">
                                <i class="bi bi-key me-1"></i>Total permissions
                            </small>
                        </div>
                        <div class="opacity-75">
                            <i class="bi bi-key" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase mb-1 opacity-75">Active Sessions</h6>
                            <h2 class="mb-0">{{ $data['users_count'] + $data['admins_count'] }}</h2>
                            <small class="opacity-75">
                                <i class="bi bi-clock me-1"></i>Last hour
                            </small>
                        </div>
                        <div class="opacity-75">
                            <i class="bi bi-activity" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase mb-1 opacity-75">System Status</h6>
                            <h5 class="mb-0">Online</h5>
                            <small class="opacity-75">
                                <i class="bi bi-check-circle me-1"></i>All systems operational
                            </small>
                        </div>
                        <div class="opacity-75">
                            <i class="bi bi-cpu" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Role-Based Content Grid -->
<div class="row g-4">
    <!-- Quick Actions -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning-charge text-primary me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @if($data['admin']->hasPermission('view-users') || $data['admin']->hasPermission('manage-users'))
                        <div class="col-sm-6">
                            <div class="d-grid">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-people me-2"></i>Manage Users
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($data['admin']->isSuperAdmin())
                        <div class="col-sm-6">
                            <div class="d-grid">
                                <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-success">
                                    <i class="bi bi-shield-plus me-2"></i>Manage Admins
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-grid">
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-warning">
                                    <i class="bi bi-diagram-3 me-2"></i>Manage Roles
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-grid">
                                <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-info">
                                    <i class="bi bi-gear me-2"></i>System Settings
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($data['admin']->hasPermission('view-reports'))
                        <div class="col-sm-6">
                            <div class="d-grid">
                                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-bar-chart me-2"></i>View Reports
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history text-success me-2"></i>Recent Activity
                </h5>
            </div>
            <div class="card-body">
                @if($data['admin']->isSuperAdmin() && isset($data['recent_users']))
                    <div class="list-group list-group-flush">
                        @forelse($data['recent_users'] as $user)
                            <div class="list-group-item border-0 px-0 py-2">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success rounded-circle p-1 me-3">
                                        <i class="bi bi-person-check text-white small"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="fw-bold">New user: {{ $user->name }}</small>
                                        <br><small class="text-muted">{{ $user->email }} • {{ $user->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-3 text-muted">
                                <i class="bi bi-clock-history mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                <p class="mb-0">No recent activity</p>
                            </div>
                        @endforelse
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded-circle p-2 me-3">
                                    <i class="bi bi-person-check text-white"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">System activity monitoring</h6>
                                    <small class="text-muted">Available for Super Admins</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="text-center mt-3">
                    @if($data['admin']->hasPermission('view-reports'))
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-eye me-1"></i>View All Reports
                        </a>
                    @else
                        <small class="text-muted">Contact Super Admin for detailed reports</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($data['admin']->isSuperAdmin())
<!-- System Management Section (Super Admin Only) -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-crown me-2"></i>System Management Overview
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="bg-primary rounded-circle p-3 d-inline-flex mb-2">
                                <i class="bi bi-people text-white" style="font-size: 1.5rem;"></i>
                            </div>
                            <h6 class="fw-bold">{{ $data['total_users'] }}</h6>
                            <small class="text-muted">Total Users</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="bg-success rounded-circle p-3 d-inline-flex mb-2">
                                <i class="bi bi-shield-check text-white" style="font-size: 1.5rem;"></i>
                            </div>
                            <h6 class="fw-bold">{{ $data['total_admins'] }}</h6>
                            <small class="text-muted">Total Admins</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="bg-warning rounded-circle p-3 d-inline-flex mb-2">
                                <i class="bi bi-diagram-3 text-white" style="font-size: 1.5rem;"></i>
                            </div>
                            <h6 class="fw-bold">{{ $data['roles_count'] }}</h6>
                            <small class="text-muted">System Roles</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="bg-info rounded-circle p-3 d-inline-flex mb-2">
                                <i class="bi bi-key text-white" style="font-size: 1.5rem;"></i>
                            </div>
                            <h6 class="fw-bold">{{ $data['permissions_count'] }}</h6>
                            <small class="text-muted">Permissions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- System Information -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle text-info me-2"></i>System Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <h6 class="text-muted">Laravel Version</h6>
                        <p class="mb-0 fw-bold">{{ app()->version() }}</p>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted">PHP Version</h6>
                        <p class="mb-0 fw-bold">{{ phpversion() }}</p>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted">Environment</h6>
                        <p class="mb-0">
                            <span class="badge bg-{{ app()->environment() === 'production' ? 'success' : 'warning' }}">
                                {{ ucfirst(app()->environment()) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted">Your Access Level</h6>
                        <p class="mb-0">
                            @if($data['admin']->isSuperAdmin())
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-crown me-1"></i>Super Admin
                                </span>
                            @else
                                <span class="badge bg-primary">
                                    <i class="bi bi-shield-check me-1"></i>Administrator
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection