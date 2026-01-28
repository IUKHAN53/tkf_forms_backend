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

<style>
.uc-detail-page {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.page-header-row {
    display: flex;
    align-items: center;
    gap: 16px;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--gray-600);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    padding: 8px 12px;
    border-radius: var(--radius);
    transition: all 0.2s ease;
}

.back-btn:hover {
    background: var(--gray-100);
    color: var(--gray-800);
}

/* UC Overview Card */
.uc-overview {
    background: white;
    border-radius: var(--radius-md);
    padding: 24px;
    border: 1px solid var(--gray-200);
    box-shadow: var(--shadow-sm);
}

.uc-overview-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--gray-100);
}

.uc-overview-icon {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
}

.uc-overview-icon svg {
    width: 28px;
    height: 28px;
    color: white;
}

.uc-overview-info h1 {
    font-size: 24px;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0 0 4px 0;
}

.uc-overview-info p {
    font-size: 14px;
    color: var(--gray-500);
    margin: 0;
}

.uc-overview-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

.overview-stat {
    text-align: center;
    padding: 16px;
    background: var(--gray-50);
    border-radius: var(--radius);
}

.overview-stat .stat-value {
    display: block;
    font-size: 28px;
    font-weight: 700;
    color: var(--gray-800);
    line-height: 1.2;
}

.overview-stat .stat-label {
    display: block;
    font-size: 13px;
    color: var(--gray-500);
    margin-top: 4px;
}

/* Map Card */
.map-card {
    background: white;
    border-radius: var(--radius-md);
    overflow: hidden;
    border: 1px solid var(--gray-200);
}

.map-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 20px;
    border-bottom: 1px solid var(--gray-100);
    gap: 12px;
    flex-wrap: wrap;
}

.map-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.map-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: linear-gradient(135deg, #10b981, #059669);
    display: flex;
    align-items: center;
    justify-content: center;
}

.map-icon svg {
    width: 18px;
    height: 18px;
    color: white;
}

.map-header-text h3 {
    font-size: 15px;
    font-weight: 600;
    color: var(--gray-800);
    margin: 0;
}

.map-count {
    font-size: 12px;
    color: var(--gray-500);
    background: var(--gray-100);
    padding: 2px 8px;
    border-radius: 10px;
    margin-left: 8px;
}

.map-header-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.map-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 6px;
    border: 1px solid var(--gray-200);
    background: white;
    color: var(--gray-500);
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
}

.map-btn:hover {
    background: var(--gray-50);
}

.map-btn.active {
    background: var(--gray-800);
    border-color: var(--gray-800);
    color: white;
}

.marker-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
}

.map-wrapper {
    position: relative;
}

#ucMap {
    height: 350px;
    width: 100%;
    background: var(--gray-100);
}

.map-legend {
    position: absolute;
    bottom: 16px;
    left: 16px;
    background: white;
    padding: 12px 16px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    z-index: 500;
}

.legend-title {
    font-size: 11px;
    font-weight: 600;
    color: var(--gray-600);
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.legend-items {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: var(--gray-600);
}

.legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
}

.map-empty-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    z-index: 500;
    background: rgba(255, 255, 255, 0.95);
    padding: 24px 32px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.map-empty-overlay svg {
    width: 48px;
    height: 48px;
    color: var(--gray-400);
    margin-bottom: 12px;
}

.map-empty-overlay p {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-700);
    margin: 0 0 4px 0;
}

.map-empty-overlay span {
    font-size: 13px;
    color: var(--gray-500);
}

/* Filters Section */
.filters-section {
    background: white;
    border-radius: var(--radius-md);
    padding: 16px 20px;
    border: 1px solid var(--gray-200);
}

