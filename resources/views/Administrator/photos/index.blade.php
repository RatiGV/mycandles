@php
    $check_childs_here = json_encode([]);
@endphp
@extends('layouts.admin')
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <form method="post" action="{{ route('Multi') }}" id="multi-form">
            @csrf
            <input type="hidden" name="table" value="{{ $main_table }}">
            <input type="hidden" name="check_childs_here" value="{{ $check_childs_here }}">
            <div class="x_title">
                <h2>@lang('admin.routes.' . $routes_suffix)</h2>
                <a class="btn btn-success btn-sm pull-right" href="{{ route('Add'.$routes_suffix) }}"> 
                    <i class="fa fa-plus"></i> @lang('admin.add')
                </a>
                @if($items->count())
                    <a href="#!" class="btn btn-danger btn-sm pull-right multi-action-btn" data-action="1"> 
                        <i class="fa fa-trash"></i> @lang('admin.remove_checkeds')
                    </a>
                    <a href="#!" class="btn btn-primary btn-sm pull-right multi-action-btn" data-action="2"> 
                        <i class="fa fa-check"></i> @lang('admin.status_checkeds')
                    </a>
                    <input type="hidden" name="action" id="action">
                @endif
                <div class="clearfix"></div>
            </div>
            <div class="x_content admin_container" data-instance="{{ $main_table }}">
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
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                    </button>
                    <strong>@lang('admin.error')</strong>
                </div>
                @endif
                @if(Session::has('no_permission'))
                <div class="alert alert-danger alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                    </button>
                    <strong>@lang('admin.no_permission')</strong>
                </div>
                @endif
                @if(Session::has('cant_remove'))
                <div class="alert alert-danger alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <ul>
                        @foreach(Session::get('cant_remove') as $cant_remove_cat_id)
                            @php 
                                $title = DB::table($translates_table)->select('title')
                                        ->where('parent_id',$cant_remove_cat_id)
                                        ->where('lang',$configuration->admin_lang)->first()->title;
                            @endphp
                            <li>
                                <strong>{{ $title }}</strong> - @lang('admin.childs_error')
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <table class="table">
                    <thead>
                    <tr>
                        @forelse($listing_columns as $listing_column)
                            <th>@lang('admin.' . $listing_column)</th>
                        @empty
                        @endforelse
                        <th class="text-center">
                            <input type="checkbox" class="form-check-input multi-check" id="exampleCheck1">
                            <label class="form-check-label" for="exampleCheck1">@lang('admin.check_all')</label>
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $key => $item)
                            <tr style="cursor:move">
                                @forelse($listing_columns as $listing_column)
                                    @if($listing_column === 'sort')
                                        <td class="sort" id="sort{{ $key }}" data-id="{{ $item->id }}" data-ordering ="{{ $item->sort }}">
                                            {{ ++$key }}
                                        </td>
                                    @elseif($listing_column === 'status')
                                        <td>
                                            <div class="iradio">
                                                <input type="checkbox" 
                                                       data-id="{{ $item->id }}" 
                                                       data-table="{{ $main_table }}"
                                                       data-column="status"
                                                       {{ $item->status ? 'checked' : '' }} 
                                                       class="js-switch change" 
                                                />
                                            </div>
                                        </td>
                                    @elseif($listing_column === 'image')
                                        <td>
                                            <div style="width: 50px;height: 50px; overflow: hidden;">
                                                <img src="{{ $item->image ? $item->image : '/uploads/no_photo.svg' }}" 
                                                     style="height: 100%;width: 100%;object-fit: cover;"
                                                >
                                            </div>
                                        </td>
                                    @else
                                        <td>{{ $item->$listing_column }}</td>
                                    @endif
                                @empty
                                @endforelse
                                <td class="text-center">
                                    <input type="checkbox" name="id[]" value="{{ $item->id }}" class="form-check-input chk multi-check">
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm pull-right delete" 
                                            data-id="{{ $item->id }}"
                                            data-table="{{ $main_table }}"
                                            data-check-childs-here="{{ $check_childs_here }}"
                                    >
                                        <i class="fa fa-trash"></i> 
                                    </button>
                                    <a class="btn btn-primary btn-sm pull-right" href="{{ route('Edit'.$routes_suffix, $item->id) }}"> 
                                        <i class="fa fa-edit"></i> 
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($listing_columns) + 3 }}">
                                    <div class="alert alert-warning text-center">
                                        @lang('admin.product_not_found')
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
@endsection
