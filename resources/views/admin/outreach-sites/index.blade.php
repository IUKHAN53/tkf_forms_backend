@extends('layouts.admin')

@section('title', 'Outreach Sites')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Outreach Sites</h2>
            <p class="text-muted">Manage outreach sites for cascading dropdowns</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.outreach-sites.template') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Download Template
            </a>
            <a href="{{ route('admin.outreach-sites.export') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Export CSV
            </a>
            <button type="button" class="btn btn-primary" onclick="document.getElementById('importModal').showModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Import CSV
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

    <!-- Heat Map Container -->
    <div class="map-container" style="margin-bottom: 24px;">
        <div class="map-header" style="margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 16px; font-weight: 600; color: var(--color-text-primary);">Geographic Distribution</h3>
            <div style="display: flex; gap: 12px; align-items: center;">
                <span style="color: var(--color-text-secondary); font-size: 14px;">
                    {{ count($mapData) }} locations with coordinates
                </span>
                <button type="button" class="btn btn-sm btn-outline" onclick="toggleFullscreen('outreach-map')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                    </svg>
                    Fullscreen
                </button>
            </div>
        </div>
        <div id="outreach-map" style="height: 400px; border-radius: 8px; overflow: hidden; border: 1px solid var(--color-border);"></div>
    </div>

    <div class="card-filters">
        <form action="{{ route('admin.outreach-sites.index') }}" method="GET" class="search-form">
            <input type="text" name="search" class="form-input" placeholder="Search by district, UC, fix site, or outreach site..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Search</button>
            @if(request('search'))
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
                    <tr>
                        <td><code>{{ $site->id }}</code></td>
                        <td>{{ $site->district }}</td>
                        <td>{{ $site->union_council }}</td>
                        <td>{{ $site->fix_site }}</td>
                        <td>{{ $site->outreach_site }}</td>
                        <td>{{ $site->coordinates ?: 'N/A' }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.outreach-sites.edit', $site) }}" class="btn-icon" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.outreach-sites.destroy', $site) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this outreach site?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                        <td colspan="7" class="text-center">No outreach sites found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $outreachSites->links() }}
    </div>
</div>

<!-- Import Modal -->
<dialog id="importModal" class="import-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Import Outreach Sites</h3>
            <button type="button" onclick="document.getElementById('importModal').close()" class="close-btn">&times;</button>
        </div>
        <form action="{{ route('admin.outreach-sites.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <p>Upload a CSV file with the following columns:</p>
                <code>district, union_council, fix_site, outreach_site, coordinates, comments</code>
                <div class="form-group">
                    <label class="form-label">Choose CSV File</label>
                    <input type="file" name="file" accept=".csv" required class="form-input">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('importModal').close()" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Import</button>
            </div>
        </form>
    </div>
</dialog>

<style>
.map-fullscreen {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 9999 !important;
    border-radius: 0 !important;
}

.fullscreen-close {
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
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: none;
}

.fullscreen-close.visible {
    display: block;
}
</style>

<button id="fullscreen-close-btn" class="fullscreen-close" onclick="exitFullscreen()">✕ Exit Fullscreen</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map centered on Karachi
    const map = L.map('outreach-map').setView([24.8607, 67.0011], 11);

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);

    // Get location data
    const locations = @json($mapData);

    if (locations.length > 0) {
        // Prepare heat map data
        const heatData = locations.map(loc => [loc.lat, loc.lon, 0.5]);

        // Add heat layer
        L.heatLayer(heatData, {
            radius: 25,
            blur: 15,
            maxZoom: 17,
            gradient: {
                0.0: 'blue',
                0.5: 'lime',
                1.0: 'red'
            }
        }).addTo(map);

        // Add markers with popups
        locations.forEach(loc => {
            const marker = L.marker([loc.lat, loc.lon]);
            marker.bindPopup(loc.popup);
            marker.addTo(map);
        });

        // Fit map to markers bounds
        const group = L.featureGroup(locations.map(loc => L.marker([loc.lat, loc.lon])));
        map.fitBounds(group.getBounds().pad(0.1));
    } else {
        // Add a message if no coordinates
        const message = L.control({position: 'topright'});
        message.onAdd = function() {
            const div = L.DomUtil.create('div', 'map-message');
            div.style.background = 'white';
            div.style.padding = '10px';
            div.style.borderRadius = '4px';
            div.innerHTML = '<strong>No location data available</strong>';
            return div;
        };
        message.addTo(map);
    }

    // Store map reference for resize
    window.outreachMap = map;
});

let currentFullscreenMap = null;

function toggleFullscreen(mapId) {
    const mapElement = document.getElementById(mapId);
    mapElement.classList.add('map-fullscreen');
    document.getElementById('fullscreen-close-btn').classList.add('visible');
    document.body.style.overflow = 'hidden';
    currentFullscreenMap = mapId;

    // Resize the map after transition
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

        // Resize the map after transition
        setTimeout(() => {
            if (window.outreachMap) {
                window.outreachMap.invalidateSize();
            }
        }, 100);

        currentFullscreenMap = null;
    }
}

// ESC key to exit fullscreen
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && currentFullscreenMap) {
        exitFullscreen();
    }
});
</script>
@endsection
