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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map centered on Karachi
    const map = L.map('map').setView([24.8607, 67.0011], 11);

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
    window.coreFormMap = map;
});

let currentFullscreenMapId = null;

function toggleMapFullscreen(mapId) {
    const mapElement = document.getElementById(mapId);
    mapElement.classList.add('map-fullscreen');
    document.getElementById('map-fullscreen-close').classList.add('visible');
    document.body.style.overflow = 'hidden';
    currentFullscreenMapId = mapId;

    // Resize the map after transition
    setTimeout(() => {
        if (window.coreFormMap) {
            window.coreFormMap.invalidateSize();
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
            if (window.coreFormMap) {
                window.coreFormMap.invalidateSize();
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
