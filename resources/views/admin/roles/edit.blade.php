@extends('layouts.admin')

@section('title', 'Edit Role - ' . $role->name)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-pencil me-2 text-primary"></i>Edit Role
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-outline-info">
                <i class="bi bi-eye me-1"></i>View Role
            </a>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Roles
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Current Role Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        @if($role->slug === 'superadmin')
                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px;">
                                <i class="bi bi-crown text-dark" style="font-size: 1.5rem;"></i>
                            </div>
                        @elseif($role->slug === 'admin')
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px;">
                                <i class="bi bi-shield-check text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        @elseif($role->slug === 'user')
                            <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px;">
                                <i class="bi bi-person text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        @else
                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px;">
                                <i class="bi bi-diagram-3 text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h5 class="mb-1">{{ $role->name }}</h5>
                        <p class="text-muted mb-1">{{ $role->description }}</p>
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>Created {{ $role->created_at->format('M d, Y') }}
                            • <span class="badge bg-info">{{ $role->permissions->count() }} permissions</span>
                            @if(in_array($role->slug, ['superadmin', 'admin', 'user']))
                                • <span class="badge bg-warning text-dark">System Role</span>
                            @endif
                        </small>
                    </div>
                    <div class="ms-auto">
                        @if($role->status)
                            <span class="badge bg-success fs-6">
                                <i class="bi bi-check-circle me-1"></i>Active
                            </span>
                        @else
                            <span class="badge bg-danger fs-6">
                                <i class="bi bi-x-circle me-1"></i>Inactive
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- System Role Warning -->
        @if(in_array($role->slug, ['superadmin', 'admin', 'user']))
        <div class="alert alert-warning border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex">
                <i class="bi bi-shield-exclamation me-3" style="font-size: 1.5rem;"></i>
                <div>
                    <h5 class="alert-heading mb-1">System Role Warning</h5>
                    <p class="mb-0">
                        This is a system role. Be very careful when modifying permissions as it may affect system functionality.
                        <strong>Role name and slug cannot be changed.</strong>
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Edit Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pencil me-2"></i>Update Role Information
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Role Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-tag"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $role->name) }}" 
                                           required placeholder="Enter role name"
                                           @if(in_array($role->slug, ['superadmin', 'admin', 'user'])) readonly @endif
                                           oninput="generateSlug()">
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                @if(in_array($role->slug, ['superadmin', 'admin', 'user']))
                                    <small class="text-warning">System role names cannot be changed</small>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug" class="form-label fw-bold">Role Slug</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-hash"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control" 
                                           id="slug" name="slug" value="{{ old('slug', $role->slug) }}" 
                                           readonly>
                                </div>
                                <small class="text-muted">System-generated identifier</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-text-paragraph"></i>
                            </span>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3"
                                      placeholder="Describe the role and its purpose">{{ old('description', $role->description) }}</textarea>
                        </div>
                        @error('description')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Permissions Section -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-key text-warning me-2"></i>Manage Permissions
                        </h6>
                        
                        @if($permissions->count() > 0)
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                                <label class="form-check-label fw-bold" for="selectAll">
                                                    Select All Permissions
                                                </label>
                                            </div>
                                            <hr>
                                        </div>
                                        
                                        @foreach($permissions->chunk(3) as $permissionChunk)
                                            @foreach($permissionChunk as $permission)
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input permission-checkbox" 
                                                               type="checkbox" 
                                                               name="permissions[]" 
                                                               value="{{ $permission->id }}" 
                                                               id="permission_{{ $permission->id }}"
                                                               {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                            <strong>{{ $permission->name }}</strong>
                                                            @if($permission->description)
                                                                <br><small class="text-muted">{{ $permission->description }}</small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                    
                                    <div class="mt-3 p-3 bg-white rounded border">
                                        <h6 class="mb-2">Selected Permissions: <span id="selectedCount">{{ count($rolePermissions) }}</span></h6>
                                        <div id="selectedPermissions" class="d-flex flex-wrap gap-1">
                                            <!-- Will be populated by JavaScript -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                No permissions available. Please create permissions first.
                            </div>
                        @endif
                    </div>

                    <!-- Role Status -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bi bi-gear me-2"></i>Role Settings
                            </h6>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" 
                                       id="status" name="status" value="1" 
                                       {{ old('status', $role->status) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">
                                    <strong>Active Role</strong>
                                    <br><small class="text-muted">Users can be assigned to this role</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-outline-info">
                                <i class="bi bi-eye me-1"></i>View Role
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Update Role
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Role Usage Information -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-people text-info me-2"></i>Role Usage Information
                </h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                                 style="width: 50px; height: 50px;">
                                <i class="bi bi-people text-white"></i>
                            </div>
                            <h6 class="fw-bold">{{ $role->users->count() }}</h6>
                            <small class="text-muted">Users Assigned</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                                 style="width: 50px; height: 50px;">
                                <i class="bi bi-shield-check text-white"></i>
                            </div>
                            <h6 class="fw-bold">{{ $role->admins->count() }}</h6>
                            <small class="text-muted">Admins Assigned</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                                 style="width: 50px; height: 50px;">
                                <i class="bi bi-key text-dark"></i>
                            </div>
                            <h6 class="fw-bold">{{ $role->permissions->count() }}</h6>
                            <small class="text-muted">Permissions</small>
                        </div>
                    </div>
                </div>
                
                @if($role->users->count() > 0 || $role->admins->count() > 0)
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        This role is currently assigned to {{ $role->users->count() + $role->admins->count() }} user(s). 
                        Changes to permissions will immediately affect all assigned users.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Generate slug from role name (only for custom roles)
function generateSlug() {
    @if(!in_array($role->slug, ['superadmin', 'admin', 'user']))
        const name = document.getElementById('name').value;
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
    @endif
}

// Permission management
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const selectedPermissions = document.getElementById('selectedPermissions');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        permissionCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedPermissions();
    });

    // Individual permission selection
    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState();
            updateSelectedPermissions();
        });
    });

    function updateSelectAllState() {
        const checkedCount = document.querySelectorAll('.permission-checkbox:checked').length;
        const totalCount = permissionCheckboxes.length;
        
        selectAllCheckbox.checked = checkedCount === totalCount;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
    }

    function updateSelectedPermissions() {
        const checkedBoxes = document.querySelectorAll('.permission-checkbox:checked');
        selectedCount.textContent = checkedBoxes.length;
        
        if (checkedBoxes.length === 0) {
            selectedPermissions.innerHTML = '<small class="text-muted">No permissions selected</small>';
        } else {
            let badges = '';
            checkedBoxes.forEach(checkbox => {
                const label = checkbox.nextElementSibling.querySelector('strong').textContent;
                badges += `<span class="badge bg-info me-1 mb-1">${label}</span>`;
            });
            selectedPermissions.innerHTML = badges;
        }
    }

    // Initialize on page load
    updateSelectAllState();
    updateSelectedPermissions();

    // Form validation
    const form = document.querySelector('form');
    const nameInput = document.getElementById('name');
    
    if (!nameInput.readOnly) {
        nameInput.addEventListener('input', function() {
            if (this.value.trim().length >= 2) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });
    }

    form.addEventListener('submit', function(e) {
        if (!nameInput.readOnly && nameInput.value.trim().length < 2) {
            e.preventDefault();
            nameInput.classList.add('is-invalid');
            alert('Please enter a valid role name (at least 2 characters).');
        }
    });
});
</script>
@endpush