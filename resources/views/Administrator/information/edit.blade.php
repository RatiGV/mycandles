@extends('layouts.admin')
@section('content')
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>@lang('admin.routes.' . $routes_suffix)</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                @if (Session::has('success'))
                    <div class="alert alert-success alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <strong>@lang('admin.success')</strong>
                    </div>
                @endif
                @if (Session::has('error'))
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">×</span>
                        </button>
                        <strong>@lang('admin.error')</strong>
                    </div>
                @endif
                @if (Session::has('no_permission'))
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">×</span>
                        </button>
                        <strong>@lang('admin.no_permission')</strong>
                    </div>
                @endif
                <div class="" role="tabpanel" data-example-id="togglable-tabs">
                    <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                        @forelse($Localization as $key => $lang)
                            <li role="presentation" class="">
                                <a href="#{{ $lang['prefix'] }}" id="home-tab" class="lang-switcher" role="tab"
                                    data-toggle="tab" aria-expanded="true" data-lang="{{ $lang['prefix'] }}">
                                    {{ $lang['name'] }}
                                </a>
                            </li>
                        @empty
                        @endforelse
                    </ul>
                    <form id="news-form" class="form-horizontal form-label-left" method="post"
                        enctype="multipart/form-data" action="{{ route('Update' . $routes_suffix, $item->id) }}">
                        @csrf
                        <input type="hidden" name="last_edited_lang"
                            value="{{ Session::has('last_edited_lang') ? Session::get('last_edited_lang') : $configuration->admin_lang }}"
                            id="lat-edited-lang-inp">
                        <div id="myTabContent" class="tab-content">
                            @forelse($Localization as $key => $lang)
                                @php
                                    $item_info = $model::getItemInfo($item->id, $lang['prefix']);
                                @endphp
                                <div role="tabpanel" class="tab-pane fade in" id="{{ $lang['prefix'] }}"
                                    aria-labelledby="home-tab">
                                    @isset($translate_columns)
                                        @forelse($translate_columns as $translate_column)
                                            @if ($translate_column === 'short_description')
                                                <div
                                                    class="form-group not-need {{ $errors->has('translates.' . $lang['prefix'] . '.short_description') ? 'bad' : '' }}">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                                        @lang('admin.' . $translate_column)
                                                        @if (in_array($translate_column, $required_columns))
                                                            <span class="required">*</span>
                                                        @endif
                                                    </label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <textarea name="translates[{{ $lang['prefix'] }}][short_description]" class="form-control col-md-7 col-xs-12"
                                                            id="editor_{{ $lang['prefix'] }}">{{ $item_info->short_description }}</textarea>
                                                    </div>
                                                </div>
                                            @elseif($translate_column === 'description')
                                                <div
                                                    class="form-group not-need {{ $errors->has('translates.' . $lang['prefix'] . '.description') ? 'bad' : '' }}">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                                        @lang('admin.' . $translate_column)
                                                        @if (in_array($translate_column, $required_columns))
                                                            <span class="required">*</span>
                                                        @endif
                                                    </label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <textarea name="translates[{{ $lang['prefix'] }}][description]" class="form-control col-md-7 col-xs-12"
                                                            id="short_description_{{ $lang['prefix'] }}">{{ $item_info->description }}</textarea>
                                                    </div>
                                                </div>
                                            @else
                                                <div
                                                    class="form-group {{ $errors->has('translates.' . $lang['prefix'] . '.' . $translate_column) ? 'bad' : '' }}">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                                        @lang('admin.' . $translate_column)
                                                        @if (in_array($translate_column, $required_columns))
                                                            <span class="required">*</span>
                                                        @endif
                                                    </label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input type="text"
                                                            name="translates[{{ $lang['prefix'] }}][{{ $translate_column }}]"
                                                            value="{{ $item_info->$translate_column }}"
                                                            class="form-control col-md-7 col-xs-12">
                                                    </div>
                                                </div>
                                            @endif
                                        @empty
                                        @endforelse
                                    @endisset
                                    <div class="ln_solid"></div>
                                </div>
                                @empty
                                @endforelse
                            </div>
                            @forelse($main_columns as $main_column)
                                <div class="form-group {{ $errors->has($main_column) ? 'bad' : '' }}">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                        @lang('admin.' . $main_column)
                                        @if (in_array($main_column, $required_columns))
                                            <span class="required">*</span>
                                        @endif
                                    </label>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <input type="text" name="{{ $main_column }}" value="{{ $item->$main_column }}"
                                            class="form-control col-md-7 col-xs-12 socials">
                                    </div>
                                </div>
                            @empty
                            @endforelse
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                    @lang('admin.pixel')
                                </label>
                                <div class="col-md-7 col-sm-7 col-xs-12">
                                    <textarea name="pixel" class="form-control col-md-7 col-xs-12" rows="5">{{ $information->pixel }}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                    @lang('admin.analytics')
                                </label>
                                <div class="col-md-7 col-sm-7 col-xs-12">
                                    <textarea name="analytics" class="form-control col-md-7 col-xs-12" rows="5">{{ $information->analytics }}</textarea>
                                </div>
                            </div>
                            <div class="ln_solid"></div>
                            <div class="form-group {{ $errors->has('logo') ? 'bad' : '' }}">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">@lang('admin.logo')</label>
                                <div class="col-md-7 col-sm-7 col-xs-12">
                                    <input type="file" name="logo" class="form-control col-md-7 col-xs-12">
                                    <div class="img-or-no">
                                        @if ($information->logo)
                                            <br /><br />
                                            <img src="{{ $information->logo }}" width="100">
                                        @else
                                            <div class="alert alert-warning" style="margin-top: 40px;">@lang('admin.not_uploaded')
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if ($information->logo)
                                    <div class="col-md-1 col-sm-1 col-xs-12">
                                        <a href="" class="btn btn-danger remove-file" data-id="{{ $item->id }}"
                                            data-table="{{ $main_table }}">
                                            X
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="ln_solid"></div>
                            <div class="form-group {{ $errors->has('favicon') ? 'bad' : '' }}">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">@lang('admin.favicon')</label>
                                <div class="col-md-7 col-sm-7 col-xs-12">
                                    <input type="file" name="favicon" class="form-control col-md-7 col-xs-12">
                                    <div class="img-or-no">
                                        @if ($information->favicon)
                                            <br /><br />
                                            <img src="{{ $information->favicon }}" width="50">
                                        @else
                                            <div class="alert alert-warning" style="margin-top: 40px;">@lang('admin.not_uploaded')
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if ($information->favicon)
                                    <div class="col-md-1 col-sm-1 col-xs-12">
                                        <a href="" class="btn btn-danger remove-file" data-id="{{ $item->id }}"
                                            data-table="{{ $main_table }}">
                                            X
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="ln_solid"></div>
                            <div class="form-group {{ $errors->has('top_banner') ? 'bad' : '' }}">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">@lang('admin.top_banner')</label>
                                <div class="col-md-7 col-sm-7 col-xs-12">
                                    <input type="file" name="top_banner" class="form-control col-md-7 col-xs-12">
                                    <div class="img-or-no">
                                        @if ($information->top_banner)
                                            <br /><br />
                                            <img src="{{ $information->top_banner }}" width="100">
                                        @else
                                            <div class="alert alert-warning" style="margin-top: 40px;">@lang('admin.not_uploaded')
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if ($information->top_banner)
                                    <div class="col-md-1 col-sm-1 col-xs-12">
                                        <a href="" class="btn btn-danger remove-file" data-id="{{ $item->id }}"
                                            data-table="{{ $main_table }}">
                                            X
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="ln_solid"></div>
                            <div class="form-group {{ $errors->has('bottom_banner') ? 'bad' : '' }}">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">@lang('admin.bottom_banner')</label>
                                <div class="col-md-7 col-sm-7 col-xs-12">
                                    <input type="file" name="bottom_banner" class="form-control col-md-7 col-xs-12">
                                    <div class="img-or-no">
                                        @if ($information->bottom_banner)
                                            <br /><br />
                                            <img src="{{ $information->bottom_banner }}" width="100">
                                        @else
                                            <div class="alert alert-warning" style="margin-top: 40px;">@lang('admin.not_uploaded')
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if ($information->bottom_banner)
                                    <div class="col-md-1 col-sm-1 col-xs-12">
                                        <a href="" class="btn btn-danger remove-file" data-id="{{ $item->id }}"
                                            data-table="{{ $main_table }}">
                                            X
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="ln_solid"></div>
                            <div class="form-group {{ $errors->has('login_bg') ? 'bad' : '' }}">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                    @lang('admin.bg_for_login')
                                </label>
                                <div class="col-md-7 col-sm-7 col-xs-12">
                                    <input type="file" name="login_bg" class="form-control col-md-7 col-xs-12">
                                    <div class="img-or-no">
                                        @if ($information->login_bg)
                                            <br /><br />
                                            <img src="{{ $information->login_bg }}" width="100%">
                                        @else
                                            <div class="alert alert-warning" style="margin-top: 40px;">
                                                @lang('admin.not_uploaded')
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if ($information->login_bg)
                                    <div class="col-md-1 col-sm-1 col-xs-12">
                                        <a href="" class="btn btn-danger remove-file" data-id="{{ $item->id }}"
                                            data-table="{{ $main_table }}">
                                            X
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="ln_solid"></div>
                            <div class="form-group {{ $errors->has('longitude') ? 'bad' : '' }}">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                    @lang('admin.longitude')
                                </label>
                                <div class="col-md-7 col-sm-6 col-xs-12">
                                    <input type="text" name="longitude" value="{{ $information->longitude }}"
                                        class="form-control col-md-7 col-xs-12" id="longclicked">
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('latitude') ? 'bad' : '' }}">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                    @lang('admin.latitude')
                                </label>
                                <div class="col-md-7 col-sm-6 col-xs-12">
                                    <input type="text" name="latitude" value="{{ $information->latitude }}"
                                        class="form-control col-md-7 col-xs-12" id="latclicked">
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('longitude') ? 'bad' : '' }}">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12"></label>
                                <div class="col-md-7 col-sm-6 col-xs-12">
                                    <div id="map"></div>
                                </div>
                            </div>
                            <div class="ln_solid"></div>
                            <input type="hidden" name="stay" id="stay-input">
                            <div class="form-group">
                                <div class="col-md-7 col-sm-7 col-xs-12 col-md-offset-3">
                                    <button type="button" class="btn btn-success save-btn" data-stay="1">
                                        @lang('admin.save')
                                    </button>
                                    <button type="button" class="btn btn-success save-btn" data-stay="0">
                                        @lang('admin.save_and_close')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @push('js')
        <script>
            var map;
            var markers = [];
            var lat_data = {{ $information->latitude }};
            var lng_data = {{ $information->longitude }};

            function initMap() {
                var haightAshbury = {
                    lat: lat_data,
                    lng: lng_data
                };

                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 14,
                    center: haightAshbury,
                    mapTypeId: 'terrain'
                });

                // This event listener will call addMarker() when the map is clicked.
                map.addListener('click', function(event) {
                    deleteMarkers();
                    addMarker(event.latLng);
                    document.getElementById('latclicked').value = event.latLng.lat();
                    document.getElementById('longclicked').value = event.latLng.lng();
                });

                // Adds a marker at the center of the map.
                addMarker(haightAshbury);

            }

            // Adds a marker to the map and push to the array.
            function addMarker(location) {
                var marker = new google.maps.Marker({
                    position: location,
                    map: map
                });
                markers.push(marker);
            }

            // Sets the map on all markers in the array.
            function setMapOnAll(map) {
                for (var i = 0; i < markers.length; i++) {
                    markers[i].setMap(map);
                }
            }

            // Removes the markers from the map, but keeps them in the array.
            function clearMarkers() {
                setMapOnAll(null);
            }

            // Shows any markers currently in the array.
            function showMarkers() {
                setMapOnAll(map);
            }

            // Deletes all markers in the array by removing references to them.
            function deleteMarkers() {
                clearMarkers();
                markers = [];
            }

            $('.save-btn').on('click', function() {
                $('#stay-input').val($(this).data('stay'));
                $('#news-form').submit();
            });
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCvLNwtkSukaPXxxkvlapBulAEreC4Wsl8&callback=initMap" async
            defer></script>
        <style>
            #map {
                height: 300px;
                width: 100%;
                margin: 0 auto;
            }
        </style>
    @endpush
