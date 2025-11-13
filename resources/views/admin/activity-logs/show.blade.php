@extends('layouts.admin')

@section('title', 'Activity Log Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.activity-logs.index') }}">Activity Logs</a>
                    </li>
                    <li class="breadcrumb-item active">Log #{{ $activityLog->id }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800 mt-2">
                <i class="fas fa-eye me-2"></i>Activity Log Details
            </h1>
        </div>
        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Logs
        </a>
    </div>

    <div class="row">
        <!-- Basic Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-1"></i>Basic Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3"><strong>ID:</strong></div>
                        <div class="col-sm-9">#{{ $activityLog->id }}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3"><strong>Date & Time:</strong></div>
                        <div class="col-sm-9">
                            {{ $activityLog->created_at->format('F j, Y \a\t g:i:s A') }}
                            <small class="text-muted">({{ $activityLog->created_at->diffForHumans() }})</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3"><strong>Activity:</strong></div>
                        <div class="col-sm-9">
                            <span class="fw-bold">{{ $activityLog->activity }}</span>
                        </div>
                    </div>
                    @if($activityLog->description)
                        <hr>
                        <div class="row">
                            <div class="col-sm-3"><strong>Description:</strong></div>
                            <div class="col-sm-9">{{ $activityLog->description }}</div>
                        </div>
                    @endif
                    <hr>
                    <div class="row">
                        <div class="col-sm-3"><strong>Log Type:</strong></div>
                        <div class="col-sm-9">
                            <span class="badge bg-info">{{ ucfirst($activityLog->log_type) }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3"><strong>Status:</strong></div>
                        <div class="col-sm-9">
                            <span class="badge bg-{{ $activityLog->status === 'success' ? 'success' : ($activityLog->status === 'failed' ? 'danger' : 'warning') }}">
                                {{ ucfirst($activityLog->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Request Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-globe me-1"></i>Request Information
                    </h6>
                </div>
                <div class="card-body">
                    @if($activityLog->method)
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Method:</strong></div>
                            <div class="col-sm-9">
                                <span class="badge badge-light">{{ $activityLog->method }}</span>
                            </div>
                        </div>
                    @endif
                    
                    @if($activityLog->url)
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>URL:</strong></div>
                            <div class="col-sm-9">
                                <code class="small">{{ $activityLog->url }}</code>
                            </div>
                        </div>
                    @endif

                    @if($activityLog->user_agent)
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>User Agent:</strong></div>
                            <div class="col-sm-9">
                                <small class="text-muted">{{ $activityLog->user_agent }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Additional Properties -->
            @if($activityLog->properties)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cog me-1"></i>Additional Properties
                        </h6>
                    </div>
                    <div class="card-body">
                        <pre class="bg-light p-3 rounded"><code>{{ $activityLog->formatted_properties }}</code></pre>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Information -->
        <div class="col-lg-4">
            <!-- User Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-1"></i>User Information
                    </h6>
                </div>
                <div class="card-body">
                    @if($activityLog->user_type)
                        <div class="mb-3">
                            <strong>User Type:</strong><br>
                            <span class="badge bg-{{ $activityLog->user_type === 'admin' ? 'danger' : 'primary' }}">
                                {{ ucfirst($activityLog->user_type) }}
                            </span>
                        </div>
                    @endif

                    @if($activityLog->user_email)
                        <div class="mb-3">
                            <strong>Email:</strong><br>
                            {{ $activityLog->user_email }}
                        </div>
                    @endif

                    @if($activityLog->user)
                        <div class="mb-3">
                            <strong>User Details:</strong><br>
                            <a href="#" class="text-decoration-none">
                                {{ $activityLog->user->name ?? $activityLog->user->email }}
                            </a>
                        </div>
                    @endif

                    @if(!$activityLog->user_email && !$activityLog->user)
                        <p class="text-muted">Guest User</p>
                    @endif
                </div>
            </div>

            <!-- Location Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-map-marker-alt me-1"></i>Location Information
                    </h6>
                </div>
                <div class="card-body">
                    @if($activityLog->ip_address)
                        <div class="mb-3">
                            <strong>IP Address:</strong><br>
                            <code>{{ $activityLog->ip_address }}</code>
                        </div>
                    @endif

                    @if($activityLog->country || $activityLog->region || $activityLog->city)
                        <div class="mb-3">
                            <strong>Location:</strong><br>
                            {{ $activityLog->location }}
                        </div>
                    @endif

                    @if($activityLog->latitude && $activityLog->longitude)
                        <div class="mb-3">
                            <strong>Coordinates:</strong><br>
                            <small>
                                Lat: {{ number_format($activityLog->latitude, 4) }}<br>
                                Lng: {{ number_format($activityLog->longitude, 4) }}
                            </small>
                        </div>
                    @endif

                    @if($activityLog->timezone)
                        <div class="mb-3">
                            <strong>Timezone:</strong><br>
                            {{ $activityLog->timezone }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Device Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-desktop me-1"></i>Device Information
                    </h6>
                </div>
                <div class="card-body">
                    @if($activityLog->browser)
                        <div class="mb-3">
                            <strong>Browser:</strong><br>
                            {{ $activityLog->browser }}
                        </div>
                    @endif

                    @if($activityLog->platform)
                        <div class="mb-3">
                            <strong>Platform:</strong><br>
                            {{ $activityLog->platform }}
                        </div>
                    @endif

                    @if($activityLog->device)
                        <div class="mb-3">
                            <strong>Device Type:</strong><br>
                            <span class="badge bg-secondary">{{ $activityLog->device }}</span>
                        </div>
                    @endif

                    @if($activityLog->browser_info)
                        <div class="mb-3">
                            <strong>Browser Info:</strong><br>
                            <small class="text-muted">{{ $activityLog->browser_info }}</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tools me-1"></i>Actions
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.activity-logs.destroy', $activityLog) }}" 
                          method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this activity log?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="fas fa-trash me-1"></i>Delete Log
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection