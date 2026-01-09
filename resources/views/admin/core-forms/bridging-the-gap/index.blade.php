@extends('layouts.admin')

@section('title', 'Bridging The Gap')

@include('admin.core-forms.partials.styles')

@section('content')
<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card stat-card-primary">
        <div class="stat-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
                <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                <line x1="6" y1="1" x2="6" y2="4"/>
                <line x1="10" y1="1" x2="10" y2="4"/>
                <line x1="14" y1="1" x2="14" y2="4"/>
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
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-value">{{ number_format($stats['total_action_plans']) }}</span>
            <span class="stat-card-label">Action Plans</span>
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
            <span class="stat-card-value">{{ number_format($stats['total_attendance']) }}</span>
            <span class="stat-card-label">Total Attendance</span>
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
            <span class="stat-card-label">Male Attendance</span>
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
            <span class="stat-card-label">Female Attendance</span>
        </div>
    </div>

    <div class="stat-card stat-card-cyan">
        <div class="stat-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-value">{{ number_format($stats['total_iit_members']) }}</span>
            <span class="stat-card-label">IIT Members</span>
        </div>
    </div>
</div>

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
        <form action="{{ route('admin.bridging-the-gap.index') }}" method="GET" class="filter-form">
            <div class="filter-row">
                <input type="text" name="search" class="form-input" placeholder="Search by district, UC, or venue..." value="{{ request('search') }}">
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
                <input type="text" name="venue" class="form-input" placeholder="Venue..." value="{{ request('venue') }}">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                @if(request()->hasAny(['search', 'district', 'uc', 'date_from', 'date_to', 'venue']))
                    <a href="{{ route('admin.bridging-the-gap.index') }}" class="btn btn-outline">Clear All</a>
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
                            <button type="button" class="btn btn-sm btn-success" onclick="openActionPlanModal({{ $item->id }}, '{{ $item->unique_id }}')">
                                Action Plan
                            </button>
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

<!-- Upload Action Plan Modal -->
<dialog id="actionPlanModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Upload Action Plan - <span id="actionPlanRecordId"></span></h3>
            <button type="button" class="modal-close" onclick="document.getElementById('actionPlanModal').close()">&times;</button>
        </div>
        <form action="{{ route('admin.bridging-the-gap.upload-action-plan', ['id' => '__ID__']) }}" method="POST" enctype="multipart/form-data" id="actionPlanForm">
            @csrf
            <div class="modal-body">
                <p class="mb-md text-muted">Upload an Excel file containing the action plan for this Bridging The Gap session.</p>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">Select Excel File</label>
                    <input type="file" name="action_plan_file" accept=".xlsx,.xls" required class="form-input" style="width: 100%;">
                </div>
                <div class="upload-info" style="background: #f8f9fa; border-radius: 8px; padding: 12px; font-size: 13px; color: #666;">
                    <p style="margin: 0 0 8px 0;"><strong>Accepted formats:</strong> .xlsx, .xls</p>
                    <p style="margin: 0;"><strong>Max file size:</strong> 5MB</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('actionPlanModal').close()">Cancel</button>
                <button type="submit" class="btn btn-success">Upload Action Plan</button>
            </div>
        </form>
    </div>
</dialog>

<script>
function openActionPlanModal(id, uniqueId) {
    const modal = document.getElementById('actionPlanModal');
    const form = document.getElementById('actionPlanForm');
    const recordIdSpan = document.getElementById('actionPlanRecordId');

    // Update the form action with the correct ID
    const baseUrl = '{{ route('admin.bridging-the-gap.upload-action-plan', ['id' => '__ID__']) }}';
    form.action = baseUrl.replace('__ID__', id);

    // Update the modal title with the record ID
    recordIdSpan.textContent = uniqueId;

    modal.showModal();
}
</script>

@endsection
