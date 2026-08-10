@extends('layouts.admin')

@section('title', 'Fixed Site Report')
@section('page-title', 'Fixed Site Report')

@section('content')
@php
    // Short date formatter for Carbon values (null-safe).
    $d = fn ($v) => $v ? $v->format('M d, Y') : '—';
@endphp

<div class="fsr-page">

    {{-- ============================ Selector ============================ --}}
    <div class="fsr-card fsr-selector fsr-no-print">
        <form method="GET" action="{{ route('admin.reports.fixed-site') }}" id="fsrForm" class="fsr-selector-form">
            <div class="fsr-field">
                <label for="fsrUc">Union Council</label>
                <select name="uc" id="fsrUc">
                    <option value="">— Select Union Council —</option>
                    @foreach ($unionCouncils as $uc)
                        <option value="{{ $uc }}" @selected($uc === $selectedUc)>{{ $uc }}</option>
                    @endforeach
                </select>
            </div>

            <div class="fsr-field">
                <label for="fsrFixSite">Fixed Site</label>
                <select name="fix_site" id="fsrFixSite" @disabled($selectedUc === '')>
                    <option value="">
                        {{ $selectedUc === '' ? 'Select a Union Council first' : '— Select Fixed Site —' }}
                    </option>
                    @foreach ($fixSites as $fs)
                        <option value="{{ $fs }}" @selected($fs === $selectedFixSite)>{{ $fs }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="fsr-btn fsr-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                View Report
            </button>
        </form>
        <p class="fsr-selector-hint">
            Pick a Union Council to load its fixed sites, then choose a fixed site to see every
            related FGD, Bridging the Gap session, vaccination record and child line list entry.
        </p>
    </div>

    @if (! $report)
        {{-- ========================= Empty state ========================= --}}
        <div class="fsr-card fsr-empty">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/>
            </svg>
            <h3>No fixed site selected</h3>
            <p>Choose a Union Council and a fixed site above to generate the consolidated report.</p>
        </div>
    @else
        @php $s = $report['summary']; @endphp

        {{-- ====================== Report header ========================= --}}
        <div class="fsr-card fsr-report-head">
            <div class="fsr-report-head-main">
                <div class="fsr-report-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                    </svg>
                </div>
                <div>
                    <div class="fsr-report-eyebrow">Fixed Site Report</div>
                    <h1>{{ $report['fix_site'] }}</h1>
                    <div class="fsr-report-meta">
                        <span><strong>UC:</strong> {{ $report['uc'] }}</span>
                        <span><strong>District:</strong> {{ $report['district'] ?: '—' }}</span>
                        <span><strong>Generated:</strong> {{ now()->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>
            <div class="fsr-report-actions fsr-no-print">
                <button type="button" class="fsr-btn fsr-btn-light" id="fsrExpandAll">Expand all</button>
                <button type="button" class="fsr-btn fsr-btn-light" id="fsrCollapseAll">Collapse all</button>
                <button type="button" class="fsr-btn fsr-btn-light" onclick="window.print()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"/>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                        <rect x="6" y="14" width="12" height="8"/>
                    </svg>
                    Print
                </button>
                <a href="{{ route('admin.reports.fixed-site.export', ['uc' => $report['uc'], 'fix_site' => $report['fix_site']]) }}"
                   class="fsr-btn fsr-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Export to Excel
                </a>
            </div>
        </div>

        {{-- ======================= Summary tiles ======================== --}}
        <div class="fsr-summary">
            <div class="fsr-tile fsr-tile-green">
                <span class="fsr-tile-value">{{ number_format($s['fgds_community']) }}</span>
                <span class="fsr-tile-label">FGDs — Community</span>
            </div>
            <div class="fsr-tile fsr-tile-amber">
                <span class="fsr-tile-value">{{ number_format($s['fgds_health_workers']) }}</span>
                <span class="fsr-tile-label">FGDs — Health Workers</span>
            </div>
            <div class="fsr-tile fsr-tile-pink">
                <span class="fsr-tile-value">{{ number_format($s['bridging']) }}</span>
                <span class="fsr-tile-label">Bridging the Gap</span>
            </div>
            <div class="fsr-tile fsr-tile-blue">
                <span class="fsr-tile-value">{{ number_format($s['vaccination']) }}</span>
                <span class="fsr-tile-label">Vaccination Records</span>
            </div>
            <div class="fsr-tile fsr-tile-indigo">
                <span class="fsr-tile-value">{{ number_format($s['child_line_list']) }}</span>
                <span class="fsr-tile-label">Children Listed</span>
            </div>
            <div class="fsr-tile fsr-tile-red">
                <span class="fsr-tile-value">{{ number_format($s['fgds_community_barriers'] + $s['fgds_health_workers_barriers']) }}</span>
                <span class="fsr-tile-label">Barriers Identified</span>
            </div>
            <div class="fsr-tile fsr-tile-purple">
                <span class="fsr-tile-value">{{ number_format($s['action_plans']) }}</span>
                <span class="fsr-tile-label">Action Plans</span>
            </div>
        </div>

        {{-- ======================= Jump navigation ====================== --}}
        <div class="fsr-jump fsr-no-print">
            <span>Jump to:</span>
            <a href="#sec-fgd-community">FGDs — Community</a>
            <a href="#sec-fgd-health">FGDs — Health Workers</a>
            <a href="#sec-bridging">Bridging the Gap</a>
            <a href="#sec-vaccination">Vaccination Records</a>
            <a href="#sec-cll">Child Line List</a>
        </div>

        {{-- ==================== FGDs — Community ========================= --}}
        <section class="fsr-card fsr-section" id="sec-fgd-community">
            <div class="fsr-section-head">
                <div class="fsr-section-title">
                    <h2>FGDs — Community</h2>
                    <span class="fsr-count">{{ $report['fgds_community']->count() }}</span>
                </div>
                <span class="fsr-scope fsr-scope-site">Site-specific</span>
            </div>
            <p class="fsr-section-note">Focus group discussions recorded at this fixed site.</p>

            @forelse ($report['fgds_community'] as $r)
                <div class="fsr-record">
                    <button type="button" class="fsr-record-head">
                        <span class="fsr-rh-main"><code>{{ $r->unique_id }}</code></span>
                        <span class="fsr-rh-cell">{{ $d($r->date) }}</span>
                        <span class="fsr-rh-cell">{{ $r->venue ?: '—' }}</span>
                        <span class="fsr-rh-cell"><span class="fsr-badge fsr-badge-primary">{{ (int) $r->participants_males + (int) $r->participants_females }} participants</span></span>
                        <span class="fsr-rh-cell"><span class="fsr-badge fsr-badge-warning">{{ $r->barriers->count() }} barriers</span></span>
                        <svg class="fsr-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="fsr-record-body">
                        @include('admin.reports.partials.kv', ['items' => [
                            'Form ID'         => $r->unique_id,
                            'Date'            => $d($r->date),
                            'Venue'           => $r->venue,
                            'District'        => $r->district,
                            'Union Council'   => $r->uc,
                            'Fixed Site'      => $r->fix_site,
                            'Outreach'        => $r->outreach,
                            'Community'       => is_array($r->community) ? implode(', ', $r->community) : $r->community,
                            'Males'           => $r->participants_males,
                            'Females'         => $r->participants_females,
                            'Total'           => (int) $r->participants_males + (int) $r->participants_females,
                            'Facilitator (TKF)' => $r->facilitator_tkf,
                            'Submitted By'    => $r->user->name ?? '—',
                            'Created'         => $d($r->created_at),
                        ]])

                        @if ($r->barriers->count())
                            <div class="fsr-subhead">Barriers ({{ $r->barriers->count() }})</div>
                            @foreach ($r->barriers->groupBy(fn ($b) => $b->category->name ?? 'Uncategorized') as $catName => $items)
                                <div class="fsr-barrier-group">
                                    <span class="fsr-barrier-cat">{{ $catName }}</span>
                                    <ul>
                                        @foreach ($items as $b)
                                            <li>{{ $b->barrier_text }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        @endif

                        @if ($r->participants->count())
                            <div class="fsr-subhead">Participants ({{ $r->participants->count() }})</div>
                            <div class="fsr-subtable-wrap">
                                <table class="fsr-subtable">
                                    <thead><tr><th>#</th><th>Name</th><th>Designation</th><th>Contact</th><th>CNIC</th><th>Gender</th></tr></thead>
                                    <tbody>
                                        @foreach ($r->participants as $p)
                                            <tr>
                                                <td>{{ $p->sr_no ?: $loop->iteration }}</td>
                                                <td>{{ $p->name ?: '—' }}</td>
                                                <td>{{ $p->designation ?: $p->title_designation ?: $p->occupation ?: '—' }}</td>
                                                <td>{{ $p->contact_no ?: '—' }}</td>
                                                <td>{{ $p->cnic ?: '—' }}</td>
                                                <td>{{ $p->gender ?: '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <a href="{{ route('admin.fgds-community.show', $r->id) }}" class="fsr-open-link" target="_blank">Open full record &rarr;</a>
                    </div>
                </div>
            @empty
                <div class="fsr-empty-row">No FGD — Community sessions recorded for this fixed site.</div>
            @endforelse
        </section>

        {{-- ================== FGDs — Health Workers ===================== --}}
        <section class="fsr-card fsr-section" id="sec-fgd-health">
            <div class="fsr-section-head">
                <div class="fsr-section-title">
                    <h2>FGDs — Health Workers</h2>
                    <span class="fsr-count">{{ $report['fgds_health_workers']->count() }}</span>
                </div>
                <span class="fsr-scope fsr-scope-site">Site-specific</span>
            </div>
            <p class="fsr-section-note">
                Health-worker focus group discussions held at this fixed site
                (matched on Health Facility).
            </p>

            @forelse ($report['fgds_health_workers'] as $r)
                <div class="fsr-record">
                    <button type="button" class="fsr-record-head">
                        <span class="fsr-rh-main"><code>{{ $r->unique_id }}</code></span>
                        <span class="fsr-rh-cell">{{ $d($r->date) }}</span>
                        <span class="fsr-rh-cell">{{ $r->hfs ?: '—' }}</span>
                        <span class="fsr-rh-cell"><span class="fsr-badge fsr-badge-primary">{{ (int) $r->participants_males + (int) $r->participants_females }} participants</span></span>
                        <span class="fsr-rh-cell"><span class="fsr-badge fsr-badge-warning">{{ $r->barriers->count() }} barriers</span></span>
                        <svg class="fsr-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="fsr-record-body">
                        @include('admin.reports.partials.kv', ['items' => [
                            'Form ID'           => $r->unique_id,
                            'Date'              => $d($r->date),
                            'Health Facility'   => $r->hfs,
                            'Address'           => $r->address,
                            'Union Council'     => $r->uc,
                            'Group Type'        => $r->group_type,
                            'Males'             => $r->participants_males,
                            'Females'           => $r->participants_females,
                            'Total'             => (int) $r->participants_males + (int) $r->participants_females,
                            'Facilitator (TKF)' => $r->facilitator_tkf,
                            'Submitted By'      => $r->user->name ?? '—',
                            'Created'           => $d($r->created_at),
                        ]])

                        @if ($r->barriers->count())
                            <div class="fsr-subhead">Barriers ({{ $r->barriers->count() }})</div>
                            @foreach ($r->barriers->groupBy(fn ($b) => $b->category->name ?? 'Uncategorized') as $catName => $items)
                                <div class="fsr-barrier-group">
                                    <span class="fsr-barrier-cat">{{ $catName }}</span>
                                    <ul>
                                        @foreach ($items as $b)
                                            <li>{{ $b->barrier_text }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        @endif

                        @if ($r->participants->count())
                            <div class="fsr-subhead">Participants ({{ $r->participants->count() }})</div>
                            <div class="fsr-subtable-wrap">
                                <table class="fsr-subtable">
                                    <thead><tr><th>#</th><th>Name</th><th>Designation</th><th>Contact</th><th>CNIC</th><th>Gender</th></tr></thead>
                                    <tbody>
                                        @foreach ($r->participants as $p)
                                            <tr>
                                                <td>{{ $p->sr_no ?: $loop->iteration }}</td>
                                                <td>{{ $p->name ?: '—' }}</td>
                                                <td>{{ $p->designation ?: $p->title_designation ?: $p->occupation ?: '—' }}</td>
                                                <td>{{ $p->contact_no ?: '—' }}</td>
                                                <td>{{ $p->cnic ?: '—' }}</td>
                                                <td>{{ $p->gender ?: '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <a href="{{ route('admin.fgds-health-workers.show', $r->id) }}" class="fsr-open-link" target="_blank">Open full record &rarr;</a>
                    </div>
                </div>
            @empty
                <div class="fsr-empty-row">No FGD — Health Workers sessions recorded for this Union Council.</div>
            @endforelse
        </section>

        {{-- ===================== Bridging the Gap ======================= --}}
        <section class="fsr-card fsr-section" id="sec-bridging">
            <div class="fsr-section-head">
                <div class="fsr-section-title">
                    <h2>Bridging the Gap</h2>
                    <span class="fsr-count">{{ $report['bridging']->count() }}</span>
                </div>
                <span class="fsr-scope fsr-scope-site">Site-specific</span>
            </div>
            <p class="fsr-section-note">Bridging the Gap sessions, action plans and IIT members for this fixed site.</p>

            @forelse ($report['bridging'] as $r)
                <div class="fsr-record">
                    <button type="button" class="fsr-record-head">
                        <span class="fsr-rh-main"><code>{{ $r->unique_id }}</code></span>
                        <span class="fsr-rh-cell">{{ $d($r->date) }}</span>
                        <span class="fsr-rh-cell">{{ $r->venue ?: '—' }}</span>
                        <span class="fsr-rh-cell"><span class="fsr-badge fsr-badge-primary">{{ $r->participants->count() }} attendance</span></span>
                        <span class="fsr-rh-cell">
                            @if ($r->actionPlans->count())
                                <span class="fsr-badge fsr-badge-purple">1 action plan ({{ $r->actionPlans->count() }} points)</span>
                            @else
                                <span class="fsr-badge fsr-badge-purple">No action plan</span>
                            @endif
                        </span>
                        <svg class="fsr-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="fsr-record-body">
                        @include('admin.reports.partials.kv', ['items' => [
                            'Form ID'       => $r->unique_id,
                            'Date'          => $d($r->date),
                            'Venue'         => $r->venue,
                            'District'      => $r->district,
                            'Union Council' => $r->uc,
                            'Fixed Site'    => $r->fix_site,
                            'Males'         => $r->participants_males,
                            'Females'       => $r->participants_females,
                            'Total'         => (int) $r->participants_males + (int) $r->participants_females,
                            'Submitted By'  => $r->user->name ?? '—',
                            'Created'       => $d($r->created_at),
                        ]])

                        @if ($r->actionPlans->count())
                            <div class="fsr-subhead">Action Plan — {{ $r->actionPlans->count() }} {{ \Illuminate\Support\Str::plural('point', $r->actionPlans->count()) }}</div>
                            <div class="fsr-subtable-wrap">
                                <table class="fsr-subtable">
                                    <thead><tr><th>#</th><th>Problem</th><th>Sub Cause</th><th>Root Cause</th><th>Solution</th><th>Action Needed</th><th>Responsible</th><th>Timeline</th></tr></thead>
                                    <tbody>
                                        @foreach ($r->actionPlans as $p)
                                            <tr>
                                                <td>{{ $p->serial_number ?: $loop->iteration }}</td>
                                                <td>{{ $p->problem ?: '—' }}</td>
                                                <td>{{ $p->sub_cause ?: '—' }}</td>
                                                <td>{{ $p->root_cause ?: '—' }}</td>
                                                <td>{{ $p->solution ?: '—' }}</td>
                                                <td>{{ $p->action_needed ?: '—' }}</td>
                                                <td>{{ $p->who_is_responsible ?: '—' }}</td>
                                                <td>{{ $p->timeline ?: '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @if ($r->teamMembers->count())
                            <div class="fsr-subhead">IIT Members ({{ $r->teamMembers->count() }})</div>
                            <div class="fsr-subtable-wrap">
                                <table class="fsr-subtable">
                                    <thead><tr><th>#</th><th>Name</th><th>Designation</th><th>Contact</th><th>CNIC</th><th>Source</th></tr></thead>
                                    <tbody>
                                        @foreach ($r->teamMembers as $m)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $m->participant->name ?? '—' }}</td>
                                                <td>{{ $m->participant->designation ?? $m->participant->title_designation ?? '—' }}</td>
                                                <td>{{ $m->participant->contact_no ?? '—' }}</td>
                                                <td>{{ $m->participant->cnic ?? '—' }}</td>
                                                <td>{{ $m->source_type ?: '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @if ($r->participants->count())
                            <div class="fsr-subhead">Attendance ({{ $r->participants->count() }})</div>
                            <div class="fsr-subtable-wrap">
                                <table class="fsr-subtable">
                                    <thead><tr><th>#</th><th>Name</th><th>Designation</th><th>Contact</th><th>CNIC</th><th>Gender</th></tr></thead>
                                    <tbody>
                                        @foreach ($r->participants as $p)
                                            <tr>
                                                <td>{{ $p->sr_no ?: $loop->iteration }}</td>
                                                <td>{{ $p->name ?: '—' }}</td>
                                                <td>{{ $p->designation ?: $p->title_designation ?: $p->occupation ?: '—' }}</td>
                                                <td>{{ $p->contact_no ?: '—' }}</td>
                                                <td>{{ $p->cnic ?: '—' }}</td>
                                                <td>{{ $p->gender ?: '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <a href="{{ route('admin.bridging-the-gap.show', $r->id) }}" class="fsr-open-link" target="_blank">Open full record &rarr;</a>
                    </div>
                </div>
            @empty
                <div class="fsr-empty-row">No Bridging the Gap sessions recorded for this fixed site.</div>
            @endforelse
        </section>

        {{-- ==================== Vaccination Records ===================== --}}
        <section class="fsr-card fsr-section" id="sec-vaccination">
            <div class="fsr-section-head">
                <div class="fsr-section-title">
                    <h2>Vaccination Records</h2>
                    <span class="fsr-count">{{ $report['vaccination']->count() }}</span>
                </div>
                <span class="fsr-scope fsr-scope-site">Site-specific</span>
            </div>
            <p class="fsr-section-note">
                {{ $s['vaccinated'] }} of {{ $s['vaccination'] }} children marked vaccinated for this fixed site.
            </p>

            @forelse ($report['vaccination'] as $r)
                <div class="fsr-record">
                    <button type="button" class="fsr-record-head">
                        <span class="fsr-rh-main"><code>{{ $r->unique_id }}</code></span>
                        <span class="fsr-rh-cell">{{ $r->child_name ?: '—' }}</span>
                        <span class="fsr-rh-cell">s/o {{ $r->father_name ?: '—' }}</span>
                        <span class="fsr-rh-cell"><span class="fsr-badge fsr-badge-info">{{ $r->category }}</span></span>
                        <span class="fsr-rh-cell">
                            <span class="fsr-badge {{ $r->vaccinated === 'YES' ? 'fsr-badge-success' : 'fsr-badge-danger' }}">
                                {{ $r->vaccinated === 'YES' ? 'Vaccinated' : 'Not vaccinated' }}
                            </span>
                        </span>
                        <svg class="fsr-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="fsr-record-body">
                        @include('admin.reports.partials.kv', ['items' => [
                            'Form ID'              => $r->unique_id,
                            'Serial No'            => $r->serial_number,
                            'Child Name'           => $r->child_name,
                            'Father Name'          => $r->father_name,
                            'Age'                  => $r->age,
                            'Address'              => $r->address,
                            'Contact'              => $r->contact_number,
                            'Union Council'        => $r->uc,
                            'Fixed Site'           => $r->fix_site,
                            'District'             => $r->district,
                            'Category'             => $r->category,
                            'Vaccinated'           => $r->vaccinated,
                            'Date of Vaccination'  => $d($r->date_of_vaccination),
                            'Community Member'     => $r->community_member_name,
                            'CM Contact'           => $r->community_member_contact,
                            'GPS'                  => $r->gps_coordinates,
                            'Submitted By'         => $r->user->name ?? '—',
                            'Created'              => $d($r->created_at),
                        ]])
                        <a href="{{ route('admin.vaccination-records.show', $r->id) }}" class="fsr-open-link" target="_blank">Open full record &rarr;</a>
                    </div>
                </div>
            @empty
                <div class="fsr-empty-row">No vaccination records recorded for this fixed site.</div>
            @endforelse
        </section>

        {{-- ====================== Child Line List ======================= --}}
        <section class="fsr-card fsr-section" id="sec-cll">
            <div class="fsr-section-head">
                <div class="fsr-section-title">
                    <h2>Child Line List</h2>
                    <span class="fsr-count">{{ $report['child_line_list']->count() }}</span>
                </div>
                <span class="fsr-scope {{ $report['cll_site_specific'] ? 'fsr-scope-site' : 'fsr-scope-uc' }}">
                    {{ $report['cll_site_specific'] ? 'Site-specific' : 'UC level' }}
                </span>
            </div>
            <p class="fsr-section-note">
                @if ($report['cll_site_specific'])
                    Children matched to this fixed site through its outreach sites
                    ({{ implode(', ', $report['outreach_sites']) }}).
                @else
                    No outreach sites are mapped to this fixed site in the catalogue, so all child
                    line list entries for Union Council <strong>{{ $report['uc'] }}</strong> are shown.
                @endif
            </p>

            @forelse ($report['child_line_list'] as $r)
                <div class="fsr-record">
                    <button type="button" class="fsr-record-head">
                        <span class="fsr-rh-main"><code>{{ $r->unique_id }}</code></span>
                        <span class="fsr-rh-cell">{{ $r->child_name ?: '—' }}</span>
                        <span class="fsr-rh-cell">s/o {{ $r->father_name ?: '—' }}</span>
                        <span class="fsr-rh-cell"><span class="fsr-badge fsr-badge-info">{{ $r->type ?: '—' }}</span></span>
                        <span class="fsr-rh-cell">{{ $r->age_in_months !== null ? $r->age_in_months . ' mo' : '—' }}</span>
                        <svg class="fsr-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="fsr-record-body">
                        @include('admin.reports.partials.kv', ['items' => [
                            'Form ID'          => $r->unique_id,
                            'Division'         => $r->division,
                            'District'         => $r->district,
                            'Town'             => $r->town,
                            'Union Council'    => $r->uc,
                            'Outreach'         => $r->outreach,
                            'Child Name'       => $r->child_name,
                            'Father Name'      => $r->father_name,
                            'Gender'           => ucfirst((string) $r->gender),
                            'Date of Birth'    => $d($r->date_of_birth),
                            'Age (months)'     => $r->age_in_months,
                            'Vaccinator'       => $r->vaccinator_name,
                            'IIT Member'       => $r->iit_member_name,
                            'IIT Contact'      => $r->iit_member_contact,
                            'Father CNIC'      => $r->father_cnic,
                            'House #'          => $r->house_number,
                            'Address'          => $r->address,
                            'Guardian Phone'   => $r->guardian_phone,
                            'Type'             => $r->type,
                            'Missed Vaccines'  => is_array($r->missed_vaccines) ? implode(', ', $r->missed_vaccines) : $r->missed_vaccines,
                            'Reasons of Missing' => $r->reasons_of_missing,
                            'Plan for Coverage'  => $r->plan_for_coverage,
                            'Date of Coverage' => $d($r->date_of_coverage),
                            'Created'          => $d($r->created_at),
                        ]])
                        <a href="{{ route('admin.child-line-list.show', $r->id) }}" class="fsr-open-link" target="_blank">Open full record &rarr;</a>
                    </div>
                </div>
            @empty
                <div class="fsr-empty-row">No child line list entries found for this fixed site.</div>
            @endforelse
        </section>
    @endif
</div>

@push('styles')
@vite('resources/css/admin/fixed-site-report.css')
@endpush

@include('admin.reports.partials.scripts')
@endsection
