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
    {{-- Leaflet core CSS/JS for local map rendering --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-WWcD2Cs3Xd8quBeG7HFxPzvlaFGVDTgkHwC7vCmKu64=" crossorigin="" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" integrity="sha512-gXGsQuksaAJ+lZeI7IuAvV38DSHLVQQDBlnJrped1IovnHgwlHGawEq+y3OC/YLXTr4Wr9PXgC7cmkGexiLR3A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-QVft3LRNpPjPVhRHETpP23RrjO6D9s46n2f3LE3gdFk=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js" integrity="sha512-CGvAZ0pHoN9vv5AoG4cglHIRMYN0dgZPCx3JzUu6m4Z0Y8CS6fwtZJzN2O0oSbYDrYom5dfdrEy7i/Dr7guPPA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                return map;
            },
            /**
             * Add a marker to the map.
             */
            addMarker(map, lat, lng, options = {}) {
                return L.marker([lat, lng], options).addTo(map);
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
                const latLngs = layer.getLatLngs();
                const coords = [];
                const extract = (points) => {
                    points.forEach((point) => {
                        if (Array.isArray(point)) {
                            extract(point);
                        } else if (point && point.lat !== undefined && point.lng !== undefined) {
                            coords.push([point.lng, point.lat]);
                        }
                    });
                };
                extract(latLngs);
                return coords;
            }
        };
    </script>
@else
    {{-- Google Maps JS (legacy) --}}
    {{-- <script src="{{ $jsUrl }}"></script> --}}
@endif

