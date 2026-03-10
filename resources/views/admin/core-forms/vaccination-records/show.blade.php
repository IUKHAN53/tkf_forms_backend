@extends('layouts.admin')

@section('title', 'Vaccination Record Details')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Vaccination Record <code>{{ $vaccinationRecord->unique_id }}</code></h2>
            <p class="text-muted">Submitted on {{ $vaccinationRecord->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.vaccination-records.index') }}" class="btn btn-outline">
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
            <span><code>{{ $vaccinationRecord->unique_id }}</code></span>
        </div>
        <div class="detail-item">
            <label>Serial #</label>
            <span>{{ $vaccinationRecord->serial_number }}</span>
        </div>
        <div class="detail-item">
            <label>Child Name</label>
            <span>{{ $vaccinationRecord->child_name }}</span>
        </div>
        <div class="detail-item">
            <label>Father Name</label>
            <span>{{ $vaccinationRecord->father_name }}</span>
        </div>
        <div class="detail-item">
            <label>Age</label>
            <span>{{ $vaccinationRecord->age ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Address</label>
            <span>{{ $vaccinationRecord->address ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Contact Number</label>
            <span>{{ $vaccinationRecord->contact_number ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Category</label>
            @php
                $catClass = match($vaccinationRecord->category) {
                    'Refusal' => 'badge-danger',
                    'Zero Dose' => 'badge-warning',
                    default => 'badge-info',
                };
            @endphp
            <span class="badge {{ $catClass }}">{{ $vaccinationRecord->category }}</span>
        </div>
        <div class="detail-item">
            <label>Vaccinated</label>
            <span class="badge {{ $vaccinationRecord->vaccinated === 'YES' ? 'badge-success' : 'badge-danger' }}">
                {{ $vaccinationRecord->vaccinated }}
            </span>
        </div>
        <div class="detail-item">
            <label>Date of Vaccination</label>
            <span>{{ $vaccinationRecord->date_of_vaccination ? $vaccinationRecord->date_of_vaccination->format('M d, Y') : 'N/A' }}</span>
        </div>
    </div>

    <!-- Location Info -->
    <h3 style="margin: 24px 0 16px; font-size: 16px; font-weight: 600; color: #374151;">Location Information</h3>
    <div class="detail-grid">
        <div class="detail-item">
            <label>District</label>
            <span>{{ $vaccinationRecord->district ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>UC</label>
            <span>{{ $vaccinationRecord->uc ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Fix Site</label>
            <span>{{ $vaccinationRecord->fix_site ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>GPS Coordinates</label>
            <span>{{ $vaccinationRecord->gps_coordinates ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Latitude</label>
            <span>{{ $vaccinationRecord->latitude ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Longitude</label>
            <span>{{ $vaccinationRecord->longitude ?? 'N/A' }}</span>
        </div>
    </div>

    <!-- Community Member -->
    <h3 style="margin: 24px 0 16px; font-size: 16px; font-weight: 600; color: #374151;">Community Member</h3>
    <div class="detail-grid">
        <div class="detail-item">
            <label>Name</label>
            <span>{{ $vaccinationRecord->community_member_name ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Contact</label>
            <span>{{ $vaccinationRecord->community_member_contact ?? 'N/A' }}</span>
        </div>
    </div>

    <!-- Submission Metadata -->
    <h3 style="margin: 24px 0 16px; font-size: 16px; font-weight: 600; color: #374151;">Submission Metadata</h3>
    <div class="detail-grid">
        <div class="detail-item">
            <label>Submitted By</label>
            <span>{{ $vaccinationRecord->user->name ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>IP Address</label>
            <span>{{ $vaccinationRecord->ip_address ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Started At</label>
            <span>{{ $vaccinationRecord->started_at ? $vaccinationRecord->started_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted At</label>
            <span>{{ $vaccinationRecord->submitted_at ? $vaccinationRecord->submitted_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        @if($vaccinationRecord->device_info)
        <div class="detail-item" style="grid-column: span 2;">
            <label>Device Info</label>
            <span style="font-family: monospace; font-size: 12px;">
                {{ $vaccinationRecord->device_info['platform'] ?? '' }} {{ $vaccinationRecord->device_info['os_version'] ?? '' }} |
                {{ $vaccinationRecord->device_info['device_brand'] ?? '' }} {{ $vaccinationRecord->device_info['device_model'] ?? '' }} |
                App v{{ $vaccinationRecord->device_info['app_version'] ?? '' }}
            </span>
        </div>
        @endif
    </div>
</div>
@endsection
