@extends('layouts.admin')

@section('title', 'Create New Role')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-plus-circle me-2 text-success"></i>Create New Role
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Roles
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Create Role Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-diagram-3 me-2"></i>Role Information
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.roles.store') }}">
                    @csrf
                    
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
                                           id="name" name="name" value="{{ old('name') }}" 
                                           required placeholder="Enter role name"
                                           oninput="generateSlug()">
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
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
                                           id="slug" name="slug" value="{{ old('slug') }}" 
                                           readonly placeholder="Auto-generated">
                                </div>
                                <small class="text-muted">Auto-generated from role name</small>
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
                                      placeholder="Describe the role and its purpose">{{ old('description') }}</textarea>
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
                            <i class="bi bi-key text-warning me-2"></i>Assign Permissions
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
                                                               {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
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
                                        <h6 class="mb-2">Selected Permissions: <span id="selectedCount">0</span></h6>
                                        <div id="selectedPermissions" class="d-flex flex-wrap gap-1">
                                            <small class="text-muted">Select permissions to see them here</small>
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
                                       id="status" name="status" value="1" checked>
                                <label class="form-check-label" for="status">
                                    <strong>Active Role</strong>
                                    <br><small class="text-muted">Users can be assigned to this role</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Create Role
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Role Creation Guide -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-lightbulb text-warning me-2"></i>Role Creation Guide
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Best Practices:</h6>
                        <ul class="list-unstyled small">
                            <li><i class="bi bi-check text-success me-1"></i>Use descriptive role names</li>
                            <li><i class="bi bi-check text-success me-1"></i>Assign minimal required permissions</li>
                            <li><i class="bi bi-check text-success me-1"></i>Document role purpose clearly</li>
                            <li><i class="bi bi-check text-success me-1"></i>Test role permissions before deployment</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Permission Guidelines:</h6>
                        <ul class="list-unstyled small">
                            <li><i class="bi bi-info-circle text-info me-1"></i>View permissions for read-only access</li>
                            <li><i class="bi bi-info-circle text-info me-1"></i>Manage permissions for full control</li>
                            <li><i class="bi bi-info-circle text-info me-1"></i>System permissions for administrative tasks</li>
                            <li><i class="bi bi-info-circle text-info me-1"></i>Reports permissions for analytics access</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Generate slug from role name
function generateSlug() {
    const name = document.getElementById('name').value;
    const slug = name.toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    document.getElementById('slug').value = slug;
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
                badges += `<span class="badge bg-info me-1">${label}</span>`;
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
    
    nameInput.addEventListener('input', function() {
        if (this.value.trim().length >= 2) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        }
    });

    form.addEventListener('submit', function(e) {
        if (nameInput.value.trim().length < 2) {
            e.preventDefault();
            nameInput.classList.add('is-invalid');
            alert('Please enter a valid role name (at least 2 characters).');
        }
    });
});
</script>
@endpush