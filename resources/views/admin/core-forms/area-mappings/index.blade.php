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

    <!-- Heat Map -->
    <div class="map-container" style="margin-bottom: 24px;">
        <div id="map" style="height: 400px; border-radius: 8px; overflow: hidden;"></div>
    </div>

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
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map centered on Karachi
    const map = L.map('map').setView([24.8607, 67.0011], 11);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);
    
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
        }).addTo(map);
        
        // Add markers
        locations.forEach(loc => {
            L.marker([loc.lat, loc.lon])
                .bindPopup(`
                    <strong>${loc.area}</strong><br>
                    District: ${loc.district}<br>
                    UC: ${loc.uc}<br>
                    Population: ${loc.population}
                `)
                .addTo(map);
        });
    }
});
</script>
@endsection
