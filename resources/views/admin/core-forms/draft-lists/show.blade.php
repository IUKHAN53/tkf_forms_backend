@extends('layouts.admin')

@section('title', 'Draft List Details')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Draft List #{{ $draftList->id }}</h2>
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
            <label>Team Number</label>
            <span>{{ $draftList->team_no }}</span>
        </div>
        <div class="detail-item">
            <label>UC Name</label>
            <span>{{ $draftList->uc_name }}</span>
        </div>
        <div class="detail-item">
            <label>Area Name</label>
            <span>{{ $draftList->area_name }}</span>
        </div>
        <div class="detail-item">
            <label>House Number</label>
            <span>{{ $draftList->house_no }}</span>
        </div>
        <div class="detail-item">
            <label>Category</label>
            <span class="badge {{ $draftList->category === 'refusal' ? 'badge-danger' : 'badge-primary' }}">
                {{ ucfirst($draftList->category) }}
            </span>
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
            <label>Age (Months)</label>
            <span>{{ $draftList->age_months }}</span>
        </div>
        <div class="detail-item">
            <label>Gender</label>
            <span>{{ ucfirst($draftList->gender) }}</span>
        </div>
        <div class="detail-item">
            <label>Contact Number</label>
            <span>{{ $draftList->contact_number ?? 'N/A' }}</span>
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

    @if($draftList->remarks)
        <div class="participants-section">
            <h3>Remarks</h3>
            <p>{{ $draftList->remarks }}</p>
        </div>
    @endif
</div>
@endsection
