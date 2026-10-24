<script>
    // Adds the configured basemap to a Leaflet map.
    //
    // CARTO's raster tiles need a paid API key; when that key lapses every tile
    // 403s and the map draws as an empty grey box with no hint as to why. So if
    // the primary layer keeps failing we swap to the keyless fallback, which
    // costs some styling but keeps the map usable. See config/services.php.
    window.clmAddBasemap = function (map) {
        const primary = @json(config('services.maps.basemap'));
        const fallback = @json(config('services.maps.fallback'));

        const build = (source) => L.tileLayer(source.url, {
            attribution: source.attribution,
            subdomains: source.subdomains,
            maxZoom: source.max_zoom,
        });

        const layer = build(primary).addTo(map);

        // Nothing to fall back to when the fallback is already in use.
        if (primary.url === fallback.url) {
            return layer;
        }

        // One dropped tile is normal; a run of them means the provider is gone.
        let failures = 0;
        layer.on('tileerror', function onTileError() {
            if (++failures < 4) {
                return;
            }

            layer.off('tileerror', onTileError);
            map.removeLayer(layer);
            build(fallback).addTo(map);
            console.warn('Basemap tiles failed to load; switched to the fallback provider.');
        });

        return layer;
    };
</script>