.filters-row {
    display: flex;
    align-items: flex-end;
    gap: 16px;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.filter-group label {
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-group .form-input {
    padding: 8px 12px;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius);
    font-size: 14px;
    min-width: 160px;
}

/* Tabs Container */
.tabs-container {
    background: white;
    border-radius: var(--radius-md);
    border: 1px solid var(--gray-200);
    overflow: hidden;
}

.tabs-nav {
    display: flex;
    border-bottom: 1px solid var(--gray-200);
    background: var(--gray-50);
    overflow-x: auto;
}

.tab-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 14px 20px;
    border: none;
    background: transparent;
    font-size: 14px;
    font-weight: 500;
    color: var(--gray-600);
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    position: relative;
}

.tab-btn svg {
    width: 18px;
    height: 18px;
}

.tab-btn:hover {
    color: var(--gray-800);
    background: var(--gray-100);
}

.tab-btn.active {
    color: var(--primary-600);
    background: white;
}

.tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--primary-500);
}

/* Tab Content */
.tab-content {
    padding: 24px;
    min-height: 400px;
    position: relative;
}

/* Loading State */
.loading-state {
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    gap: 16px;
}

.loading-state.active {
    display: flex;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 3px solid var(--gray-200);
    border-top-color: var(--primary-500);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.loading-state p {
    color: var(--gray-500);
    font-size: 14px;
}

/* Tab Stats Cards */
.tab-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.tab-stat-card {
    background: var(--gray-50);
    border-radius: var(--radius);
    padding: 16px;
    text-align: center;
    border: 1px solid var(--gray-100);
}

.tab-stat-card.primary { background: #eff6ff; border-color: #bfdbfe; }
.tab-stat-card.success { background: #ecfdf5; border-color: #a7f3d0; }
.tab-stat-card.warning { background: #fffbeb; border-color: #fde68a; }
.tab-stat-card.info { background: #f0f9ff; border-color: #bae6fd; }
.tab-stat-card.purple { background: #faf5ff; border-color: #e9d5ff; }
.tab-stat-card.pink { background: #fdf2f8; border-color: #fbcfe8; }

.tab-stat-card .value {
    display: block;
    font-size: 24px;
    font-weight: 700;
    color: var(--gray-800);
    line-height: 1.2;
}

.tab-stat-card .label {
    display: block;
    font-size: 12px;
    color: var(--gray-600);
    margin-top: 4px;
}

/* Table Styles */
.tab-table-container {
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    overflow: hidden;
}

.table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
}

.table-header h3 {
    font-size: 15px;
    font-weight: 600;
    color: var(--gray-800);
    margin: 0;
}

.record-count {
    font-size: 13px;
    color: var(--gray-500);
    background: white;
    padding: 4px 10px;
    border-radius: 12px;
    border: 1px solid var(--gray-200);
}

.table-wrapper {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    text-align: left;
    padding: 12px 14px;
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    white-space: nowrap;
}

.data-table td {
    padding: 12px 14px;
    font-size: 14px;
    color: var(--gray-700);
    border-bottom: 1px solid var(--gray-100);
}

.data-table tr:last-child td {
    border-bottom: none;
}

.data-table tr:hover {
    background: var(--gray-50);
}

.data-table code {
    font-size: 12px;
    background: var(--gray-100);
    padding: 2px 6px;
    border-radius: 4px;
    color: var(--gray-700);
}

.badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 500;
}

.badge-info { background: #dbeafe; color: #1e40af; }
.badge-success { background: #dcfce7; color: #166534; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-primary { background: #ede9fe; color: #5b21b6; }

.action-links {
    display: flex;
    gap: 12px;
}

.action-link {
    color: var(--primary-600);
    text-decoration: none;
    font-weight: 500;
    font-size: 13px;
}

.action-link:hover {
    text-decoration: underline;
}

.action-link.edit-link {
    color: var(--gray-600);
}

.action-link.edit-link:hover {
    color: var(--gray-800);
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: var(--gray-500);
}

.empty-state svg {
    width: 48px;
    height: 48px;
    color: var(--gray-300);
    margin-bottom: 12px;
}

.empty-state p {
    margin: 0;
    font-size: 14px;
}

/* Responsive */
@media (max-width: 768px) {
    .uc-overview-stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .filters-row {
        flex-direction: column;
        align-items: stretch;
    }

    .filter-group .form-input {
        min-width: 100%;
    }

    .tabs-nav {
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .tab-btn {
        padding: 12px 16px;
        font-size: 13px;
    }

    .tab-stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .map-header-actions {
        width: 100%;
    }

    .map-btn span:not(.marker-dot) {
        display: none;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ucSlug = '{{ $ucSlug }}';
    let currentTab = 'fgds_community';
    let startDate = null;
    let endDate = null;
    let subsetUc = 'all';

    // Elements
    const datePreset = document.getElementById('datePreset');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const applyBtn = document.getElementById('applyFilters');
    const subsetUcSelect = document.getElementById('subsetUc');
    const customDateElements = document.querySelectorAll('.custom-date-range');
    const loadingState = document.getElementById('loadingState');
    const tabStats = document.getElementById('tabStats');
    const tableHead = document.getElementById('tableHead');
    const tableBody = document.getElementById('tableBody');
    const tableTitle = document.getElementById('tableTitle');
    const recordCount = document.getElementById('recordCount');

    // Tab buttons
    const tabBtns = document.querySelectorAll('.tab-btn');

    // Map initialization - always show map
    const mapData = @json($mapData);
    const map = L.map('ucMap').setView([24.8607, 67.0011], 11);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap &copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    // Marker layers by type
    const layers = {
        fgds_community: L.layerGroup(),
        fgds_health_workers: L.layerGroup(),
        bridging_the_gap: L.layerGroup()
    };

    const markerColors = {
        fgds_community: '#22c55e',
        fgds_health_workers: '#f59e0b',
        bridging_the_gap: '#ec4899'
    };

    // Create markers if we have data
    if (mapData.length > 0) {
        mapData.forEach(loc => {
            const color = markerColors[loc.type] || '#6366f1';
            const icon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="width:12px;height:12px;background:${color};border:2px solid white;border-radius:50%;box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>`,
                iconSize: [12, 12],
                iconAnchor: [6, 6]
            });

            const marker = L.marker([loc.lat, loc.lon], { icon: icon });
            marker.bindPopup(loc.popup);
            marker.ucVariant = loc.uc;
            layers[loc.type].addLayer(marker);
        });

        // Add all layers to map
        Object.values(layers).forEach(layer => layer.addTo(map));

        // Fit bounds
        const allMarkers = [];
        mapData.forEach(loc => allMarkers.push([loc.lat, loc.lon]));
        if (allMarkers.length > 0) {
            map.fitBounds(allMarkers, { padding: [30, 30] });
        }
    }

    // Toggle layer buttons
    document.querySelectorAll('.map-btn[data-type]').forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            if (this.classList.contains('active')) {
                map.removeLayer(layers[type]);
                this.classList.remove('active');
            } else {
                layers[type].addTo(map);
                this.classList.add('active');
            }
        });
    });

    // Filter markers by subset UC
    function filterMapBySubset(selectedUc) {
        Object.entries(layers).forEach(([type, layer]) => {
            layer.eachLayer(marker => {
                if (selectedUc === 'all' || marker.ucVariant === selectedUc) {
                    marker.setOpacity(1);
                } else {
                    marker.setOpacity(0.2);
                }
            });
        });
    }

    // Tab click handler
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentTab = this.dataset.tab;
            loadData();
        });
    });

    // Subset UC change
    if (subsetUcSelect) {
        subsetUcSelect.addEventListener('change', function() {
            subsetUc = this.value;
            if (mapData.length > 0) {
                filterMapBySubset(subsetUc);
            }
            loadData();
        });
    }

    // Date preset change
    datePreset.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateElements.forEach(el => el.style.display = 'flex');
        } else {
            customDateElements.forEach(el => el.style.display = 'none');
            const range = getDateRange(this.value);
            startDate = range.startDate;
            endDate = range.endDate;
            loadData();
        }
    });

    // Apply filters button
    applyBtn.addEventListener('click', function() {
        startDate = startDateInput.value || null;
        endDate = endDateInput.value || null;
        loadData();
    });

    // Calculate date range
    function getDateRange(preset) {
        const today = new Date();
        let start = null;
        let end = today.toISOString().split('T')[0];

        switch (preset) {
            case 'today':
                start = end;
                break;
            case 'yesterday':
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);
                start = yesterday.toISOString().split('T')[0];
                end = start;
                break;
            case '7days':
                const week = new Date(today);
                week.setDate(week.getDate() - 6);
                start = week.toISOString().split('T')[0];
                break;
            case '30days':
                const month = new Date(today);
                month.setDate(month.getDate() - 29);
                start = month.toISOString().split('T')[0];
                break;
            case 'this_month':
                start = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                break;
            case 'last_month':
                const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                start = lastMonth.toISOString().split('T')[0];
                const lastDay = new Date(today.getFullYear(), today.getMonth(), 0);
                end = lastDay.toISOString().split('T')[0];
                break;
            case 'all':
            default:
                start = null;
                end = null;
                break;
        }

        return { startDate: start, endDate: end };
    }

    // Load data via AJAX
    async function loadData() {
        loadingState.classList.add('active');
        tabStats.innerHTML = '';
        tableHead.innerHTML = '';
        tableBody.innerHTML = '';

        const params = new URLSearchParams({ tab: currentTab });
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (subsetUc && subsetUc !== 'all') params.append('subset_uc', subsetUc);

        try {
            const response = await fetch(`{{ route('admin.uc.data', $ucSlug) }}?${params.toString()}`);
            const result = await response.json();

            if (result.success) {
                renderStats(result.data.stats);
                renderTable(result.data.records);
            }
        } catch (error) {
            console.error('Error loading data:', error);
            tableBody.innerHTML = '<tr><td colspan="10" class="empty-state"><p>Error loading data</p></td></tr>';
        } finally {
            loadingState.classList.remove('active');
        }
    }

    // Render stats cards
    function renderStats(stats) {
        let html = '';
        const colors = ['primary', 'success', 'warning', 'info', 'purple', 'pink'];
        let i = 0;

        for (const [key, value] of Object.entries(stats)) {
            const label = formatLabel(key);
            const color = colors[i % colors.length];
            html += `
                <div class="tab-stat-card ${color}">
                    <span class="value">${formatNumber(value)}</span>
                    <span class="label">${label}</span>
                </div>
            `;
            i++;
        }

        tabStats.innerHTML = html;
    }

    // Render table
    function renderTable(records) {
        const columns = getColumnsForTab(currentTab);
        const titles = getTitleForTab(currentTab);

        tableTitle.textContent = titles.title;
        recordCount.textContent = `${records.length} records`;

        // Render header
        let headerHtml = '<tr>';
        columns.forEach(col => {
            headerHtml += `<th>${col.label}</th>`;
        });
        headerHtml += '<th>Actions</th></tr>';
        tableHead.innerHTML = headerHtml;

        // Render body
        if (records.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="${columns.length + 1}">
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                            <p>No records found for the selected filters</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        let bodyHtml = '';
        records.forEach(record => {
            bodyHtml += '<tr>';
            columns.forEach(col => {
                let value = record[col.key] ?? 'N/A';
                if (col.key === 'unique_id') {
                    value = `<code>${value}</code>`;
                } else if (col.key === 'gender') {
                    const badgeClass = value === 'Male' ? 'badge-info' : 'badge-success';
                    value = `<span class="badge ${badgeClass}">${value}</span>`;
                } else if (col.key === 'total_participants' || col.key === 'barriers_count' || col.key === 'action_plans_count' || col.key === 'iit_members_count') {
                    value = `<span class="badge badge-primary">${value}</span>`;
                }
                bodyHtml += `<td>${value}</td>`;
            });
            bodyHtml += `<td class="action-links">
                <a href="${getViewUrl(record.id)}" class="action-link">View</a>
                <a href="${getEditUrl(record.id)}" class="action-link edit-link">Edit</a>
            </td>`;
            bodyHtml += '</tr>';
        });

        tableBody.innerHTML = bodyHtml;
    }

    // Get columns based on current tab
    function getColumnsForTab(tab) {
        switch (tab) {
            case 'fgds_community':
                return [
                    { key: 'unique_id', label: 'Form ID' },
                    { key: 'date', label: 'Date' },
                    { key: 'venue', label: 'Venue' },
                    { key: 'uc', label: 'UC' },
                    { key: 'total_participants', label: 'Participants' },
                    { key: 'barriers_count', label: 'Barriers' },
                    { key: 'submitted_by', label: 'Submitted By' },
                    { key: 'created_at', label: 'Created' }
                ];
            case 'fgds_health_workers':
                return [
                    { key: 'unique_id', label: 'Form ID' },
                    { key: 'date', label: 'Date' },
                    { key: 'hfs', label: 'Health Facility' },
                    { key: 'uc', label: 'UC' },
                    { key: 'total_participants', label: 'Participants' },
                    { key: 'submitted_by', label: 'Submitted By' },
                    { key: 'created_at', label: 'Created' }
                ];
            case 'bridging_the_gap':
                return [
                    { key: 'unique_id', label: 'Form ID' },
                    { key: 'date', label: 'Date' },
                    { key: 'venue', label: 'Venue' },
                    { key: 'uc', label: 'UC' },
                    { key: 'total_participants', label: 'Attendance' },
                    { key: 'iit_members_count', label: 'IIT Members' },
                    { key: 'action_plans_count', label: 'Action Plans' },
                    { key: 'created_at', label: 'Created' }
                ];
            case 'child_line_list':
                return [
                    { key: 'unique_id', label: 'Form ID' },
                    { key: 'child_name', label: 'Child Name' },
                    { key: 'father_name', label: 'Father Name' },
                    { key: 'gender', label: 'Gender' },
                    { key: 'age_in_months', label: 'Age (months)' },
                    { key: 'type', label: 'Type' },
                    { key: 'uc', label: 'UC' },
                    { key: 'created_at', label: 'Created' }
                ];
            default:
                return [];
        }
    }

    // Get title for tab
    function getTitleForTab(tab) {
        switch (tab) {
            case 'fgds_community':
                return { title: 'FGDs Community Sessions' };
            case 'fgds_health_workers':
                return { title: 'FGDs Health Workers Sessions' };
            case 'bridging_the_gap':
                return { title: 'Bridging The Gap Sessions' };
            case 'child_line_list':
                return { title: 'Child Line List Records' };
            default:
                return { title: 'Records' };
        }
    }

    // Get view URL based on tab
    function getViewUrl(id) {
        switch (currentTab) {
            case 'fgds_community':
                return `{{ url('admin/fgds-community') }}/${id}`;
            case 'fgds_health_workers':
                return `{{ url('admin/fgds-health-workers') }}/${id}`;
            case 'bridging_the_gap':
                return `{{ url('admin/bridging-the-gap') }}/${id}`;
            case 'child_line_list':
                return `{{ url('admin/child-line-list') }}/${id}`;
            default:
                return '#';
        }
    }

    // Get edit URL based on tab
    function getEditUrl(id) {
        switch (currentTab) {
            case 'fgds_community':
                return `{{ url('admin/fgds-community') }}/${id}/edit`;
            case 'fgds_health_workers':
                return `{{ url('admin/fgds-health-workers') }}/${id}/edit`;
            case 'bridging_the_gap':
                return `{{ url('admin/bridging-the-gap') }}/${id}/edit`;
            case 'child_line_list':
                return `{{ url('admin/child-line-list') }}/${id}/edit`;
            default:
                return '#';
        }
    }

    // Format label (convert snake_case to Title Case)
    function formatLabel(key) {
        return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    // Format number with commas
    function formatNumber(num) {
        return new Intl.NumberFormat().format(num);
    }

    // Load initial data
    loadData();
});
</script>
@endsection
