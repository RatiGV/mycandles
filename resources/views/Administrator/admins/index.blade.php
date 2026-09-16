@php
    $check_childs_here = json_encode([]);
@endphp
@extends('layouts.admin')
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2>@lang('admin.routes.Admins')</h2>
            <a class="btn btn-primary btn-sm pull-right" href="{{ route('AddAdmins') }}"> 
                <i class="fa fa-plus"></i> {{ trans('admin.add') }}
            </a>
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
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('admin.first_name') @lang('admin.last_name')</th>
                        <th>@lang('admin.email')</th>
                        <th>@lang('admin.role')</th>
                        <th>@lang('admin.last_login')</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $key => $admin)
                        <tr>
                            <th scope="row">{{ $key+=1 }}</th>
                            <td>{{ $admin->name }} {{ $admin->surname }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->role == 1 ? trans('admin.super') : ($admin->role == 2 ? trans('admin.standard') : trans('admin.moderator')) }}</td>
                            <td>{{ $admin->last_login }}</td>
                            <td>
                                <a class="btn btn-primary btn-sm pull-right" href="{{ route('EditAdmins', $admin->id) }}">
                                    <i class="fa fa-edit"></i> 
                                </a>
                                @if($admin->role > 1)
                                <button class="btn btn-danger btn-sm pull-right delete" 
                                        data-id="{{ $admin->id }}"
                                        data-table="admins"
                                        data-check-childs-here="{{ $check_childs_here }}"
                                > 
                                    <i class="fa fa-trash"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
