@extends('layouts.admin')

@section('title', 'Role Details - ' . $role->name)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-diagram-3 me-2 text-info"></i>Role Details
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-pencil me-1"></i>Edit Role
            </a>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Roles
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Role Profile Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-4">
                @if($role->slug === 'superadmin')
                    <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 100px; height: 100px;">
                        <i class="bi bi-crown text-dark" style="font-size: 3rem;"></i>
                    </div>
                @elseif($role->slug === 'admin')
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 100px; height: 100px;">
                        <i class="bi bi-shield-check text-white" style="font-size: 3rem;"></i>
                    </div>
                @elseif($role->slug === 'user')
                    <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 100px; height: 100px;">
                        <i class="bi bi-person text-white" style="font-size: 3rem;"></i>
                    </div>
                @else
                    <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 100px; height: 100px;">
                        <i class="bi bi-diagram-3 text-white" style="font-size: 3rem;"></i>
                    </div>
                @endif
                
                <h4 class="card-title">{{ $role->name }}</h4>
                <p class="text-muted">{{ $role->slug }}</p>
                
                <div class="mb-3">
                    @if($role->status)
                        <span class="badge bg-success fs-6">
                            <i class="bi bi-check-circle me-1"></i>Active
                        </span>
                    @else
                        <span class="badge bg-danger fs-6">
                            <i class="bi bi-x-circle me-1"></i>Inactive
                        </span>
                    @endif
                    
                    @if(in_array($role->slug, ['superadmin', 'admin', 'user']))
                        <span class="badge bg-warning text-dark fs-6">
                            <i class="bi bi-shield-lock me-1"></i>System Role
                        </span>
                    @endif
                </div>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Edit Role
                    </a>
                    @if(!in_array($role->slug, ['superadmin', 'admin', 'user']))
                        <button class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="bi bi-trash me-1"></i>Delete Role
                        </button>
                        
                        <form id="delete-form" action="{{ route('admin.roles.destroy', $role) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    @else
                        <button class="btn btn-outline-secondary" disabled>
                            <i class="bi bi-shield-lock me-1"></i>Protected Role
                        </button>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-light border-0">
                <h6 class="card-title mb-0">
                    <i class="bi bi-bar-chart text-success me-2"></i>Usage Statistics
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="border-end">
                            <h5 class="text-primary">{{ $role->users->count() }}</h5>
                            <small class="text-muted">Users</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border-end">
                            <h5 class="text-success">{{ $role->admins->count() }}</h5>
                            <small class="text-muted">Admins</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <h5 class="text-warning">{{ $role->permissions->count() }}</h5>
                        <small class="text-muted">Permissions</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Role Information Details -->
    <div class="col-lg-8">
        <!-- Basic Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle text-primary me-2"></i>Role Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Role ID</label>
                            <p class="fw-bold">#{{ $role->id }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Role Name</label>
                            <p class="fw-bold">{{ $role->name }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Role Slug</label>
                            <p class="fw-bold">
                                <code>{{ $role->slug }}</code>
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Status</label>
                            <p>
                                @if($role->status)
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
                            <label class="form-label text-muted">Role Type</label>
                            <p>
                                @if(in_array($role->slug, ['superadmin', 'admin', 'user']))
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-shield-lock me-1"></i>System Role
                                    </span>
                                @else
                                    <span class="badge bg-info">
                                        <i class="bi bi-plus-circle me-1"></i>Custom Role
                                    </span>
                                @endif
                            </p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Created</label>
                            <p class="fw-bold">{{ $role->created_at->format('M d, Y H:i') }}</p>
                            <small class="text-muted">{{ $role->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
                
                @if($role->description)
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label text-muted">Description</label>
                                <p class="fw-bold">{{ $role->description }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Permissions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-key text-warning me-2"></i>Assigned Permissions ({{ $role->permissions->count() }})
                </h5>
            </div>
            <div class="card-body">
                @if($role->permissions->count() > 0)
                    <div class="row">
                        @foreach($role->permissions->chunk(2) as $permissionChunk)
                            @foreach($permissionChunk as $permission)
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" 
                                             style="width: 30px; height: 30px; flex-shrink: 0;">
                                            <i class="bi bi-check text-white small"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $permission->name }}</h6>
                                            @if($permission->description)
                                                <p class="text-muted mb-0 small">{{ $permission->description }}</p>
                                            @endif
                                            <small class="text-muted">{{ $permission->slug }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-key text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                        <h6 class="mt-2 text-muted">No Permissions Assigned</h6>
                        <p class="text-muted">This role has no specific permissions assigned.</p>
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>Assign Permissions
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Assigned Users -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-people text-info me-2"></i>Users with this Role
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Regular Users -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Regular Users ({{ $role->users->count() }})</h6>
                        @if($role->users->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($role->users->take(5) as $user)
                                    <div class="list-group-item border-0 px-0 py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 35px; height: 35px;">
                                                <i class="bi bi-person text-white small"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                            <span class="badge bg-{{ $user->status ? 'success' : 'danger' }}">
                                                {{ $user->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                                @if($role->users->count() > 5)
                                    <div class="list-group-item border-0 px-0 py-2">
                                        <small class="text-muted">
                                            ... and {{ $role->users->count() - 5 }} more users
                                        </small>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="bi bi-person text-muted opacity-50" style="font-size: 2rem;"></i>
                                <p class="text-muted mb-0">No users assigned to this role</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Admin Users -->
                    <div class="col-md-6">
                        <h6 class="text-success mb-3">Admin Users ({{ $role->admins->count() }})</h6>
                        @if($role->admins->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($role->admins->take(5) as $admin)
                                    <div class="list-group-item border-0 px-0 py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 35px; height: 35px;">
                                                <i class="bi bi-shield-check text-white small"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $admin->name }}</h6>
                                                <small class="text-muted">{{ $admin->email }}</small>
                                            </div>
                                            <span class="badge bg-{{ $admin->status ? 'success' : 'danger' }}">
                                                {{ $admin->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                                @if($role->admins->count() > 5)
                                    <div class="list-group-item border-0 px-0 py-2">
                                        <small class="text-muted">
                                            ... and {{ $role->admins->count() - 5 }} more admins
                                        </small>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="bi bi-shield text-muted opacity-50" style="font-size: 2rem;"></i>
                                <p class="text-muted mb-0">No admins assigned to this role</p>
                            </div>
                        @endif
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
    if (confirm('Are you sure you want to delete this role? This action cannot be undone and will affect all users assigned to this role.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush