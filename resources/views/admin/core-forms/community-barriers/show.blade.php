@extends('layouts.admin')

@section('title', 'Community Explore Immunization Barriers Details')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Community Explore Immunization Barriers <code>{{ $communityBarrier->unique_id }}</code></h2>
            <p class="text-muted">Submitted on {{ $communityBarrier->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.community-barriers.index') }}" class="btn btn-outline">
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
            <span><code>{{ $communityBarrier->unique_id }}</code></span>
        </div>
        <div class="detail-item">
            <label>Date</label>
            <span>{{ $communityBarrier->date ? $communityBarrier->date->format('M d, Y') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>District</label>
            <span>{{ $communityBarrier->district }}</span>
        </div>
        <div class="detail-item">
            <label>UC</label>
            <span>{{ $communityBarrier->uc }}</span>
        </div>
        <div class="detail-item">
            <label>Fix Site</label>
            <span>{{ $communityBarrier->fix_site }}</span>
        </div>
        <div class="detail-item">
            <label>Outreach</label>
            <span>{{ $communityBarrier->outreach }}</span>
        </div>
        <div class="detail-item">
            <label>Venue</label>
            <span>{{ $communityBarrier->venue }}</span>
        </div>
        <div class="detail-item">
            <label>Community</label>
            <span>
                @if(is_array($communityBarrier->community))
                    @foreach($communityBarrier->community as $comm)
                        <span class="badge badge-info">{{ $comm }}</span>
                    @endforeach
                @else
                    {{ $communityBarrier->community }}
                @endif
            </span>
        </div>
        <div class="detail-item">
            <label>Group Type</label>
            <span>
                @if(is_array($communityBarrier->group_type))
                    @foreach($communityBarrier->group_type as $type)
                        <span class="badge badge-primary">{{ $type }}</span>
                    @endforeach
                @else
                    <span class="badge badge-primary">{{ $communityBarrier->group_type }}</span>
                @endif
            </span>
        </div>
        <div class="detail-item">
            <label>Participants (Males)</label>
            <span>{{ $communityBarrier->participants_males }}</span>
        </div>
        <div class="detail-item">
            <label>Participants (Females)</label>
            <span>{{ $communityBarrier->participants_females }}</span>
        </div>
        <div class="detail-item">
            <label>TKF Facilitator</label>
            <span>{{ $communityBarrier->facilitator_tkf }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted By</label>
            <span>{{ $communityBarrier->user->name ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Latitude</label>
            <span>{{ $communityBarrier->latitude ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Longitude</label>
            <span>{{ $communityBarrier->longitude ?? 'N/A' }}</span>
        </div>
    </div>

    @if($communityBarrier->participants && $communityBarrier->participants->count() > 0)
        <div class="participants-section">
            <h3>Participants ({{ $communityBarrier->participants->count() }})</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Occupation</th>
                        <th>Contact</th>
                        <th>Gender</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($communityBarrier->participants as $participant)
                        <tr>
                            <td>{{ $participant->name }}</td>
                            <td>{{ $participant->occupation ?? 'N/A' }}</td>
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
            <span>{{ $communityBarrier->ip_address ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Started At</label>
            <span>{{ $communityBarrier->started_at ? $communityBarrier->started_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted At</label>
            <span>{{ $communityBarrier->submitted_at ? $communityBarrier->submitted_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Time to Complete</label>
            <span>
                @if($communityBarrier->started_at && $communityBarrier->submitted_at)
                    {{ $communityBarrier->started_at->diffForHumans($communityBarrier->submitted_at, true) }}
                @else
                    N/A
                @endif
            </span>
        </div>
        @if($communityBarrier->device_info)
        <div class="detail-item" style="grid-column: span 2;">
            <label>Device Info</label>
            <span style="font-family: monospace; font-size: 12px;">
                {{ $communityBarrier->device_info['platform'] ?? '' }} {{ $communityBarrier->device_info['os_version'] ?? '' }} |
                {{ $communityBarrier->device_info['device_brand'] ?? '' }} {{ $communityBarrier->device_info['device_model'] ?? '' }} |
                App v{{ $communityBarrier->device_info['app_version'] ?? '' }}
            </span>
        </div>
        @endif
    </div>
</div>

@endsection
