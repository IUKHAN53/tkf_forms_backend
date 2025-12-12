@extends('layouts.admin')

@section('title', 'Draft List Details')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Draft List <code>{{ $draftList->unique_id }}</code></h2>
            <p class="text-muted">Submitted on {{ $draftList->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.draft-lists.index') }}" class="btn btn-outline">
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
            <span><code>{{ $draftList->unique_id }}</code></span>
        </div>
        <div class="detail-item">
            <label>Division</label>
            <span>{{ $draftList->division }}</span>
        </div>
        <div class="detail-item">
            <label>District</label>
            <span>{{ $draftList->district }}</span>
        </div>
        <div class="detail-item">
            <label>UC</label>
            <span>{{ $draftList->uc }}</span>
        </div>
        <div class="detail-item">
            <label>Outreach</label>
            <span>{{ $draftList->outreach }}</span>
        </div>
        <div class="detail-item">
            <label>Child Name</label>
            <span>{{ $draftList->child_name }}</span>
        </div>
        <div class="detail-item">
            <label>Father Name</label>
            <span>{{ $draftList->father_name }}</span>
        </div>
        <div class="detail-item">
            <label>Gender</label>
            <span>{{ ucfirst($draftList->gender) }}</span>
        </div>
        <div class="detail-item">
            <label>Date of Birth</label>
            <span>{{ $draftList->date_of_birth ? $draftList->date_of_birth->format('M d, Y') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Age (Months)</label>
            <span>{{ $draftList->age_in_months }}</span>
        </div>
        <div class="detail-item">
            <label>Type</label>
            <span class="badge badge-primary">{{ $draftList->type }}</span>
        </div>
        <div class="detail-item">
            <label>Guardian Phone</label>
            <span>{{ $draftList->guardian_phone ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Address</label>
            <span>{{ $draftList->address }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted By</label>
            <span>{{ $draftList->user->name ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Latitude</label>
            <span>{{ $draftList->latitude ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Longitude</label>
            <span>{{ $draftList->longitude ?? 'N/A' }}</span>
        </div>
    </div>

    @if($draftList->reasons_of_missing)
        <div class="participants-section">
            <h3>Reasons for Missing</h3>
            <p>{{ $draftList->reasons_of_missing }}</p>
        </div>
    @endif

    @if($draftList->plan_for_coverage)
        <div class="participants-section">
            <h3>Plan for Coverage</h3>
            <p>{{ $draftList->plan_for_coverage }}</p>
        </div>
    @endif

    <h3 style="margin: 24px 0 16px; font-size: 16px; font-weight: 600; color: #374151;">Submission Metadata</h3>
    <div class="detail-grid">
        <div class="detail-item">
            <label>IP Address</label>
            <span>{{ $draftList->ip_address ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Started At</label>
            <span>{{ $draftList->started_at ? $draftList->started_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted At</label>
            <span>{{ $draftList->submitted_at ? $draftList->submitted_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Time to Complete</label>
            <span>
                @if($draftList->started_at && $draftList->submitted_at)
                    {{ $draftList->started_at->diffForHumans($draftList->submitted_at, true) }}
                @else
                    N/A
                @endif
            </span>
        </div>
        @if($draftList->device_info)
        <div class="detail-item" style="grid-column: span 2;">
            <label>Device Info</label>
            <span style="font-family: monospace; font-size: 12px;">
                {{ $draftList->device_info['platform'] ?? '' }} {{ $draftList->device_info['os_version'] ?? '' }} |
                {{ $draftList->device_info['device_brand'] ?? '' }} {{ $draftList->device_info['device_model'] ?? '' }} |
                App v{{ $draftList->device_info['app_version'] ?? '' }}
            </span>
        </div>
        @endif
    </div>
</div>
@endsection
