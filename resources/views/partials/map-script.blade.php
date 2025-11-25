@php
    /** @var \App\Services\Map\MapService $mapService */
    $mapService = app(\App\Services\Map\MapService::class);
    $config = $mapService->webConfig();
    $libraries = $libraries ?? null;
    $callback = $callback ?? null;
    $jsUrl = $config['js_url'] ?? null;

    if ($jsUrl && $libraries && !str_contains($jsUrl, 'libraries=')) {
        $separator = str_contains($jsUrl, '?') ? '&' : '?';
        $jsUrl .= $separator . 'libraries=' . $libraries;
    }

    if ($jsUrl && $callback && !str_contains($jsUrl, 'callback=')) {
        $separator = str_contains($jsUrl, '?') ? '&' : '?';
        $jsUrl .= $separator . 'callback=' . $callback;
    }
@endphp

@if(($config['driver'] ?? 'iran') === 'iran')
    {{-- Leaflet core CSS/JS for local map rendering (integrity removed to avoid blocking) --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

    {{-- Lightweight map helper bridging Blade maps to Leaflet --}}
    <script>
        window.AppMap = window.AppMap || {
            /**
             * Create a Leaflet map instance using defaults from config.
             */
            createMap(elementId, options = {}) {
                const center = options.center || { lat: {{ $config['default_center']['lat'] ?? 35.6892 }}, lng: {{ $config['default_center']['lng'] ?? 51.3890 }} };
                const zoom = options.zoom || {{ $config['default_center']['zoom'] ?? 12 }};

                const map = L.map(elementId).setView([center.lat, center.lng], zoom);
                const tileUrl = options.tileUrl || 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
                const tileOptions = Object.assign({
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }, options.tileOptions || {});

                const tileLayer = L.tileLayer(tileUrl, tileOptions);
                tileLayer.on('tileerror', function (errorEvent) {
                    console.warn('Tile load error', { url: errorEvent?.tile?.src, message: errorEvent?.message });
                });
                tileLayer.addTo(map);

                return map;
            },
            /**
             * Add a marker to the map.
             */
            addMarker(map, lat, lng, options = {}) {
                return L.marker([lat, lng], options).addTo(map);
            },
            /**
             * Create a polygon from coordinate pairs and optionally fit bounds.
             */
            addPolygon(map, coords = [], options = {}, fit = true) {
                if (!Array.isArray(coords) || coords.length === 0) {
                    console.warn('AppMap.addPolygon: invalid or empty coordinates', coords);
                    return null;
                }

                const latLngs = [];
                coords.forEach((point, idx) => {
                    const lat = Number(point?.lat ?? point?.latitude);
                    const lng = Number(point?.lng ?? point?.longitude);

                    if (Number.isFinite(lat) && Number.isFinite(lng)) {
                        latLngs.push([lat, lng]);
                    } else {
                        console.warn('AppMap.addPolygon: skipping invalid coordinate', { point, idx });
                    }
                });

                if (!latLngs.length) {
                    console.warn('AppMap.addPolygon: no valid coordinates to draw', coords);
                    return null;
                }

                try {
                    const polygon = L.polygon(latLngs, options).addTo(map);
                    if (fit) {
                        map.fitBounds(polygon.getBounds());
                    }
                    return polygon;
                } catch (error) {
                    console.error('AppMap.addPolygon: failed to draw polygon', error);
                    return null;
                }
            },
            /**
             * Fit map to given coordinate bounds helper.
             */
            fitToCoordinates(map, coords = []) {
                if (!Array.isArray(coords) || coords.length === 0) {
                    return;
                }
                const latLngs = [];
                coords.forEach((point, idx) => {
                    const lat = Number(point?.lat ?? point?.latitude);
                    const lng = Number(point?.lng ?? point?.longitude);
                    if (Number.isFinite(lat) && Number.isFinite(lng)) {
                        latLngs.push([lat, lng]);
                    } else {
                        console.warn('AppMap.fitToCoordinates: skipping invalid coordinate', { point, idx });
                    }
                });
                if (!latLngs.length) {
                    return;
                }
                const bounds = L.latLngBounds(latLngs);
                map.fitBounds(bounds);
            },
            /**
             * Enable polygon drawing and return feature group + draw control to mirror zone editing.
             */
            enablePolygonDrawing(map) {
                const drawnItems = new L.FeatureGroup();
                map.addLayer(drawnItems);
                const drawControl = new L.Control.Draw({
                    edit: { featureGroup: drawnItems },
                    draw: {
                        marker: false,
                        circle: false,
                        circlemarker: false,
                        polyline: false,
                        rectangle: false,
                        polygon: { showArea: true }
                    }
                });
                map.addControl(drawControl);
                return { drawnItems, drawControl };
            },
            /**
             * Convert a Leaflet layer's latlngs to an array of [lng, lat] to align with backend storage.
             */
            layerToCoordinateArray(layer) {
                if (!layer.getLatLngs) {
                    return [];
                }
                const coords = [];
                const extract = (points) => {
                    if (!points) {
                        return;
                    }
                    if (Array.isArray(points)) {
                        points.forEach((point) => extract(point));
                        return;
                    }
                    const lat = Number(points?.lat);
                    const lng = Number(points?.lng);
                    if (Number.isFinite(lat) && Number.isFinite(lng)) {
                        coords.push([lng, lat]);
                    }
                };
                try {
                    extract(layer.getLatLngs());
                } catch (error) {
                    console.warn('AppMap.layerToCoordinateArray: unable to extract latlngs', error);
                }
                return coords;
            }
        };
    </script>
@else
    {{-- Google Maps JS (legacy) --}}
    {{-- <script src="{{ $jsUrl }}"></script> --}}
@endif

