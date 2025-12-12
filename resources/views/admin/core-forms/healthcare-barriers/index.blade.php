@extends('layouts.admin')

@section('title', 'Healthcare Barriers')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Healthcare Barriers</h2>
            <p class="text-muted">Manage healthcare facility barrier records</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.healthcare-barriers.template') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Download Template
            </a>
            <a href="{{ route('admin.healthcare-barriers.export') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Export CSV
            </a>
            <button type="button" class="btn btn-primary" onclick="document.getElementById('importModal').showModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Import CSV
            </button>
        </div>
    </div>

    <div class="card-filters">
        <form action="{{ route('admin.healthcare-barriers.index') }}" method="GET" class="search-form">
            <input type="text" name="search" class="form-input" placeholder="Search by facility, UC, or area..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.healthcare-barriers.index') }}" class="btn btn-outline">Clear</a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Form ID</th>
                    <th>Date</th>
                    <th>UC</th>
                    <th>HFS</th>
                    <th>Group Type</th>
                    <th>Participants</th>
                    <th>Facilitator</th>
                    <th>Submitted By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($healthcareBarriers as $item)
                    <tr>
                        <td><code>{{ $item->unique_id }}</code></td>
                        <td>{{ $item->date->format('M d, Y') }}</td>
                        <td>{{ $item->uc }}</td>
                        <td>{{ $item->hfs }}</td>
                        <td>
                            <span class="badge badge-primary">
                                {{ $item->group_type }}
                            </span>
                        </td>
                        <td>{{ $item->participants_males + $item->participants_females }}</td>
                        <td>{{ $item->facilitator_tkf }}</td>
                        </td>
                        <td>{{ $item->user->name ?? 'N/A' }}</td>
                        <td class="action-buttons">
                            <a href="{{ route('admin.healthcare-barriers.show', $item) }}" class="btn btn-sm btn-outline">View</a>
                            <form action="{{ route('admin.healthcare-barriers.destroy', $item) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">No healthcare barrier records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($healthcareBarriers->hasPages())
        <div class="card-footer">
            {{ $healthcareBarriers->links() }}
        </div>
    @endif
</div>

<!-- Import Modal -->
<dialog id="importModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Import Healthcare Barriers</h3>
            <button type="button" class="modal-close" onclick="document.getElementById('importModal').close()">&times;</button>
        </div>
        <form action="{{ route('admin.healthcare-barriers.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <p class="mb-md text-muted">Upload a CSV file to import healthcare barrier records. Download the template first to see the required format.</p>
                <input type="file" name="file" accept=".csv" required class="form-input" style="width: 100%;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('importModal').close()">Cancel</button>
                <button type="submit" class="btn btn-primary">Import</button>
            </div>
        </form>
    </div>
</dialog>

@endsection
