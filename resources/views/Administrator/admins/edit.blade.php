@extends('layouts.admin')
@section('content')
{{ App::setLocale($configuration->admin_lang) }}
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2>@lang('admin.routes.Admins'), @lang('admin.edit')</h2>
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
                <form id="news-form"  
                      class="form-horizontal form-label-left" 
                      method="post" 
                      action="{{ route('UpdateAdmins', $admin->id) }}"
                >
                    @csrf
                    <div id="myTabContent" class="tab-content">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                @lang('admin.first_name') <span class="required">*</span>
                            </label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <input type="text" 
                                       name="name" 
                                       value="{{ $admin->name }}"
                                       class="form-control col-md-7 col-xs-12" 
                                       required
                                >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                @lang('admin.last_name') <span class="required">*</span>
                            </label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <input type="text" 
                                       name="surname" 
                                       value="{{ $admin->surname }}"
                                       class="form-control col-md-7 col-xs-12" 
                                       required
                                >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">
                               @lang('admin.email') <span class="required">*</span>
                            </label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <input type="email" 
                                       name="email" 
                                       value="{{ $admin->email }}"
                                       class="form-control col-md-7 col-xs-12" 
                                       required
                                >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                @lang('admin.password') 
                            </label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <input type="password" 
                                       name="password" 
                                       class="form-control col-md-7 col-xs-12" 
                                       required
                                >
                            </div>
                        </div>
                        @if($admin->role !== 1)
                            <div class="form-group not-need {{ $errors->has('role') ? 'bad' : '' }}">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                    @lang('admin.role') <span class="required">*</span>
                                </label>
                                <div class="col-md-7 col-sm-7 col-xs-12">
                                    <select name="role" class="form-control col-md-7 col-xs-12" required>
                                        <option value="">@lang('admin.role')</option>
                                        <option value="2" {{ $admin->role == 2 ? 'selected' : '' }}>
                                            @lang('admin.standard')
                                        </option>
                                        <option value="3" {{ $admin->role == 3 ? 'selected' : '' }}>
                                            @lang('admin.moderator')
                                        </option>
                                    </select>
                                </div>
                            </div>
                        @else
                            <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                @lang('admin.role') 
                            </label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <input type="text" 
                                       value="@lang('admin.super')" 
                                       class="form-control col-md-7 col-xs-12" 
                                       disabled
                                >
                            </div>
                        </div>
                        @endif
                        <input type="hidden" name="stay" id="stay-input">
                        <div class="form-group">
                            <div class="col-md-7 col-sm-7 col-xs-12 col-md-offset-3">
                                <button type="button" class="btn btn-success save-btn"  data-stay="1">
                                    @lang('admin.save')
                                </button>
                                <button type="button" class="btn btn-success save-btn"  data-stay="0">
                                    @lang('admin.save_and_close')
                                </button>
                                <a href="{{ route('Admins') }}" class="btn btn-warning save-btn">
                                    @lang('admin.cancel')
                                </a>
                            </div>
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
    
    $('.save-btn').on('click', function(){
        $('#stay-input').val($(this).data('stay'));
        $('#news-form').submit();
    });
        
</script>
@endpush

                    
               