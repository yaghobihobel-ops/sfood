@php
    /** @var \App\Services\Map\MapService $mapService */
    $mapService = app(\App\Services\Map\MapService::class);
    $config = $mapService->webConfig();
    $libraries = $libraries ?? null;
    $callback = $callback ?? null;
    $jsUrl = $config['js_url'] ?? null;
    $tileUrl = $config['tile_url'] ?? 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    $tileUrls = $config['tile_urls'] ?? [];

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
        const defaultTileUrl = @json($tileUrl);
        const defaultTileUrls = (() => {
            const urls = @json($tileUrls);
            if (Array.isArray(urls) && urls.length) {
                return urls;
            }
            return [defaultTileUrl];
        })();

        window.AppMap = window.AppMap || {
            /**
             * Create a Leaflet map instance using defaults from config.
             */
            createMap(elementId, options = {}) {
                const center = options.center || { lat: {{ $config['default_center']['lat'] ?? 35.6892 }}, lng: {{ $config['default_center']['lng'] ?? 51.3890 }} };
                const zoom = options.zoom || {{ $config['default_center']['zoom'] ?? 12 }};

                const map = L.map(elementId).setView([center.lat, center.lng], zoom);

                const fallbackTileUrls = Array.isArray(options.fallbackTileUrls) && options.fallbackTileUrls.length
                    ? options.fallbackTileUrls
                    : (options.tileUrls || defaultTileUrls);

                const tileOptions = Object.assign({
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }, options.tileOptions || {});

                const tileLayer = this.addTileLayer(map, fallbackTileUrls, tileOptions);
                map.__appMapTileLayer = tileLayer;

                return map;
            },
            /**
             * Add a tile layer with basic fallback support and error logging.
             */
            addTileLayer(map, tileUrls, tileOptions = {}) {
                const sources = Array.isArray(tileUrls) && tileUrls.length ? tileUrls : defaultTileUrls;
                let currentIndex = 0;
                const layer = L.tileLayer(sources[currentIndex], tileOptions);

                layer.on('tileerror', (errorEvent) => {
                    console.warn('Tile load error', { url: errorEvent?.tile?.src, message: errorEvent?.message });
                    if (sources.length > currentIndex + 1) {
                        currentIndex += 1;
                        layer.setUrl(sources[currentIndex]);
                    } else {
                        map.__tilesFailed = true;
                        const container = map.getContainer();
                        if (container && !container.querySelector('.tile-fallback-message')) {
                            const overlay = document.createElement('div');
                            overlay.className = 'tile-fallback-message';
                            overlay.style.position = 'absolute';
                            overlay.style.top = '0';
                            overlay.style.left = '0';
                            overlay.style.right = '0';
                            overlay.style.bottom = '0';
                            overlay.style.display = 'flex';
                            overlay.style.alignItems = 'center';
                            overlay.style.justifyContent = 'center';
                            overlay.style.background = 'rgba(0,0,0,0.35)';
                            overlay.style.color = '#fff';
                            overlay.style.textAlign = 'center';
                            overlay.style.padding = '8px';
                            overlay.style.fontSize = '14px';
                            overlay.innerText = 'Map tiles could not be loaded. Please configure MAP_TILE_URL(S) or check your network connection.';
                            container.style.position = 'relative';
                            container.appendChild(overlay);
                        }
                    }
                });

                layer.addTo(map);
                return layer;
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
                    let lat = null;
                    let lng = null;

                    if (Array.isArray(point) && point.length >= 2) {
                        lng = Number(point[0]);
                        lat = Number(point[1]);
                    } else {
                        lat = Number(point?.lat ?? point?.latitude);
                        lng = Number(point?.lng ?? point?.longitude);
                    }

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
                    let lat = null;
                    let lng = null;

                    if (Array.isArray(point) && point.length >= 2) {
                        lng = Number(point[0]);
                        lat = Number(point[1]);
                    } else {
                        lat = Number(point?.lat ?? point?.latitude);
                        lng = Number(point?.lng ?? point?.longitude);
                    }

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

