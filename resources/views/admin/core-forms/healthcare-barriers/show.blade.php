@extends('layouts.admin')

@section('title', 'Healthcare Barrier Details')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Healthcare Barrier #{{ $healthcareBarrier->id }}</h2>
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
            <label>Team Number</label>
            <span>{{ $healthcareBarrier->team_no }}</span>
        </div>
        <div class="detail-item">
            <label>UC Name</label>
            <span>{{ $healthcareBarrier->uc_name }}</span>
        </div>
        <div class="detail-item">
            <label>Facility Name</label>
            <span>{{ $healthcareBarrier->facility_name }}</span>
        </div>
        <div class="detail-item">
            <label>Facility Type</label>
            <span>{{ ucfirst(str_replace('_', ' ', $healthcareBarrier->facility_type)) }}</span>
        </div>
        <div class="detail-item">
            <label>In-charge Name</label>
            <span>{{ $healthcareBarrier->incharge_name ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Phone Number</label>
            <span>{{ $healthcareBarrier->phone_number ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Barrier Type</label>
            <span>{{ ucfirst(str_replace('_', ' ', $healthcareBarrier->barrier_type)) }}</span>
        </div>
        <div class="detail-item">
            <label>Severity</label>
            <span class="badge {{ $healthcareBarrier->severity === 'high' ? 'badge-danger' : ($healthcareBarrier->severity === 'medium' ? 'badge-warning' : 'badge-success') }}">
                {{ ucfirst($healthcareBarrier->severity) }}
            </span>
        </div>
        <div class="detail-item">
            <label>Status</label>
            <span class="badge {{ $healthcareBarrier->status === 'resolved' ? 'badge-success' : ($healthcareBarrier->status === 'in_progress' ? 'badge-primary' : 'badge-warning') }}">
                {{ ucfirst(str_replace('_', ' ', $healthcareBarrier->status)) }}
            </span>
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

    @if($healthcareBarrier->description)
        <div class="participants-section">
            <h3>Description</h3>
            <p>{{ $healthcareBarrier->description }}</p>
        </div>
    @endif

    @if($healthcareBarrier->resolution_notes)
        <div class="participants-section">
            <h3>Resolution Notes</h3>
            <p>{{ $healthcareBarrier->resolution_notes }}</p>
        </div>
    @endif

    @if($healthcareBarrier->participants && $healthcareBarrier->participants->count() > 0)
        <div class="participants-section">
            <h3>Participants ({{ $healthcareBarrier->participants->count() }})</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($healthcareBarrier->participants as $participant)
                        <tr>
                            <td>{{ $participant->name }}</td>
                            <td>{{ $participant->designation ?? 'N/A' }}</td>
                            <td>{{ $participant->phone ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
