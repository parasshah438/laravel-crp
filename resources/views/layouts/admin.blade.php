<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Panel - @yield('title', 'Dashboard')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        .sidebar {
            min-height: 100vh;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
        }
        
        .sidebar .nav-link {
            color: #ecf0f1;
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar .nav-link.active {
            background-color: #3498db;
            color: white;
        }
        
        .sidebar .nav-link.disabled {
            opacity: 0.5;
            pointer-events: none;
        }
        
        .navbar-brand {
            font-weight: 600;
        }
        
        .main-content {
            min-height: calc(100vh - 56px);
        }
        
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .role-badge {
            font-size: 0.7rem;
        }
        
        .nav-section {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .nav-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .nav-section-title {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.5rem 0.75rem;
            margin-bottom: 0.5rem;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div id="app">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark admin-header">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-shield-lock me-2"></i>
                    Admin Panel
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        @auth('admin')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle me-1"></i>
                                    {{ Auth::guard('admin')->user()->name }}
                                    @if(Auth::guard('admin')->user()->isSuperAdmin())
                                        <span class="badge bg-warning text-dark ms-1 role-badge">
                                            <i class="bi bi-crown me-1"></i>Super Admin
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark ms-1 role-badge">Admin</span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.profile.show') }}">
                                        <i class="bi bi-person me-2"></i>My Profile</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
                                        <i class="bi bi-pencil me-2"></i>Edit Profile</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.profile.change-password') }}">
                                        <i class="bi bi-key me-2"></i>Change Password</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.profile.security') }}">
                                        <i class="bi bi-shield-lock me-2"></i>Security</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('admin.logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                @auth('admin')
                <!-- Sidebar with Role-Based Navigation -->
                <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                    <div class="position-sticky pt-3">
                        <!-- Main Navigation -->
                        <div class="nav-section">
                            <div class="nav-section-title">Dashboard</div>
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                                       href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>
                                        Overview
                                    </a>
                                </li>
                                @can('view-reports', Auth::guard('admin')->user())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" 
                                       href="{{ route('admin.reports.index') }}">
                                        <i class="bi bi-bar-chart me-2"></i>
                                        Reports
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </div>

                        <!-- User Management -->
                        @if(Auth::guard('admin')->user()->hasAnyPermission(['view-users', 'manage-users']))
                        <div class="nav-section">
                            <div class="nav-section-title">User Management</div>
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                                       href="{{ route('admin.users.index') }}">
                                        <i class="bi bi-people me-2"></i>
                                        Users
                                    </a>
                                </li>
                            </ul>
                        </div>
                        @endif

                        <!-- System Management (SuperAdmin Only) -->
                        @if(Auth::guard('admin')->user()->isSuperAdmin())
                        <div class="nav-section">
                            <div class="nav-section-title">
                                <i class="bi bi-crown me-1"></i>System Management
                            </div>
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}" 
                                       href="{{ route('admin.admins.index') }}">
                                        <i class="bi bi-shield-check me-2"></i>
                                        Administrators
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" 
                                       href="{{ route('admin.roles.index') }}">
                                        <i class="bi bi-diagram-3 me-2"></i>
                                        Roles & Permissions
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" 
                                       href="{{ route('admin.settings.index') }}">
                                        <i class="bi bi-gear me-2"></i>
                                        System Settings
                                    </a>
                                </li>
                            </ul>
                        </div>
                        @endif
                    </div>
                </nav>
                
                <!-- Main content -->
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                @else
                <main class="col-12">
                @endauth
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>