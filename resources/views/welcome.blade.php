@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="container-fluid p-0">
    <div class="hero-section bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Welcome to Our Platform</h1>
                    <p class="lead mb-4">Experience the power of role-based authentication with our advanced Laravel system featuring multiple guards and comprehensive permission management.</p>
                    <div class="d-flex gap-3">
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                                <i class="bi bi-person-plus me-2"></i>Get Started
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        @endguest
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="bi bi-shield-check display-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container py-5">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold">Platform Features</h2>
                <p class="text-muted">Comprehensive role-based access control system</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-people-fill text-primary display-4 mb-3"></i>
                        <h5 class="card-title">Multi-Guard Authentication</h5>
                        <p class="card-text text-muted">Separate authentication for users and administrators with distinct login systems.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-diagram-3-fill text-success display-4 mb-3"></i>
                        <h5 class="card-title">Role Management</h5>
                        <p class="card-text text-muted">Dynamic role assignment with admin and superadmin privileges for comprehensive access control.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-key-fill text-warning display-4 mb-3"></i>
                        <h5 class="card-title">Permission System</h5>
                        <p class="card-text text-muted">Granular permission control for fine-tuned access to specific features and resources.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Access Section -->
    <div class="bg-light py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h3 class="fw-bold mb-4">User Access</h3>
                    <p class="mb-4">Access the user panel with role-based dashboard and management capabilities.</p>
                    <a href="{{ route('login') }}" class="btn btn-dark btn-lg">
                        <i class="bi bi-shield-lock me-2"></i>User Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection