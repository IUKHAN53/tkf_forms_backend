@extends('layouts.admin')

@section('title', 'Vaccination Sites')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Vaccination Sites</h2>
            <p class="text-muted">Manage fix sites and outreach sites for cascading dropdowns</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.outreach-sites.template') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Template
            </a>
            <a href="{{ route('admin.outreach-sites.export') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Export
            </a>
            <button type="button" class="btn btn-outline" onclick="document.getElementById('importModal').showModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Import
            </button>
            <a href="{{ route('admin.outreach-sites.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add Site
            </a>
        </div>
    </div>

    <!-- Compact Map Card -->
    <div class="map-card" style="margin: 16px; margin-bottom: 0;">
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
                <button type="button" class="map-btn" id="toggleHeatmap" title="Toggle Heatmap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/>
                    </svg>
                    <span>Heatmap</span>
                </button>
                <button type="button" class="map-btn" id="toggleMarkers" title="Toggle Markers">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <span>Markers</span>
                </button>
                <button type="button" class="map-btn map-btn-primary" onclick="toggleFullscreen('outreach-map')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                    </svg>
                    <span>Fullscreen</span>
                </button>
            </div>
        </div>
        <div class="map-wrapper">
            <div id="outreach-map"></div>
            <div class="map-legend">
                <div class="legend-title">Site Density</div>
                <div class="legend-gradient">
                    <div class="gradient-bar"></div>
                    <div class="gradient-labels">
                        <span>Low</span>
                        <span>Medium</span>
                        <span>High</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-filters">
        <form action="{{ route('admin.outreach-sites.index') }}" method="GET" class="filter-form">
            <input type="text" name="search" class="form-input" placeholder="Search by district, UC, fix site, or outreach site..." value="{{ request('search') }}">
            <label class="checkbox-filter">
                <input type="checkbox" name="invalid_coords" value="1" {{ request('invalid_coords') == '1' ? 'checked' : '' }} onchange="this.form.submit()">
                <span>With coordinates only</span>
            </label>
            <button type="submit" class="btn btn-primary">Search</button>
            @if(request()->hasAny(['search', 'invalid_coords']))
                <a href="{{ route('admin.outreach-sites.index') }}" class="btn btn-outline">Clear</a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>District</th>
                    <th>Union Council</th>
                    <th>Fix Site</th>
                    <th>Outreach Site</th>
                    <th>Coordinates</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($outreachSites as $site)
                    <tr class="{{ $site->coordinate_status === 'outside_karachi' ? 'row-warning' : '' }}">
                        <td><code>{{ $site->id }}</code></td>
                        <td>{{ $site->district }}</td>
                        <td>{{ $site->union_council }}</td>
                        <td>{{ $site->fix_site }}</td>
                        <td>{{ $site->outreach_site }}</td>
                        <td>
                            @if($site->coordinate_status === 'outside_karachi')
                                <span class="coordinates-invalid" title="Coordinates outside Karachi zone">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                    </svg>
                                    {{ $site->coordinates }}
                                </span>
                            @elseif($site->coordinate_status === 'invalid')
                                <span class="coordinates-error" title="Invalid coordinate format">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="15" y1="9" x2="9" y2="15"/>
                                        <line x1="9" y1="9" x2="15" y2="15"/>
                                    </svg>
                                    {{ $site->coordinates }}
                                </span>
                            @elseif($site->coordinate_status === 'valid')
                                <span class="coordinates-valid">{{ $site->coordinates }}</span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.outreach-sites.edit', $site) }}" class="btn btn-sm btn-outline" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.outreach-sites.destroy', $site) }}" method="POST" onsubmit="return confirm('Delete this site?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No vaccination sites found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($outreachSites->hasPages())
        <div class="card-footer">
            {{ $outreachSites->links() }}
        </div>
    @endif
</div>

<!-- Import Modal -->
<dialog id="importModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Import Vaccination Sites</h3>
            <button type="button" class="modal-close" onclick="document.getElementById('importModal').close()">&times;</button>
        </div>
        <form action="{{ route('admin.outreach-sites.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <p class="mb-md text-muted">Upload a CSV file with columns: district, union_council, fix_site, outreach_site, coordinates, comments</p>
                <input type="file" name="file" accept=".csv" required class="form-input" style="width: 100%;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('importModal').close()">Cancel</button>
                <button type="submit" class="btn btn-primary">Import</button>
            </div>
        </form>
    </div>
</dialog>

<button id="fullscreen-close-btn" class="fullscreen-close-btn" onclick="exitFullscreen()">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
    </svg>
    Exit Fullscreen
</button>

@push('styles')
<style>
/* Compact Map Card Styles for this page */
.map-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
    border: 1px solid var(--gray-200, #e5e7eb);
}

.map-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid var(--gray-100, #f3f4f6);
    gap: 12px;
    flex-wrap: wrap;
}

.map-header-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.map-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--primary-500, #10b981), var(--primary-600, #059669));
    display: flex;
    align-items: center;
    justify-content: center;
}

