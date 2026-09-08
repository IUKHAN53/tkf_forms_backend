@extends('layouts.admin')

@section('title', 'Barriers Report')
@section('page-title', 'Barriers Report')

@section('content')
@php $t = $report['totals']; $maxBar = collect($report['by_category'])->max('total') ?: 1; @endphp
<div class="fsr-page">

    {{-- Filter bar --}}
    <div class="fsr-card fsr-selector fsr-no-print">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
            <a href="{{ route('admin.reports.index') }}" class="fsr-btn fsr-btn-light">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                All Reports
            </a>
            <a href="{{ route('admin.reports.barriers.export', request()->query()) }}" class="fsr-btn fsr-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export to Excel
            </a>
        </div>
        <form method="GET" action="{{ route('admin.reports.barriers') }}" class="fsr-selector-form">
            <div class="fsr-field rpt-field-sm">
                <label for="scope">Source</label>
                <select name="scope" id="scope">
                    <option value="both" @selected($scope === 'both')>Community + Health Workers</option>
                    <option value="community" @selected($scope === 'community')>Community only</option>
                    <option value="health_workers" @selected($scope === 'health_workers')>Health Workers only</option>
                </select>
            </div>
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
            @if ($filters['district'] || $filters['uc'] || $filters['from'] || $filters['to'] || $scope !== 'both')
                <a href="{{ route('admin.reports.barriers') }}" class="fsr-btn fsr-btn-light">Reset</a>
            @endif
        </form>
    </div>

    {{-- Totals --}}
    <div class="fsr-summary">
        <div class="fsr-tile fsr-tile-red">
            <span class="fsr-tile-value">{{ number_format($t['barriers']) }}</span>
            <span class="fsr-tile-label">Total Barriers</span>
        </div>
        <div class="fsr-tile fsr-tile-green">
            <span class="fsr-tile-value">{{ number_format($t['community']) }}</span>
            <span class="fsr-tile-label">Community Sessions</span>
        </div>
        <div class="fsr-tile fsr-tile-amber">
            <span class="fsr-tile-value">{{ number_format($t['health']) }}</span>
            <span class="fsr-tile-label">Health-Worker Sessions</span>
        </div>
    </div>

    {{-- Category breakdown --}}
    <section class="fsr-card fsr-section">
        <div class="fsr-section-head">
            <div class="fsr-section-title"><h2>Barriers by Category</h2><span class="fsr-count">{{ number_format($t['barriers']) }}</span></div>
        </div>
        @if ($t['barriers'] === 0)
            <p class="fsr-section-note">No barriers recorded for the selected filters.</p>
        @else
            <div class="rpt-bars">
                @foreach ($report['by_category'] as $cat)
                    @if ($cat['total'] > 0)
                        <div class="rpt-bar-row">
                            <span class="rpt-bar-label">{{ $cat['name'] }}</span>
                            <span class="rpt-bar-track"><span class="rpt-bar-fill" style="width: {{ round($cat['total'] / $maxBar * 100) }}%"></span></span>
                            <span class="rpt-bar-value">{{ number_format($cat['total']) }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="fsr-subtable-wrap" style="margin-top:18px;">
                <table class="fsr-subtable">
                    <thead><tr><th>Category</th><th>Community</th><th>Health Workers</th><th>Total</th></tr></thead>
                    <tbody>
                        @foreach ($report['by_category'] as $cat)
                            @if ($cat['total'] > 0)
                                <tr>
                                    <td>{{ $cat['name'] }}</td>
                                    <td>{{ number_format($cat['community']) }}</td>
                                    <td>{{ number_format($cat['health_workers']) }}</td>
                                    <td><strong>{{ number_format($cat['total']) }}</strong></td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- Detailed barriers --}}
    <section class="fsr-card fsr-section">
        <div class="fsr-section-head">
            <div class="fsr-section-title"><h2>Barrier Details</h2><span class="fsr-count">{{ count($report['details']) }}</span></div>
        </div>
        <div class="fsr-subtable-wrap">
            <table class="fsr-subtable">
                <thead><tr><th>Source</th><th>Form ID</th><th>Date</th><th>UC</th><th>Location</th><th>Category</th><th>Barrier</th></tr></thead>
                <tbody>
                    @forelse ($report['details'] as $row)
                        <tr>
                            <td>{{ $row['source'] }}</td>
                            <td><code>{{ $row['form_id'] }}</code></td>
                            <td>{{ $row['date'] ?: '—' }}</td>
                            <td>{{ $row['uc'] ?: '—' }}</td>
                            <td>{{ $row['location'] ?: '—' }}</td>
                            <td>{{ $row['category'] }}</td>
                            <td>{{ $row['text'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="fsr-empty-row">No barriers for the selected filters.</td></tr>
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
