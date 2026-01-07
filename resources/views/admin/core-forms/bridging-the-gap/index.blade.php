@extends('layouts.admin')

@section('title', 'Bridging The Gap')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Bridging The Gap</h2>
            <p class="text-muted">Immunization Improvement Teams and attendance records</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.bridging-the-gap.template') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Download Template
            </a>
            <a href="{{ route('admin.bridging-the-gap.export') }}" class="btn btn-outline">
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

    @include('admin.core-forms.partials.map', ['mapData' => $mapData])

    <div class="card-filters">
        <form action="{{ route('admin.bridging-the-gap.index') }}" method="GET" class="search-form">
            <input type="text" name="search" class="form-input" placeholder="Search by district, UC, or venue..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.bridging-the-gap.index') }}" class="btn btn-outline">Clear</a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Form ID</th>
                    <th>Date</th>
                    <th>District</th>
                    <th>UC</th>
                    <th>Venue</th>
                    <th>Attendance</th>
                    <th>IIT Members</th>
                    <th>Submitted By</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $item)
                    <tr>
                        <td><code>{{ $item->unique_id }}</code></td>
                        <td>{{ $item->date->format('M d, Y') }}</td>
                        <td>{{ $item->district }}</td>
                        <td>{{ $item->uc }}</td>
                        <td>{{ $item->venue }}</td>
                        <td>
                            <span class="badge badge-info">{{ $item->participants->count() }}</span>
                            <small class="text-muted">(M:{{ $item->participants_males }}/F:{{ $item->participants_females }})</small>
                        </td>
                        <td>
                            <span class="badge badge-success">{{ $item->teamMembers->count() }}</span>
                        </td>
                        <td>{{ $item->user->name ?? 'N/A' }}</td>
                        <td>{{ $item->created_at->format('M d, Y') }}</td>
                        <td class="action-buttons">
                            <a href="{{ route('admin.bridging-the-gap.show', $item) }}" class="btn btn-sm btn-outline">View</a>
                            <form action="{{ route('admin.bridging-the-gap.destroy', $item) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">No Bridging The Gap records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($records->hasPages())
        <div class="card-footer">
            {{ $records->links() }}
        </div>
    @endif
</div>

<!-- Import Modal -->
<dialog id="importModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Import Bridging The Gap Records</h3>
            <button type="button" class="modal-close" onclick="document.getElementById('importModal').close()">&times;</button>
        </div>
        <form action="{{ route('admin.bridging-the-gap.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <p class="mb-md text-muted">Upload a CSV file to import records. Download the template first to see the required format.</p>
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
