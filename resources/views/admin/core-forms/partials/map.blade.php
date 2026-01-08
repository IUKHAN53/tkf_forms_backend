<!-- Compact Heat Map Container -->
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
            <button type="button" class="map-btn map-btn-primary" onclick="toggleMapFullscreen('map')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                </svg>
                <span>Fullscreen</span>
            </button>
        </div>
    </div>
    <div class="map-wrapper">
        <div id="map"></div>
        <div class="map-legend">
            <div class="legend-title">Activity Density</div>
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

<style>
/* Compact Map Card Styles */
.map-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
    margin-bottom: 16px;
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
    border-color: var(--primary-600, #059669);
}

.map-wrapper {
    position: relative;
}

#map {
    height: 380px;
    width: 100%;
    background: var(--gray-100, #f3f4f6);
}

/* Map Legend - Compact */
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

/* Fullscreen Styles */
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
    padding: 12px 24px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    display: none;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.fullscreen-close-btn:hover {
    background: var(--gray-100, #f3f4f6);
    transform: scale(1.02);
}

.fullscreen-close-btn.visible {
    display: flex;
}

/* Custom Leaflet Controls */
.leaflet-control-zoom {
    border: none !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    border-radius: 10px !important;
    overflow: hidden;
}

.leaflet-control-zoom a {
    width: 36px !important;
    height: 36px !important;
    line-height: 36px !important;
    font-size: 18px !important;
    color: var(--gray-700, #374151) !important;
    background: white !important;
    border-bottom: 1px solid var(--gray-100, #f3f4f6) !important;
    transition: all 0.2s ease !important;
}

.leaflet-control-zoom a:hover {
    background: var(--gray-50, #f9fafb) !important;
    color: var(--primary-600, #059669) !important;
}

.leaflet-control-zoom a:last-child {
    border-bottom: none !important;
}

.leaflet-control-attribution {
    background: rgba(255, 255, 255, 0.9) !important;
    padding: 4px 10px !important;
    font-size: 11px !important;
    border-radius: 6px 0 0 0 !important;
}

/* Custom Popup Styles */
.leaflet-popup-content-wrapper {
    border-radius: 12px !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
    padding: 0 !important;
}

.leaflet-popup-content {
    margin: 0 !important;
    padding: 16px 20px !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 13px !important;
    line-height: 1.6 !important;
    color: var(--gray-700, #374151) !important;
}

.leaflet-popup-content strong {
    display: block;
    font-size: 14px;
    color: var(--gray-900, #111827);
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--gray-100, #f3f4f6);
}

.leaflet-popup-tip {
    box-shadow: none !important;
}

.leaflet-popup-close-button {
    top: 8px !important;
    right: 8px !important;
    width: 24px !important;
    height: 24px !important;
    font-size: 18px !important;
    color: var(--gray-400, #9ca3af) !important;
    border-radius: 6px !important;
    transition: all 0.2s ease !important;
}

.leaflet-popup-close-button:hover {
    background: var(--gray-100, #f3f4f6) !important;
    color: var(--gray-700, #374151) !important;
}

/* Custom Marker Styles */
.custom-marker {
    background: linear-gradient(135deg, var(--primary-500, #10b981), var(--primary-600, #059669));
    border: 3px solid white;
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}

/* Responsive */
@media (max-width: 768px) {
    .map-card-header {
        padding: 16px;
    }

    .map-header-actions {
        width: 100%;
        justify-content: flex-end;
    }

    .map-btn span {
        display: none;
    }

    .map-btn {
        padding: 10px;
    }

    #map {
        height: 350px;
    }

    .map-legend {
        bottom: 16px;
        left: 16px;
        padding: 12px;
    }
}
</style>

<button id="map-fullscreen-close" class="fullscreen-close-btn" onclick="exitMapFullscreen()">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
    </svg>
    Exit Fullscreen
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map centered on Karachi
    const map = L.map('map', {
        zoomControl: true,
        attributionControl: true
    }).setView([24.8607, 67.0011], 11);

    // CartoDB Positron - Modern Light Theme
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
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
        // Prepare heat map data with intensity
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

        // Create custom marker icon
        const customIcon = L.divIcon({
            className: 'custom-marker-container',
            html: `<div style="
                width: 12px;
                height: 12px;
                background: linear-gradient(135deg, #10b981, #059669);
                border: 2px solid white;
                border-radius: 50%;
                box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);
            "></div>`,
            iconSize: [12, 12],
            iconAnchor: [6, 6]
        });

        // Add markers with popups
        locations.forEach(loc => {
            const marker = L.marker([loc.lat, loc.lon], { icon: customIcon });
            marker.bindPopup(loc.popup, {
                maxWidth: 300,
                className: 'modern-popup'
            });
            markersLayer.addLayer(marker);
        });
        markersLayer.addTo(map);

        // Fit map to markers bounds with padding
        const group = L.featureGroup(locations.map(loc => L.marker([loc.lat, loc.lon])));
        map.fitBounds(group.getBounds().pad(0.15));
    } else {
        // Add a styled message if no coordinates
        const message = L.control({position: 'topright'});
        message.onAdd = function() {
            const div = L.DomUtil.create('div', 'map-no-data');
            div.style.cssText = `
                background: white;
                padding: 16px 20px;
                border-radius: 10px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                font-family: 'Inter', sans-serif;
                font-size: 14px;
                color: #6b7280;
            `;
            div.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>No location data available</span>
                </div>
            `;
            return div;
        };
        message.addTo(map);
    }

    // Store map reference
    window.coreFormMap = map;
    window.coreFormHeatLayer = heatLayer;
    window.coreFormMarkersLayer = markersLayer;

    // Toggle buttons functionality
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