.map-icon svg {
    width: 16px;
    height: 16px;
    color: white;
}

.map-header-text {
    display: flex;
    align-items: center;
    gap: 10px;
}

.map-header-text h3 {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-800, #1f2937);
    margin: 0;
}

.map-count {
    font-size: 12px;
    color: var(--gray-500, #6b7280);
    background: var(--gray-100, #f3f4f6);
    padding: 3px 8px;
    border-radius: 12px;
}

.map-header-actions {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.map-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid var(--gray-200, #e5e7eb);
    background: white;
    color: var(--gray-600, #4b5563);
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
}

.map-btn:hover {
    background: var(--gray-50, #f9fafb);
    border-color: var(--gray-300, #d1d5db);
}

.map-btn.active {
    background: var(--primary-50, #ecfdf5);
    border-color: var(--primary-400, #34d399);
    color: var(--primary-700, #047857);
}

.map-btn svg {
    width: 14px;
    height: 14px;
}

.map-btn-primary {
    background: linear-gradient(135deg, var(--primary-500, #10b981), var(--primary-600, #059669));
    border-color: var(--primary-500, #10b981);
    color: white;
}

.map-btn-primary:hover {
    background: linear-gradient(135deg, var(--primary-600, #059669), var(--primary-700, #047857));
}

.map-wrapper {
    position: relative;
}

#outreach-map {
    height: 380px;
    width: 100%;
    background: var(--gray-100, #f3f4f6);
}

.map-legend {
    position: absolute;
    bottom: 16px;
    left: 16px;
    background: white;
    padding: 10px 14px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    z-index: 500;
    min-width: 140px;
}

.legend-title {
    font-size: 10px;
    font-weight: 600;
    color: var(--gray-600, #4b5563);
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.gradient-bar {
    height: 8px;
    border-radius: 4px;
    background: linear-gradient(to right, #3b82f6, #10b981, #f59e0b, #ef4444);
    margin-bottom: 5px;
}

.gradient-labels {
    display: flex;
    justify-content: space-between;
    font-size: 10px;
    color: var(--gray-400, #9ca3af);
}

/* Checkbox filter */
.checkbox-filter {
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    white-space: nowrap;
    font-size: 13px;
    color: var(--gray-600, #4b5563);
}

.checkbox-filter input {
    width: 16px;
    height: 16px;
    accent-color: var(--primary-500, #10b981);
}

/* Coordinate validation styles */
.row-warning {
    background-color: rgba(245, 158, 11, 0.08) !important;
}

.row-warning:hover {
    background-color: rgba(245, 158, 11, 0.12) !important;
}

.coordinates-invalid {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #b45309;
    background-color: #fef3c7;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    cursor: help;
}

.coordinates-error {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #b91c1c;
    background-color: #fee2e2;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    cursor: help;
}

.coordinates-valid {
    color: #047857;
    font-size: 12px;
}

/* Fullscreen styles */
.map-fullscreen {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 9999 !important;
    border-radius: 0 !important;
}

.fullscreen-close-btn {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    background: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: none;
    align-items: center;
    gap: 6px;
    transition: all 0.15s ease;
}

.fullscreen-close-btn:hover {
    background: var(--gray-100, #f3f4f6);
}

.fullscreen-close-btn.visible {
    display: flex;
}

/* Custom Leaflet controls */
.leaflet-control-zoom {
    border: none !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12) !important;
    border-radius: 8px !important;
    overflow: hidden;
}

.leaflet-control-zoom a {
    width: 32px !important;
    height: 32px !important;
    line-height: 32px !important;
    font-size: 16px !important;
    color: var(--gray-600, #4b5563) !important;
    background: white !important;
    border-bottom: 1px solid var(--gray-100, #f3f4f6) !important;
}

.leaflet-control-zoom a:hover {
    background: var(--gray-50, #f9fafb) !important;
    color: var(--primary-600, #059669) !important;
}

.leaflet-control-zoom a:last-child {
    border-bottom: none !important;
}

.leaflet-popup-content-wrapper {
    border-radius: 10px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12) !important;
}

.leaflet-popup-content {
    margin: 12px 16px !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 13px !important;
}

@media (max-width: 768px) {
    .map-btn span {
        display: none;
    }

    .map-btn {
        padding: 8px;
    }

    #outreach-map {
        height: 300px;
    }
}
</style>
@endpush

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map centered on Karachi
    const map = L.map('outreach-map', {
        zoomControl: true
    }).setView([24.8607, 67.0011], 11);

    // CartoDB Positron - Modern Light Theme
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    // Get location data
    const locations = @json($mapData);

    let heatLayer = null;
    let markersLayer = L.layerGroup();
    let heatEnabled = true;
    let markersEnabled = true;

    if (locations.length > 0) {
        // Prepare heat map data
        const heatData = locations.map(loc => [loc.lat, loc.lon, 0.6]);

        // Add heat layer with modern gradient
        heatLayer = L.heatLayer(heatData, {
            radius: 30,
            blur: 20,
            maxZoom: 17,
            max: 1.0,
            gradient: {
                0.0: '#3b82f6',
                0.25: '#10b981',
                0.5: '#22c55e',
                0.75: '#f59e0b',
                1.0: '#ef4444'
            }
        }).addTo(map);

        // Custom marker icon
        const customIcon = L.divIcon({
            className: 'custom-marker-container',
            html: `<div style="
                width: 10px;
                height: 10px;
                background: linear-gradient(135deg, #10b981, #059669);
                border: 2px solid white;
                border-radius: 50%;
                box-shadow: 0 2px 6px rgba(16, 185, 129, 0.4);
            "></div>`,
            iconSize: [10, 10],
            iconAnchor: [5, 5]
        });

        // Add markers with popups
        locations.forEach(loc => {
            const marker = L.marker([loc.lat, loc.lon], { icon: customIcon });
            marker.bindPopup(loc.popup, { maxWidth: 280 });
            markersLayer.addLayer(marker);
        });
        markersLayer.addTo(map);

        // Fit map to markers bounds
        const group = L.featureGroup(locations.map(loc => L.marker([loc.lat, loc.lon])));
        map.fitBounds(group.getBounds().pad(0.15));
    } else {
        const message = L.control({position: 'topright'});
        message.onAdd = function() {
            const div = L.DomUtil.create('div');
            div.style.cssText = 'background: white; padding: 12px 16px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-size: 13px; color: #6b7280;';
            div.innerHTML = 'No location data available';
            return div;
        };
        message.addTo(map);
    }

    // Store map reference
    window.outreachMap = map;
    window.outreachHeatLayer = heatLayer;
    window.outreachMarkersLayer = markersLayer;

    // Toggle buttons
    const toggleHeatmapBtn = document.getElementById('toggleHeatmap');
    const toggleMarkersBtn = document.getElementById('toggleMarkers');

    if (toggleHeatmapBtn) {
        toggleHeatmapBtn.classList.add('active');
        toggleHeatmapBtn.addEventListener('click', function() {
            if (heatLayer) {
                if (heatEnabled) {
                    map.removeLayer(heatLayer);
                    this.classList.remove('active');
                } else {
                    heatLayer.addTo(map);
                    this.classList.add('active');
                }
                heatEnabled = !heatEnabled;
            }
        });
    }

    if (toggleMarkersBtn) {
        toggleMarkersBtn.classList.add('active');
        toggleMarkersBtn.addEventListener('click', function() {
            if (markersEnabled) {
                map.removeLayer(markersLayer);
                this.classList.remove('active');
            } else {
                markersLayer.addTo(map);
                this.classList.add('active');
            }
            markersEnabled = !markersEnabled;
        });
    }
});

let currentFullscreenMap = null;

function toggleFullscreen(mapId) {
    const mapElement = document.getElementById(mapId);
    mapElement.classList.add('map-fullscreen');
    document.getElementById('fullscreen-close-btn').classList.add('visible');
    document.body.style.overflow = 'hidden';
    currentFullscreenMap = mapId;

    setTimeout(() => {
        if (window.outreachMap) {
            window.outreachMap.invalidateSize();
        }
    }, 100);
}

function exitFullscreen() {
    if (currentFullscreenMap) {
        const mapElement = document.getElementById(currentFullscreenMap);
        mapElement.classList.remove('map-fullscreen');
        document.getElementById('fullscreen-close-btn').classList.remove('visible');
        document.body.style.overflow = '';

        setTimeout(() => {
            if (window.outreachMap) {
                window.outreachMap.invalidateSize();
            }
        }, 100);

        currentFullscreenMap = null;
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && currentFullscreenMap) {
        exitFullscreen();
    }
});
</script>
@endsection
