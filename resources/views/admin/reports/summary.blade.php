@extends('layouts.admin')

@section('title', 'Program Summary Report')
@section('page-title', 'Program Summary Report')

@section('content')
@php $s = $report['summary']; @endphp
<div class="fsr-page">

    {{-- Filter bar --}}
    <div class="fsr-card fsr-selector fsr-no-print">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
            <a href="{{ route('admin.reports.index') }}" class="fsr-btn fsr-btn-light">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                All Reports
            </a>
            <a href="{{ route('admin.reports.summary.export', request()->query()) }}" class="fsr-btn fsr-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export to Excel
            </a>
        </div>
        <form method="GET" action="{{ route('admin.reports.summary') }}" class="fsr-selector-form">
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
                <select name="uc" id="uc">
                    <option value="">All union councils</option>
                    @foreach ($unionCouncils as $uc)
                        <option value="{{ $uc }}" @selected($uc === $filters['uc'])>{{ $uc }}</option>
                    @endforeach
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
            @if ($filters['district'] || $filters['uc'] || $filters['from'] || $filters['to'])
                <a href="{{ route('admin.reports.summary') }}" class="fsr-btn fsr-btn-light">Reset</a>
            @endif
        </form>
    </div>

    {{-- Summary tiles --}}
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
        <div class="fsr-tile fsr-tile-green">
            <span class="fsr-tile-value">{{ number_format($s['fgds_community_participants'] + $s['fgds_health_workers_participants'] + $s['bridging_participants']) }}</span>
            <span class="fsr-tile-label">Participants Engaged</span>
        </div>
    </div>

    {{-- By Union Council --}}
    <section class="fsr-card fsr-section">
        <div class="fsr-section-head">
            <div class="fsr-section-title"><h2>By Union Council</h2><span class="fsr-count">{{ count($report['by_uc']) }}</span></div>
        </div>
        <div class="fsr-subtable-wrap">
            <table class="fsr-subtable">
                <thead><tr>
                    <th>Union Council</th><th>FGD-Community</th><th>FGD-Health Workers</th>
                    <th>Bridging the Gap</th><th>Vaccination</th><th>Child Line List</th>
                </tr></thead>
                <tbody>
                    @forelse ($report['by_uc'] as $row)
                        <tr>
                            <td>{{ $row['uc'] }}</td>
                            <td>{{ number_format($row['fgds_community']) }}</td>
                            <td>{{ number_format($row['fgds_health_workers']) }}</td>
                            <td>{{ number_format($row['bridging']) }}</td>
                            <td>{{ number_format($row['vaccination']) }}</td>
                            <td>{{ number_format($row['child_line_list']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="fsr-empty-row">No records for the selected filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Barriers by category --}}
    @php $maxBar = collect($report['barriers_by_category'])->max('total') ?: 1; @endphp
    <section class="fsr-card fsr-section">
        <div class="fsr-section-head">
            <div class="fsr-section-title"><h2>Barriers by Category</h2>
                <span class="fsr-count">{{ number_format($s['fgds_community_barriers'] + $s['fgds_health_workers_barriers']) }}</span>
            </div>
        </div>
        <div class="rpt-bars">
            @foreach ($report['barriers_by_category'] as $cat)
                @if ($cat['total'] > 0)
                    <div class="rpt-bar-row">
                        <span class="rpt-bar-label">{{ $cat['name'] }}</span>
                        <span class="rpt-bar-track"><span class="rpt-bar-fill" style="width: {{ round($cat['total'] / $maxBar * 100) }}%"></span></span>
                        <span class="rpt-bar-value">{{ number_format($cat['total']) }}</span>
                    </div>
                @endif
            @endforeach
            @if (($s['fgds_community_barriers'] + $s['fgds_health_workers_barriers']) === 0)
                <p class="fsr-section-note">No barriers recorded for the selected filters.</p>
            @endif
        </div>
    </section>
</div>
@endsection

@push('styles')
@vite('resources/css/admin/fixed-site-report.css')
@vite('resources/css/admin/reports.css')
@endpush
