@extends('layouts.admin')
@section('content')
    @php
        $file_labels = [
            'logo' => 'logo',
            'logo_for_admin' => 'logo_for_admin',
            'favicon' => 'favicon',
            'login_bg' => 'bg_for_login',
            'top_banner' => 'top_banner',
            'bottom_banner' => 'bottom_banner',
        ];
        $file_widths = [
            'favicon' => '50',
            'login_bg' => '100%',
        ];
    @endphp
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
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>@lang('admin.success')</strong>
                    </div>
                @endif
                @if (Session::has('error'))
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>@lang('admin.error')</strong>
                    </div>
                @endif
                @if (Session::has('no_permission'))
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>@lang('admin.no_permission')</strong>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <ul style="margin: 0; padding-left: 18px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
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
                                    $prefix = $lang['prefix'];
                                    $translation = $translations->get($prefix);
                                @endphp
                                <div role="tabpanel" class="tab-pane fade in" id="{{ $prefix }}"
                                    aria-labelledby="home-tab">
                                    @forelse($translate_columns as $translate_column)
                                        @php
                                            $field_name = 'translates[' . $prefix . '][' . $translate_column . ']';
                                            $field_key = 'translates.' . $prefix . '.' . $translate_column;
                                            $field_value = old($field_key, $translation->$translate_column ?? '');
                                            $is_editor = in_array($translate_column, ['short_description', 'description'], true);
                                        @endphp
                                        <div
                                            class="form-group {{ $is_editor ? 'not-need' : '' }} {{ $errors->has($field_key) ? 'bad' : '' }}">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                                @lang('admin.' . $translate_column)
                                                @if (in_array($translate_column, $required_columns))
                                                    <span class="required">*</span>
                                                @endif
                                            </label>
                                            <div class="col-md-7 col-sm-7 col-xs-12">
                                                @if ($translate_column === 'short_description')
                                                    <textarea name="{{ $field_name }}" class="form-control col-md-7 col-xs-12"
                                                        id="editor_{{ $prefix }}">{{ $field_value }}</textarea>
                                                @elseif($translate_column === 'description')
                                                    <textarea name="{{ $field_name }}" class="form-control col-md-7 col-xs-12"
                                                        id="short_description_{{ $prefix }}">{{ $field_value }}</textarea>
                                                @else
                                                    <input type="text" name="{{ $field_name }}"
                                                        value="{{ $field_value }}"
                                                        class="form-control col-md-7 col-xs-12">
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                    @endforelse
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
                                    <input type="text" name="{{ $main_column }}"
                                        value="{{ old($main_column, $item->$main_column) }}"
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
                                <textarea name="pixel" class="form-control col-md-7 col-xs-12" rows="5">{{ old('pixel', $item->pixel) }}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                @lang('admin.analytics')
                            </label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <textarea name="analytics" class="form-control col-md-7 col-xs-12" rows="5">{{ old('analytics', $item->analytics) }}</textarea>
                            </div>
                        </div>
                        @foreach ($file_columns as $file_column)
                            @php
                                $file_value = $item->$file_column;
                                $file_width = $file_widths[$file_column] ?? '100';
                            @endphp
                            <div class="ln_solid"></div>
                            <div class="form-group {{ $errors->has($file_column) ? 'bad' : '' }}">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                    @lang('admin.' . ($file_labels[$file_column] ?? $file_column))
                                </label>
                                <div class="col-md-7 col-sm-7 col-xs-12">
                                    <input type="file" name="{{ $file_column }}"
                                        class="form-control col-md-7 col-xs-12">
                                    <div class="img-or-no">
                                        @if ($file_value)
                                            <br /><br />
                                            <img src="{{ $file_value }}" width="{{ $file_width }}">
                                        @else
                                            <div class="alert alert-warning" style="margin-top: 40px;">
                                                @lang('admin.not_uploaded')
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if ($file_value)
                                    <div class="col-md-1 col-sm-1 col-xs-12">
                                        <a href="" class="btn btn-danger remove-file" data-id="{{ $item->id }}"
                                            data-table="{{ $main_table }}">
                                            X
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        <div class="ln_solid"></div>
                        <div class="form-group {{ $errors->has('longitude') ? 'bad' : '' }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                @lang('admin.longitude')
                            </label>
                            <div class="col-md-7 col-sm-6 col-xs-12">
                                <input type="text" name="longitude" value="{{ old('longitude', $item->longitude) }}"
                                    class="form-control col-md-7 col-xs-12" id="longclicked">
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('latitude') ? 'bad' : '' }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                @lang('admin.latitude')
                            </label>
                            <div class="col-md-7 col-sm-6 col-xs-12">
                                <input type="text" name="latitude" value="{{ old('latitude', $item->latitude) }}"
                                    class="form-control col-md-7 col-xs-12" id="latclicked">
                            </div>
                        </div>
                        <div class="form-group">
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
        /*
         * კოორდინატები ყოველთვის რიცხვითი ლიტერალია - ცარიელი მნიშვნელობა
         * ადრე javascript-ის სინტაქსურ შეცდომას იწვევდა და მთელი ბლოკი ითიშებოდა.
         */
        var lat_data = {{ (float) (old('latitude', $item->latitude) ?: 41.7151) }};
        var lng_data = {{ (float) (old('longitude', $item->longitude) ?: 44.8271) }};
        var map;
        var markers = [];
        function initMap() {
            var center = {
                lat: lat_data,
                lng: lng_data
            };
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 14,
                center: center,
                mapTypeId: 'terrain'
            });
            map.addListener('click', function(event) {
                deleteMarkers();
                addMarker(event.latLng);
                document.getElementById('latclicked').value = event.latLng.lat();
                document.getElementById('longclicked').value = event.latLng.lng();
            });
            addMarker(center);
        }
        function addMarker(location) {
            var marker = new google.maps.Marker({
                position: location,
                map: map
            });
            markers.push(marker);
        }
        function setMapOnAll(map) {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(map);
            }
        }
        function clearMarkers() {
            setMapOnAll(null);
        }
        function deleteMarkers() {
            clearMarkers();
            markers = [];
        }
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
