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

    @php
        $iitParticipantIds = $bridgingTheGap->teamMembers->pluck('participant_id')->all();
        $nonIitAttendees = $bridgingTheGap->participants->reject(fn ($p) => in_array($p->id, $iitParticipantIds))->values();
        $personJson = fn ($p) => json_encode([
            'name' => $p->name,
            'gender' => $p->gender,
            'title_designation' => $p->title_designation ?: $p->designation,
            'occupation' => $p->occupation,
            'contact_no' => $p->contact_no,
            'cnic' => $p->cnic,
            'address' => $p->address,
        ]);
        $plans = $bridgingTheGap->actionPlans->sortBy('serial_number');
        $planStatuses = \App\Models\BridgingTheGapActionPlan::STATUSES;
    @endphp

    @if ($errors->any())
        <div class="alert alert-error" style="margin-top: 16px; padding: 12px 16px; border-radius: 8px; background: #fee2e2; color: #991b1b;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- ========================= Attendance sheet ========================= --}}
    <div class="participants-section" id="attendance">
        <div class="section-head">
            <h3>Attendance Sheet ({{ $bridgingTheGap->participants->count() }})</h3>
            <button type="button" class="btn btn-sm btn-primary" onclick="openPersonModal('add')">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Participant
            </button>
        </div>
        @if ($bridgingTheGap->participants->count() > 0)
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Sr. No</th>
                            <th>Name</th>
                            <th>Role / Designation</th>
                            <th>Occupation</th>
                            <th>Contact</th>
                            <th>Gender</th>
                            <th>IIT Member</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bridgingTheGap->participants as $participant)
                            @php $isIit = in_array($participant->id, $iitParticipantIds); @endphp
                            <tr>
                                <td>{{ $participant->sr_no ?? '—' }}</td>
                                <td>{{ $participant->name }}</td>
                                <td>{{ ($participant->title_designation ?: $participant->designation) ?: '—' }}</td>
                                <td>{{ $participant->occupation ?: '—' }}</td>
                                <td>{{ $participant->contact_no ?: '—' }}</td>
                                <td>{{ $participant->gender ?: '—' }}</td>
                                <td>
                                    @if ($isIit)
                                        <span class="badge badge-success">Yes</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                                <td class="action-buttons">
                                    <form action="{{ route('admin.bridging-the-gap.toggle-iit', ['bridgingTheGap' => $bridgingTheGap->id, 'participant' => $participant->id]) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @if ($isIit)
                                            <button type="submit" class="btn btn-sm btn-outline" title="Remove from IIT team">Remove IIT</button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-success" title="Mark as IIT team member">Mark as IIT</button>
                                        @endif
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline"
                                            data-person="{{ $personJson($participant) }}"
                                            data-url="{{ route('admin.bridging-the-gap.participants.update', ['bridgingTheGap' => $bridgingTheGap->id, 'participant' => $participant->id]) }}"
                                            onclick="openPersonModal('edit', this)">Edit</button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                            onclick="submitRowAction(@js(route('admin.bridging-the-gap.participants.destroy', ['bridgingTheGap' => $bridgingTheGap->id, 'participant' => $participant->id])), 'DELETE', @js('Remove ' . $participant->name . ' from the attendance sheet?' . ($isIit ? ' They will also be removed from the IIT team.' : '')))">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted" style="font-style: italic;">No attendance has been recorded for this session yet.</p>
        @endif
    </div>

    {{-- ========================= IIT team members ========================= --}}
    <div class="participants-section" id="iit-team" style="margin-top: 24px;">
        <div class="section-head">
            <div>
                <h3>IIT Team Members ({{ $bridgingTheGap->teamMembers->count() }})</h3>
                <p class="text-muted" style="margin: 4px 0 0;">Members of this session's Immunization Improvement Team, chosen from its attendance sheet.</p>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('admin.iit-members.index', ['bridging_the_gap_id' => $bridgingTheGap->id]) }}" class="btn btn-sm btn-outline">All IIT Members</a>
                <button type="button" class="btn btn-sm btn-primary" onclick="openIitAddModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add IIT Member
                </button>
            </div>
        </div>
        @if ($bridgingTheGap->teamMembers->count() > 0)
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Role / Designation</th>
                            <th>Contact</th>
                            <th>CNIC</th>
                            <th>Gender</th>
                            <th>Source</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bridgingTheGap->teamMembers as $index => $member)
                            @php $person = $member->participant; @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $person->name ?? 'Participant Deleted' }}</td>
                                <td>{{ $person?->role ?: '—' }}</td>
                                <td>{{ $person->contact_no ?? '—' }}</td>
                                <td>{{ $person->cnic ?? '—' }}</td>
                                <td>{{ $person->gender ?? '—' }}</td>
                                <td>
                                    @php $source = \App\Http\Controllers\Admin\IitMemberController::sourceLabel($member->source_type); @endphp
                                    <span class="badge {{ $source === 'Attendance' ? 'badge-primary' : 'badge-info' }}">{{ $source }}</span>
                                </td>
                                <td class="action-buttons">
                                    @if ($person)
                                        <button type="button" class="btn btn-sm btn-outline"
                                                data-person="{{ $personJson($person) }}"
                                                data-url="{{ route('admin.iit-members.update', $member) }}"
                                                onclick="openPersonModal('edit', this)">Edit</button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-danger"
                                            onclick="submitRowAction(@js(route('admin.iit-members.destroy', $member)), 'DELETE', @js('Remove ' . ($person->name ?? 'this member') . ' from the IIT team? They will stay on the attendance sheet.'))">Remove</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted" style="font-style: italic;">No IIT team members have been selected for this session.</p>
        @endif
    </div>

    {{-- ============================ Action plans ============================ --}}
    <div class="participants-section" id="action-plans" style="margin-top: 24px;">
        <div class="section-head">
            <h3>Action Plans ({{ $plans->count() }})</h3>
            <button type="button" class="btn btn-sm btn-primary" onclick="openActionPlanModal({{ $bridgingTheGap->id }}, @js($bridgingTheGap->unique_id))">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Manage / Update Action Plans
            </button>
        </div>
        @if ($plans->count() > 0)
            <div class="ap-summary">
                @foreach ($planStatuses as $statusKey => $statusLabel)
                    <span class="ap-status ap-status-{{ $statusKey }}">{{ $statusLabel }}: {{ $plans->where('implementation_status', $statusKey)->count() }}</span>
                @endforeach
            </div>
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Problem</th>
                            <th>Sub Cause</th>
                            <th>Root Cause</th>
                            <th>Solution</th>
                            <th>Action Needed</th>
                            <th>Responsible</th>
                            <th>Timeline</th>
                            <th>Status</th>
                            <th>Follow-up Remarks</th>
                            <th>Activity Pictures</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($plans as $plan)
                            <tr>
                                <td>{{ $plan->serial_number }}</td>
                                <td class="cell-clamp">{{ $plan->problem }}</td>
                                <td class="cell-clamp">{{ $plan->sub_cause ?? '—' }}</td>
                                <td class="cell-clamp">{{ $plan->root_cause ?? '—' }}</td>
                                <td class="cell-clamp">{{ $plan->solution ?? '—' }}</td>
                                <td class="cell-clamp">{{ $plan->action_needed ?? '—' }}</td>
                                <td>{{ $plan->who_is_responsible ?? '—' }}</td>
                                <td>{{ $plan->timeline ?? '—' }}</td>
                                <td>
                                    <span class="ap-status ap-status-{{ $plan->implementation_status }}">{{ $plan->implementation_status_label }}</span>
                                    @if ($plan->status_updated_at)
                                        <span class="ap-status-meta">Updated {{ $plan->status_updated_at->format('M d, Y') }}</span>
                                    @endif
                                </td>
                                <td class="cell-clamp">{{ $plan->implementation_remarks ?: '—' }}</td>
                                <td>
                                    @if ($plan->photos->count())
                                        <div class="ap-photos">
                                            @foreach ($plan->photos as $photo)
                                                <a class="ap-thumb ap-thumb-sm" href="{{ $photo->url }}" target="_blank" rel="noopener" title="{{ $photo->original_name }}">
                                                    <img src="{{ $photo->url }}" alt="Activity picture" loading="lazy">
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted" style="font-style: italic;">No action plans have been added for this session.</p>
        @endif
    </div>

    @include('admin.core-forms.bridging-the-gap.partials.action-plans-modal')

    {{-- ===================== Add / edit person dialog ===================== --}}
    <dialog id="personModal" class="modal modal-md">
        <div class="modal-content">
            <form id="personForm" method="POST" action="{{ route('admin.bridging-the-gap.participants.store', $bridgingTheGap) }}">
                @csrf
                <input type="hidden" name="_method" id="personFormMethod" value="POST">
                <div class="modal-header">
                    <h3 id="personModalTitle">Add Participant</h3>
                    <button type="button" class="modal-close" onclick="this.closest('dialog').close()">&times;</button>
                </div>
                <div class="modal-body">
                    @include('admin.core-forms.bridging-the-gap.partials.person-fields', ['prefix' => 'pm'])
                    <label class="person-mode" id="personMarkIitWrap" style="margin: 4px 0 0;">
                        <input type="checkbox" name="mark_iit" value="1" id="personMarkIit"> Also add as an IIT team member
                    </label>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- ======================= Add IIT member dialog ======================= --}}
    <dialog id="iitAddModal" class="modal modal-md">
        <div class="modal-content">
            <form id="iitAddForm" method="POST" action="{{ route('admin.iit-members.store') }}">
                @csrf
                <input type="hidden" name="bridging_the_gap_id" value="{{ $bridgingTheGap->id }}">
                <div class="modal-header">
                    <h3>Add IIT Team Member — {{ $bridgingTheGap->unique_id }}</h3>
                    <button type="button" class="modal-close" onclick="this.closest('dialog').close()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="person-mode">
                        <label><input type="radio" name="iit_mode" value="existing" onchange="setIitMode(this.value)" @checked($nonIitAttendees->count() > 0) @disabled($nonIitAttendees->count() === 0)> Choose from attendance sheet</label>
                        <label><input type="radio" name="iit_mode" value="new" onchange="setIitMode(this.value)" @checked($nonIitAttendees->count() === 0)> Register a new person</label>
                    </div>

                    <div class="person-block" data-iit-block="existing">
                        @if ($nonIitAttendees->count() > 0)
                            <label class="form-label" for="iitParticipant">Person *</label>
                            <select name="participant_id" id="iitParticipant" class="form-input" required>
                                <option value="">— Select from this session's attendance —</option>
                                @foreach ($nonIitAttendees as $attendee)
                                    <option value="{{ $attendee->id }}">
                                        {{ $attendee->name }}{{ $attendee->role ? ' — ' . $attendee->role : '' }}{{ $attendee->contact_no ? ' — ' . $attendee->contact_no : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="form-hint">Only people on this session's attendance sheet can be its IIT members.</p>
                        @endif
                    </div>

                    <div class="person-block" data-iit-block="new" hidden>
                        <p class="form-hint" style="margin: 0 0 10px;">The person is added to this session's attendance sheet and to its IIT team.</p>
                        @include('admin.core-forms.bridging-the-gap.partials.person-fields', ['prefix' => 'iitNew'])
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add to IIT Team</button>
                </div>
            </form>
        </div>
    </dialog>

    <form id="rowActionForm" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="_method" value="DELETE">
    </form>

    <script>
    (function () {
        const personModal = document.getElementById('personModal');
        const personForm = document.getElementById('personForm');
        const storeUrl = personForm.getAttribute('action');

        window.openPersonModal = function (mode, button) {
            personForm.reset();
            const isEdit = mode === 'edit';
            document.getElementById('personModalTitle').textContent = isEdit ? 'Edit Person' : 'Add Participant';
            document.getElementById('personFormMethod').value = isEdit ? 'PUT' : 'POST';
            document.getElementById('personMarkIitWrap').hidden = isEdit;
            personForm.setAttribute('action', isEdit ? button.dataset.url : storeUrl);

            if (isEdit) {
                const person = JSON.parse(button.dataset.person || '{}');
                personForm.querySelectorAll('[data-field]').forEach(function (el) {
                    el.value = person[el.dataset.field] || '';
                });
            }
            personModal.showModal();
        };

        window.submitRowAction = function (url, method, message) {
            if (message && !confirm(message)) return;
            const form = document.getElementById('rowActionForm');
            form.setAttribute('action', url);
            form.querySelector('[name="_method"]').value = method;
            form.submit();
        };

        // Only the visible block's inputs are enabled, so hidden fields are
        // neither submitted nor validated by the browser.
        window.setIitMode = function (mode) {
            document.querySelectorAll('#iitAddForm [data-iit-block]').forEach(function (block) {
                const active = block.dataset.iitBlock === mode;
                block.hidden = !active;
                block.querySelectorAll('input, select, textarea').forEach(function (el) { el.disabled = !active; });
            });
        };

        window.openIitAddModal = function () {
            const checked = document.querySelector('#iitAddForm [name="iit_mode"]:checked');
            setIitMode(checked ? checked.value : 'new');
            document.getElementById('iitAddModal').showModal();
        };
    })();
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
