@extends('layouts.admin')

@section('title', 'Healthcare Barrier Details')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Healthcare Barrier <code>{{ $healthcareBarrier->unique_id }}</code></h2>
            <p class="text-muted">Submitted on {{ $healthcareBarrier->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.healthcare-barriers.index') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <label>Form ID</label>
            <span><code>{{ $healthcareBarrier->unique_id }}</code></span>
        </div>
        <div class="detail-item">
            <label>Date</label>
            <span>{{ $healthcareBarrier->date ? $healthcareBarrier->date->format('M d, Y') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>UC</label>
            <span>{{ $healthcareBarrier->uc }}</span>
        </div>
        <div class="detail-item">
            <label>HFS (Health Facility Site)</label>
            <span>{{ $healthcareBarrier->hfs }}</span>
        </div>
        <div class="detail-item">
            <label>Address</label>
            <span>{{ $healthcareBarrier->address }}</span>
        </div>
        <div class="detail-item">
            <label>Group Type</label>
            <span class="badge badge-primary">{{ $healthcareBarrier->group_type }}</span>
        </div>
        <div class="detail-item">
            <label>Participants (Males)</label>
            <span>{{ $healthcareBarrier->participants_males }}</span>
        </div>
        <div class="detail-item">
            <label>Participants (Females)</label>
            <span>{{ $healthcareBarrier->participants_females }}</span>
        </div>
        <div class="detail-item">
            <label>TKF Facilitator</label>
            <span>{{ $healthcareBarrier->facilitator_tkf }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted By</label>
            <span>{{ $healthcareBarrier->user->name ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Latitude</label>
            <span>{{ $healthcareBarrier->latitude ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Longitude</label>
            <span>{{ $healthcareBarrier->longitude ?? 'N/A' }}</span>
        </div>
    </div>

    @if($healthcareBarrier->participants && $healthcareBarrier->participants->count() > 0)
        <div class="participants-section">
            <h3>Participants ({{ $healthcareBarrier->participants->count() }})</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Title/Designation</th>
                        <th>Contact</th>
                        <th>Gender</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($healthcareBarrier->participants as $participant)
                        <tr>
                            <td>{{ $participant->name }}</td>
                            <td>{{ $participant->title_designation ?? 'N/A' }}</td>
                            <td>{{ $participant->contact_no ?? 'N/A' }}</td>
                            <td>{{ $participant->gender ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <h3 style="margin: 24px 0 16px; font-size: 16px; font-weight: 600; color: #374151;">Submission Metadata</h3>
    <div class="detail-grid">
        <div class="detail-item">
            <label>IP Address</label>
            <span>{{ $healthcareBarrier->ip_address ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Started At</label>
            <span>{{ $healthcareBarrier->started_at ? $healthcareBarrier->started_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted At</label>
            <span>{{ $healthcareBarrier->submitted_at ? $healthcareBarrier->submitted_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Time to Complete</label>
            <span>
                @if($healthcareBarrier->started_at && $healthcareBarrier->submitted_at)
                    {{ $healthcareBarrier->started_at->diffForHumans($healthcareBarrier->submitted_at, true) }}
                @else
                    N/A
                @endif
            </span>
        </div>
        @if($healthcareBarrier->device_info)
        <div class="detail-item" style="grid-column: span 2;">
            <label>Device Info</label>
            <span style="font-family: monospace; font-size: 12px;">
                {{ $healthcareBarrier->device_info['platform'] ?? '' }} {{ $healthcareBarrier->device_info['os_version'] ?? '' }} |
                {{ $healthcareBarrier->device_info['device_brand'] ?? '' }} {{ $healthcareBarrier->device_info['device_model'] ?? '' }} |
                App v{{ $healthcareBarrier->device_info['app_version'] ?? '' }}
            </span>
        </div>
        @endif
    </div>
</div>

@endsection
