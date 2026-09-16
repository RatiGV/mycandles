@extends('layouts.admin')
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2>@lang('admin.routes.' . $routes_suffix)</h2>
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
            @if(Session::has('cache_cleared'))
            <div class="alert alert-success alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <strong>@lang('admin.cache_cleared')</strong>
            </div>
            @endif
            <div class="" role="tabpanel" data-example-id="togglable-tabs">
                <form id="news-form"  
                      class="form-horizontal form-label-left" 
                      method="post"
                      enctype="multipart/form-data" 
                      action="{{ route('Update'.$routes_suffix, $item->id) }}"
                >
                    @csrf
                    <div class="form-group not-need {{ $errors->has('language') ? 'bad' : '' }}">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">
                            @lang('admin.admin_language')
                        </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <select name="admin_lang" class="form-control col-md-7 col-xs-12">
                                <option value="0" >@lang('admin.admin_language')</option>
                                @forelse($languages as $lang)
                                    <option value="{{ $lang['prefix'] }}" 
                                            {{ $item->admin_lang == $lang['prefix'] ? 'selected' : '' }}
                                    >
                                        {{ $lang['name'] }}
                                    </option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('color') ? 'bad' : '' }}">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">
                            @lang('admin.admin_color')
                        </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div>
                                <div id="color-picker-component" class="colors input-group colorpicker-component" style="margin: 0;" >
                                    <input type="text" name="admin_color" value="{{ $item->admin_color }}" class="form-control"/>
                                    <span class="input-group-addon"><i></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">
                            @lang('admin.crop_images_for_this_modules')
                        </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            @php
                                $crop_images_for_this_tables = json_decode($item->crop_images_for_this_tables)
                            @endphp
                            @foreach($modules as $module)
                                <label class="checkbox-inline">
                                    <input type="checkbox" 
                                           value="{{ $module['title'] }}" 
                                           name="crop_images_for_this_tables[]"
                                           {{ count($crop_images_for_this_tables) && in_array($module['title'], $crop_images_for_this_tables) ? 'checked' : '' }}
                                    >
                                    {{ trans('admin.routes.' . $module['route']) }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">
                            @lang('admin.standard_admin_actions')
                        </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            @php
                                $standard_admin_actions = json_decode($item->standard_admin_actions)
                            @endphp
                            @foreach($actions as $action)
                                <label class="checkbox-inline">
                                    <input type="checkbox" 
                                           value="{{ $action }}" 
                                           name="standard_admin_actions[]"
                                           {{ count($standard_admin_actions) && in_array($action, $standard_admin_actions) ? 'checked' : '' }}
                                    >
                                    {{ trans('admin.' . $action) }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">
                            @lang('admin.moderator_admin_actions')
                        </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            @php
                                $moderator_admin_actions = json_decode($item->moderator_admin_actions)
                            @endphp
                            @foreach($actions as $action)
                                <label class="checkbox-inline">
                                    <input type="checkbox" 
                                           value="{{ $action }}" 
                                           name="moderator_admin_actions[]"
                                           {{ count($moderator_admin_actions) && in_array($action, $moderator_admin_actions) ? 'checked' : '' }}
                                    >
                                    {{ trans('admin.' . $action) }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">
                            @lang('admin.cache')
                        </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div class="iradio">
                                <input type="checkbox" 
                                       name="cache" 
                                       class="js-switch change-caches {{ count($config) ? 'active' : '' }}" 
                                       {{ count($config) ? 'checked' : '' }}
                                />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12"></label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <div id="cache-div" style="display: {{ count($config) ? 'block' : 'none' }};">
                                <table class="table table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th class="text-center"></th>
                                            <th class="text-center">@lang('admin.module')</th>
                                            <th class="text-center">@lang('admin.cache_time')</th>
                                            <th class="text-center">@lang('admin.expires_at')</th>
                                            <th class="text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($modules as $module)
                                            <tr>
                                                <th class="text-center">
                                                    <input type="checkbox" 
                                                           value="{{ $module['title'] }}" 
                                                           name="module[]"
                                                           class="cache-module-checkbox"
                                                           {{ Cache::has($module['title']) ? 'checked' : '' }}
                                                    >
                                                <td>
                                                    {{ trans('admin.routes.' . $module['route']) }}
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                           name="cache_time[]"
                                                           value="{{ Cache::has($module['title']) && array_key_exists($module['title'], $config) ? $config[$module['title']]['minutes'] : '' }}"
                                                           placeholder="@lang('admin.min')"
                                                           step="1"
                                                           min="1"
                                                           class="cache-time-inp"
                                                           {{ Cache::has($module['title']) ? 'readonly' : '' }}
                                                    >
                                                </td>
                                                <td>
                                                    {{ Cache::has($module['title']) && array_key_exists($module['title'], $config) ? $config[$module['title']]['expires_at'] : '' }}
                                                </td>
                                                <th  class="text-center">
                                                    @if(Cache::has($module['title']))
                                                        <a href="javascript:void(0)" 
                                                           class="btn btn-danger btn-xs forget-cache-btn" 
                                                           data-cache-key="{{ $module['title'] }}"
                                                           style="margin-top: -5px;margin-left: 10px;"
                                                        >
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    @endif
                                                </th>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">
                            @lang('admin.whitelist')
                            <i class="fas fa-question-circle"
                               data-toggle="modal" 
                               data-target="#ip-info-modal"
                               style="cursor: pointer"
                            ></i>
                        </label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <span class="badge badge-info">
                                178.19.146.189
                                <i class="fas fa-times remove-ip"></i>
                            </span>
                            <span class="badge badge-info">
                                132.345.132186
                                <i class="fas fa-times remove-ip"></i>
                            </span>
                            <i class="fas fa-plus" 
                               data-toggle="modal" 
                               data-target="#ip-modal"
                               style="cursor: pointer"></i>
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <input type="hidden" name="stay" value="1" id="stay-input">
                    <div class="form-group">
                        <div class="col-md-7 col-sm-7 col-xs-12 col-md-offset-3">
                            <button type="submit" class="btn btn-success save-btn">@lang('admin.save')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- ინფორმაცია IP მისამართების შესახებ -->
<div id="ip-info-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">
                    @lang('admin.nice_day')
                    <i class="far fa-smile"></i>
                </h4>
            </div>
            <div class="modal-body" style="padding: 20px; font-family: 'Menu Font' !important;">
                <p>
                    არის შემთხვევები, როდესაც მომხმარებელი მოითხოვს, რომ ვებ-გვერდი
                    გაიხსნას მხოლოდ კონკრეტულ IP მისამართებზე. ასეთი საჭიროების შემთხვევაში,
                    საიტი გაიხსნება მხოლოდ იმ IP მისამართებზე, რომლებსაც აქ მიუთითებთ.
                    თუ არცერთი IP მისამართი გაქვთ მითითებული, საიტი გაიხსნება ყველგან, 
                    შეუზღუდავად.
                </p>
                <p>
                    P.S ფუნქციონალი ამჟამად გამორთულია !
                </p>
            </div>
        </div>
    </div>
</div>
<!-- /ინფორმაცია IP მისამართების შესახებ -->
<!-- IP მისამართის დამატების ფორმა -->
<div id="ip-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">
                    @lang('admin.ip')
                </h4>
            </div>
            <div class="modal-body">
                <form class="form-inline">
                    <div class="form-group mx-sm-3 mb-2">
                        <input type="password" class="form-control" placeholder="@lang('admin.ip')">
                    </div>
                    <button type="submit" class="btn btn-success mb-2" style="margin-top: 6px;">
                        @lang('admin.save')
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /IP მისამართის დამატების ფორმა -->
@endsection
@push('js')
<script src="/Administrator/vendors/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
<script>
    
    $('.change-caches').on('change',function(){
        
        let inp = $(this);
        
        if(inp.hasClass('active') && inp.attr('checked','checked'))
        {
            bootbox.confirm({
                message: areYouSure, // ცვლადი აღწერილია layouts/admin.blade.php-ში
                buttons: {
                    confirm: {
                        label: yes, // ცვლადი აღწერილია layouts/admin.blade.php-ში
                        className: 'btn-success' // ცვლადი აღწერილია layouts/admin.blade.php-ში
                    },
                    cancel: {
                        label: no,
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if(result) 
                    {
                        window.location.href = '/admin/configuration/clear_cache';
                    }
                    else
                    {
                        location.reload();
                    }
                }
            });
        }
        else
        {
            $('#cache-div').slideToggle('slow'); 
        }
         
    });
    
    $('.cache-module-checkbox').on('change',function(){
        $(this).parent().next().next().find('input').prop('required', $(this).is(':checked')); 
        $(this).parent().next().next().find('input').prop('readonly', !$(this).is(':checked')) ;
        
        if(!$(this).is(':checked'))
        {
            $(this).parent().next().next().find('input').val('');
        }
        else
        {
            $(this).parent().next().next().find('input').val('60');
        }
    });
    
    $('.remove-ip').on('click',function(){
        
        let badge = $(this).parent();
        
        bootbox.confirm({
            message: areYouSure, // ცვლადი აღწერილია layouts/admin.blade.php-ში
            buttons: {
                confirm: {
                    label: yes, // ცვლადი აღწერილია layouts/admin.blade.php-ში
                    className: 'btn-success' // ცვლადი აღწერილია layouts/admin.blade.php-ში
                },
                cancel: {
                    label: no,
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                if(result) 
                {
                    badge.fadeOut();
                }
            }
        });
            
    });    
    
    $('.forget-cache-btn').on('click',function(){
        
        let cacheKey = $(this).data('cache-key');
        
        bootbox.confirm({
            message: areYouSure, // ცვლადი აღწერილია layouts/admin.blade.php-ში
            buttons: {
                confirm: {
                    label: yes, // ცვლადი აღწერილია layouts/admin.blade.php-ში
                    className: 'btn-success' // ცვლადი აღწერილია layouts/admin.blade.php-ში
                },
                cancel: {
                    label: no,
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                if(result) 
                {
                    window.location.href = 'configuration/remove_cache_key/' + cacheKey;
                }
            }
        });
            
    });    
    
    // ფერის ველის ინიციალიზაცია
    const colorPicker = $('.colors').colorpicker({});  
    
    $(document).ready(function() {
        $('#menu_toggle').trigger('click');
    });
   
</script>
@endpush
