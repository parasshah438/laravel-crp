@extends('layouts.admin')

@section('title', 'Add New User')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-person-plus me-2 text-success"></i>Add New User
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Users
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-plus me-2"></i>User Information
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           required placeholder="Enter full name">
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
                                <label for="email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" 
                                           required placeholder="Enter email address">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" 
                                           required placeholder="Enter password">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <i class="bi bi-eye" id="password-icon"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Minimum 8 characters required</small>
                                @error('password')
                                    <div class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role_id" class="form-label fw-bold">Assign Role</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-diagram-3"></i>
                                    </span>
                                    <select class="form-select @error('role_id') is-invalid @enderror" 
                                            id="role_id" name="role_id">
                                        <option value="">Select a role (optional)</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }} - {{ $role->description }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('role_id')
                                    <div class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Account Settings -->
                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bi bi-gear me-2"></i>Account Settings
                            </h6>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" 
                                       id="status" name="status" value="1" checked>
                                <label class="form-check-label" for="status">
                                    <strong>Active Account</strong>
                                    <br><small class="text-muted">User can log in and access the system</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Card -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-info-circle text-info me-2"></i>Help & Information
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Password Requirements:</h6>
                        <ul class="list-unstyled small">
                            <li><i class="bi bi-check text-success me-1"></i>Minimum 8 characters</li>
                            <li><i class="bi bi-check text-success me-1"></i>Mix of letters and numbers recommended</li>
                            <li><i class="bi bi-check text-success me-1"></i>Special characters for extra security</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Role Assignment:</h6>
                        <ul class="list-unstyled small">
                            <li><i class="bi bi-info-circle text-info me-1"></i>Roles are optional for users</li>
                            <li><i class="bi bi-info-circle text-info me-1"></i>Roles determine system permissions</li>
                            <li><i class="bi bi-info-circle text-info me-1"></i>Can be changed later if needed</li>
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
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Form validation feedback
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim() !== '') {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });
});
</script>
@endpush