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
                                    <thead><tr><th>#</th><th>Problem</th><th>Solution</th><th>Action Needed</th><th>Responsible</th><th>Timeline</th></tr></thead>
                                    <tbody>
                                        @foreach ($r->actionPlans as $p)
                                            <tr>
                                                <td>{{ $p->serial_number ?: $loop->iteration }}</td>
                                                <td>{{ $p->problem ?: '—' }}</td>
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

<style>
.fsr-page { display: flex; flex-direction: column; gap: 20px; }

.fsr-card {
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
}

/* ----- Selector ----- */
.fsr-selector { padding: 20px 24px; }
.fsr-selector-form { display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap; }
.fsr-field { display: flex; flex-direction: column; gap: 6px; }
.fsr-field label {
    font-size: 12px; font-weight: 600; color: var(--gray-600);
    text-transform: uppercase; letter-spacing: 0.5px;
}
.fsr-field select {
    padding: 10px 12px; border: 1px solid var(--gray-300);
    border-radius: var(--radius); font-size: 14px; min-width: 240px;
    background: #fff; color: var(--gray-800); font-family: inherit;
}
.fsr-field select:disabled { background: var(--gray-100); color: var(--gray-400); }
.fsr-selector-hint { margin-top: 12px; font-size: 13px; color: var(--gray-500); }

