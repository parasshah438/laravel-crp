@extends('layouts.admin')

@section('title', 'Change Password')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-key me-2 text-warning"></i>Change Password
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
    <div class="col-lg-7">
        <!-- Security Alert -->
        <div class="alert alert-warning border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex">
                <i class="bi bi-shield-exclamation me-3" style="font-size: 1.5rem;"></i>
                <div>
                    <h5 class="alert-heading mb-1">Security Notice</h5>
                    <p class="mb-0">
                        After changing your password, you will be logged out from all other devices for security purposes.
                    </p>
                </div>
            </div>
        </div>

        <!-- Password Change Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lock me-2"></i>Update Your Password
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.profile.update-password') }}" id="passwordForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Current Password -->
                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-bold">
                            Current Password <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-shield-lock"></i>
                            </span>
                            <input type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" name="current_password" 
                                   required placeholder="Enter your current password"
                                   autocomplete="current-password">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                <i class="bi bi-eye" id="current_password-icon"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Enter your current password to verify your identity</small>
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">
                            New Password <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" 
                                   required placeholder="Enter your new password"
                                   autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                <i class="bi bi-eye" id="password-icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                        
                        <!-- Password Strength Indicator -->
                        <div class="mt-2">
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" id="password-strength" 
                                     role="progressbar" style="width: 0%"></div>
                            </div>
                            <small id="password-strength-text" class="text-muted">Password strength will appear here</small>
                        </div>
                    </div>

                    <!-- Confirm New Password -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-bold">
                            Confirm New Password <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-check-circle"></i>
                            </span>
                            <input type="password" 
                                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" name="password_confirmation" 
                                   required placeholder="Confirm your new password"
                                   autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                <i class="bi bi-eye" id="password_confirmation-icon"></i>
                            </button>
                        </div>
                        <div id="password-match-feedback" class="invalid-feedback">Passwords do not match</div>
                        <small class="text-muted">Re-enter your new password to confirm</small>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.profile.show') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-warning text-dark" id="submitBtn" disabled>
                            <i class="bi bi-check-circle me-1"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Password Requirements -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-info-circle text-info me-2"></i>Password Requirements
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Security Requirements:</h6>
                        <ul class="list-unstyled small" id="password-requirements">
                            <li id="req-length"><i class="bi bi-x text-danger me-1"></i>At least 8 characters long</li>
                            <li id="req-uppercase"><i class="bi bi-x text-danger me-1"></i>Contains uppercase letters</li>
                            <li id="req-lowercase"><i class="bi bi-x text-danger me-1"></i>Contains lowercase letters</li>
                            <li id="req-numbers"><i class="bi bi-x text-danger me-1"></i>Contains numbers</li>
                            <li id="req-symbols"><i class="bi bi-x text-danger me-1"></i>Contains special characters</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Security Tips:</h6>
                        <ul class="list-unstyled small">
                            <li><i class="bi bi-lightbulb text-warning me-1"></i>Avoid using personal information</li>
                            <li><i class="bi bi-lightbulb text-warning me-1"></i>Don't reuse old passwords</li>
                            <li><i class="bi bi-lightbulb text-warning me-1"></i>Use a unique password for admin access</li>
                            <li><i class="bi bi-lightbulb text-warning me-1"></i>Consider using a password manager</li>
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
// Password visibility toggle
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

// Password strength checker
function checkPasswordStrength(password) {
    let score = 0;
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        numbers: /\d/.test(password),
        symbols: /[^A-Za-z0-9]/.test(password)
    };
    
    // Update requirement indicators
    Object.keys(requirements).forEach(req => {
        const element = document.getElementById(`req-${req}`);
        const icon = element.querySelector('i');
        if (requirements[req]) {
            icon.className = 'bi bi-check text-success me-1';
            score++;
        } else {
            icon.className = 'bi bi-x text-danger me-1';
        }
    });
    
    // Update strength bar
    const strengthBar = document.getElementById('password-strength');
    const strengthText = document.getElementById('password-strength-text');
    const percentage = (score / 5) * 100;
    
    strengthBar.style.width = percentage + '%';
    
    if (score === 0) {
        strengthBar.className = 'progress-bar';
        strengthText.textContent = 'Enter a password';
        strengthText.className = 'text-muted';
    } else if (score <= 2) {
        strengthBar.className = 'progress-bar bg-danger';
        strengthText.textContent = 'Weak password';
        strengthText.className = 'text-danger';
    } else if (score <= 3) {
        strengthBar.className = 'progress-bar bg-warning';
        strengthText.textContent = 'Fair password';
        strengthText.className = 'text-warning';
    } else if (score <= 4) {
        strengthBar.className = 'progress-bar bg-info';
        strengthText.textContent = 'Good password';
        strengthText.className = 'text-info';
    } else {
        strengthBar.className = 'progress-bar bg-success';
        strengthText.textContent = 'Strong password';
        strengthText.className = 'text-success';
    }
    
    return score >= 4; // Require at least 4/5 criteria
}

// Password confirmation checker
function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmation = document.getElementById('password_confirmation').value;
    const feedback = document.getElementById('password-match-feedback');
    const confirmField = document.getElementById('password_confirmation');
    
    if (confirmation === '') {
        confirmField.classList.remove('is-invalid', 'is-valid');
        return false;
    }
    
    if (password === confirmation) {
        confirmField.classList.remove('is-invalid');
        confirmField.classList.add('is-valid');
        return true;
    } else {
        confirmField.classList.remove('is-valid');
        confirmField.classList.add('is-invalid');
        return false;
    }
}

// Form validation
function updateSubmitButton() {
    const currentPassword = document.getElementById('current_password').value;
    const password = document.getElementById('password').value;
    const confirmation = document.getElementById('password_confirmation').value;
    const submitBtn = document.getElementById('submitBtn');
    
    const isCurrentPasswordValid = currentPassword.length >= 1;
    const isPasswordStrong = checkPasswordStrength(password);
    const isPasswordMatch = checkPasswordMatch();
    
    if (isCurrentPasswordValid && isPasswordStrong && isPasswordMatch) {
        submitBtn.disabled = false;
        submitBtn.className = 'btn btn-warning text-dark';
    } else {
        submitBtn.disabled = true;
        submitBtn.className = 'btn btn-warning text-dark opacity-50';
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const currentPassword = document.getElementById('current_password');
    const password = document.getElementById('password');
    const confirmation = document.getElementById('password_confirmation');
    
    currentPassword.addEventListener('input', updateSubmitButton);
    password.addEventListener('input', updateSubmitButton);
    confirmation.addEventListener('input', updateSubmitButton);
    
    // Form submission confirmation
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        if (!confirm('Are you sure you want to change your password? You will be logged out from all other devices.')) {
            e.preventDefault();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.progress-bar {
    transition: all 0.3s ease;
}

#password-requirements li {
    transition: all 0.3s ease;
}
</style>
@endpush