@extends('layouts.admin')

@section('title', 'FGDs-Health Workers Details')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>FGDs-Health Workers <code>{{ $fgdsHealthWorker->unique_id }}</code></h2>
            <p class="text-muted">Submitted on {{ $fgdsHealthWorker->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.fgds-health-workers.index') }}" class="btn btn-outline">
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
            <span><code>{{ $fgdsHealthWorker->unique_id }}</code></span>
        </div>
        <div class="detail-item">
            <label>Date</label>
            <span>{{ $fgdsHealthWorker->date ? $fgdsHealthWorker->date->format('M d, Y') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>UC</label>
            <span>{{ $fgdsHealthWorker->uc }}</span>
        </div>
        <div class="detail-item">
            <label>HFS (Health Facility Site)</label>
            <span>{{ $fgdsHealthWorker->hfs }}</span>
        </div>
        <div class="detail-item">
            <label>Address</label>
            <span>{{ $fgdsHealthWorker->address }}</span>
        </div>
        <div class="detail-item">
            <label>Group Type</label>
            <span class="badge badge-primary">{{ $fgdsHealthWorker->group_type }}</span>
        </div>
        <div class="detail-item">
            <label>Participants (Males)</label>
            <span>{{ $fgdsHealthWorker->participants_males }}</span>
        </div>
        <div class="detail-item">
            <label>Participants (Females)</label>
            <span>{{ $fgdsHealthWorker->participants_females }}</span>
        </div>
        <div class="detail-item">
            <label>TKF Facilitator</label>
            <span>{{ $fgdsHealthWorker->facilitator_tkf }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted By</label>
            <span>{{ $fgdsHealthWorker->user->name ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Latitude</label>
            <span>{{ $fgdsHealthWorker->latitude ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Longitude</label>
            <span>{{ $fgdsHealthWorker->longitude ?? 'N/A' }}</span>
        </div>
    </div>

    @if($fgdsHealthWorker->participants && $fgdsHealthWorker->participants->count() > 0)
        <div class="participants-section">
            <h3>Participants ({{ $fgdsHealthWorker->participants->count() }})</h3>
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
                    @foreach($fgdsHealthWorker->participants as $participant)
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

    @if($fgdsHealthWorker->barriers && $fgdsHealthWorker->barriers->count() > 0)
        <div class="barriers-section" style="margin-top: 24px;">
            <h3 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px;">
                Identified Barriers ({{ $fgdsHealthWorker->barriers->count() }})
            </h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Sr. No</th>
                        <th>Barrier</th>
                        <th style="width: 280px;">Category</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fgdsHealthWorker->barriers->sortBy('serial_number') as $barrier)
                        <tr>
                            <td>{{ $barrier->serial_number ?? '-' }}</td>
                            <td>{{ $barrier->barrier_text }}</td>
                            <td>
                                <span class="badge badge-warning" style="font-size: 11px;">
                                    {{ $barrier->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>
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
            <span>{{ $fgdsHealthWorker->ip_address ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Started At</label>
            <span>{{ $fgdsHealthWorker->started_at ? $fgdsHealthWorker->started_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted At</label>
            <span>{{ $fgdsHealthWorker->submitted_at ? $fgdsHealthWorker->submitted_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Time to Complete</label>
            <span>
                @if($fgdsHealthWorker->started_at && $fgdsHealthWorker->submitted_at)
                    {{ $fgdsHealthWorker->started_at->diffForHumans($fgdsHealthWorker->submitted_at, true) }}
                @else
                    N/A
                @endif
            </span>
        </div>
        @if($fgdsHealthWorker->device_info)
        <div class="detail-item" style="grid-column: span 2;">
            <label>Device Info</label>
            <span style="font-family: monospace; font-size: 12px;">
                {{ $fgdsHealthWorker->device_info['platform'] ?? '' }} {{ $fgdsHealthWorker->device_info['os_version'] ?? '' }} |
                {{ $fgdsHealthWorker->device_info['device_brand'] ?? '' }} {{ $fgdsHealthWorker->device_info['device_model'] ?? '' }} |
                App v{{ $fgdsHealthWorker->device_info['app_version'] ?? '' }}
            </span>
        </div>
        @endif
    </div>
</div>

@endsection
