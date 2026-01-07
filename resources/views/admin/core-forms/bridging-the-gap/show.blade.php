@extends('layouts.admin')

@section('title', 'Bridging The Gap Details')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Bridging The Gap <code>{{ $bridgingTheGap->unique_id }}</code></h2>
            <p class="text-muted">Submitted on {{ $bridgingTheGap->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.bridging-the-gap.index') }}" class="btn btn-outline">
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
            <span><code>{{ $bridgingTheGap->unique_id }}</code></span>
        </div>
        <div class="detail-item">
            <label>Date</label>
            <span>{{ $bridgingTheGap->date ? $bridgingTheGap->date->format('M d, Y h:i A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Venue</label>
            <span>{{ $bridgingTheGap->venue }}</span>
        </div>
        <div class="detail-item">
            <label>District</label>
            <span>{{ $bridgingTheGap->district }}</span>
        </div>
        <div class="detail-item">
            <label>UC</label>
            <span>{{ $bridgingTheGap->uc }}</span>
        </div>
        <div class="detail-item">
            <label>Fix Site</label>
            <span>{{ $bridgingTheGap->fix_site }}</span>
        </div>
        <div class="detail-item">
            <label>Participants (Males)</label>
            <span>{{ $bridgingTheGap->participants_males }}</span>
        </div>
        <div class="detail-item">
            <label>Participants (Females)</label>
            <span>{{ $bridgingTheGap->participants_females }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted By</label>
            <span>{{ $bridgingTheGap->user->name ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Latitude</label>
            <span>{{ $bridgingTheGap->latitude ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Longitude</label>
            <span>{{ $bridgingTheGap->longitude ?? 'N/A' }}</span>
        </div>
    </div>

    @if($bridgingTheGap->participants && $bridgingTheGap->participants->count() > 0)
        <div class="participants-section">
            <h3>Attendance Participants ({{ $bridgingTheGap->participants->count() }})</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Sr. No</th>
                        <th>Name</th>
                        <th>Occupation</th>
                        <th>Contact</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bridgingTheGap->participants as $participant)
                        <tr>
                            <td>{{ $participant->sr_no }}</td>
                            <td>{{ $participant->name }}</td>
                            <td>{{ $participant->occupation ?? 'N/A' }}</td>
                            <td>{{ $participant->contact_no ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($bridgingTheGap->teamMembers && $bridgingTheGap->teamMembers->count() > 0)
        <div class="participants-section" style="margin-top: 24px;">
            <h3>IIT Team Members ({{ $bridgingTheGap->teamMembers->count() }})</h3>
            <p class="text-muted" style="margin-bottom: 12px;">Team members selected from Community Barriers and Healthcare Barriers forms</p>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Source</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bridgingTheGap->teamMembers as $index => $member)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $member->participant->name ?? 'N/A' }}</td>
                            <td>{{ $member->participant->contact_no ?? 'N/A' }}</td>
                            <td>
                                @if($member->source_type === 'community_barrier')
                                    <span class="badge badge-info">Community Barriers</span>
                                @else
                                    <span class="badge badge-success">Healthcare Barriers</span>
                                @endif
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
            <span>{{ $bridgingTheGap->ip_address ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Started At</label>
            <span>{{ $bridgingTheGap->started_at ? $bridgingTheGap->started_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted At</label>
            <span>{{ $bridgingTheGap->submitted_at ? $bridgingTheGap->submitted_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Time to Complete</label>
            <span>
                @if($bridgingTheGap->started_at && $bridgingTheGap->submitted_at)
                    {{ $bridgingTheGap->started_at->diffForHumans($bridgingTheGap->submitted_at, true) }}
                @else
                    N/A
                @endif
            </span>
        </div>
        @if($bridgingTheGap->device_info)
        <div class="detail-item" style="grid-column: span 2;">
            <label>Device Info</label>
            <span style="font-family: monospace; font-size: 12px;">
                {{ $bridgingTheGap->device_info['platform'] ?? '' }} {{ $bridgingTheGap->device_info['os_version'] ?? '' }} |
                {{ $bridgingTheGap->device_info['device_brand'] ?? '' }} {{ $bridgingTheGap->device_info['device_model'] ?? '' }} |
                App v{{ $bridgingTheGap->device_info['app_version'] ?? '' }}
            </span>
        </div>
        @endif
    </div>
</div>

@endsection
