@extends('layouts.admin.app')

@section('title',translate('Add_new_zone'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">
                        <img src="{{dynamicAsset('/public/assets/admin/img/zone.png')}}" alt="" class="mr-2"> {{translate('messages.Add_New_Business_Zone')}}
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="javascript:" method="post" id="zone_form" class="shadow--card">
                    @csrf
                    <div class="row justify-content-between">
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
                                        {{translate('messages.Use_this_‘Shape_Tool’_to_point_out_the_areas_and_connect_the_dots._A_minimum_of_3_points/dots_is_required.')}}
                                    </div>
                                </div>
                                <div class="instructions-image mt-4">
                                    <img src={{dynamicAsset('public/assets/admin/img/instructions.gif')}} alt="instructions">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-7 zone-setup">
                            <div class="pl-xl-5 pl-xxl-0">
                                    @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                                    @php($language = $language?->value )
                                    @php($default_lang = str_replace('_', '-', app()->getLocale()))
                                <div class="js-nav-scroller hs-nav-scroller-horizontal">
                                        <ul class="nav nav-tabs mb-4">
                                            <li class="nav-item">
                                                <a class="nav-link lang_link active"
                                                href="#"
                                                id="default-link">{{translate('messages.default')}} <span class="form-label-secondary text-danger mt-2"
                                                data-toggle="tooltip" data-placement="right"
                                                data-original-title="{{ translate('Choose_your_preferred_language_&_set_your_zone_name.') }}"><img
                                                src="{{ dynamicAsset('/public/assets/admin/img/info-circle.svg') }}"
                                                alt="{{ translate('messages.veg_non_veg') }}"></span></a>

                                            </li>
                                            @if ($language)
                                            @forelse (json_decode($language) as $lang)
                                                <li class="nav-item">
                                                    <a class="nav-link lang_link"
                                                        href="#"
                                                        id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                                </li>
                                                @empty
                                            @endforelse
                                            @endif

                                        </ul>
                                </div>
                                <div class="tab-content">

                                    <div class="lang_form" id="default-form">
                                        <div class="form-group mb-3 " id="">
                                            <div class="row g-3">
                                                <div class="col-sm-6">
                                                    <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.business_Zone_name')}} ({{ translate('messages.default') }})</label>
                                                    <input type="text" name="name[]"  class="form-control mb-3" placeholder="{{translate('messages.Type_new_zone_name_here')}}" maxlength="191" id="default-form-input"  oninvalid="document.getElementById('default-form-input').click()">
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.Zone_Display_Name')}} ({{ translate('messages.default') }})</label>
                                                    <input type="text" name="display_name[]" class="form-control" placeholder="{{translate('messages.Write_a_New_Display_Zone_Name')}}" maxlength="191"  >
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="lang[]" value="default">
                                    @if ($language)
                                    @forelse (json_decode($language) as $lang)
                                        <div class="d-none lang_form" id="{{$lang}}-form">
                                            <div class="form-group mb-3">
                                                <div class="row g-3">
                                                    <div class="col-sm-6">
                                                        <label class="input-label"
                                                            for="exampleFormControlInput1">{{ translate('messages.business_Zone_name')}} ({{strtoupper($lang)}})</label>
                                                        <input id="name" type="text" name="name[]" class="form-control mb-3 h--45px" placeholder="{{translate('messages.Type_new_zone_name_here')}}" >
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.Zone_Display_Name')}} ({{strtoupper($lang)}})</label>
                                                        <input type="text" name="display_name[]" class="form-control" placeholder="{{translate('messages.Write_a_New_Display_Zone_Name')}}" maxlength="191"  >
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                        <input type="hidden" name="lang[]" value="{{$lang}}">
                                    @empty
                                    @endforelse
                                    @endif
                                </div>


                                <div class="form-group mb-3 d-none">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{ translate('Coordinates') }}<span class="form-label-secondary" data-toggle="tooltip" data-placement="right"
                                        data-original-title="{{translate('messages.draw_your_zone_on_the_map')}}">{{translate('messages.draw_your_zone_on_the_map')}}</span></label>
                                        <textarea type="text" rows="8" name="coordinates"  id="coordinates" class="form-control" readonly></textarea>
                                </div>

                                <div class="map-warper overflow-hidden rounded">
                                    <input id="pac-input" class="controls rounded initial-8" title="{{translate('messages.search_your_location_here')}}" type="text" placeholder="{{translate('messages.search_here')}}"/>
                                    <div id="map-canvas" class="h-100 m-0 p-0"></div>
                                </div>
                                <div class="btn--container mt-3 justify-content-end">
                                    <button id="reset_btn" type="button" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                    <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-sm-12 col-lg-12 mb-3 my-lg-2">
                <div class="card">
                    <div class="card-header py-2 flex-wrap border-0 align-items-center">
                        <div class="search--button-wrapper">
                            <h5 class="card-title">{{translate('messages.zone_list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$zones->total()}}</span></h5>
                            <form class="my-2 mr-sm-2 mr-xl-4 ml-sm-auto flex-grow-1 flex-grow-sm-0">
                                            <!-- Search -->

                                <div class="input--group input-group input-group-merge input-group-flush">
                                    <input id="datatableSearch_" type="search" name="search" class="form-control" value="{{ request()?->search ?? null }}"
                                            placeholder="{{ translate('messages.Search_by_name') }}" aria-label="{{translate('messages.search')}}">
                                    <button type="submit" class="btn btn--secondary">
                                        <i class="tio-search"></i>
                                    </button>
                                </div>
                                <!-- End Search -->
                            </form>
                            <!-- Unfold -->
                            <div class="hs-unfold ml-3">
                                    <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle btn export-btn btn-outline-primary btn--primary font--sm" href="javascript:"
                                    data-hs-unfold-options='{
                                        "target": "#usersExportDropdown",
                                        "type": "css-animation"
                                    }'>
                                    <i class="tio-download-to mr-1"></i> {{translate('messages.export')}}
                                </a>

                                <div id="usersExportDropdown"
                                        class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                    <span class="dropdown-header">{{translate('messages.download_options')}}</span>
                                    <a target="__blank" id="export-excel" class="dropdown-item" href="{{route('admin.zone.export-zones', ['type'=>'excel', request()->getQueryString()])}}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{dynamicAsset('public/assets/admin')}}/svg/components/excel.svg"
                                        alt="Image Description">
                                        {{translate('messages.excel')}}
                                    </a>
                                    <a target="__blank" id="export-csv" class="dropdown-item" href="{{route('admin.zone.export-zones', ['type'=>'csv', request()->getQueryString()])}}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                                src="{{dynamicAsset('public/assets/admin')}}/svg/components/placeholder-csv-format.svg"
                                                alt="Image Description">
                                        {{translate('messages.csv')}}
                                    </a>
                                </div>
                            </div>
                            <!-- End Unfold -->
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                data-hs-datatables-options='{
                                    "order": [],
                                    "orderCellsTop": true,
                                    "paging":false
                                }'>
                            <thead class="thead-light">
                            <tr>
                                <th>{{translate('messages.sl')}}</th>
                                <th class="text-center">{{translate('messages.zone_id')}}</th>
                                <th class="pl-5">{{translate('messages.name')}}</th>
                                <th class="pl-5">{{translate('messages.Zone_Display_Name')}}</th>
                                <th class="text-center">{{translate('messages.restaurants')}}</th>
                                <th class="text-center">{{translate('messages.deliverymen')}}</th>
                                <th >{{translate('messages.status')}}</th>
                                <th class="w-40px text-center">{{translate('messages.action')}}</th>
                            </tr>
                            </thead>

                            <tbody id="set-rows">
                                @php($non_mod = 0)
                            @foreach($zones as $key=>$zone)
                            @php($non_mod = ( ($zone?->minimum_shipping_charge && $zone?->per_km_shipping_charge ) && $non_mod == 0) ? $non_mod:$non_mod+1 )

                                <tr>
                                    <td>{{$key+$zones?->firstItem()}}</td>
                                    <td class="text-center">
                                        <span class="move-left">
                                            {{$zone->id}}
                                        </span>
                                    </td>
                                    <td class="pl-5">
                                        <span class="d-block font-size-sm text-body">
                                            {{$zone['name']}}
                                        </span>
                                    </td>
                                    <td class="pl-5">
                                        <span class="d-block font-size-sm text-body">
                                            {{$zone['display_name']}}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="move-left">
                                            {{$zone->restaurants_count}}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="move-left">
                                            {{$zone->deliverymen_count}}
                                        </span>
                                    </td>
                                    <td>
                                        <label class="toggle-switch toggle-switch-sm">
                                            <input type="checkbox" class="toggle-switch-input dynamic-checkbox" id="status-{{$zone['id']}}" {{$zone->status?'checked':''}}
                                                   data-id="status-{{$zone['id']}}"
                                                   data-type="status"
                                                   data-image-on='{{dynamicAsset('/public/assets/admin/img/modal')}}/zone-status-on.png'
                                                   data-image-off="{{dynamicAsset('/public/assets/admin/img/modal')}}/zone-status-off.png"
                                                   data-title-on="{{translate('Want_to_activate_this_Zone?')}}"
                                                   data-title-off="{{translate('Want_to_deactivate_this_Zone?')}}"
                                                   data-text-on="<p>{{translate('If_you_activate_this_zone,_Customers_can_see_all_restaurants_&_products_available_under_this_Zone_from_the_Customer_App_&_Website.')}}</p>"
                                                   data-text-off="<p>{{translate('If_you_deactivate_this_zone,_Customers_Will_NOT_see_all_restaurants_&_products_available_under_this_Zone_from_the_Customer_App_&_Website.')}}</p>"
                                            >
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <form action="{{route('admin.zone.status',[$zone['id'],$zone->status?0:1])}}" method="get" id="status-{{$zone['id']}}_form">
                                        </form>
                                    </td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn btn-sm btn--primary btn-outline-primary action-btn"
                                                href="{{route('admin.zone.edit',[$zone['id']])}}" title="{{translate('messages.edit_zone')}}"><i class="tio-edit"></i>
                                            </a>
                                            <div class="popover-wrapper hide_data {{ $non_mod == 1 ? 'active':'' }} ">
                                                <a class="btn active action-btn btn--warning btn-outline-warning" href="{{route('admin.zone.settings',['id'=>$zone['id']])}}" title="{{translate('messages.zone_settings')}}">
                                                    <i class="tio-settings"></i>
                                                </a>
                                                <div class="popover __popover  {{ $non_mod == 1  ? '':'d-none' }}">
                                                    <div class="arrow"></div>
                                                    <h3 class="popover-header d-flex justify-content-between">
                                                        <span>{{ translate('messages.Important!') }}</span>
                                                        <span class="tio-clear hide-data"></span>
                                                    </h3>
                                                    <div class="popover-body">
                                                        {{ translate('The_Business_Zone_will_NOT_work_if_you_don’t_add_the_minimum_delivery_charge_&_per_km_delivery_charge.') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if(count($zones) === 0)
                        <div class="empty--data">
                            <img src="{{dynamicAsset('/public/assets/admin/img/empty.png')}}" alt="public">
                            <h5>
                                {{translate('no_data_found')}}
                            </h5>
                        </div>
                        @endif
                        <div class="page-area px-4 pb-3">
                            <div class="d-flex align-items-center justify-content-end">
                                <div>
                                {!! $zones->withQueryString()->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>

    <div class="modal fade" id="warning-modal">
        <div class="modal-dialog modal-lg warning-modal">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <h3 class="modal-title mb-3">{{translate('messages.New_Business_Zone_Created_Successfully!')}}</h3>
                        <p class="txt">
                            {{translate('messages.NEXT_IMPORTANT_STEP:_You_need_to_add_‘Delivery_Charge’_with_other_details_from_the_Zone_Settings._If_you_don’t_add_a_delivery_charge,_the_Zone_you_created_won’t_function_properly.')}}
                        </p>
                    </div>
                    <img src="{{dynamicAsset('/public/assets/admin/img/zone-instruction.png')}}" alt="admin/img" class="w-100">
                    <div class="mt-3 d-flex flex-wrap align-items-center justify-content-between">
                        <label class="form-check form--check m-0">
                        </label>
                        <div class="btn--container justify-content-end">
                            <button id="reset_btn" type="reset" class="btn btn--reset" data-dismiss="modal">{{translate('messages.I_Will_Do_It_Later')}}</button>
                            <a href="{{route('admin.zone.latest-settings')}}" class="btn btn--primary">{{translate('messages.Go_to_Zone_Settings')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
{{-- // OLD GOOGLE MAPS IMPLEMENTATION (commented out, kept for reference) --}}
{{-- <script src="https://maps.googleapis.com/maps/api/js?key={{ \App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value }}&libraries=drawing,places&v=3.45.8"></script> --}}
@include('partials.map-script', ['libraries' => 'drawing,places'])
<script>
    "use strict";

    $('.hide-data').click(function(){
        $(".hide_data").removeClass('active');
    })

    function status_form_alert(id, message, e) {
            e.preventDefault();
            Swal.fire({
                title: "{{translate('messages.are_you_sure_?')}}",
                text: message,
                type: 'warning',
                showCloseButton: true,
                showCancelButton: true,
                cancelButtonColor: 'var(--secondary-clr)',
                confirmButtonColor: 'var(--primary-clr)',
                cancelButtonText: '{{ translate('Cancel') }}',
                confirmButtonText: '{{ translate('yes') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#'+id).submit()
                }
            })
        }
    auto_grow();
    function auto_grow() {
        let element = document.getElementById("coordinates");
        element.style.height = "5px";
        element.style.height = (element.scrollHeight)+"px";
    }


        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            let datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });


            $('#column3_search').on('change', function () {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                let select2 = $.HSCore.components.HSSelect2.init($(this));
            });

            $("#zone_form").on('keydown', function(e){
                if (e.keyCode === 13) {
                    e.preventDefault();
                }
            })
        });

        let map; // Global declaration of the map
        let drawContext;
        let lastpolygon = null;
        let polygons = [];

        // OLD GOOGLE MAPS IMPLEMENTATION (commented out after Leaflet migration)
        // function initialize() {
        //     ...
        // }

        /**
         * Normalize any coordinate structure (array pairs, nested arrays, or lat/lng objects)
         * into an array of {lat, lng} objects that Leaflet can consume safely.
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

        function initializeLeaflet() {
            @php($default_location=\App\Models\BusinessSetting::where('key','default_location')->first())
            @php($default_location=$default_location->value?json_decode($default_location->value, true):0)
            let myLatlng = { lat: {{$default_location?$default_location['lat']:'23.757989'}}, lng: {{$default_location?$default_location['lng']:'90.360587'}} };

            map = AppMap.createMap('map-canvas', {
                center: myLatlng,
                zoom: 13
            });

            drawContext = AppMap.enablePolygonDrawing(map);

            map.on(L.Draw.Event.CREATED, function (event) {
                if (lastpolygon) {
                    drawContext.drawnItems.removeLayer(lastpolygon);
                }
                lastpolygon = event.layer;
                drawContext.drawnItems.addLayer(event.layer);
                const latLngs = event.layer.getLatLngs()[0] || [];
                const coords = latLngs.map((point) => `(${point.lat}, ${point.lng})`);
                $('#coordinates').val(coords.join(','));
                auto_grow();
            });
            // Geolocation intentionally disabled to avoid external location provider errors in some environments.
        }


        function set_all_zones()
        {
            $.get({
                url: '{{route('admin.zone.zoneCoordinates')}}',
                dataType: 'json',
                success: function (data) {
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
            $('.tab-content').find('input:text').val('');
            if (lastpolygon) {
                drawContext.drawnItems.removeLayer(lastpolygon);
                lastpolygon = null;
            }
            $('#coordinates').val(null);
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



    $('#zone_form').on('submit', function () {
            let formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.zone.store')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    if(data.errors){
                        for (let i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    }
                    else{
                        $('.tab-content').find('input:text').val('');
                        $('input[name="name"]').val(null);
                        lastpolygon.setMap(null);
                        $('#coordinates').val(null);
                        toastr.success("{{ translate('messages.New_Business_Zone_Created_Successfully!') }}", {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        $('#set-rows').html(data.view);
                        $('#itemCount').html(data.total);
                        $("#warning-modal").modal("show");
                    }
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
</script>
@endpush
