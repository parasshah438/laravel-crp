@extends('layouts.admin')

@section('title', 'Edit Profile')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-pencil me-2 text-primary"></i>Edit Profile
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.profile.show') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Profile
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Current Profile Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-gradient rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 70px; height: 70px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="bi bi-person text-white" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">{{ $admin->name }}</h5>
                        <p class="text-muted mb-1">{{ $admin->email }}</p>
                        <small class="text-muted">
                            @if($admin->isSuperAdmin())
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-crown me-1"></i>Super Administrator
                                </span>
                            @else
                                <span class="badge bg-primary">
                                    <i class="bi bi-shield-check me-1"></i>Administrator
                                </span>
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-gear me-2"></i>Update Profile Information
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.profile.update') }}">
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
                                           id="name" name="name" value="{{ old('name', $admin->name) }}" 
                                           required placeholder="Enter your full name"
                                           autocomplete="name">
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                <small class="text-muted">This name will be displayed throughout the admin panel</small>
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
                                           id="email" name="email" value="{{ old('email', $admin->email) }}" 
                                           required placeholder="Enter your email address"
                                           autocomplete="email">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                <small class="text-muted">This email is used for login and notifications</small>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information (Read-only) -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bi bi-info-circle text-info me-2"></i>Account Information
                            </h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <small class="text-muted">Admin ID:</small>
                                        <div class="fw-bold">#{{ $admin->id }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <small class="text-muted">Role:</small>
                                        <div>
                                            @if($admin->isSuperAdmin())
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-crown me-1"></i>Super Admin
                                                </span>
                                            @else
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-shield-check me-1"></i>Admin
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <small class="text-muted">Member Since:</small>
                                        <div class="fw-bold">{{ $admin->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('admin.profile.show') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.profile.change-password') }}" class="btn btn-outline-warning">
                                <i class="bi bi-key me-1"></i>Change Password
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Update Profile
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-shield-exclamation text-warning me-2"></i>Important Security Information
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Profile Security:</h6>
                        <ul class="list-unstyled small">
                            <li><i class="bi bi-check text-success me-1"></i>Your email is used for login verification</li>
                            <li><i class="bi bi-check text-success me-1"></i>Profile changes are logged for security</li>
                            <li><i class="bi bi-check text-success me-1"></i>Only you can modify your profile</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Need Help?</h6>
                        <ul class="list-unstyled small">
                            <li><i class="bi bi-info-circle text-info me-1"></i>Contact system administrator for role changes</li>
                            <li><i class="bi bi-info-circle text-info me-1"></i>Use strong passwords for security</li>
                            <li><i class="bi bi-info-circle text-info me-1"></i>Keep your contact information updated</li>
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
// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    
    // Real-time validation
    function validateField(field, minLength = 2) {
        if (field.value.trim().length >= minLength) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            return true;
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            return false;
        }
    }
    
    nameInput.addEventListener('input', () => validateField(nameInput, 2));
    emailInput.addEventListener('input', () => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailRegex.test(emailInput.value)) {
            emailInput.classList.remove('is-invalid');
            emailInput.classList.add('is-valid');
        } else {
            emailInput.classList.remove('is-valid');
            emailInput.classList.add('is-invalid');
        }
    });
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        const isNameValid = validateField(nameInput, 2);
        const isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value);
        
        if (!isNameValid || !isEmailValid) {
            e.preventDefault();
            alert('Please fill in all required fields correctly.');
        }
    });
});
</script>
@endpush