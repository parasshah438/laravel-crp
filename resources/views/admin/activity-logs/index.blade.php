@extends('layouts.admin')

@section('title', 'Activity Logs')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-clipboard-list me-2"></i>Activity Logs
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.activity-logs.stats') }}" class="btn btn-info">
                <i class="fas fa-chart-bar me-1"></i>Statistics
            </a>
            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-1"></i>Export
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="#" onclick="exportData('csv')">
                            <i class="fas fa-file-csv me-1"></i>Export as CSV
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" onclick="exportData('json')">
                            <i class="fas fa-file-code me-1"></i>Export as JSON
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter me-1"></i>Filters
                </h6>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="collapse show" id="filterCollapse">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.activity-logs.index') }}" id="filterForm">
                    <div class="row g-3">
                        <!-- Search -->
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Search activities, emails, IPs...">
                            </div>
                        </div>

                        <!-- User Type -->
                        <div class="col-md-2">
                            <label for="user_type" class="form-label">User Type</label>
                            <select class="form-select" id="user_type" name="user_type">
                                <option value="">All Types</option>
                                @foreach($userTypes as $type)
                                    <option value="{{ $type }}" {{ request('user_type') == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Log Type -->
                        <div class="col-md-2">
                            <label for="log_type" class="form-label">Log Type</label>
                            <select class="form-select" id="log_type" name="log_type">
                                <option value="">All Types</option>
                                @foreach($logTypes as $type)
                                    <option value="{{ $type }}" {{ request('log_type') == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date From -->
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="date_from" 
                                   name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>

                        <!-- Date To -->
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="date_to" 
                                   name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>

                        <!-- IP Address -->
                        <div class="col-md-3">
                            <label for="ip_address" class="form-label">IP Address</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="ip_address" 
                                   name="ip_address" 
                                   value="{{ request('ip_address') }}"
                                   placeholder="Filter by IP address">
                        </div>

                        <!-- Per Page -->
                        <div class="col-md-2">
                            <label for="per_page" class="form-label">Per Page</label>
                            <select class="form-select" id="per_page" name="per_page">
                                <option value="25" {{ request('per_page', 50) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page', 50) == 100 ? 'selected' : '' }}>100</option>
                                <option value="200" {{ request('per_page', 50) == 200 ? 'selected' : '' }}>200</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="col-md-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                                <button type="button" class="btn btn-danger" id="bulkDeleteBtn" style="display: none;">
                                    <i class="fas fa-trash me-1"></i>Delete Selected
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Results Card -->
    <div class="card shadow">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    Activity Logs ({{ $logs->total() }} results)
                </h6>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label" for="selectAll">
                        Select All
                    </label>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="selectAllTable" class="form-check-input">
                                </th>
                                <th>Date/Time</th>
                                <th>User</th>
                                <th>Activity</th>
                                <th>Details</th>
                                <th>IP/Location</th>
                                <th>Status</th>
                                <th width="80">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input log-checkbox" value="{{ $log->id }}">
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $log->created_at->format('M j, Y') }}</div>
                                        <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        @if($log->user_type)
                                            <span class="badge bg-{{ $log->user_type === 'admin' ? 'danger' : 'primary' }} mb-1">
                                                {{ ucfirst($log->user_type) }}
                                            </span>
                                        @endif
                                        <div class="small">{{ $log->user_email ?: 'Guest' }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $log->activity }}</div>
                                        <span class="badge bg-secondary">{{ $log->log_type }}</span>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $log->description }}">
                                            {{ $log->description }}
                                        </div>
                                        @if($log->method && $log->url)
                                            <small class="text-muted d-block">
                                                <span class="badge badge-light">{{ $log->method }}</span>
                                                {{ Str::limit($log->url, 50) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold small">{{ $log->ip_address }}</div>
                                        <small class="text-muted">{{ $log->location }}</small>
                                        @if($log->browser_info)
                                            <div class="small text-muted">{{ Str::limit($log->browser_info, 30) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $log->status === 'success' ? 'success' : ($log->status === 'failed' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.activity-logs.show', $log) }}">
                                                        <i class="fas fa-eye me-1"></i>View Details
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.activity-logs.destroy', $log) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this log?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash me-1"></i>Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} results
                        </div>
                        {{ $logs->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No activity logs found</h5>
                    <p class="text-muted">Try adjusting your search criteria.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Scripts -->
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const selectAllTable = document.getElementById('selectAllTable');
    const checkboxes = document.querySelectorAll('.log-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

    // Select all functionality
    function updateSelectAll() {
        const checkedBoxes = document.querySelectorAll('.log-checkbox:checked');
        selectAll.checked = checkedBoxes.length > 0;
        selectAllTable.checked = checkedBoxes.length === checkboxes.length;
        bulkDeleteBtn.style.display = checkedBoxes.length > 0 ? 'block' : 'none';
    }

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateSelectAll();
    });

    selectAllTable.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateSelectAll();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectAll);
    });

    // Bulk delete
    bulkDeleteBtn.addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.log-checkbox:checked'))
            .map(cb => cb.value);

        if (selectedIds.length === 0) {
            alert('Please select at least one log to delete.');
            return;
        }

        if (!confirm(`Are you sure you want to delete ${selectedIds.length} selected log(s)?`)) {
            return;
        }

        fetch('{{ route("admin.activity-logs.bulk-delete") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ids: selectedIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting logs: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting logs.');
        });
    });
});

function exportData(format) {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    formData.append('format', format);
    
    const params = new URLSearchParams(formData);
    window.open('{{ route("admin.activity-logs.export") }}?' + params.toString(), '_blank');
}
</script>
@endpush
@endsection