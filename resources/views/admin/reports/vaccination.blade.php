@extends('layouts.admin')

@section('title', 'Vaccination Coverage Report')
@section('page-title', 'Vaccination Coverage Report')

@section('content')
@php $s = $report['summary']; $records = $report['records']; $shown = $records->take(200); @endphp
<div class="fsr-page">

    {{-- Filter bar --}}
    <div class="fsr-card fsr-selector fsr-no-print">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
            <a href="{{ route('admin.reports.index') }}" class="fsr-btn fsr-btn-light">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                All Reports
            </a>
            <a href="{{ route('admin.reports.vaccination.export', request()->query()) }}" class="fsr-btn fsr-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export to Excel
            </a>
        </div>
        <form method="GET" action="{{ route('admin.reports.vaccination') }}" class="fsr-selector-form">
            <div class="fsr-field">
                <label for="district">District</label>
                <select name="district" id="district">
                    <option value="">All districts</option>
                    @foreach ($districts as $d)
                        <option value="{{ $d }}" @selected($d === $filters['district'])>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <div class="fsr-field">
                <label for="uc">Union Council</label>
                <select name="uc" id="uc" onchange="this.form.submit()">
                    <option value="">All union councils</option>
                    @foreach ($unionCouncils as $uc)
                        <option value="{{ $uc }}" @selected($uc === $filters['uc'])>{{ $uc }}</option>
                    @endforeach
                </select>
            </div>
            <div class="fsr-field">
                <label for="fix_site">Fixed Site</label>
                <select name="fix_site" id="fix_site" @disabled($filters['uc'] === '')>
                    <option value="">{{ $filters['uc'] === '' ? 'Select a UC first' : 'All fixed sites' }}</option>
                    @foreach ($fixSites as $fs)
                        <option value="{{ $fs }}" @selected($fs === $filters['fix_site'])>{{ $fs }}</option>
                    @endforeach
                </select>
            </div>
            <div class="fsr-field rpt-field-sm">
                <label for="category">Category</label>
                <select name="category" id="category">
                    <option value="">All categories</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c }}" @selected($c === $category)>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="fsr-field rpt-field-sm">
                <label for="vaccinated">Vaccinated</label>
                <select name="vaccinated" id="vaccinated">
                    <option value="">All</option>
                    <option value="YES" @selected($vaccinated === 'YES')>Yes</option>
                    <option value="NO" @selected($vaccinated === 'NO')>No</option>
                </select>
            </div>
            <div class="fsr-field rpt-field-sm">
                <label for="from">From (submitted)</label>
                <input type="date" name="from" id="from" value="{{ $filters['from'] }}">
            </div>
            <div class="fsr-field rpt-field-sm">
                <label for="to">To (submitted)</label>
                <input type="date" name="to" id="to" value="{{ $filters['to'] }}">
            </div>
            <button type="submit" class="fsr-btn fsr-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Apply
            </button>
            <a href="{{ route('admin.reports.vaccination') }}" class="fsr-btn fsr-btn-light">Reset</a>
        </form>
    </div>

    {{-- Coverage meter --}}
    <div class="fsr-card fsr-section">
        <div class="fsr-section-head"><div class="fsr-section-title"><h2>Overall Coverage</h2></div></div>
        <div class="rpt-coverage">
            <span class="rpt-coverage-num">{{ $s['coverage'] }}%</span>
            <span class="rpt-coverage-track"><span class="rpt-coverage-fill" style="width: {{ $s['coverage'] }}%"></span></span>
        </div>
    </div>

    {{-- Tiles --}}
    <div class="fsr-summary">
        <div class="fsr-tile fsr-tile-blue">
            <span class="fsr-tile-value">{{ number_format($s['total']) }}</span>
            <span class="fsr-tile-label">Total Records</span>
        </div>
        <div class="fsr-tile fsr-tile-green">
            <span class="fsr-tile-value">{{ number_format($s['vaccinated']) }}</span>
            <span class="fsr-tile-label">Vaccinated</span>
        </div>
        <div class="fsr-tile fsr-tile-red">
            <span class="fsr-tile-value">{{ number_format($s['not_vaccinated']) }}</span>
            <span class="fsr-tile-label">Not Vaccinated</span>
        </div>
    </div>

    {{-- By category --}}
    <section class="fsr-card fsr-section">
        <div class="fsr-section-head"><div class="fsr-section-title"><h2>By Category</h2></div></div>
        <div class="fsr-subtable-wrap">
            <table class="fsr-subtable">
                <thead><tr><th>Category</th><th>Total</th><th>Vaccinated</th><th>Not Vaccinated</th><th>Coverage</th></tr></thead>
                <tbody>
                    @foreach ($report['by_category'] as $c)
                        <tr>
                            <td>{{ $c['category'] }}</td>
                            <td>{{ number_format($c['total']) }}</td>
                            <td>{{ number_format($c['vaccinated']) }}</td>
                            <td>{{ number_format($c['not_vaccinated']) }}</td>
                            <td>{{ $c['coverage'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    {{-- By UC --}}
    @if (count($report['by_uc']))
        <section class="fsr-card fsr-section">
            <div class="fsr-section-head"><div class="fsr-section-title"><h2>By Union Council</h2><span class="fsr-count">{{ count($report['by_uc']) }}</span></div></div>
            <div class="fsr-subtable-wrap">
                <table class="fsr-subtable">
                    <thead><tr><th>Union Council</th><th>Total</th><th>Vaccinated</th><th>Coverage</th></tr></thead>
                    <tbody>
                        @foreach ($report['by_uc'] as $row)
                            <tr>
                                <td>{{ $row['uc'] }}</td>
                                <td>{{ number_format($row['total']) }}</td>
                                <td>{{ number_format($row['vaccinated']) }}</td>
                                <td>{{ $row['total'] > 0 ? round($row['vaccinated'] / $row['total'] * 100, 1) : 0 }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    {{-- Records --}}
    <section class="fsr-card fsr-section">
        <div class="fsr-section-head">
            <div class="fsr-section-title"><h2>Records</h2><span class="fsr-count">{{ number_format($records->count()) }}</span></div>
        </div>
        @if ($records->count() > $shown->count())
            <p class="fsr-section-note">Showing the first {{ number_format($shown->count()) }} of {{ number_format($records->count()) }}. Export to Excel for the complete list.</p>
        @endif
        <div class="fsr-subtable-wrap">
            <table class="fsr-subtable">
                <thead><tr><th>Form ID</th><th>UC</th><th>Fixed Site</th><th>Child</th><th>Father</th><th>Age</th><th>Category</th><th>Vaccinated</th><th>Date</th></tr></thead>
                <tbody>
                    @forelse ($shown as $r)
                        <tr>
                            <td><code>{{ $r->unique_id }}</code></td>
                            <td>{{ $r->uc ?: '—' }}</td>
                            <td>{{ $r->fix_site ?: '—' }}</td>
                            <td>{{ $r->child_name ?: '—' }}</td>
                            <td>{{ $r->father_name ?: '—' }}</td>
                            <td>{{ $r->age ?: '—' }}</td>
                            <td>{{ $r->category ?: '—' }}</td>
                            <td>
                                @if ($r->vaccinated === 'YES')
                                    <span class="fsr-badge fsr-badge-success">Yes</span>
                                @else
                                    <span class="fsr-badge fsr-badge-danger">No</span>
                                @endif
                            </td>
                            <td>{{ optional($r->date_of_vaccination)->format('M d, Y') ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="fsr-empty-row">No vaccination records for the selected filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

@push('styles')
@vite('resources/css/admin/fixed-site-report.css')
@vite('resources/css/admin/reports.css')
@endpush
