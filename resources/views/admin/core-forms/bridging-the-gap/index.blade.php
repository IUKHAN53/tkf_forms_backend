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

    <form id="bulkDeleteForm" action="{{ route('admin.bridging-the-gap.bulk-destroy') }}" method="POST">
        @csrf
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
            <button type="submit" class="btn btn-sm btn-danger" id="bulkDeleteBtn" style="display: none;" onclick="return confirm('Are you sure you want to delete the selected records?')">
                Delete Selected (<span id="selectedCount">0</span>)
            </button>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>Form ID</th>
                        <th>Date</th>
                        <th>District</th>
                        <th>UC</th>
                        <th>Venue</th>
                        <th>Attendance</th>
                        <th>IIT Members</th>
                        <th>Action Plans</th>
                        <th>Submitted By</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $item)
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="{{ $item->id }}" class="row-checkbox" onclick="updateBulkDeleteBtn()"></td>
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
                            <td>
                                <span class="badge badge-warning">{{ $item->action_plans_count }}</span>
                            </td>
                            <td>{{ $item->user->name ?? 'N/A' }}</td>
                            <td>{{ $item->created_at->format('M d, Y') }}</td>
                            <td class="action-buttons">
                                <a href="{{ route('admin.bridging-the-gap.show', $item) }}" class="btn btn-sm btn-outline">View</a>
                                <a href="{{ route('admin.bridging-the-gap.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a>
                                <button type="button" class="btn btn-sm btn-success" onclick="openActionPlanModal({{ $item->id }}, '{{ $item->unique_id }}')">
                                    Action Plan
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteRecord('{{ route('admin.bridging-the-gap.destroy', $item) }}')">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted">No Bridging The Gap records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div class="card-footer">
        {{ $records->links() }}
    </div>
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

<!-- Manage Action Plans Modal -->
<dialog id="actionPlanModal" class="modal" style="max-width: 900px;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Manage Action Plans - <span id="actionPlanRecordId"></span></h3>
            <button type="button" class="modal-close" onclick="document.getElementById('actionPlanModal').close()">&times;</button>
        </div>
        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
            {{-- Existing Action Plans List --}}
            <div id="actionPlansList" style="margin-bottom: 20px;">
                <div style="text-align: center; padding: 20px; color: #9ca3af;">Loading action plans...</div>
            </div>

            {{-- Add New Action Plan --}}
            <div style="border-top: 2px solid #e5e7eb; padding-top: 16px; margin-top: 16px;">
                <h4 style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">Add New Action Plan</h4>
                <div class="form-grid-2" style="margin-bottom: 10px;">
                    <div class="form-group" style="margin-bottom: 8px;">
                        <label class="form-label">Problem *</label>
                        <textarea id="newApProblem" class="form-input" style="width:100%; min-height:60px;" placeholder="Describe the problem..."></textarea>
                    </div>
                    <div class="form-group" style="margin-bottom: 8px;">
                        <label class="form-label">Solution</label>
                        <textarea id="newApSolution" class="form-input" style="width:100%; min-height:60px;" placeholder="Proposed solution..."></textarea>
                    </div>
                </div>
                <div class="form-grid-3" style="margin-bottom: 10px;">
                    <div class="form-group" style="margin-bottom: 8px;">
                        <label class="form-label">Action Needed</label>
                        <input type="text" id="newApAction" class="form-input" style="width:100%;" placeholder="Action needed...">
                    </div>
                    <div class="form-group" style="margin-bottom: 8px;">
                        <label class="form-label">Responsible</label>
                        <input type="text" id="newApResponsible" class="form-input" style="width:100%;" placeholder="Who is responsible...">
                    </div>
                    <div class="form-group" style="margin-bottom: 8px;">
                        <label class="form-label">Timeline</label>
                        <input type="text" id="newApTimeline" class="form-input" style="width:100%;" placeholder="e.g. 2 weeks...">
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-primary" onclick="addActionPlan()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Action Plan
                </button>
            </div>

            {{-- Excel Upload Section --}}
            <div style="border-top: 2px solid #e5e7eb; padding-top: 16px; margin-top: 16px;">
                <h4 style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">Or Upload via Excel</h4>
                <div style="background: #dcfce7; border: 1px solid #86efac; border-radius: 8px; padding: 10px; margin-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 13px; color: #166534;">Need the correct format?</span>
                        <a href="{{ route('admin.bridging-the-gap.action-plan-sample') }}" class="btn btn-sm btn-success" style="margin-left: auto;">Download Sample</a>
                    </div>
                </div>
                <div style="display: flex; gap: 10px; align-items: flex-end;">
                    <div style="flex: 1;">
                        <input type="file" id="apExcelFile" accept=".xlsx,.xls" class="form-input" style="width: 100%;">
                    </div>
                    <button type="button" class="btn btn-sm btn-success" onclick="uploadActionPlanExcel()">Upload Excel</button>
                </div>
                <p style="font-size: 11px; color: #9ca3af; margin-top: 6px;">Uploading an Excel file will replace all existing action plans for this record.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="document.getElementById('actionPlanModal').close()">Close</button>
        </div>
    </div>
