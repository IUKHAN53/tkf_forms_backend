@extends('layouts.admin')

@section('title', 'Community Barrier Details')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Community Barrier #{{ $communityBarrier->id }}</h2>
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
            <label>Team Number</label>
            <span>{{ $communityBarrier->team_no }}</span>
        </div>
        <div class="detail-item">
            <label>UC Name</label>
            <span>{{ $communityBarrier->uc_name }}</span>
        </div>
        <div class="detail-item">
            <label>Area Name</label>
            <span>{{ $communityBarrier->area_name }}</span>
        </div>
        <div class="detail-item">
            <label>Barrier Type</label>
            <span>{{ ucfirst(str_replace('_', ' ', $communityBarrier->barrier_type)) }}</span>
        </div>
        <div class="detail-item">
            <label>Severity</label>
            <span class="badge {{ $communityBarrier->severity === 'high' ? 'badge-danger' : ($communityBarrier->severity === 'medium' ? 'badge-warning' : 'badge-success') }}">
                {{ ucfirst($communityBarrier->severity) }}
            </span>
        </div>
        <div class="detail-item">
            <label>Status</label>
            <span class="badge {{ $communityBarrier->status === 'resolved' ? 'badge-success' : ($communityBarrier->status === 'in_progress' ? 'badge-primary' : 'badge-warning') }}">
                {{ ucfirst(str_replace('_', ' ', $communityBarrier->status)) }}
            </span>
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

    @if($communityBarrier->description)
        <div class="participants-section">
            <h3>Description</h3>
            <p>{{ $communityBarrier->description }}</p>
        </div>
    @endif

    @if($communityBarrier->resolution_notes)
        <div class="participants-section">
            <h3>Resolution Notes</h3>
            <p>{{ $communityBarrier->resolution_notes }}</p>
        </div>
    @endif

    @if($communityBarrier->participants && $communityBarrier->participants->count() > 0)
        <div class="participants-section">
            <h3>Participants ({{ $communityBarrier->participants->count() }})</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($communityBarrier->participants as $participant)
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
