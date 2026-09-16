<?php
    $check_childs_here = json_encode([]);

    $export_parameters = [
        'name' => Request::get('name'),
        'surname' => Request::get('surname'),
        'email' => Request::get('email'),
        'from' => Request::get('from'),
        'to' => Request::get('to')
    ];
?>
@extends('layouts.admin')
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
    <!-- ფილტრი -->
    @include('Administrator.users.filter')
    <!-- /ფილტრი -->
    <div class="x_panel">
        <div class="x_title">
            <h2>@lang('admin.routes.Users') ({{ $users->total() }})</h2>
            <a href="{{ route('ExportUsers',$export_parameters) }}"  class="btn btn-success btn-md pull-right">
                <i class="far fa-file-excel"></i> @lang('admin.export')
            </a>
            @if(Request::getQueryString())
                <a href="{{ route('Users') }}"  class="btn btn-danger btn-md pull-right">
                    <i class="fas fa-undo"></i> @lang('admin.cancel')
                </a>
            @endif
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <table id="example" class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('admin.first_name')</th>
                        <th>@lang('admin.last_name')</th>
                        <th>@lang('admin.phone')</th>
                        <th>@lang('admin.email')</th>
                        <th>@lang('admin.register_date')</th>
                        <th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $key => $user)
                        <tr>
                            <td scope="row">{{ ++$key }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->surname }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ strftime( '%d %b, %Y' , strtotime($user->created_at)) }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm pull-right delete"
                                        data-id="{{ $user->id }}"
                                        data-table="users"
                                        data-check-childs-here="{{ $check_childs_here }}"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="alert alert-warning text-center">
                                    @lang('admin.product_not_found')
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->count())
            <div>
                {!! $users->appends(request()->query())->links() !!}
            </div>
        @endif
    </div>
</div>
@endsection
