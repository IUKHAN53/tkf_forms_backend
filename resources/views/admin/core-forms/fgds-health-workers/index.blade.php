@extends('layouts.admin')

@section('title', 'FGDs-Health Workers')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>FGDs-Health Workers</h2>
            <p class="text-muted">Focus Group Discussions with health workers on immunization barriers</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.fgds-health-workers.template') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Download Template
            </a>
            <a href="{{ route('admin.fgds-health-workers.export') }}" class="btn btn-outline">
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
        <form action="{{ route('admin.fgds-health-workers.index') }}" method="GET" class="filter-form">
            <div class="filter-row">
                <input type="text" name="search" class="form-input" placeholder="Search by UC, HFS, or facilitator..." value="{{ request('search') }}">
                <select name="uc" class="form-input filter-select">
                    <option value="">All UCs</option>
                    @foreach($ucs as $uc)
                        <option value="{{ $uc }}" {{ request('uc') == $uc ? 'selected' : '' }}>{{ $uc }}</option>
                    @endforeach
                </select>
                <select name="group_type" class="form-input filter-select">
                    <option value="">All Group Types</option>
                    @foreach($groupTypes as $type)
                        <option value="{{ $type }}" {{ request('group_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
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
                @if(request()->hasAny(['search', 'uc', 'group_type', 'date_from', 'date_to', 'facilitator']))
                    <a href="{{ route('admin.fgds-health-workers.index') }}" class="btn btn-outline">Clear All</a>
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
                @forelse($fgdsHealthWorkers as $item)
                    <tr>
                        <td><code>{{ $item->unique_id }}</code></td>
                        <td>{{ $item->date ? $item->date->format('M d, Y') : 'N/A' }}</td>
                        <td>{{ $item->uc }}</td>
                        <td>{{ $item->hfs }}</td>
                        <td>
                            <span class="badge badge-primary">
                                {{ $item->group_type }}
                            </span>
                        </td>
                        <td>{{ $item->participants_males + $item->participants_females }}</td>
                        <td>{{ $item->facilitator_tkf }}</td>
                        <td>{{ $item->user->name ?? 'N/A' }}</td>
                        <td class="action-buttons">
                            <a href="{{ route('admin.fgds-health-workers.show', $item) }}" class="btn btn-sm btn-outline">View</a>
                            <button type="button" class="btn btn-sm btn-warning" onclick="openBarriersModal({{ $item->id }}, '{{ $item->unique_id }}')">
                                Barriers
                            </button>
                            <form action="{{ route('admin.fgds-health-workers.destroy', $item) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No FGDs-Health Workers records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($fgdsHealthWorkers->hasPages())
        <div class="card-footer">
            {{ $fgdsHealthWorkers->links() }}
        </div>
    @endif
</div>

<!-- Import Modal -->
<dialog id="importModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Import FGDs-Health Workers</h3>
            <button type="button" class="modal-close" onclick="document.getElementById('importModal').close()">&times;</button>
        </div>
        <form action="{{ route('admin.fgds-health-workers.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <p class="mb-md text-muted">Upload a CSV file to import FGDs-Health Workers records. Download the template first to see the required format.</p>
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
        <form action="{{ route('admin.fgds-health-workers.upload-barriers', ['id' => '__ID__']) }}" method="POST" enctype="multipart/form-data" id="barriersForm">
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
    const baseUrl = '{{ route('admin.fgds-health-workers.upload-barriers', ['id' => '__ID__']) }}';
    form.action = baseUrl.replace('__ID__', id);

    // Update the modal title with the record ID
    recordIdSpan.textContent = uniqueId;

    modal.showModal();
}
</script>

@endsection