/* ----- Buttons ----- */
.fsr-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 16px; border-radius: var(--radius);
    font-size: 14px; font-weight: 600; cursor: pointer;
    border: 1px solid transparent; text-decoration: none;
    transition: all var(--transition); font-family: inherit;
}
.fsr-btn svg { width: 16px; height: 16px; }
.fsr-btn-primary { background: var(--primary-600); color: #fff; }
.fsr-btn-primary:hover { background: var(--primary-700); }
.fsr-btn-light { background: var(--gray-100); color: var(--gray-700); border-color: var(--gray-200); }
.fsr-btn-light:hover { background: var(--gray-200); }

/* ----- Empty state ----- */
.fsr-empty { padding: 56px 24px; text-align: center; }
.fsr-empty svg { width: 56px; height: 56px; color: var(--gray-300); margin-bottom: 14px; }
.fsr-empty h3 { font-size: 18px; color: var(--gray-700); margin-bottom: 6px; }
.fsr-empty p { font-size: 14px; color: var(--gray-500); }

/* ----- Report header ----- */
.fsr-report-head {
    padding: 24px; display: flex; justify-content: space-between;
    align-items: center; gap: 20px; flex-wrap: wrap;
}
.fsr-report-head-main { display: flex; align-items: center; gap: 18px; }
.fsr-report-icon {
    width: 60px; height: 60px; flex-shrink: 0;
    border-radius: var(--radius-md);
    background: linear-gradient(135deg, var(--primary-500), var(--primary-700));
    display: flex; align-items: center; justify-content: center;
}
.fsr-report-icon svg { width: 30px; height: 30px; color: #fff; }
.fsr-report-eyebrow {
    font-size: 11px; font-weight: 600; color: var(--primary-600);
    text-transform: uppercase; letter-spacing: 0.8px;
}
.fsr-report-head-main h1 { font-size: 26px; font-weight: 700; color: var(--gray-900); margin: 2px 0 6px; }
.fsr-report-meta { display: flex; gap: 18px; flex-wrap: wrap; font-size: 13px; color: var(--gray-500); }
.fsr-report-meta strong { color: var(--gray-700); font-weight: 600; }
.fsr-report-actions { display: flex; gap: 10px; flex-wrap: wrap; }

/* ----- Summary tiles ----- */
.fsr-summary {
    display: grid; gap: 14px;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
}
.fsr-tile {
    padding: 18px; border-radius: var(--radius-md);
    border: 1px solid var(--gray-200); text-align: center;
}
.fsr-tile-value { display: block; font-size: 30px; font-weight: 700; line-height: 1.1; }
.fsr-tile-label { display: block; font-size: 12px; margin-top: 6px; color: var(--gray-600); font-weight: 500; }
.fsr-tile-green  { background: #ecfdf5; border-color: #a7f3d0; } .fsr-tile-green  .fsr-tile-value { color: #047857; }
.fsr-tile-amber  { background: #fffbeb; border-color: #fde68a; } .fsr-tile-amber  .fsr-tile-value { color: #b45309; }
.fsr-tile-pink   { background: #fdf2f8; border-color: #fbcfe8; } .fsr-tile-pink   .fsr-tile-value { color: #be185d; }
.fsr-tile-blue   { background: #eff6ff; border-color: #bfdbfe; } .fsr-tile-blue   .fsr-tile-value { color: #1d4ed8; }
.fsr-tile-indigo { background: #eef2ff; border-color: #c7d2fe; } .fsr-tile-indigo .fsr-tile-value { color: #4338ca; }
.fsr-tile-red    { background: #fef2f2; border-color: #fecaca; } .fsr-tile-red    .fsr-tile-value { color: #b91c1c; }
.fsr-tile-purple { background: #faf5ff; border-color: #e9d5ff; } .fsr-tile-purple .fsr-tile-value { color: #7e22ce; }

/* ----- Jump nav ----- */
.fsr-jump {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    font-size: 13px; color: var(--gray-500); padding: 0 4px;
}
.fsr-jump a {
    padding: 6px 12px; background: #fff; border: 1px solid var(--gray-200);
    border-radius: 999px; color: var(--gray-700); text-decoration: none; font-weight: 500;
}
.fsr-jump a:hover { border-color: var(--primary-400); color: var(--primary-700); }

/* ----- Section ----- */
.fsr-section { padding: 20px 24px; scroll-margin-top: 90px; }
.fsr-section-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; }
.fsr-section-title { display: flex; align-items: center; gap: 10px; }
.fsr-section-title h2 { font-size: 17px; font-weight: 700; color: var(--gray-900); margin: 0; }
.fsr-count {
    background: var(--gray-100); color: var(--gray-700);
    font-size: 13px; font-weight: 700; padding: 2px 10px; border-radius: 999px;
}
.fsr-scope {
    font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 999px;
    text-transform: uppercase; letter-spacing: 0.4px;
}
.fsr-scope-site { background: #d1fae5; color: #065f46; }
.fsr-scope-uc   { background: #fef3c7; color: #92400e; }
.fsr-section-note { font-size: 13px; color: var(--gray-500); margin: 6px 0 14px; }

/* ----- Record ----- */
.fsr-record {
    border: 1px solid var(--gray-200); border-radius: var(--radius);
    margin-bottom: 10px; overflow: hidden;
}
.fsr-record:last-child { margin-bottom: 0; }
.fsr-record-head {
    width: 100%; display: flex; align-items: center; gap: 14px;
    padding: 12px 16px; background: #fff; border: none; cursor: pointer;
    font-family: inherit; text-align: left; transition: background var(--transition);
}
.fsr-record-head:hover { background: var(--gray-50); }
.fsr-record.open .fsr-record-head { background: var(--primary-50); border-bottom: 1px solid var(--gray-200); }
.fsr-rh-main { flex: 0 0 auto; }
.fsr-rh-cell { flex: 1 1 0; font-size: 13px; color: var(--gray-600); min-width: 90px; }
.fsr-record-head code {
    font-size: 12px; background: var(--gray-100); color: var(--gray-700);
    padding: 3px 8px; border-radius: 4px;
}
.fsr-chevron {
    width: 18px; height: 18px; color: var(--gray-400);
    flex-shrink: 0; transition: transform var(--transition);
}
.fsr-record.open .fsr-chevron { transform: rotate(180deg); }

.fsr-badge {
    display: inline-block; padding: 3px 9px; border-radius: 999px;
    font-size: 12px; font-weight: 600; white-space: nowrap;
}
.fsr-badge-primary { background: #ede9fe; color: #5b21b6; }
.fsr-badge-warning { background: #fef3c7; color: #92400e; }
.fsr-badge-purple  { background: #f3e8ff; color: #7e22ce; }
.fsr-badge-info    { background: #dbeafe; color: #1e40af; }
.fsr-badge-success { background: #dcfce7; color: #166534; }
.fsr-badge-danger  { background: #fee2e2; color: #991b1b; }

/* ----- Record body ----- */
.fsr-record-body { display: none; padding: 16px; background: var(--gray-50); }
.fsr-record.open .fsr-record-body { display: block; }

.fsr-kv {
    display: grid; gap: 10px 20px;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
}
.fsr-kv-item { display: flex; flex-direction: column; gap: 2px; }
.fsr-kv-label {
    font-size: 11px; font-weight: 600; color: var(--gray-400);
    text-transform: uppercase; letter-spacing: 0.4px;
}
.fsr-kv-value { font-size: 14px; color: var(--gray-800); word-break: break-word; }

.fsr-subhead {
    font-size: 13px; font-weight: 700; color: var(--gray-700);
    margin: 16px 0 8px; padding-top: 12px; border-top: 1px solid var(--gray-200);
}
.fsr-barrier-group { margin-bottom: 10px; }
.fsr-barrier-cat {
    display: inline-block; font-size: 12px; font-weight: 600;
    color: #92400e; background: #fef3c7; padding: 2px 8px; border-radius: 4px; margin-bottom: 4px;
}
.fsr-barrier-group ul { margin: 4px 0 0 18px; }
.fsr-barrier-group li { font-size: 13px; color: var(--gray-700); margin-bottom: 2px; }

.fsr-subtable-wrap { overflow-x: auto; border: 1px solid var(--gray-200); border-radius: var(--radius); }
.fsr-subtable { width: 100%; border-collapse: collapse; background: #fff; }
.fsr-subtable th {
    text-align: left; padding: 8px 12px; font-size: 11px; font-weight: 600;
    color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.4px;
    background: var(--gray-50); border-bottom: 1px solid var(--gray-200); white-space: nowrap;
}
.fsr-subtable td {
    padding: 8px 12px; font-size: 13px; color: var(--gray-700);
    border-bottom: 1px solid var(--gray-100); vertical-align: top;
}
.fsr-subtable tr:last-child td { border-bottom: none; }

.fsr-open-link {
    display: inline-block; margin-top: 16px; font-size: 13px;
    font-weight: 600; color: var(--primary-600); text-decoration: none;
}
.fsr-open-link:hover { text-decoration: underline; }

.fsr-empty-row {
    padding: 28px; text-align: center; font-size: 14px; color: var(--gray-500);
    background: var(--gray-50); border: 1px dashed var(--gray-200); border-radius: var(--radius);
}

@media (max-width: 768px) {
    .fsr-field select { min-width: 100%; }
    .fsr-selector-form { flex-direction: column; align-items: stretch; }
    .fsr-record-head { flex-wrap: wrap; }
    .fsr-rh-cell { flex: 1 1 40%; }
}

/* ----- Print ----- */
@media print {
    .sidebar, .header, .footer, .fsr-no-print { display: none !important; }
    .main-wrapper { margin-left: 0 !important; }
    .main-content { padding: 0 !important; }
    body { background: #fff; }
    .fsr-card { box-shadow: none; border-color: #ccc; }
    .fsr-record-body { display: block !important; }
    .fsr-record { break-inside: avoid; }
    .fsr-chevron { display: none; }
    a[href]:after { content: ''; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cascading selector: changing the UC reloads to refresh fixed sites.
    var ucSelect = document.getElementById('fsrUc');
    var fixSelect = document.getElementById('fsrFixSite');
    var form = document.getElementById('fsrForm');

    if (ucSelect) {
        ucSelect.addEventListener('change', function () {
            if (fixSelect) fixSelect.value = '';
            form.submit();
        });
    }
    if (fixSelect) {
        fixSelect.addEventListener('change', function () {
            if (this.value) form.submit();
        });
    }

    // Expand / collapse individual records.
    document.querySelectorAll('.fsr-record-head').forEach(function (head) {
        head.addEventListener('click', function () {
            head.closest('.fsr-record').classList.toggle('open');
        });
    });

    // Expand / collapse all.
    var expandAll = document.getElementById('fsrExpandAll');
    var collapseAll = document.getElementById('fsrCollapseAll');
    if (expandAll) {
        expandAll.addEventListener('click', function () {
            document.querySelectorAll('.fsr-record').forEach(function (r) { r.classList.add('open'); });
        });
    }
    if (collapseAll) {
        collapseAll.addEventListener('click', function () {
            document.querySelectorAll('.fsr-record').forEach(function (r) { r.classList.remove('open'); });
        });
    }
});
</script>
@endsection
