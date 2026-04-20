@extends('layouts.admin')

@section('title', 'Bridging The Gap Details')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Bridging The Gap <code>{{ $bridgingTheGap->unique_id }}</code></h2>
            <p class="text-muted">Submitted on {{ $bridgingTheGap->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.bridging-the-gap.index') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <label>Form ID</label>
            <span><code>{{ $bridgingTheGap->unique_id }}</code></span>
        </div>
        <div class="detail-item">
            <label>Date</label>
            <span>{{ $bridgingTheGap->date ? $bridgingTheGap->date->format('M d, Y h:i A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Venue</label>
            <span>{{ $bridgingTheGap->venue }}</span>
        </div>
        <div class="detail-item">
            <label>District</label>
            <span>{{ $bridgingTheGap->district }}</span>
        </div>
        <div class="detail-item">
            <label>UC</label>
            <span>{{ $bridgingTheGap->uc }}</span>
        </div>
        <div class="detail-item">
            <label>Fix Site</label>
            <span>{{ $bridgingTheGap->fix_site }}</span>
        </div>
        <div class="detail-item">
            <label>Participants (Males)</label>
            <span>{{ $bridgingTheGap->participants_males }}</span>
        </div>
        <div class="detail-item">
            <label>Participants (Females)</label>
            <span>{{ $bridgingTheGap->participants_females }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted By</label>
            <span>{{ $bridgingTheGap->user->name ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Latitude</label>
            <span>{{ $bridgingTheGap->latitude ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Longitude</label>
            <span>{{ $bridgingTheGap->longitude ?? 'N/A' }}</span>
        </div>
    </div>

    @if($bridgingTheGap->participants && $bridgingTheGap->participants->count() > 0)
        @php
            $iitParticipantIds = $bridgingTheGap->teamMembers->pluck('participant_id')->all();
        @endphp
        <div class="participants-section">
            <h3>Attendance Participants ({{ $bridgingTheGap->participants->count() }})</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Sr. No</th>
                        <th>Name</th>
                        <th>Occupation</th>
                        <th>Contact</th>
                        <th>IIT Member</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bridgingTheGap->participants as $participant)
                        @php $isIit = in_array($participant->id, $iitParticipantIds); @endphp
                        <tr>
                            <td>{{ $participant->sr_no }}</td>
                            <td>{{ $participant->name }}</td>
                            <td>{{ $participant->occupation ?? 'N/A' }}</td>
                            <td>{{ $participant->contact_no ?? 'N/A' }}</td>
                            <td>
                                @if($isIit)
                                    <span class="badge badge-success">Yes</span>
                                @else
                                    <span class="badge badge-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.bridging-the-gap.toggle-iit', ['bridgingTheGap' => $bridgingTheGap->id, 'participant' => $participant->id]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @if($isIit)
                                        <button type="submit" class="btn btn-sm btn-outline" title="Remove from IIT team">Remove IIT</button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-primary" title="Mark as IIT team member">Mark as IIT</button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="participants-section" style="margin-top: 24px;">
        <h3>IIT Team Members ({{ $bridgingTheGap->teamMembers ? $bridgingTheGap->teamMembers->count() : 0 }})</h3>
        <p class="text-muted" style="margin-bottom: 12px;">Team members selected from FGDs-Community and FGDs-Health Workers forms</p>
        @if($bridgingTheGap->teamMembers && $bridgingTheGap->teamMembers->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Source</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bridgingTheGap->teamMembers as $index => $member)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $member->participant->name ?? 'Participant Deleted' }}</td>
                            <td>{{ $member->participant->contact_no ?? 'N/A' }}</td>
                            <td>
                                @if($member->source_type === 'fgds_community' || $member->source_type === 'community_barrier')
                                    <span class="badge badge-info">FGDs-Community</span>
                                @elseif($member->source_type === 'fgds_health_workers' || $member->source_type === 'healthcare_barrier')
                                    <span class="badge badge-success">FGDs-Health Workers</span>
                                @elseif($member->source_type === 'bridging_the_gap')
                                    <span class="badge badge-primary">Attendance Participant</span>
                                @else
                                    <span class="badge badge-warning">{{ $member->source_type }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted" style="font-style: italic;">No IIT team members were selected for this session.</p>
        @endif
    </div>

    {{-- Action Plans Section --}}
    <div class="participants-section" style="margin-top: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
            <h3 style="margin: 0;">Action Plans ({{ $bridgingTheGap->actionPlans ? $bridgingTheGap->actionPlans->count() : 0 }})</h3>
            <button type="button" class="btn btn-sm btn-primary" onclick="openManageActionPlansModal({{ $bridgingTheGap->id }}, '{{ $bridgingTheGap->unique_id }}')">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Manage Action Plans
            </button>
        </div>
        @if($bridgingTheGap->actionPlans && $bridgingTheGap->actionPlans->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Problem</th>
                        <th>Solution</th>
                        <th>Action Needed</th>
                        <th>Responsible</th>
                        <th>Timeline</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bridgingTheGap->actionPlans->sortBy('serial_number') as $plan)
                        <tr>
                            <td>{{ $plan->serial_number }}</td>
                            <td>{{ $plan->problem }}</td>
                            <td>{{ $plan->solution ?? '-' }}</td>
                            <td>{{ $plan->action_needed ?? '-' }}</td>
                            <td>{{ $plan->who_is_responsible ?? '-' }}</td>
                            <td>{{ $plan->timeline ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted" style="font-style: italic;">No action plans have been added for this session.</p>
        @endif
    </div>

    {{-- Action Plans Management Modal --}}
    <dialog id="manageActionPlansModal" class="modal" style="max-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Manage Action Plans - <span id="manageApRecordId"></span></h3>
                <button type="button" class="modal-close" onclick="document.getElementById('manageActionPlansModal').close()">&times;</button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                {{-- Existing Action Plans --}}
                <div id="actionPlansList" style="margin-bottom: 20px;">
                    <div style="text-align: center; padding: 20px; color: #9ca3af;">Loading action plans...</div>
                </div>

                {{-- Add New Action Plan Form --}}
                <div style="border-top: 2px solid #e5e7eb; padding-top: 16px; margin-top: 16px;">
                    <h4 style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">Add New Action Plan</h4>
                    <div id="addActionPlanForm">
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
                    <form id="manageApUploadForm" enctype="multipart/form-data" style="display: flex; gap: 10px; align-items: flex-end;">
                        <div style="flex: 1;">
                            <input type="file" id="manageApFile" accept=".xlsx,.xls" class="form-input" style="width: 100%;">
                        </div>
                        <button type="button" class="btn btn-sm btn-success" onclick="uploadActionPlanExcel()">Upload Excel</button>
                    </form>
                    <p style="font-size: 11px; color: #9ca3af; margin-top: 6px;">Uploading an Excel file will replace all existing action plans for this record.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('manageActionPlansModal').close()">Close</button>
            </div>
        </div>
    </dialog>

    <script>
    let currentManageApRecordId = null;
    const baseUrl = '{{ url("admin/bridging-the-gap") }}';
    const csrfToken = '{{ csrf_token() }}';

    function openManageActionPlansModal(id, uniqueId) {
        currentManageApRecordId = id;
        document.getElementById('manageApRecordId').textContent = uniqueId;
        document.getElementById('manageActionPlansModal').showModal();
        loadActionPlans();
    }

    function loadActionPlans() {
        const listEl = document.getElementById('actionPlansList');
        listEl.innerHTML = '<div style="text-align: center; padding: 20px; color: #9ca3af;">Loading...</div>';

        fetch(baseUrl + '/' + currentManageApRecordId + '/action-plans', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.action_plans || data.action_plans.length === 0) {
                listEl.innerHTML = '<div style="text-align: center; padding: 16px; color: #9ca3af; font-style: italic;">No action plans yet.</div>';
                return;
            }
            let html = '<table class="data-table"><thead><tr><th style="width:40px">#</th><th>Problem</th><th>Solution</th><th>Action Needed</th><th>Responsible</th><th>Timeline</th><th style="width:100px">Actions</th></tr></thead><tbody>';
            data.action_plans.forEach(plan => {
                html += '<tr id="ap-row-' + plan.id + '">';
                html += '<td>' + (plan.serial_number || '-') + '</td>';
                html += '<td>' + escHtml(plan.problem) + '</td>';
                html += '<td>' + escHtml(plan.solution || '-') + '</td>';
                html += '<td>' + escHtml(plan.action_needed || '-') + '</td>';
                html += '<td>' + escHtml(plan.who_is_responsible || '-') + '</td>';
                html += '<td>' + escHtml(plan.timeline || '-') + '</td>';
                html += '<td class="action-buttons">';
                html += '<button class="btn btn-sm btn-outline" onclick="editActionPlan(' + plan.id + ')">Edit</button>';
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

        fetch(baseUrl + '/' + currentManageApRecordId + '/action-plans', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
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
                alert('Failed to add action plan.');
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

    function escAttr(str) {
        return str.replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
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

        fetch(baseUrl + '/action-plans/' + id, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(body),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                loadActionPlans();
            } else {
                alert('Failed to update action plan.');
            }
        })
        .catch(() => alert('Error updating action plan.'));
    }

    function deleteActionPlan(id) {
        if (!confirm('Are you sure you want to delete this action plan?')) return;

        fetch(baseUrl + '/action-plans/' + id, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                loadActionPlans();
            } else {
                alert('Failed to delete action plan.');
            }
        })
        .catch(() => alert('Error deleting action plan.'));
    }

    function uploadActionPlanExcel() {
        const fileInput = document.getElementById('manageApFile');
        if (!fileInput.files.length) { alert('Please select an Excel file.'); return; }

        if (!confirm('Uploading an Excel file will replace all existing action plans for this record. Continue?')) return;

        const formData = new FormData();
        formData.append('action_plan_file', fileInput.files[0]);
        formData.append('_token', csrfToken);

        fetch(baseUrl + '/' + currentManageApRecordId + '/action-plan', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        })
        .then(r => {
            if (r.redirected) {
                // The upload endpoint redirects — reload the page
                window.location.reload();
                return;
            }
            return r.json();
        })
        .then(data => {
            if (data && data.success) {
                fileInput.value = '';
                loadActionPlans();
            }
        })
        .catch(() => {
            // Redirect-based response — reload the page
            window.location.reload();
        });
    }
    </script>

    <h3 style="margin: 24px 0 16px; font-size: 16px; font-weight: 600; color: #374151;">Submission Metadata</h3>
    <div class="detail-grid">
        <div class="detail-item">
            <label>IP Address</label>
            <span>{{ $bridgingTheGap->ip_address ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Started At</label>
            <span>{{ $bridgingTheGap->started_at ? $bridgingTheGap->started_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Submitted At</label>
            <span>{{ $bridgingTheGap->submitted_at ? $bridgingTheGap->submitted_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Time to Complete</label>
            <span>
                @if($bridgingTheGap->started_at && $bridgingTheGap->submitted_at)
                    {{ $bridgingTheGap->started_at->diffForHumans($bridgingTheGap->submitted_at, true) }}
                @else
                    N/A
                @endif
            </span>
        </div>
        @if($bridgingTheGap->device_info)
        <div class="detail-item" style="grid-column: span 2;">
            <label>Device Info</label>
            <span style="font-family: monospace; font-size: 12px;">
                {{ $bridgingTheGap->device_info['platform'] ?? '' }} {{ $bridgingTheGap->device_info['os_version'] ?? '' }} |
                {{ $bridgingTheGap->device_info['device_brand'] ?? '' }} {{ $bridgingTheGap->device_info['device_model'] ?? '' }} |
                App v{{ $bridgingTheGap->device_info['app_version'] ?? '' }}
            </span>
        </div>
        @endif
    </div>
</div>

@endsection
