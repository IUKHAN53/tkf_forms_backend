@extends('layouts.admin')

@section('title', $ucName . ' - UC Details')
@section('page-title', $ucName)

@section('content')
<div class="uc-detail-page">
    <!-- Back Button & Header -->
    <div class="page-header-row">
        <a href="{{ route('admin.dashboard') }}" class="back-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <!-- UC Overview Stats -->
    <div class="uc-overview">
        <div class="uc-overview-header">
            <div class="uc-overview-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
            </div>
            <div class="uc-overview-info">
                <h1>{{ $ucName }}</h1>
                <p>Union Council Statistics Overview</p>
            </div>
        </div>
        <div class="uc-overview-stats">
            <div class="overview-stat">
                <span class="stat-value">{{ number_format($stats['child_line_lists']) }}</span>
                <span class="stat-label">Children</span>
            </div>
            <div class="overview-stat">
                <span class="stat-value">{{ number_format($stats['fgds_community']) }}</span>
                <span class="stat-label">FGDs Community</span>
            </div>
            <div class="overview-stat">
                <span class="stat-value">{{ number_format($stats['fgds_health_workers']) }}</span>
                <span class="stat-label">FGDs Health</span>
            </div>
            <div class="overview-stat">
                <span class="stat-value">{{ number_format($stats['bridging_the_gap']) }}</span>
                <span class="stat-label">Bridging Gap</span>
            </div>
        </div>
    </div>

    <!-- Interactive Map -->
    <div class="map-card">
        <div class="map-card-header">
            <div class="map-header-left">
                <div class="map-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                </div>
                <div class="map-header-text">
                    <h3>Geographic Distribution</h3>
                    <span class="map-count">{{ count($mapData) }} locations</span>
                </div>
            </div>
            <div class="map-header-actions">
                <button type="button" class="map-btn active" id="toggleCommunity" data-type="fgds_community">
                    <span class="marker-dot" style="background: #22c55e;"></span>
                    <span>FGDs Community</span>
                </button>
                <button type="button" class="map-btn active" id="toggleHealth" data-type="fgds_health_workers">
                    <span class="marker-dot" style="background: #f59e0b;"></span>
                    <span>FGDs Health</span>
                </button>
                <button type="button" class="map-btn active" id="toggleBridging" data-type="bridging_the_gap">
                    <span class="marker-dot" style="background: #ec4899;"></span>
                    <span>Bridging Gap</span>
                </button>
            </div>
        </div>
        <div class="map-wrapper">
            <div id="ucMap"></div>
            @if(count($mapData) === 0)
            <div class="map-empty-overlay">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                <p>No location data available yet</p>
                <span>GPS coordinates will appear here once forms with location data are submitted</span>
            </div>
            @endif
            <div class="map-legend">
                <div class="legend-title">Form Types</div>
                <div class="legend-items">
                    <div class="legend-item"><span class="legend-dot" style="background: #22c55e;"></span> FGDs Community</div>
                    <div class="legend-item"><span class="legend-dot" style="background: #f59e0b;"></span> FGDs Health Workers</div>
                    <div class="legend-item"><span class="legend-dot" style="background: #ec4899;"></span> Bridging The Gap</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-section">
        <div class="filters-row">
            @if($hasSubsets)
            <div class="filter-group">
                <label>UC Subset:</label>
                <select id="subsetUc" class="form-input">
                    <option value="all" selected>All ({{ count($variants) }} areas)</option>
                    @foreach($variants as $variant)
                    <option value="{{ $variant }}">{{ $variant }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="filter-group">
                <label>Date Range:</label>
                <select id="datePreset" class="form-input">
                    <option value="all" selected>All Time</option>
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="7days">Last 7 Days</option>
                    <option value="30days">Last 30 Days</option>
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>
            <div class="filter-group custom-date-range" style="display: none;">
                <label>From:</label>
                <input type="date" id="startDate" class="form-input">
            </div>
            <div class="filter-group custom-date-range" style="display: none;">
                <label>To:</label>
                <input type="date" id="endDate" class="form-input">
            </div>
            <button type="button" id="applyFilters" class="btn btn-primary custom-date-range" style="display: none;">
                Apply Filters
            </button>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="tabs-container">
        <div class="tabs-nav">
            <button class="tab-btn active" data-tab="fgds_community">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                FGDs Community
            </button>
            <button class="tab-btn" data-tab="fgds_health_workers">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
                FGDs Health Workers
            </button>
            <button class="tab-btn" data-tab="bridging_the_gap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
                    <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                    <line x1="6" y1="1" x2="6" y2="4"/>
                    <line x1="10" y1="1" x2="10" y2="4"/>
                    <line x1="14" y1="1" x2="14" y2="4"/>
                </svg>
                Bridging The Gap
            </button>
            <button class="tab-btn" data-tab="child_line_list">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2a3 3 0 0 0-3 3v1a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"/>
                    <path d="M19 8a2 2 0 0 1 2 2v1a2 2 0 0 1-4 0v-1a2 2 0 0 1 2-2z"/>
                    <path d="M5 8a2 2 0 0 1 2 2v1a2 2 0 0 1-4 0v-1a2 2 0 0 1 2-2z"/>
                    <path d="M12 14a4 4 0 0 0-4 4v4h8v-4a4 4 0 0 0-4-4z"/>
                </svg>
                Child Line List
            </button>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Loading State -->
            <div class="loading-state" id="loadingState">
                <div class="spinner"></div>
                <p>Loading data...</p>
            </div>

            <!-- Stats Cards -->
            <div class="tab-stats" id="tabStats"></div>

            <!-- Barriers by Category -->
            <div id="barriersCategorySection" style="display: none; margin-bottom: 24px;">
                <div style="font-size: 15px; font-weight: 600; color: var(--gray-800); margin-bottom: 12px;">Barriers by Category</div>
                <div id="barriersCategoryGrid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;"></div>
            </div>

            <!-- Barrier Category Drill-down Modal -->
            <div id="barrierModalOverlay" class="barrier-modal-overlay" onclick="closeBarrierCategoryModal(event)">
                <div class="barrier-modal" onclick="event.stopPropagation()">
                    <div class="barrier-modal-header">
                        <div>
                            <h3 id="barrierModalTitle">Barriers</h3>
                            <p id="barrierModalSubtitle" class="barrier-modal-subtitle"></p>
                        </div>
                        <button type="button" class="barrier-modal-close" onclick="closeBarrierCategoryModal()" aria-label="Close">&times;</button>
                    </div>
                    <div class="barrier-modal-body" id="barrierModalBody"></div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="tab-table-container">
                <div class="table-header">
                    <h3 id="tableTitle">Records</h3>
                    <span class="record-count" id="recordCount">0 records</span>
                </div>
                <div class="table-wrapper">
                    <table class="data-table" id="dataTable">
                        <thead id="tableHead"></thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
@vite('resources/css/admin/uc-detail.css')
@endpush

@include('admin.uc.partials.scripts')
@endsection
