@extends('layouts.admin')
@section('content')
{{ App::setLocale($configuration->admin_lang) }}
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2>@lang('admin.routes.Admins'), @lang('admin.add')</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="" role="tabpanel" data-example-id="togglable-tabs">
                <form class="form-horizontal form-label-left" 
                      method="post" 
                      action="{{ route('StoreAdmins') }}"
                >
                    @csrf
                    <div id="myTabContent" class="tab-content">
                        <div role="tabpanel">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                    @lang('admin.first_name') <span class="required">*</span>
                                </label>
                                <div class="col-md-7 col-sm-7 col-xs-12">
                                    <input type="text" 
                                           name="name" 
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
                                           class="form-control col-md-7 col-xs-12" 
                                           required
                                    >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                    @lang('admin.password') <span class="required">*</span>
                                </label>
                                <div class="col-md-7 col-sm-7 col-xs-12">
                                    <input type="password" 
                                           name="password" 
                                           class="form-control col-md-7 col-xs-12" 
                                           required
                                    >
                                </div>
                            </div>
                            <div class="form-group not-need {{ $errors->has('role') ? 'bad' : '' }}">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                    @lang('admin.role') <span class="required">*</span>
                                </label>
                                <div class="col-md-7 col-sm-7 col-xs-12">
                                    <select name="role" class="form-control col-md-7 col-xs-12" required>
                                        <option value="">@lang('admin.role')</option>
                                        <option value="2" >@lang('admin.standard')</option>
                                        <option value="3" >@lang('admin.moderator')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="ln_solid"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 col-sm-7 col-xs-12 col-md-offset-3">
                            <button type="submit" class="btn btn-success">{{ trans('admin.add') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
