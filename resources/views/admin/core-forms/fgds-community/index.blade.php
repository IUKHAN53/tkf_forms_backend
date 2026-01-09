@extends('layouts.admin')

@section('title', 'FGDs-Community')

@include('admin.core-forms.partials.styles')

@section('content')
<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card stat-card-primary">
        <div class="stat-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-value">{{ number_format($stats['total']) }}</span>
            <span class="stat-card-label">Total Sessions</span>
        </div>
    </div>

    <div class="stat-card stat-card-warning">
        <div class="stat-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-value">{{ number_format($stats['total_barriers']) }}</span>
            <span class="stat-card-label">Total Barriers</span>
        </div>
    </div>

    <div class="stat-card stat-card-purple">
        <div class="stat-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <line x1="19" y1="8" x2="19" y2="14"/>
                <line x1="22" y1="11" x2="16" y2="11"/>
            </svg>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-value">{{ number_format($stats['total_participants']) }}</span>
            <span class="stat-card-label">Total Participants</span>
        </div>
    </div>

    <div class="stat-card stat-card-info">
        <div class="stat-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"/>
                <path d="M12 2v4m0 12v4M2 12h4m12 0h4"/>
            </svg>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-value">{{ number_format($stats['total_males']) }}</span>
            <span class="stat-card-label">Male Participants</span>
        </div>
    </div>

    <div class="stat-card stat-card-success">
        <div class="stat-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"/>
                <line x1="12" y1="22" x2="12" y2="19"/>
                <path d="M9 19h6"/>
            </svg>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-value">{{ number_format($stats['total_females']) }}</span>
            <span class="stat-card-label">Female Participants</span>
        </div>
    </div>

    <div class="stat-card stat-card-cyan">
        <div class="stat-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-value">{{ number_format($stats['districts_covered']) }}</span>
            <span class="stat-card-label">Districts Covered</span>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>FGDs-Community</h2>
            <p class="text-muted">Focus Group Discussions with community members on immunization barriers</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.fgds-community.template') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Download Template
            </a>
            <a href="{{ route('admin.fgds-community.export') }}" class="btn btn-outline">
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
        <form action="{{ route('admin.fgds-community.index') }}" method="GET" class="filter-form">
            <div class="filter-row">
                <input type="text" name="search" class="form-input" placeholder="Search by district, UC, venue, or facilitator..." value="{{ request('search') }}">
                <select name="district" class="form-input filter-select">
                    <option value="">All Districts</option>
                    @foreach($districts as $district)
                        <option value="{{ $district }}" {{ request('district') == $district ? 'selected' : '' }}>{{ $district }}</option>
                    @endforeach
                </select>
                <select name="uc" class="form-input filter-select">
                    <option value="">All UCs</option>
                    @foreach($ucs as $uc)
                        <option value="{{ $uc }}" {{ request('uc') == $uc ? 'selected' : '' }}>{{ $uc }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-row">
                <div class="date-filter">
                    <label>From:</label>
                    <input type="date" name="date_from" class="form-input" value="{{ request('date_from') }}">
                </div>
                <div class="date-filter">
                    <label>To:</label>
                    <input type="date" name="date_to" class="form-input" value="{{ request('date_to') }}">
                </div>
                <input type="text" name="facilitator" class="form-input" placeholder="Facilitator name..." value="{{ request('facilitator') }}">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                @if(request()->hasAny(['search', 'district', 'uc', 'date_from', 'date_to', 'facilitator']))
                    <a href="{{ route('admin.fgds-community.index') }}" class="btn btn-outline">Clear All</a>
                @endif
            </div>
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
                    <th>Community</th>
                    <th>Participants</th>
                    <th>Submitted By</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fgdsCommunity as $item)
                    <tr>
                        <td><code>{{ $item->unique_id }}</code></td>
                        <td>{{ $item->date ? $item->date->format('M d, Y') : 'N/A' }}</td>
                        <td>{{ $item->district }}</td>
                        <td>{{ $item->uc }}</td>
                        <td>{{ $item->venue }}</td>
                        <td>{{ is_array($item->community) ? implode(', ', $item->community) : $item->community }}</td>
                        <td>{{ $item->participants_males + $item->participants_females }}</td>
                        <td>{{ $item->user->name ?? 'N/A' }}</td>
                        <td>{{ $item->created_at->format('M d, Y') }}</td>
                        <td class="action-buttons">
                            <a href="{{ route('admin.fgds-community.show', $item) }}" class="btn btn-sm btn-outline">View</a>
                            <button type="button" class="btn btn-sm btn-warning" onclick="openBarriersModal({{ $item->id }}, '{{ $item->unique_id }}')">
                                Barriers
                            </button>
                            <form action="{{ route('admin.fgds-community.destroy', $item) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">No FGDs-Community records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($fgdsCommunity->hasPages())
        <div class="card-footer">
            {{ $fgdsCommunity->links() }}
        </div>
    @endif
</div>

<!-- Import Modal -->
<dialog id="importModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Import FGDs-Community</h3>
            <button type="button" class="modal-close" onclick="document.getElementById('importModal').close()">&times;</button>
        </div>
        <form action="{{ route('admin.fgds-community.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <p class="mb-md text-muted">Upload a CSV file to import FGDs-Community records. Download the template first to see the required format.</p>
                <input type="file" name="file" accept=".csv" required class="form-input" style="width: 100%;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('importModal').close()">Cancel</button>
                <button type="submit" class="btn btn-primary">Import</button>
            </div>
        </form>
    </div>
</dialog>

<!-- Upload Barriers Modal -->
<dialog id="barriersModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Upload Barriers - <span id="barriersRecordId"></span></h3>
            <button type="button" class="modal-close" onclick="document.getElementById('barriersModal').close()">&times;</button>
        </div>
        <form action="{{ route('admin.fgds-community.upload-barriers', ['id' => '__ID__']) }}" method="POST" enctype="multipart/form-data" id="barriersForm">
            @csrf
            <div class="modal-body">
                <p class="mb-md text-muted">Upload an Excel file containing the identified immunization barriers from this FGD session.</p>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">Select Excel File</label>
                    <input type="file" name="barriers_file" accept=".xlsx,.xls" required class="form-input" style="width: 100%;">
                </div>
                <div class="upload-info" style="background: #f8f9fa; border-radius: 8px; padding: 12px; font-size: 13px; color: #666;">
                    <p style="margin: 0 0 8px 0;"><strong>Accepted formats:</strong> .xlsx, .xls</p>
                    <p style="margin: 0;"><strong>Max file size:</strong> 5MB</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('barriersModal').close()">Cancel</button>
                <button type="submit" class="btn btn-warning">Upload Barriers</button>
            </div>
        </form>
    </div>
</dialog>

<script>
function openBarriersModal(id, uniqueId) {
    const modal = document.getElementById('barriersModal');
    const form = document.getElementById('barriersForm');
    const recordIdSpan = document.getElementById('barriersRecordId');

    // Update the form action with the correct ID
    const baseUrl = '{{ route('admin.fgds-community.upload-barriers', ['id' => '__ID__']) }}';
    form.action = baseUrl.replace('__ID__', id);

    // Update the modal title with the record ID
    recordIdSpan.textContent = uniqueId;

    modal.showModal();
}
</script>

@endsection
