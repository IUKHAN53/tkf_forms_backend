@extends('layouts.admin')

@section('title', 'IIT Members')

@include('admin.core-forms.partials.styles')

@section('content')
<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card stat-card-primary">
        <div class="stat-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-value">{{ number_format($stats['total']) }}</span>
            <span class="stat-card-label">IIT Members</span>
        </div>
    </div>

    <div class="stat-card stat-card-purple">
        <div class="stat-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
            </svg>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-value">{{ number_format($stats['sessions']) }}</span>
            <span class="stat-card-label">BTG Sessions with a Team</span>
        </div>
    </div>

    <div class="stat-card stat-card-cyan">
        <div class="stat-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
            </svg>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-value">{{ number_format($stats['ucs']) }}</span>
            <span class="stat-card-label">UCs Covered</span>
        </div>
    </div>

    <div class="stat-card stat-card-info">
        <div class="stat-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"/><path d="M12 2v4m0 12v4M2 12h4m12 0h4"/>
            </svg>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-value">{{ number_format($stats['males']) }}</span>
            <span class="stat-card-label">Male Members</span>
        </div>
    </div>

    <div class="stat-card stat-card-success">
        <div class="stat-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"/><line x1="12" y1="22" x2="12" y2="19"/><path d="M9 19h6"/>
            </svg>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-value">{{ number_format($stats['females']) }}</span>
            <span class="stat-card-label">Female Members</span>
        </div>
    </div>

    <div class="stat-card stat-card-warning">
        <div class="stat-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/>
            </svg>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-value">{{ $stats['avg_per_session'] }}</span>
            <span class="stat-card-label">Avg. Members per Session</span>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>IIT Members</h2>
            <p class="text-muted">Immunization Improvement Team members from Bridging The Gap sessions. Each member belongs to one session and is chosen from that session's attendance sheet.</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.iit-members.export', request()->query()) }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Export CSV
            </a>
            <button type="button" class="btn btn-primary" onclick="openAddMember()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add Member
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div style="margin: 0 0 16px; padding: 12px 16px; border-radius: 8px; background: #fee2e2; color: #991b1b;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card-filters">
        <form action="{{ route('admin.iit-members.index') }}" method="GET" class="filter-form">
            <div class="filter-row">
                <input type="text" name="search" class="form-input" placeholder="Search by name, contact, CNIC, role or occupation..." value="{{ request('search') }}">
                <select name="district" class="form-input filter-select">
                    <option value="">All Districts</option>
                    @foreach ($districts as $district)
                        <option value="{{ $district }}" @selected(request('district') == $district)>{{ $district }}</option>
                    @endforeach
                </select>
                <select name="uc" class="form-input filter-select">
                    <option value="">All UCs</option>
                    @foreach ($ucs as $uc)
                        <option value="{{ $uc }}" @selected(request('uc') == $uc)>{{ $uc }}</option>
                    @endforeach
                </select>
                <select name="fix_site" class="form-input filter-select">
                    <option value="">All Fix Sites</option>
                    @foreach ($fixSites as $fixSite)
                        <option value="{{ $fixSite }}" @selected(request('fix_site') == $fixSite)>{{ $fixSite }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-row">
                <select name="bridging_the_gap_id" class="form-input filter-select">
                    <option value="">All BTG Sessions</option>
                    @foreach ($sessions as $session)
                        <option value="{{ $session->id }}" @selected(request('bridging_the_gap_id') == $session->id)>
                            {{ $session->unique_id }} — {{ $session->uc }}{{ $session->date ? ' — ' . $session->date->format('M d, Y') : '' }}
                        </option>
                    @endforeach
                </select>
                <select name="gender" class="form-input filter-select">
                    <option value="">All Genders</option>
                    <option value="Male" @selected(request('gender') === 'Male')>Male</option>
                    <option value="Female" @selected(request('gender') === 'Female')>Female</option>
                </select>
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                @if (request()->hasAny(['search', 'district', 'uc', 'fix_site', 'bridging_the_gap_id', 'gender']))
                    <a href="{{ route('admin.iit-members.index') }}" class="btn btn-outline">Clear All</a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-container table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Role / Designation</th>
                    <th>Contact</th>
                    <th>CNIC</th>
                    <th>Gender</th>
                    <th>BTG Session</th>
                    <th>UC</th>
                    <th>Fix Site</th>
                    <th>Source</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($members as $member)
                    @php
                        $person = $member->participant;
                        $session = $member->bridgingTheGap;
                        $source = \App\Http\Controllers\Admin\IitMemberController::sourceLabel($member->source_type);
                    @endphp
                    <tr>
                        <td>{{ $members->firstItem() + $loop->index }}</td>
                        <td><strong>{{ $person->name }}</strong></td>
                        <td>{{ $person->role ?: '—' }}</td>
                        <td>{{ $person->contact_no ?: '—' }}</td>
                        <td>{{ $person->cnic ?: '—' }}</td>
                        <td>
                            @if ($person->gender)
                                <span class="badge {{ $person->gender === 'Male' ? 'badge-info' : 'badge-success' }}">{{ $person->gender }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if ($session)
                                <a href="{{ route('admin.bridging-the-gap.show', $session) }}#iit-team"><code>{{ $session->unique_id }}</code></a>
                                @if ($session->date)
                                    <span class="ap-status-meta">{{ $session->date->format('M d, Y') }}</span>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $session->uc ?? '—' }}</td>
                        <td>{{ $session->fix_site ?? '—' }}</td>
                        <td><span class="badge {{ $source === 'Attendance' ? 'badge-primary' : 'badge-info' }}">{{ $source }}</span></td>
                        <td class="action-buttons">
                            <button type="button" class="btn btn-sm btn-outline"
                                    data-url="{{ route('admin.iit-members.update', $member) }}"
                                    data-person="{{ json_encode([
                                        'name' => $person->name,
                                        'gender' => $person->gender,
                                        'title_designation' => $person->title_designation ?: $person->designation,
                                        'occupation' => $person->occupation,
                                        'contact_no' => $person->contact_no,
                                        'cnic' => $person->cnic,
                                        'address' => $person->address,
                                    ]) }}"
                                    onclick="openEditMember(this)">Edit</button>
                            <button type="button" class="btn btn-sm btn-danger"
                                    onclick="removeMember(@js(route('admin.iit-members.destroy', $member)), @js($person->name))">Remove</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted">No IIT members found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        {{ $members->links() }}
    </div>
