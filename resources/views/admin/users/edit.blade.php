@extends('layouts.admin')

@section('title', 'Edit User - ' . $user->name)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-pencil me-2 text-primary"></i>Edit User
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info">
                <i class="bi bi-eye me-1"></i>View User
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Users
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- User Info Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 60px; height: 60px;">
                        <i class="bi bi-person text-white" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <p class="text-muted mb-1">{{ $user->email }}</p>
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>Joined {{ $user->created_at->format('M d, Y') }}
                            @if($user->role)
                                • <span class="badge bg-info">{{ $user->role->name }}</span>
                            @endif
                        </small>
                    </div>
                    <div class="ms-auto">
                        @if($user->status)
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

        <!-- Edit Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pencil me-2"></i>Update User Information
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')
                    
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
                                           id="name" name="name" value="{{ old('name', $user->name) }}" 
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
                                           id="email" name="email" value="{{ old('email', $user->email) }}" 
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
                                <label for="password" class="form-label fw-bold">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" 
                                           placeholder="Leave blank to keep current password">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <i class="bi bi-eye" id="password-icon"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Leave blank to keep current password. Minimum 8 characters if changing.</small>
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
                                        <option value="">No role assigned</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" 
                                                {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
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

                    <!-- Account Status -->
                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bi bi-gear me-2"></i>Account Status
                            </h6>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" 
                                       id="status" name="status" value="1" 
                                       {{ old('status', $user->status) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">
                                    <strong>Active Account</strong>
                                    <br><small class="text-muted">User can log in and access the system</small>
                                </label>
                            </div>
                            @if(!$user->status)
                                <div class="alert alert-warning mt-2 mb-0">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    This account is currently inactive. Enable it to allow user login.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-info">
                                <i class="bi bi-eye me-1"></i>View User
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Account Statistics -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-graph-up text-success me-2"></i>Account Statistics
                </h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <h6 class="text-muted">Member Since</h6>
                            <p class="fw-bold">{{ $user->created_at->format('M d, Y') }}</p>
                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h6 class="text-muted">Last Updated</h6>
                            <p class="fw-bold">{{ $user->updated_at->format('M d, Y') }}</p>
                            <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h6 class="text-muted">Email Status</h6>
                            @if($user->email_verified_at)
                                <p class="fw-bold text-success">Verified</p>
                                <small class="text-muted">{{ $user->email_verified_at->format('M d, Y') }}</small>
                            @else
                                <p class="fw-bold text-warning">Not Verified</p>
                                <small class="text-muted">Pending verification</small>
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