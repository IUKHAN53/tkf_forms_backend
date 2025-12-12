@extends('layouts.admin')

@section('title', 'Area Mapping Details')
@section('page-title', 'Area Mapping Details')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <a href="{{ route('admin.area-mappings.index') }}" class="btn btn-outline btn-sm">← Back to List</a>
            <h2>Area Mapping <code>{{ $areaMapping->unique_id }}</code></h2>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <label>Form ID</label>
            <span><code>{{ $areaMapping->unique_id }}</code></span>
        </div>
        <div class="detail-item">
            <label>District</label>
            <span>{{ $areaMapping->district }}</span>
        </div>
        <div class="detail-item">
            <label>Town</label>
            <span>{{ $areaMapping->town }}</span>
        </div>
        <div class="detail-item">
            <label>UC Name</label>
            <span>{{ $areaMapping->uc_name }}</span>
        </div>
        <div class="detail-item">
            <label>Area Name</label>
            <span>{{ $areaMapping->area_name }}</span>
        </div>
        <div class="detail-item">
            <label>Assigned AIC</label>
            <span>{{ $areaMapping->assigned_aic }}</span>
        </div>
        <div class="detail-item">
            <label>Assigned CM</label>
            <span>{{ $areaMapping->assigned_cm }}</span>
        </div>
        <div class="detail-item">
            <label>Total Population</label>
            <span>{{ $areaMapping->total_population }}</span>
        </div>
        <div class="detail-item">
            <label>Total Under 2 Years</label>
            <span>{{ $areaMapping->total_under_2_years }}</span>
        </div>
        <div class="detail-item">
            <label>Zero Dose</label>
            <span>{{ $areaMapping->total_zero_dose }}</span>
        </div>
        <div class="detail-item">
            <label>Defaulter</label>
            <span>{{ $areaMapping->total_defaulter }}</span>
        </div>
        <div class="detail-item">
            <label>Refusal</label>
            <span>{{ $areaMapping->total_refusal }}</span>
        </div>
        <div class="detail-item">
            <label>Latitude</label>
            <span>{{ $areaMapping->latitude ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Longitude</label>
            <span>{{ $areaMapping->longitude ?? 'N/A' }}</span>
        </div>
    </div>

    <h3 style="margin: 24px 0 16px; font-size: 16px; font-weight: 600; color: #374151;">Submission Metadata</h3>
    <div class="detail-grid">
        <div class="detail-item">
            <label>IP Address</label>
            <span>{{ $areaMapping->ip_address ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Started At</label>
            <span>{{ $areaMapping->started_at ? $areaMapping->started_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted At</label>
            <span>{{ $areaMapping->submitted_at ? $areaMapping->submitted_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Time to Complete</label>
            <span>
                @if($areaMapping->started_at && $areaMapping->submitted_at)
                    {{ $areaMapping->started_at->diffForHumans($areaMapping->submitted_at, true) }}
                @else
                    N/A
                @endif
            </span>
        </div>
        @if($areaMapping->device_info)
        <div class="detail-item" style="grid-column: span 2;">
            <label>Device Info</label>
            <span style="font-family: monospace; font-size: 12px;">
                {{ $areaMapping->device_info['platform'] ?? '' }} {{ $areaMapping->device_info['os_version'] ?? '' }} |
                {{ $areaMapping->device_info['device_brand'] ?? '' }} {{ $areaMapping->device_info['device_model'] ?? '' }} |
                App v{{ $areaMapping->device_info['app_version'] ?? '' }}
            </span>
        </div>
        @endif
        <div class="detail-item">
            <label>Created At</label>
            <span>{{ $areaMapping->created_at->format('M d, Y h:i A') }}</span>
        </div>
        <div class="detail-item">
            <label>Updated At</label>
            <span>{{ $areaMapping->updated_at->format('M d, Y h:i A') }}</span>
        </div>
    </div>
</div>

@include('admin.core-forms.partials.styles')
@endsection