</div>

{{-- ============================ Add member ============================ --}}
<dialog id="addMemberModal" class="modal modal-md">
    <div class="modal-content">
        <form id="addMemberForm" method="POST" action="{{ route('admin.iit-members.store') }}">
            @csrf
            <div class="modal-header">
                <h3>Add IIT Team Member</h3>
                <button type="button" class="modal-close" onclick="this.closest('dialog').close()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="addSession">Bridging The Gap session *</label>
                    <select name="bridging_the_gap_id" id="addSession" class="form-input" required onchange="loadCandidates()">
                        <option value="">— Select a session —</option>
                        @foreach ($sessions->groupBy(fn ($s) => $s->uc ?: 'No UC') as $ucName => $ucSessions)
                            <optgroup label="{{ $ucName }}">
                                @foreach ($ucSessions as $session)
                                    <option value="{{ $session->id }}" @selected(request('bridging_the_gap_id') == $session->id)>
                                        {{ $session->unique_id }}{{ $session->fix_site ? ' — ' . $session->fix_site : '' }}{{ $session->date ? ' — ' . $session->date->format('M d, Y') : '' }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <p class="form-hint">Members can only be chosen from the selected session's attendance sheet.</p>
                </div>

                <div class="person-mode" id="addModeWrap" hidden>
                    <label><input type="radio" name="iit_mode" value="existing" onchange="setAddMode(this.value)"> Choose from attendance sheet</label>
                    <label><input type="radio" name="iit_mode" value="new" onchange="setAddMode(this.value)"> Register a new person</label>
                </div>

                <div class="person-block" data-add-block="existing" hidden>
                    <label class="form-label" for="addParticipant">Person *</label>
                    <select name="participant_id" id="addParticipant" class="form-input" required></select>
                    <p class="form-hint" id="addCandidateHint"></p>
                </div>

                <div class="person-block" data-add-block="new" hidden>
                    <p class="form-hint" style="margin: 0 0 10px;">The person is added to the session's attendance sheet and to its IIT team.</p>
                    @include('admin.core-forms.bridging-the-gap.partials.person-fields', ['prefix' => 'addNew'])
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="addMemberSubmit" disabled>Add to IIT Team</button>
            </div>
        </form>
    </div>
</dialog>

{{-- ============================ Edit member ============================ --}}
<dialog id="editMemberModal" class="modal modal-md">
    <div class="modal-content">
        <form id="editMemberForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h3>Edit IIT Member</h3>
                <button type="button" class="modal-close" onclick="this.closest('dialog').close()">&times;</button>
            </div>
            <div class="modal-body">
                @include('admin.core-forms.bridging-the-gap.partials.person-fields', ['prefix' => 'edit'])
                <p class="form-hint">Changes apply to this person everywhere they appear, including the session's attendance sheet.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</dialog>

<form id="removeMemberForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
(function () {
    const CANDIDATES_URL = @json(route('admin.iit-members.candidates'));
    const sessionSelect = document.getElementById('addSession');
    const participantSelect = document.getElementById('addParticipant');
    const modeWrap = document.getElementById('addModeWrap');
    const submitBtn = document.getElementById('addMemberSubmit');
    const hint = document.getElementById('addCandidateHint');

    function esc(str) {
        const div = document.createElement('div');
        div.textContent = str == null ? '' : String(str);
        return div.innerHTML;
    }

    // Only the visible block's inputs are enabled, so hidden fields are
    // neither submitted nor validated by the browser.
    window.setAddMode = function (mode) {
        document.querySelectorAll('#addMemberForm [data-add-block]').forEach(function (block) {
            const active = block.dataset.addBlock === mode;
            block.hidden = !active;
            block.querySelectorAll('input, select, textarea').forEach(function (el) { el.disabled = !active; });
        });
        const radio = document.querySelector('#addMemberForm [name="iit_mode"][value="' + mode + '"]');
        if (radio) radio.checked = true;
    };

    function resetAddForm() {
        modeWrap.hidden = true;
        submitBtn.disabled = true;
        document.querySelectorAll('#addMemberForm [data-add-block]').forEach(function (block) {
            block.hidden = true;
            block.querySelectorAll('input, select, textarea').forEach(function (el) { el.disabled = true; });
        });
    }

    window.loadCandidates = async function () {
        resetAddForm();
        if (!sessionSelect.value) return;

        participantSelect.innerHTML = '<option value="">Loading…</option>';
        try {
            const res = await fetch(CANDIDATES_URL + '?bridging_the_gap_id=' + encodeURIComponent(sessionSelect.value), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await res.json();
            const people = data.candidates || [];

            participantSelect.innerHTML = '<option value="">— Select from this session\'s attendance —</option>' +
                people.map(function (p) {
                    const extra = [p.role, p.contact_no].filter(Boolean).join(' — ');
                    return '<option value="' + p.id + '">' + esc(p.name) + (extra ? ' — ' + esc(extra) : '') + '</option>';
                }).join('');

            const existingRadio = document.querySelector('#addMemberForm [name="iit_mode"][value="existing"]');
            existingRadio.disabled = people.length === 0;
            hint.textContent = people.length
                ? people.length + ' person(s) on the attendance sheet are not yet IIT members.'
                : '';

            modeWrap.hidden = false;
            submitBtn.disabled = false;
            setAddMode(people.length ? 'existing' : 'new');
        } catch (e) {
            participantSelect.innerHTML = '<option value="">Failed to load attendance</option>';
        }
    };

    window.openAddMember = function () {
        document.getElementById('addMemberForm').reset();
        if (@json((bool) request('bridging_the_gap_id'))) {
            sessionSelect.value = @json((string) request('bridging_the_gap_id'));
        }
        loadCandidates();
        document.getElementById('addMemberModal').showModal();
    };

    window.openEditMember = function (button) {
        const form = document.getElementById('editMemberForm');
        form.reset();
        form.setAttribute('action', button.dataset.url);
        const person = JSON.parse(button.dataset.person || '{}');
        form.querySelectorAll('[data-field]').forEach(function (el) {
            el.value = person[el.dataset.field] || '';
        });
        document.getElementById('editMemberModal').showModal();
    };

    window.removeMember = function (url, name) {
        if (!confirm('Remove ' + name + ' from the IIT team?\n\nThey will stay on the session\'s attendance sheet.')) return;
        const form = document.getElementById('removeMemberForm');
        form.setAttribute('action', url);
        form.submit();
    };
})();
</script>
@endsection
