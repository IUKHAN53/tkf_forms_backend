@extends('layouts.admin')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<div class="rpt-hub">

    <div class="rpt-hub-intro">
        <h1>Reports</h1>
        <p>One place for every program report. Pick a report, filter it by district, union council and date, view it on screen and export to Excel.</p>
    </div>

    {{-- Quick program totals --}}
    <div class="fsr-summary rpt-hub-totals">
        <div class="fsr-tile fsr-tile-green">
            <span class="fsr-tile-value">{{ number_format($totals['fgds_community']) }}</span>
            <span class="fsr-tile-label">FGDs — Community</span>
        </div>
        <div class="fsr-tile fsr-tile-amber">
            <span class="fsr-tile-value">{{ number_format($totals['fgds_health_workers']) }}</span>
            <span class="fsr-tile-label">FGDs — Health Workers</span>
        </div>
        <div class="fsr-tile fsr-tile-pink">
            <span class="fsr-tile-value">{{ number_format($totals['bridging']) }}</span>
            <span class="fsr-tile-label">Bridging the Gap</span>
        </div>
        <div class="fsr-tile fsr-tile-blue">
            <span class="fsr-tile-value">{{ number_format($totals['vaccination']) }}</span>
            <span class="fsr-tile-label">Vaccination Records</span>
        </div>
        <div class="fsr-tile fsr-tile-indigo">
            <span class="fsr-tile-value">{{ number_format($totals['child_line_list']) }}</span>
            <span class="fsr-tile-label">Children Listed</span>
        </div>
    </div>

    {{-- Report cards --}}
    <div class="rpt-grid">
        <a href="{{ route('admin.reports.summary') }}" class="rpt-card">
            <div class="rpt-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/>
                </svg>
            </div>
            <h3>Program Summary</h3>
            <p>Totals across all five core forms — sessions, participants, barriers, action plans, vaccinations and children — broken down by Union Council. Filter by district, UC and date.</p>
            <span class="rpt-card-cta">Open report
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </span>
        </a>

        <a href="{{ route('admin.reports.barriers') }}" class="rpt-card">
            <div class="rpt-card-icon rpt-icon-amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <h3>Barriers Report</h3>
            <p>Immunization barriers from FGDs, grouped by the 11 canonical categories across Community and Health-Worker sessions, with a full drill-down list.</p>
            <span class="rpt-card-cta">Open report
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </span>
        </a>

        <a href="{{ route('admin.reports.vaccination') }}" class="rpt-card">
            <div class="rpt-card-icon rpt-icon-blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <h3>Vaccination Coverage</h3>
            <p>CLM Tracker vaccination records by category (defaulter, refusal, zero dose) and vaccinated status, with coverage rates by Union Council.</p>
            <span class="rpt-card-cta">Open report
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </span>
        </a>

        <a href="{{ route('admin.reports.fixed-site') }}" class="rpt-card">
            <div class="rpt-card-icon rpt-icon-pink">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                </svg>
            </div>
            <h3>Fixed Site Report</h3>
            <p>A consolidated dossier for one fixed vaccination site: every FGD, Bridging the Gap session, vaccination record and child line list entry in one place.</p>
            <span class="rpt-card-cta">Open report
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </span>
        </a>
    </div>
</div>
@endsection

@push('styles')
@vite('resources/css/admin/fixed-site-report.css')
@vite('resources/css/admin/reports.css')
@endpush
