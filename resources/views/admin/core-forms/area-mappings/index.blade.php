@extends('layouts.admin')

@section('title', 'Area Mappings')
@section('page-title', 'Area Mappings')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Area Mappings</h2>
            <p class="text-muted">Manage area mapping records</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.area-mappings.template') }}" class="btn btn-outline">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Download Template
            </a>
            <button type="button" class="btn btn-outline" onclick="document.getElementById('importModal').showModal()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                Import CSV
            </button>
            <a href="{{ route('admin.area-mappings.export') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Export CSV
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
                <button type="button" class="btn btn-sm btn-outline" onclick="toggleMapFullscreen('map')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                    </svg>
                    Fullscreen
                </button>
            </div>
        </div>
        <div id="map" style="height: 400px; border-radius: 8px; overflow: hidden; border: 1px solid var(--color-border);"></div>
    </div>

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
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: none;
}

.fullscreen-close-btn.visible {
    display: block;
}
</style>

<button id="map-fullscreen-close" class="fullscreen-close-btn" onclick="exitMapFullscreen()">✕ Exit Fullscreen</button>

    <!-- Search -->
    <div class="card-filters">
        <form method="GET" class="search-form">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by district, UC, tehsil, area..." class="form-input">
            <button type="submit" class="btn btn-primary">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.area-mappings.index') }}" class="btn btn-outline">Clear</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Form ID</th>
                    <th>District</th>
                    <th>UC Name</th>
                    <th>Area Name</th>
                    <th>Population</th>
                    <th>Under 2 Years</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mappings as $mapping)
                    <tr>
                        <td><code>{{ $mapping->unique_id }}</code></td>
                        <td>{{ $mapping->district }}</td>
                        <td>{{ $mapping->uc_name }}</td>
                        <td>{{ $mapping->area_name }}</td>
                        <td>{{ $mapping->total_population }}</td>
                        <td>{{ $mapping->total_under_2_years }}</td>
                        <td>{{ $mapping->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.area-mappings.show', $mapping) }}" class="btn btn-sm btn-outline">View</a>
                                <form action="{{ route('admin.area-mappings.destroy', $mapping) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No area mappings found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="card-footer">
        {{ $mappings->links() }}
    </div>
</div>

<!-- Import Modal -->
<dialog id="importModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Import Area Mappings</h3>
            <button type="button" onclick="document.getElementById('importModal').close()" class="modal-close">&times;</button>
        </div>
        <form action="{{ route('admin.area-mappings.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <p class="text-muted mb-md">Upload a CSV file with the required columns. <a href="{{ route('admin.area-mappings.template') }}">Download template</a></p>
                <input type="file" name="file" accept=".csv,.txt" required class="form-input">
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('importModal').close()" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Import</button>
            </div>
        </form>
    </div>
</dialog>

@include('admin.core-forms.partials.styles')

<script>
let areaMappingMap = null;
let currentFullscreenMapId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize map centered on Karachi
    areaMappingMap = L.map('map').setView([24.8607, 67.0011], 11);

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(areaMappingMap);

    // Get location data from Laravel
    const locations = @json($mapData);

    // Prepare heat map data
    const heatData = locations.map(loc => [loc.lat, loc.lon, 0.5]);

    // Add heat layer
    if (heatData.length > 0) {
        L.heatLayer(heatData, {
            radius: 25,
            blur: 15,
            maxZoom: 17,
            gradient: {
                0.0: 'blue',
                0.5: 'lime',
                1.0: 'red'
            }
        }).addTo(areaMappingMap);

        // Add markers
        locations.forEach(loc => {
            L.marker([loc.lat, loc.lon])
                .bindPopup(`
                    <strong>${loc.area}</strong><br>
                    District: ${loc.district}<br>
                    UC: ${loc.uc}<br>
                    Population: ${loc.population}
                `)
                .addTo(areaMappingMap);
        });

        // Fit map to markers bounds
        const group = L.featureGroup(locations.map(loc => L.marker([loc.lat, loc.lon])));
        areaMappingMap.fitBounds(group.getBounds().pad(0.1));
    }
});

function toggleMapFullscreen(mapId) {
    const mapElement = document.getElementById(mapId);
    mapElement.classList.add('map-fullscreen');
    document.getElementById('map-fullscreen-close').classList.add('visible');
    document.body.style.overflow = 'hidden';
    currentFullscreenMapId = mapId;

    // Resize the map after transition
    setTimeout(() => {
        if (areaMappingMap) {
            areaMappingMap.invalidateSize();
        }
    }, 100);
}

function exitMapFullscreen() {
    if (currentFullscreenMapId) {
        const mapElement = document.getElementById(currentFullscreenMapId);
        mapElement.classList.remove('map-fullscreen');
        document.getElementById('map-fullscreen-close').classList.remove('visible');
        document.body.style.overflow = '';

        // Resize the map after transition
        setTimeout(() => {
            if (areaMappingMap) {
                areaMappingMap.invalidateSize();
            }
        }, 100);

        currentFullscreenMapId = null;
    }
}

// ESC key to exit fullscreen
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && currentFullscreenMapId) {
        exitMapFullscreen();
    }
});
</script>
@endsection
