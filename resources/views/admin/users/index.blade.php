@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-people me-2 text-primary"></i>User Management
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-person-plus me-1"></i>Add New User
            </a>
        </div>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Users</h6>
                        <h3 class="mb-0">{{ $users->total() }}</h3>
                    </div>
                    <i class="bi bi-people" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Active Users</h6>
                        <h3 class="mb-0">{{ $users->where('status', true)->count() }}</h3>
                    </div>
                    <i class="bi bi-person-check" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Inactive Users</h6>
                        <h3 class="mb-0">{{ $users->where('status', false)->count() }}</h3>
                    </div>
                    <i class="bi bi-person-x" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">This Month</h6>
                        <h3 class="mb-0">{{ $users->where('created_at', '>=', now()->startOfMonth())->count() }}</h3>
                    </div>
                    <i class="bi bi-calendar-plus" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-light border-0">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list me-2"></i>All Users
                </h5>
            </div>
            <div class="col-auto">
                <div class="input-group input-group-sm">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Search users..." id="searchInput">
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark">#{{ $user->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-person text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $user->name }}</h6>
                                            <small class="text-muted">User ID: {{ $user->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $user->email }}</span>
                                    @if($user->email_verified_at)
                                        <i class="bi bi-patch-check-fill text-success ms-1" title="Verified"></i>
                                    @else
                                        <i class="bi bi-exclamation-triangle text-warning ms-1" title="Unverified"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($user->role)
                                        <span class="badge bg-info">{{ $user->role->name }}</span>
                                    @else
                                        <span class="badge bg-secondary">No Role</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->status)
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
                                    <small class="text-muted">
                                        {{ $user->created_at->format('M d, Y') }}
                                        <br>
                                        <span class="text-muted">{{ $user->created_at->diffForHumans() }}</span>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.users.show', $user) }}" 
                                           class="btn btn-sm btn-outline-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if(Auth::guard('admin')->user()->id !== $user->id)
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    title="Delete" onclick="confirmDelete({{ $user->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    @if(Auth::guard('admin')->user()->id !== $user->id)
                                        <form id="delete-form-{{ $user->id }}" 
                                              action="{{ route('admin.users.destroy', $user) }}" 
                                              method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($users->hasPages())
                <div class="card-footer bg-light border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                        </small>
                        {{ $users->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                <h5 class="mt-3 text-muted">No Users Found</h5>
                <p class="text-muted">There are no users to display.</p>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus me-1"></i>Add First User
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        document.getElementById('delete-form-' + userId).submit();
    }
}

// Simple search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    var input = this.value.toLowerCase();
    var rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(function(row) {
        var name = row.cells[1].textContent.toLowerCase();
        var email = row.cells[2].textContent.toLowerCase();
        
        if (name.includes(input) || email.includes(input)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
@endpush