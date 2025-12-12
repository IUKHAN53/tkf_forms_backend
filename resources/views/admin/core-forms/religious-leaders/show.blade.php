@extends('layouts.admin')

@section('title', 'Religious Leader Details')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Religious Leader <code>{{ $religiousLeader->unique_id }}</code></h2>
            <p class="text-muted">Submitted on {{ $religiousLeader->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.religious-leaders.index') }}" class="btn btn-outline">
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
            <span><code>{{ $religiousLeader->unique_id }}</code></span>
        </div>
        <div class="detail-item">
            <label>Date</label>
            <span>{{ $religiousLeader->date ? $religiousLeader->date->format('M d, Y') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>District</label>
            <span>{{ $religiousLeader->district }}</span>
        </div>
        <div class="detail-item">
            <label>UC</label>
            <span>{{ $religiousLeader->uc }}</span>
        </div>
        <div class="detail-item">
            <label>Outreach</label>
            <span>{{ $religiousLeader->outreach }}</span>
        </div>
        <div class="detail-item">
            <label>Attached HF</label>
            <span>{{ $religiousLeader->attached_hf }}</span>
        </div>
        <div class="detail-item">
            <label>Group Type</label>
            <span class="badge badge-primary">{{ $religiousLeader->group_type }}</span>
        </div>
        <div class="detail-item">
            <label>TKF Facilitator</label>
            <span>{{ $religiousLeader->facilitator_tkf }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted By</label>
            <span>{{ $religiousLeader->user->name ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Latitude</label>
            <span>{{ $religiousLeader->latitude ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Longitude</label>
            <span>{{ $religiousLeader->longitude ?? 'N/A' }}</span>
        </div>
    </div>

    @if($religiousLeader->participants && $religiousLeader->participants->count() > 0)
        <div class="participants-section">
            <h3>Participants ({{ $religiousLeader->participants->count() }})</h3>
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
                    @foreach($religiousLeader->participants as $participant)
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
            <span>{{ $religiousLeader->ip_address ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Started At</label>
            <span>{{ $religiousLeader->started_at ? $religiousLeader->started_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted At</label>
            <span>{{ $religiousLeader->submitted_at ? $religiousLeader->submitted_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Time to Complete</label>
            <span>
                @if($religiousLeader->started_at && $religiousLeader->submitted_at)
                    {{ $religiousLeader->started_at->diffForHumans($religiousLeader->submitted_at, true) }}
                @else
                    N/A
                @endif
            </span>
        </div>
        @if($religiousLeader->device_info)
        <div class="detail-item" style="grid-column: span 2;">
            <label>Device Info</label>
            <span style="font-family: monospace; font-size: 12px;">
                {{ $religiousLeader->device_info['platform'] ?? '' }} {{ $religiousLeader->device_info['os_version'] ?? '' }} |
                {{ $religiousLeader->device_info['device_brand'] ?? '' }} {{ $religiousLeader->device_info['device_model'] ?? '' }} |
                App v{{ $religiousLeader->device_info['app_version'] ?? '' }}
            </span>
        </div>
        @endif
    </div>
</div>
@endsection
