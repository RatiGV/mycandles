@extends('layouts.admin')
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2>@lang('admin.routes.' . $routes_suffix), @lang('admin.edit')</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <strong>@lang('admin.success')</strong>
            </div>
            @endif
            @if(Session::has('error'))
            <div class="alert alert-danger alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <strong>@lang('admin.error')</strong>
            </div>
            @endif
            <div class="" role="tabpanel" data-example-id="togglable-tabs">
                <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                    @forelse($Localization as $key => $lang)
                    <li role="presentation" class="">
                        <a href="#{{ $lang['prefix'] }}"
                           id="home-tab"
                           class="lang-switcher"
                           role="tab"
                           data-toggle="tab"
                           aria-expanded="true"
                           data-lang="{{ $lang['prefix'] }}"
                        >
                            {{ $lang['name'] }}
                        </a>
                    </li>
                    @empty
                    @endforelse
                </ul>
                <form id="news-form"
                      class="form-horizontal form-label-left"
                      method="post" enctype="multipart/form-data"
                      action="{{ route('Update'.$routes_suffix, $item->id) }}"
                >
                    @csrf
                    <input type="hidden"
                           name="last_edited_lang"
                           value="{{ Session::has('last_edited_lang') ? Session::get('last_edited_lang') : $configuration->admin_lang }}"
                           id="lat-edited-lang-inp"
                    >
                    <div id="myTabContent" class="tab-content">
                        <div class="form-group {{ $errors->has('image') ? 'bad' : '' }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">@lang('admin.image')</label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <input type="file" name="image" class="form-control col-md-7 col-xs-12">
                                <div class="img-or-no">
                                    @if($item->image)
                                        <br /><br />
                                        <img src="{{ $item->image }}" width="100%">
                                    @else
                                        <div class="alert alert-warning" style="margin-top: 40px;">@lang('admin.not_uploaded')</div>
                                    @endif
                                </div>
                            </div>
                            @if($item->image)
                                <div class="col-md-1 col-sm-1 col-xs-12">
                                    <a href=""
                                       class="btn btn-danger remove-file"
                                       data-id="{{ $item->id }}"
                                       data-table="{{ $main_table }}"
                                    >
                                        X
                                    </a>
                                </div>
                            @endif
                        </div>
                        @forelse($Localization as $key => $lang)
                            @php
                                $item_info = $model::getItemInfo($item->id , $lang['prefix']);
                            @endphp
                            <div role="tabpanel"
                                 class="tab-pane fadein"
                                 id="{{ $lang['prefix'] }}"
                                 aria-labelledby="home-tab"
                            >
                                @isset($translate_columns)
                                    @forelse($translate_columns as $translate_column)
                                        @if($translate_column === 'meta_description')
                                            <div class="form-group not-need {{ $errors->has('translates.'.$lang['prefix'].'.meta_description') ? 'bad' : '' }}">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                                    @lang('admin.' . $translate_column)
                                                    @if(in_array($translate_column,$required_columns))
                                                        <span class="required">*</span>
                                                    @endif
                                                </label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <textarea name="translates[{{ $lang['prefix'] }}][meta_description]"
                                                              class="form-control col-md-7 col-xs-12"
                                                              rows="5"
                                                    >{{ $item_info->meta_description }}</textarea>
                                                </div>
                                            </div>
                                        @elseif($translate_column === 'description')
                                            <div class="form-group not-need {{ $errors->has('translates.'.$lang['prefix'].'.description') ? 'bad' : '' }}">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                                    @lang('admin.' . $translate_column)
                                                    @if(in_array($translate_column,$required_columns))
                                                        <span class="required">*</span>
                                                    @endif
                                                </label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <textarea name="translates[{{ $lang['prefix'] }}][description]"
                                                              class="form-control col-md-7 col-xs-12"
                                                              id="short_description_{{ $lang["prefix"] }}"
                                                    >{{ $item_info->description }}</textarea>
                                                </div>
                                            </div>
                                        @else
                                            <div class="form-group {{ $errors->has('translates.'.$lang['prefix'].'.'.$translate_column) ? 'bad' : '' }}">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                                    @lang('admin.' . $translate_column)
                                                    @if(in_array($translate_column,$required_columns))
                                                        <span class="required">*</span>
                                                    @endif
                                                    @if($translate_column == 'meta_title')
                                                        <i class="fas fa-question-circle"
                                                           data-toggle="modal"
                                                           data-target="#mt-modal"
                                                           style="cursor: pointer"
                                                        ></i>
                                                    @endif
                                                </label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <input type="text"
                                                           name="translates[{{ $lang['prefix'] }}][{{ $translate_column }}]"
                                                           value="{{ $item_info->$translate_column }}"
                                                           class="form-control col-md-7 col-xs-12"
                                                    >
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
                                @if(in_array($main_column,$required_columns))
                                    <span class="required">*</span>
                                @endif
                            </label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <input type="text"
                                       name="{{ $main_column }}"
                                       value="{{ $item->$main_column }}"
                                       class="form-control col-md-7 col-xs-12 socials"
                                >
                            </div>
                        </div>
                    @empty
                    @endforelse
                    @php
                        if(Session::get('admin')->role == 2)
                        {
                            $actions = json_decode(DB::table('configurations')->first()->standard_admin_actions);
                        }
                        else if(Session::get('admin')->role == 3)
                        {
                            $actions = json_decode(DB::table('configurations')->first()->moderator_admin_actions);
                        }
                    @endphp
                    <input type="hidden" name="stay" id="stay-input">
                    <div class="form-group">
                        <div class="col-md-7 col-sm-7 col-xs-12 col-md-offset-3">
                            <button type="button" class="btn btn-success save-btn"  data-stay="1">
                                @lang('admin.save')
                            </button>
                            <button type="button" class="btn btn-success save-btn"  data-stay="0">
                                @lang('admin.save_and_close')
                            </button>
                            <a href="{{ route($routes_suffix) }}" class="btn btn-warning">
                                @lang('admin.cancel')
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- meta title ფანჯარა -->
<div id="mt-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">
                    @lang('admin.nice_day')
                    <i class="far fa-smile"></i>
                </h4>
            </div>
            <div class="modal-body">
                <p>
                    <img src="/Administrator/images/mt.jpg" style="width: 100%; height: 100%;">
                </p>
            </div>
        </div>
    </div>
</div>
<!-- meta title ფანჯარა -->
@endsection
