@extends('layouts.admin')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-crown me-2 text-warning"></i>Super Admin Dashboard
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-warning">
                <i class="bi bi-calendar me-1"></i>
                {{ now()->format('M d, Y') }}
            </button>
        </div>
    </div>
</div>

<!-- Super Admin Welcome -->
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-crown text-warning me-3" style="font-size: 1.5rem;"></i>
                <div>
                    <h5 class="alert-heading mb-1">Welcome, Super Administrator!</h5>
                    <p class="mb-0">
                        You have <strong>full system access</strong> with all administrative privileges.
                        <span class="badge bg-warning text-dark ms-2">{{ Auth::guard('admin')->user()->role->name }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1 opacity-75 small">Users</h6>
                        <h4 class="mb-0">{{ $data['total_users'] }}</h4>
                        <small class="opacity-75">{{ $data['users_count'] }} active</small>
                    </div>
                    <i class="bi bi-people opacity-75" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1 opacity-75 small">Admins</h6>
                        <h4 class="mb-0">{{ $data['total_admins'] }}</h4>
                        <small class="opacity-75">{{ $data['admins_count'] }} active</small>
                    </div>
                    <i class="bi bi-shield-check opacity-75" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm bg-warning text-white">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1 opacity-75 small">Roles</h6>
                        <h4 class="mb-0">{{ $data['roles_count'] }}</h4>
                        <small class="opacity-75">Total roles</small>
                    </div>
                    <i class="bi bi-diagram-3 opacity-75" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1 opacity-75 small">Permissions</h6>
                        <h4 class="mb-0">{{ $data['permissions_count'] }}</h4>
                        <small class="opacity-75">Total perms</small>
                    </div>
                    <i class="bi bi-key opacity-75" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm bg-dark text-white">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1 opacity-75 small">Sessions</h6>
                        <h4 class="mb-0">{{ $data['users_count'] + $data['admins_count'] }}</h4>
                        <small class="opacity-75">Active now</small>
                    </div>
                    <i class="bi bi-activity opacity-75" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm bg-secondary text-white">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1 opacity-75 small">System</h6>
                        <h6 class="mb-0">Online</h6>
                        <small class="opacity-75">All systems</small>
                    </div>
                    <i class="bi bi-cpu opacity-75" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Management Sections -->
<div class="row g-4">
    <!-- User Management -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-primary text-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-people me-2"></i>User Management
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="d-grid">
                            <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-list me-2"></i>View All Users
                            </a>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-grid">
                            <button class="btn btn-outline-success">
                                <i class="bi bi-person-plus me-2"></i>Add New User
                            </button>
                        </div>
                    </div>
                </div>
                
                <h6 class="mb-3">Recent Users</h6>
                <div class="list-group list-group-flush">
                    <div class="list-group-item border-0 px-0 py-2">
                        <div class="d-flex align-items-center">
                            <div class="bg-success rounded-circle p-1 me-3">
                                <i class="bi bi-person text-white small"></i>
                            </div>
                            <div class="flex-grow-1">
                                <small class="fw-bold">New user registered</small>
                                <br><small class="text-muted">john@example.com • 2 min ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Management -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-success text-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-shield-check me-2"></i>Admin Management
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="d-grid">
                            <a href="{{ route('superadmin.admins.index') }}" class="btn btn-outline-success">
                                <i class="bi bi-list me-2"></i>View All Admins
                            </a>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-grid">
                            <button class="btn btn-outline-warning">
                                <i class="bi bi-shield-plus me-2"></i>Add New Admin
                            </button>
                        </div>
                    </div>
                </div>
                
                <h6 class="mb-3">Admin Activity</h6>
                <div class="list-group list-group-flush">
                    <div class="list-group-item border-0 px-0 py-2">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning rounded-circle p-1 me-3">
                                <i class="bi bi-shield text-white small"></i>
                            </div>
                            <div class="flex-grow-1">
                                <small class="fw-bold">Admin login detected</small>
                                <br><small class="text-muted">admin@example.com • 5 min ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role & Permission Management -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-warning text-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-diagram-3 me-2"></i>Role Management
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="d-grid">
                            <a href="{{ route('superadmin.roles.index') }}" class="btn btn-outline-warning">
                                <i class="bi bi-diagram-3 me-2"></i>Manage Roles
                            </a>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-grid">
                            <button class="btn btn-outline-info">
                                <i class="bi bi-plus-circle me-2"></i>Create Role
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="fw-bold">System Roles</small>
                    <span class="badge bg-warning">{{ $data['roles_count'] }}</span>
                </div>
                
                <div class="progress mb-2" style="height: 6px;">
                    <div class="progress-bar bg-warning" style="width: 75%"></div>
                </div>
                <small class="text-muted">Role configuration: 75% complete</small>
            </div>
        </div>
    </div>

    <!-- System Controls -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-info text-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>System Controls
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="d-grid">
                            <a href="{{ route('superadmin.permissions.index') }}" class="btn btn-outline-info">
                                <i class="bi bi-key me-2"></i>Permissions
                            </a>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-grid">
                            <button class="btn btn-outline-secondary">
                                <i class="bi bi-gear me-2"></i>Settings
                            </button>
                        </div>
                    </div>
                </div>
                
                <h6 class="mb-3">System Status</h6>
                <div class="d-flex justify-content-between mb-2">
                    <small>Database Connection</small>
                    <span class="badge bg-success">Online</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <small>Cache System</small>
                    <span class="badge bg-success">Active</span>
                </div>
                <div class="d-flex justify-content-between">
                    <small>Security Level</small>
                    <span class="badge bg-success">High</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection