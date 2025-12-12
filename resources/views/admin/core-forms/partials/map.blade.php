<!-- Heat Map Container -->
<div class="map-container" style="margin-bottom: 24px;">
    <div class="map-header" style="margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 16px; font-weight: 600; color: var(--color-text-primary);">Geographic Distribution</h3>
        <span style="color: var(--color-text-secondary); font-size: 14px;">
            {{ count($mapData) }} locations with coordinates
        </span>
    </div>
    <div id="map" style="height: 400px; border-radius: 8px; overflow: hidden; border: 1px solid var(--color-border);"></div>
</div>

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
});
</script>
