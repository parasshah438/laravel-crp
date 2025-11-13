@extends('layouts.admin')

@section('title', 'Role Management')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-diagram-3 me-2 text-warning"></i>Role Management
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.roles.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Create New Role
            </a>
        </div>
    </div>
</div>

<!-- Super Admin Notice -->
<div class="alert alert-warning border-0 shadow-sm mb-4" role="alert">
    <div class="d-flex">
        <i class="bi bi-crown me-3" style="font-size: 1.5rem;"></i>
        <div>
            <h5 class="alert-heading mb-1">Super Admin Access</h5>
            <p class="mb-0">
                You have full access to manage all roles and permissions. Changes here affect system-wide access control.
                <strong>Please use with caution.</strong>
            </p>
        </div>
    </div>
</div>

<!-- Role Statistics -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Roles</h6>
                        <h3 class="mb-0">{{ $roles->total() }}</h3>
                    </div>
                    <i class="bi bi-diagram-3" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Active Roles</h6>
                        <h3 class="mb-0">{{ $roles->where('status', true)->count() }}</h3>
                    </div>
                    <i class="bi bi-check-circle" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">System Roles</h6>
                        <h3 class="mb-0">{{ $roles->whereIn('slug', ['superadmin', 'admin', 'user'])->count() }}</h3>
                    </div>
                    <i class="bi bi-shield-lock" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Permissions</h6>
                        <h3 class="mb-0">{{ $roles->sum(function($role) { return $role->permissions->count(); }) }}</h3>
                    </div>
                    <i class="bi bi-key" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Roles Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-light border-0">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list me-2"></i>System Roles
                </h5>
            </div>
            <div class="col-auto">
                <div class="input-group input-group-sm">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Search roles..." id="searchInput">
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($roles->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Role</th>
                            <th>Description</th>
                            <th>Permissions</th>
                            <th>Status</th>
                            <th>Users</th>
                            <th>Created</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark">#{{ $role->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($role->slug === 'superadmin')
                                                <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="bi bi-crown text-dark"></i>
                                                </div>
                                            @elseif($role->slug === 'admin')
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="bi bi-shield-check text-white"></i>
                                                </div>
                                            @elseif($role->slug === 'user')
                                                <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="bi bi-person text-white"></i>
                                                </div>
                                            @else
                                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="bi bi-diagram-3 text-white"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $role->name }}</h6>
                                            <small class="text-muted">{{ $role->slug }}</small>
                                            @if(in_array($role->slug, ['superadmin', 'admin', 'user']))
                                                <span class="badge bg-warning text-dark ms-1">System Role</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ Str::limit($role->description, 50) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $role->permissions->count() }} permissions</span>
                                    @if($role->permissions->count() > 0)
                                        <br><small class="text-muted">
                                            {{ $role->permissions->take(2)->pluck('name')->join(', ') }}
                                            @if($role->permissions->count() > 2)
                                                +{{ $role->permissions->count() - 2 }} more
                                            @endif
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if($role->status)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Active
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i>Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-light text-dark mb-1">
                                            {{ $role->users->count() }} Users
                                        </span>
                                        <span class="badge bg-light text-dark">
                                            {{ $role->admins->count() }} Admins
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $role->created_at->format('M d, Y') }}
                                        <br>
                                        <span class="text-muted">{{ $role->created_at->diffForHumans() }}</span>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.roles.show', $role) }}" 
                                           class="btn btn-sm btn-outline-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.roles.edit', $role) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if(!in_array($role->slug, ['superadmin', 'admin', 'user']))
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    title="Delete" onclick="confirmDelete({{ $role->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="delete-form-{{ $role->id }}" 
                                                  action="{{ route('admin.roles.destroy', $role) }}" 
                                                  method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                    title="System Role - Cannot Delete" disabled>
                                                <i class="bi bi-shield-lock"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($roles->hasPages())
                <div class="card-footer bg-light border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Showing {{ $roles->firstItem() }} to {{ $roles->lastItem() }} of {{ $roles->total() }} roles
                        </small>
                        {{ $roles->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="bi bi-diagram-3 text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                <h5 class="mt-3 text-muted">No Roles Found</h5>
                <p class="text-muted">There are no roles to display.</p>
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Create First Role
                </a>
            </div>
        @endif
    </div>
</div>

<!-- System Roles Information -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-info-circle text-info me-2"></i>System Role Information
                </h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 30px; height: 30px;">
                                <i class="bi bi-crown text-dark small"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Super Administrator</h6>
                                <small class="text-muted">Full system access and control</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 30px; height: 30px;">
                                <i class="bi bi-shield-check text-white small"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Administrator</h6>
                                <small class="text-muted">Limited administrative access</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-info rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 30px; height: 30px;">
                                <i class="bi bi-person text-white small"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">User</h6>
                                <small class="text-muted">Standard user access</small>
                            </div>
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
function confirmDelete(roleId) {
    if (confirm('Are you sure you want to delete this role? This action cannot be undone and will affect all users assigned to this role.')) {
        document.getElementById('delete-form-' + roleId).submit();
    }
}

// Simple search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    var input = this.value.toLowerCase();
    var rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(function(row) {
        var name = row.cells[1].textContent.toLowerCase();
        var description = row.cells[2].textContent.toLowerCase();
        
        if (name.includes(input) || description.includes(input)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
@endpush