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
                @if($items->count())
                    <a href="#!" class="btn btn-danger btn-sm pull-right multi-action-btn" data-action="1"> 
                        <i class="fa fa-trash"></i> @lang('admin.remove_checkeds')
                    </a>
                    <input type="hidden" name="action" id="action">
                @endif
                <div class="clearfix"></div>
            </div>
            <div class="x_content admin_container" data-instance="{{ $main_table }}">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
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
                                <td>{{ ++$key  }}</td>
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
                                        <td>
                                            {{ $item->$listing_column }}
                                            @if($listing_column == 'search_count')
                                                @if($item->$listing_column == 1)
                                                    @lang('admin.times_for_one')
                                                @else
                                                    @lang('admin.times')
                                                @endif
                                            @endif
                                        </td>
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
