@extends('layouts.admin')

@section('title', 'Child Line List Details')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Child Line List <code>{{ $childLineList->unique_id }}</code></h2>
            <p class="text-muted">Submitted on {{ $childLineList->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.child-line-list.index') }}" class="btn btn-outline">
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
            <span><code>{{ $childLineList->unique_id }}</code></span>
        </div>
        <div class="detail-item">
            <label>Division</label>
            <span>{{ $childLineList->division }}</span>
        </div>
        <div class="detail-item">
            <label>District</label>
            <span>{{ $childLineList->district }}</span>
        </div>
        <div class="detail-item">
            <label>UC</label>
            <span>{{ $childLineList->uc }}</span>
        </div>
        <div class="detail-item">
            <label>Outreach</label>
            <span>{{ $childLineList->outreach }}</span>
        </div>
        <div class="detail-item">
            <label>Child Name</label>
            <span>{{ $childLineList->child_name }}</span>
        </div>
        <div class="detail-item">
            <label>Father Name</label>
            <span>{{ $childLineList->father_name }}</span>
        </div>
        <div class="detail-item">
            <label>Gender</label>
            <span>{{ ucfirst($childLineList->gender) }}</span>
        </div>
        <div class="detail-item">
            <label>Date of Birth</label>
            <span>{{ $childLineList->date_of_birth ? $childLineList->date_of_birth->format('M d, Y') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Age (Months)</label>
            <span>{{ $childLineList->age_in_months }}</span>
        </div>
        <div class="detail-item">
            <label>Type</label>
            <span class="badge {{ $childLineList->type === 'Zero Dose' ? 'badge-danger' : 'badge-warning' }}">{{ $childLineList->type }}</span>
        </div>
        <div class="detail-item">
            <label>Guardian Phone</label>
            <span>{{ $childLineList->guardian_phone ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Address</label>
            <span>{{ $childLineList->address }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted By</label>
            <span>{{ $childLineList->user->name ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Latitude</label>
            <span>{{ $childLineList->latitude ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Longitude</label>
            <span>{{ $childLineList->longitude ?? 'N/A' }}</span>
        </div>
    </div>

    @if($childLineList->missed_vaccines && count($childLineList->missed_vaccines) > 0)
        <div class="participants-section">
            <h3>Missed Vaccines</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px;">
                @foreach($childLineList->missed_vaccines as $vaccine)
                    <span class="badge badge-danger">{{ $vaccine }}</span>
                @endforeach
            </div>
        </div>
    @endif

    @if($childLineList->reasons_of_missing)
        <div class="participants-section">
            <h3>Reasons for Missing</h3>
            <p>{{ $childLineList->reasons_of_missing }}</p>
        </div>
    @endif

    @if($childLineList->plan_for_coverage)
        <div class="participants-section">
            <h3>Plan for Coverage</h3>
            <p>{{ $childLineList->plan_for_coverage }}</p>
        </div>
    @endif

    <h3 style="margin: 24px 0 16px; font-size: 16px; font-weight: 600; color: #374151;">Submission Metadata</h3>
    <div class="detail-grid">
        <div class="detail-item">
            <label>IP Address</label>
            <span>{{ $childLineList->ip_address ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Started At</label>
            <span>{{ $childLineList->started_at ? $childLineList->started_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted At</label>
            <span>{{ $childLineList->submitted_at ? $childLineList->submitted_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Time to Complete</label>
            <span>
                @if($childLineList->started_at && $childLineList->submitted_at)
                    {{ $childLineList->started_at->diffForHumans($childLineList->submitted_at, true) }}
                @else
                    N/A
                @endif
            </span>
        </div>
        @if($childLineList->device_info)
        <div class="detail-item" style="grid-column: span 2;">
            <label>Device Info</label>
            <span style="font-family: monospace; font-size: 12px;">
                {{ $childLineList->device_info['platform'] ?? '' }} {{ $childLineList->device_info['os_version'] ?? '' }} |
                {{ $childLineList->device_info['device_brand'] ?? '' }} {{ $childLineList->device_info['device_model'] ?? '' }} |
                App v{{ $childLineList->device_info['app_version'] ?? '' }}
            </span>
        </div>
        @endif
    </div>
</div>
@endsection
