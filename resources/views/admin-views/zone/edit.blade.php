@extends('layouts.admin.app')

@section('title',translate('Update_Zone'))


@section('content')

    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title text-capitalize"><i class="tio-edit"></i> {{translate('messages.Business_Zone_update')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <form action="{{route('admin.zone.update', $zone->id)}}" method="post"  class="shadow--card">
                    @csrf
                    <div class="row">
                        <div class="col-md-5">
                            <div class="zone-setup-instructions">
                                <div class="zone-setup-top">
                                    <h6 class="subtitle">{{translate('messages.instructions')}}</h6>
                                    <p>
                                        {{translate('messages.Create_&_connect_dots_in_a_specific_area_on_the_map_to_add_a_new_business_zone.')}}
                                    </p>
                                </div>
                                <div class="zone-setup-item">
                                    <div class="zone-setup-icon">
                                        <i class="tio-hand-draw"></i>
                                    </div>
                                    <div class="info">
                                        {{translate('messages.Use_this_‘Hand_Tool’_to_find_your_target_zone.')}}
                                    </div>
                                </div>
                                <div class="zone-setup-item">
                                    <div class="zone-setup-icon">
                                        <i class="tio-free-transform"></i>
                                    </div>
                                    <div class="info">
                                        {{translate('messages.Use this ‘Shape Tool’ to point out the areas and connect the dots. A minimum of 3 points/dots is required.')}}
                                    </div>
                                </div>
                                <div class="instructions-image mt-4">
                                    <img src={{dynamicAsset('public/assets/admin/img/instructions.gif')}} alt="instructions">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-7 zone-setup">
                            <div class="form-group">
                                @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                                @php($language = $language->value ?? null)
                                @php($default_lang = str_replace('_', '-', app()->getLocale()))
                                <div class="js-nav-scroller hs-nav-scroller-horizontal">
                                <ul class="nav nav-tabs mb-4">
                                    <li class="nav-item">
                                        <a class="nav-link lang_link active"
                                        href="#"
                                        id="default-link">{{translate('messages.default')}}  <span class="form-label-secondary text-danger mt-2"
                                        data-toggle="tooltip" data-placement="right"
                                        data-original-title="{{ translate('Choose_your_preferred_language_&_set_your_zone_name.') }}"><img
                                        src="{{ dynamicAsset('/public/assets/admin/img/info-circle.svg') }}"
                                        alt="{{ translate('messages.veg_non_veg') }}"></span></a>
                                    </li>
                                    @if($language)
                                        @foreach (json_decode($language) as $lang)
                                            <li class="nav-item">
                                                <a class="nav-link lang_link"
                                                    href="#"
                                                    id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                            </li>
                                        @endforeach
                                    @endif

                                    </ul>
                                </div>
                            </div>

                            <div class="form-group lang_form" id="default-form">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.business_Zone_name')}} ({{ translate('messages.default') }})</label>
                                        <input type="text" name="name[]" class="form-control md-3" placeholder="{{translate('messages.Type_new_zone_name_here')}}" maxlength="191" value="{{$zone?->getRawOriginal('name')}}"   >
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.Zone_Display_Name')}} ({{ translate('messages.default') }})</label>
                                        <input type="text" name="display_name[]" class="form-control" placeholder="{{translate('messages.Zone_Display_Name')}}" maxlength="191" value="{{$zone?->getRawOriginal('display_name')}}"  >
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="lang[]" value="default">
                            @if($language)
                            @foreach(json_decode($language) as $lang)
                            <?php
                                if($zone?->translations){
                                    $translate = [];
                                    foreach($zone['translations'] as $t)
                                    {
                                        if($t->locale == $lang && $t->key=="name"){
                                            $translate[$lang]['name'] = $t->value;
                                        }
                                        if($t->locale == $lang && $t->key=="display_name"){
                                            $translate[$lang]['display_name'] = $t->value;
                                        }
                                    }
                                }
                            ?>
                            <div class="form-group d-none lang_form" id="{{$lang}}-form">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.business_Zone_name')}} ({{strtoupper($lang)}})</label>
                                        <input type="text" name="name[]" class="form-control mb-3" placeholder="{{translate('messages.Type_new_zone_name_here')}}" maxlength="191" value="{{$translate[$lang]['name']??''}}"  >
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.Zone_Display_Name')}} ({{strtoupper($lang)}})</label>
                                        <input type="text" name="display_name[]" class="form-control" placeholder="{{translate('messages.display_name')}}" maxlength="191" value="{{$translate[$lang]['display_name']??''}}"  >
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="lang[]" value="{{$lang}}">
                        @endforeach
                        @endif
                            <div class="form-group mb-3 initial-hidden">
                                <label class="input-label"
                                    for="exampleFormControlInput1">{{translate('messages.Coordinates')}}<span
                                    class="input-label-secondary" title="{{translate('messages.draw_your_zone_on_the_map')}}">{{translate('messages.draw_your_zone_on_the_map')}}</span></label>
                                    <textarea  type="text" name="coordinates"  id="coordinates" class="form-control">
                                        @foreach($area['coordinates'] as $key=>$coords)
                                        <?php if(count($area['coordinates']) != $key+1) {if($key != 0) echo(','); ?>({{$coords[1]}}, {{$coords[0]}})
                                        <?php } ?>
                                        @endforeach
                                    </textarea>
                            </div>

                            <div class="initial-60">
                                <input id="pac-input" class="controls rounded initial-8" title="{{translate('messages.search_your_location_here')}}" type="text" placeholder="{{translate('messages.search_here')}}"/>
                                <div id="map-canvas" class="h-100 m-0 p-0"></div>
                            </div>
                            <div class="btn--container mt-3 justify-content-end">
                                <button id="reset_btn" type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
{{-- // OLD GOOGLE MAPS IMPLEMENTATION (commented out, kept for reference) --}}
{{-- <script src="https://maps.googleapis.com/maps/api/js?v=3.45.8&key={{ \App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value }}&libraries=drawing,places"></script> --}}
@include('partials.map-script', ['libraries' => 'drawing,places'])
<script>
    "use strict";
    auto_grow();
    function auto_grow() {
        let element = document.getElementById("coordinates");
        element.style.height = "5px";
        element.style.height = (element.scrollHeight)+"px";
    }

    // OLD GOOGLE MAPS IMPLEMENTATION (commented out after Leaflet migration)
    // ... original initialize(), drawingManager, and SearchBox logic ...

    let map; // Global declaration of the map
    let drawContext;
    let lastpolygon = null;
    let polygons = [];

    /**
     * Normalize raw coordinate structures (arrays of [lng, lat], nested arrays, or objects with lat/lng)
     * into an array of {lat, lng} objects usable by Leaflet.
     */
    function normalizeCoordinates(rawCoords) {
        const normalized = [];
        const walk = (value) => {
            if (Array.isArray(value)) {
                if (value.length === 2 && Number.isFinite(Number(value[0])) && Number.isFinite(Number(value[1]))) {
                    normalized.push({ lat: Number(value[1]), lng: Number(value[0]) });
                } else {
                    value.forEach((child) => walk(child));
                }
                return;
            }

            if (value && typeof value === 'object' && Number.isFinite(Number(value.lat)) && Number.isFinite(Number(value.lng))) {
                normalized.push({ lat: Number(value.lat), lng: Number(value.lng) });
            }
        };

        walk(rawCoords);
        return normalized;
    }

    /**
     * Format an array of Leaflet latlngs into the original coordinate string
     * "(lat, lng),(lat, lng)" expected by the backend.
     */
    function formatCoordinates(latLngs) {
        if (!Array.isArray(latLngs) || !latLngs.length) {
            return '';
        }
        const coords = latLngs.map((point) => `(${point.lat}, ${point.lng})`);
        return coords.join(',');
    }

    function initializeLeaflet() {
        const center = {
            lat: {{trim(explode(' ',$zone->center)[1], 'POINT()')}},
            lng: {{trim(explode(' ',$zone->center)[0], 'POINT()')}}
        };

        map = AppMap.createMap('map-canvas', {
            center: center,
            zoom: 13
        });

        drawContext = AppMap.enablePolygonDrawing(map);

        const polygonCoords = @json($area['coordinates']);
        const normalizedPolygonCoords = normalizeCoordinates(polygonCoords);

        if (normalizedPolygonCoords.length) {
            const polygon = AppMap.addPolygon(map, normalizedPolygonCoords, {
                color: '#050df2',
                weight: 2,
                opacity: 0.8,
                fillColor: '#050df2',
                fillOpacity: 0.1
            });
            if (polygon) {
                drawContext.drawnItems.addLayer(polygon);
                lastpolygon = polygon;
                $('#coordinates').val(formatCoordinates(polygon.getLatLngs()[0] || []));
                auto_grow();
            }
        }

        map.on(L.Draw.Event.CREATED, function (event) {
            if (lastpolygon) {
                drawContext.drawnItems.removeLayer(lastpolygon);
            }
            lastpolygon = event.layer;
            drawContext.drawnItems.addLayer(event.layer);
            const latLngs = event.layer.getLatLngs()[0] || [];
            $('#coordinates').val(formatCoordinates(latLngs));
            auto_grow();
        });

        map.on(L.Draw.Event.EDITED, function (event) {
            event.layers.eachLayer(function (layer) {
                const latLngs = layer.getLatLngs()[0] || [];
                $('#coordinates').val(formatCoordinates(latLngs));
                auto_grow();
            });
        });
    }

    function set_all_zones()
    {
        $.get({
            url: '{{route('admin.zone.zoneCoordinates')}}/{{$zone->id}}',
            dataType: 'json',
            success: function (data) {
                polygons = [];
                for(let i=0; i<data.length;i++)
                {
                    const zonePoints = Array.isArray(data[i]) ? data[i] : [];
                    const coords = normalizeCoordinates(zonePoints);

                    const polygon = AppMap.addPolygon(map, coords, {
                        color: '#FF0000',
                        weight: 2,
                        opacity: 0.8,
                        fillColor: '#FF0000',
                        fillOpacity: 0.1
                    }, false);
                    if (polygon) {
                        polygons.push(polygon);
                    }
                }
                if (polygons.length) {
                    const allPoints = polygons
                        .map((poly) => AppMap.layerToCoordinateArray(poly))
                        .flat()
                        .map((pair) => ({ lat: pair[1], lng: pair[0] }))
                        .filter((point) => Number.isFinite(point.lat) && Number.isFinite(point.lng));
                    AppMap.fitToCoordinates(map, allPoints);
                }

            },
        });
    }
    $(document).on('ready', function(){
        initializeLeaflet();
        set_all_zones();
    });

    $('#reset_btn').click(function(){
        if (lastpolygon && drawContext) {
            drawContext.drawnItems.removeLayer(lastpolygon);
            lastpolygon = null;
        }
        $('#coordinates').val('');
        auto_grow();
    })
    $(document).on('ready', function() {

$("#maximum_shipping_charge_status").on('change', function() {
    if ($("#maximum_shipping_charge_status").is(':checked')) {
        $('#maximum_shipping_charge').removeAttr('readonly');
    } else {
        $('#maximum_shipping_charge').attr('readonly', true);
        $('#maximum_shipping_charge').val('Ex : 0');
    }
});
$("#max_cod_order_amount_status").on('change', function() {
    if ($("#max_cod_order_amount_status").is(':checked')) {
        $('#max_cod_order_amount').removeAttr('readonly');
    } else {
        $('#max_cod_order_amount').attr('readonly', true);
        $('#max_cod_order_amount').val('Ex : 0');
    }
});
});
</script>
@endpush
