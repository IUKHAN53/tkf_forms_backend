@extends('layouts.admin')

@section('title', 'FGDs-Community Details')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>FGDs-Community <code>{{ $fgdsCommunity->unique_id }}</code></h2>
            <p class="text-muted">Submitted on {{ $fgdsCommunity->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.fgds-community.index') }}" class="btn btn-outline">
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
            <span><code>{{ $fgdsCommunity->unique_id }}</code></span>
        </div>
        <div class="detail-item">
            <label>Date</label>
            <span>{{ $fgdsCommunity->date ? $fgdsCommunity->date->format('M d, Y') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>District</label>
            <span>{{ $fgdsCommunity->district }}</span>
        </div>
        <div class="detail-item">
            <label>UC</label>
            <span>{{ $fgdsCommunity->uc }}</span>
        </div>
        <div class="detail-item">
            <label>Fix Site</label>
            <span>{{ $fgdsCommunity->fix_site }}</span>
        </div>
        <div class="detail-item">
            <label>Outreach</label>
            <span>{{ $fgdsCommunity->outreach }}</span>
        </div>
        <div class="detail-item">
            <label>Venue</label>
            <span>{{ $fgdsCommunity->venue }}</span>
        </div>
        <div class="detail-item">
            <label>Community</label>
            <span>
                @if(is_array($fgdsCommunity->community))
                    @foreach($fgdsCommunity->community as $comm)
                        <span class="badge badge-info">{{ $comm }}</span>
                    @endforeach
                @else
                    {{ $fgdsCommunity->community }}
                @endif
            </span>
        </div>
        <div class="detail-item">
            <label>Participants (Males)</label>
            <span>{{ $fgdsCommunity->participants_males }}</span>
        </div>
        <div class="detail-item">
            <label>Participants (Females)</label>
            <span>{{ $fgdsCommunity->participants_females }}</span>
        </div>
        <div class="detail-item">
            <label>TKF Facilitator</label>
            <span>{{ $fgdsCommunity->facilitator_tkf }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted By</label>
            <span>{{ $fgdsCommunity->user->name ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Latitude</label>
            <span>{{ $fgdsCommunity->latitude ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Longitude</label>
            <span>{{ $fgdsCommunity->longitude ?? 'N/A' }}</span>
        </div>
    </div>

    @if($fgdsCommunity->participants && $fgdsCommunity->participants->count() > 0)
        <div class="participants-section">
            <h3>Participants ({{ $fgdsCommunity->participants->count() }})</h3>
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
                    @foreach($fgdsCommunity->participants as $participant)
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
            <span>{{ $fgdsCommunity->ip_address ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Started At</label>
            <span>{{ $fgdsCommunity->started_at ? $fgdsCommunity->started_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted At</label>
            <span>{{ $fgdsCommunity->submitted_at ? $fgdsCommunity->submitted_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Time to Complete</label>
            <span>
                @if($fgdsCommunity->started_at && $fgdsCommunity->submitted_at)
                    {{ $fgdsCommunity->started_at->diffForHumans($fgdsCommunity->submitted_at, true) }}
                @else
                    N/A
                @endif
            </span>
        </div>
        @if($fgdsCommunity->device_info)
        <div class="detail-item" style="grid-column: span 2;">
            <label>Device Info</label>
            <span style="font-family: monospace; font-size: 12px;">
                {{ $fgdsCommunity->device_info['platform'] ?? '' }} {{ $fgdsCommunity->device_info['os_version'] ?? '' }} |
                {{ $fgdsCommunity->device_info['device_brand'] ?? '' }} {{ $fgdsCommunity->device_info['device_model'] ?? '' }} |
                App v{{ $fgdsCommunity->device_info['app_version'] ?? '' }}
            </span>
        </div>
        @endif
    </div>
</div>

@endsection
