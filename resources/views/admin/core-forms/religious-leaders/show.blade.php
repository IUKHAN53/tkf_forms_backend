@extends('layouts.admin')

@section('title', 'Religious Leader Details')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Religious Leader #{{ $religiousLeader->id }}</h2>
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
            <label>Team Number</label>
            <span>{{ $religiousLeader->team_no }}</span>
        </div>
        <div class="detail-item">
            <label>UC Name</label>
            <span>{{ $religiousLeader->uc_name }}</span>
        </div>
        <div class="detail-item">
            <label>Mosque/Madrassa Name</label>
            <span>{{ $religiousLeader->mosque_madrassa_name }}</span>
        </div>
        <div class="detail-item">
            <label>Religious Leader Name</label>
            <span>{{ $religiousLeader->religious_leader_name }}</span>
        </div>
        <div class="detail-item">
            <label>Phone Number</label>
            <span>{{ $religiousLeader->phone_number ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Sect</label>
            <span>{{ ucfirst($religiousLeader->sect) }}</span>
        </div>
        <div class="detail-item">
            <label>Support Level</label>
            <span class="badge {{ $religiousLeader->support_level === 'high' ? 'badge-success' : ($religiousLeader->support_level === 'medium' ? 'badge-primary' : 'badge-warning') }}">
                {{ ucfirst($religiousLeader->support_level) }}
            </span>
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

    @if($religiousLeader->remarks)
        <div class="participants-section">
            <h3>Remarks</h3>
            <p>{{ $religiousLeader->remarks }}</p>
        </div>
    @endif

    @if($religiousLeader->participants && $religiousLeader->participants->count() > 0)
        <div class="participants-section">
            <h3>Participants ({{ $religiousLeader->participants->count() }})</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($religiousLeader->participants as $participant)
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