</dialog>

<form id="individualDeleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script>
let currentApRecordId = null;
const apBaseUrl = '{{ url("admin/bridging-the-gap") }}';
const apCsrfToken = '{{ csrf_token() }}';

function toggleSelectAll(source) {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
    updateBulkDeleteBtn();
}

function updateBulkDeleteBtn() {
    const checked = document.querySelectorAll('.row-checkbox:checked').length;
    const btn = document.getElementById('bulkDeleteBtn');
    const count = document.getElementById('selectedCount');
    btn.style.display = checked > 0 ? 'inline-flex' : 'none';
    count.textContent = checked;
    document.getElementById('selectAll').checked = checked === document.querySelectorAll('.row-checkbox').length && checked > 0;
}

function deleteRecord(url) {
    if (confirm('Are you sure?')) {
        const form = document.getElementById('individualDeleteForm');
        form.action = url;
        form.submit();
    }
}

function openActionPlanModal(id, uniqueId) {
    currentApRecordId = id;
    document.getElementById('actionPlanRecordId').textContent = uniqueId;
    document.getElementById('actionPlanModal').showModal();
    loadActionPlans();
}

function loadActionPlans() {
    const listEl = document.getElementById('actionPlansList');
    listEl.innerHTML = '<div style="text-align: center; padding: 20px; color: #9ca3af;">Loading...</div>';

    fetch(apBaseUrl + '/' + currentApRecordId + '/action-plans', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.action_plans || data.action_plans.length === 0) {
            listEl.innerHTML = '<div style="text-align: center; padding: 16px; color: #9ca3af; font-style: italic;">No action plans yet. Add one below or upload an Excel file.</div>';
            return;
        }
        let html = '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;"><span style="font-size:13px; font-weight:600; color:#374151;">' + data.action_plans.length + ' Action Plan(s)</span>';
        html += '<button class="btn btn-sm btn-danger" onclick="deleteAllActionPlans()">Delete All</button></div>';
        html += '<table class="data-table"><thead><tr><th style="width:40px">#</th><th>Problem</th><th>Solution</th><th>Action Needed</th><th>Responsible</th><th>Timeline</th><th style="width:110px">Actions</th></tr></thead><tbody>';
        data.action_plans.forEach(plan => {
            html += '<tr id="ap-row-' + plan.id + '">';
            html += '<td>' + (plan.serial_number || '-') + '</td>';
            html += '<td>' + escHtml(plan.problem) + '</td>';
            html += '<td>' + escHtml(plan.solution || '-') + '</td>';
            html += '<td>' + escHtml(plan.action_needed || '-') + '</td>';
            html += '<td>' + escHtml(plan.who_is_responsible || '-') + '</td>';
            html += '<td>' + escHtml(plan.timeline || '-') + '</td>';
            html += '<td class="action-buttons">';
            html += '<button class="btn btn-sm btn-outline" onclick="editActionPlan(' + plan.id + ')">Edit</button> ';
            html += '<button class="btn btn-sm btn-danger" onclick="deleteActionPlan(' + plan.id + ')">Delete</button>';
            html += '</td></tr>';
        });
        html += '</tbody></table>';
        listEl.innerHTML = html;
    })
    .catch(() => {
        listEl.innerHTML = '<div style="text-align:center;padding:16px;color:#ef4444;">Failed to load action plans.</div>';
    });
}

function escHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function escAttr(str) {
    return str.replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function addActionPlan() {
    const problem = document.getElementById('newApProblem').value.trim();
    if (!problem) { alert('Problem field is required.'); return; }

    const body = {
        problem: problem,
        solution: document.getElementById('newApSolution').value.trim() || null,
        action_needed: document.getElementById('newApAction').value.trim() || null,
        who_is_responsible: document.getElementById('newApResponsible').value.trim() || null,
        timeline: document.getElementById('newApTimeline').value.trim() || null,
    };

    fetch(apBaseUrl + '/' + currentApRecordId + '/action-plans', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': apCsrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(body),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('newApProblem').value = '';
            document.getElementById('newApSolution').value = '';
            document.getElementById('newApAction').value = '';
            document.getElementById('newApResponsible').value = '';
            document.getElementById('newApTimeline').value = '';
            loadActionPlans();
        } else {
            alert(data.message || 'Failed to add action plan.');
        }
    })
    .catch(() => alert('Error adding action plan.'));
}

function editActionPlan(id) {
    const row = document.getElementById('ap-row-' + id);
    const cells = row.querySelectorAll('td');

    const problem = cells[1].textContent === '-' ? '' : cells[1].textContent;
    const solution = cells[2].textContent === '-' ? '' : cells[2].textContent;
    const actionNeeded = cells[3].textContent === '-' ? '' : cells[3].textContent;
    const responsible = cells[4].textContent === '-' ? '' : cells[4].textContent;
    const timeline = cells[5].textContent === '-' ? '' : cells[5].textContent;

    cells[1].innerHTML = '<textarea class="form-input" style="width:100%;min-height:50px;font-size:12px;">' + escHtml(problem) + '</textarea>';
    cells[2].innerHTML = '<textarea class="form-input" style="width:100%;min-height:50px;font-size:12px;">' + escHtml(solution) + '</textarea>';
    cells[3].innerHTML = '<input type="text" class="form-input" style="width:100%;font-size:12px;" value="' + escAttr(actionNeeded) + '">';
    cells[4].innerHTML = '<input type="text" class="form-input" style="width:100%;font-size:12px;" value="' + escAttr(responsible) + '">';
    cells[5].innerHTML = '<input type="text" class="form-input" style="width:100%;font-size:12px;" value="' + escAttr(timeline) + '">';
    cells[6].innerHTML = '<button class="btn btn-sm btn-success" onclick="saveActionPlan(' + id + ')">Save</button> <button class="btn btn-sm btn-outline" onclick="loadActionPlans()">Cancel</button>';
}

function saveActionPlan(id) {
    const row = document.getElementById('ap-row-' + id);
    const textareas = row.querySelectorAll('textarea');
    const inputs = row.querySelectorAll('input[type="text"]');

    const body = {
        problem: textareas[0].value.trim(),
        solution: textareas[1].value.trim() || null,
        action_needed: inputs[0].value.trim() || null,
        who_is_responsible: inputs[1].value.trim() || null,
        timeline: inputs[2].value.trim() || null,
    };

    if (!body.problem) { alert('Problem field is required.'); return; }

    fetch(apBaseUrl + '/action-plans/' + id, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': apCsrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(body),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            loadActionPlans();
        } else {
            alert(data.message || 'Failed to update action plan.');
        }
    })
    .catch(() => alert('Error updating action plan.'));
}

function deleteActionPlan(id) {
    if (!confirm('Are you sure you want to delete this action plan?')) return;

    fetch(apBaseUrl + '/action-plans/' + id, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': apCsrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            loadActionPlans();
        } else {
            alert(data.message || 'Failed to delete action plan.');
        }
    })
    .catch(() => alert('Error deleting action plan.'));
}

function deleteAllActionPlans() {
    if (!confirm('Are you sure you want to delete ALL action plans for this record? This cannot be undone.')) return;

    fetch(apBaseUrl + '/' + currentApRecordId + '/action-plans', {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': apCsrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            loadActionPlans();
        } else {
            alert(data.message || 'Failed to delete action plans.');
        }
    })
    .catch(() => alert('Error deleting action plans.'));
}

function uploadActionPlanExcel() {
    const fileInput = document.getElementById('apExcelFile');
    if (!fileInput.files.length) { alert('Please select an Excel file.'); return; }
    if (!confirm('Uploading an Excel file will replace all existing action plans for this record. Continue?')) return;

    const formData = new FormData();
    formData.append('action_plan_file', fileInput.files[0]);
    formData.append('_token', apCsrfToken);

    fetch(apBaseUrl + '/' + currentApRecordId + '/action-plan', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
    })
    .then(r => {
        if (r.redirected) {
            window.location.reload();
            return;
        }
        return r.text();
    })
    .then(() => {
        fileInput.value = '';
        loadActionPlans();
    })
    .catch(() => {
        window.location.reload();
    });
}
</script>

@endsection
