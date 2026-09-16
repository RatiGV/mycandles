@php
    $check_childs_here = json_encode([]);
@endphp
@extends('layouts.admin')
@section('content')
    <div class="col-md-12 col-sm-12 col-xs-12">
        <!-- ფილტრი -->
        <div class="x_panel" style="padding: 20px;">
            @include('Administrator.products.products.filter')
        </div>
        <!-- /ფილტრი -->
        <div class="x_panel">
            <form method="post" action="{{ route('Multi') }}" id="multi-form">
                @csrf
                <input type="hidden" name="table" value="{{ $main_table }}">
                <input type="hidden" name="check_childs_here" value="{{ $check_childs_here }}">
                <div class="x_title">
                    <h2>@lang('admin.routes.' . $routes_suffix)</h2>
                    @if (Request::getQueryString())
                        <a href="{{ route($routes_suffix) }}" class="btn btn-danger btn-sm pull-right">
                            <i class="fas fa-undo"></i> @lang('admin.cancel')
                        </a>
                    @endif
                    <a class="btn btn-success btn-sm pull-right" href="{{ route('Add' . $routes_suffix) }}">
                        <i class="fa fa-plus"></i> @lang('admin.add')
                    </a>
                    <a class="btn btn-success btn-sm pull-right" href="{{ route('Import' . $routes_suffix) }}">
                        <i class="fa fa-file-excel"></i> @lang('admin.import')
                    </a>
                    @if ($items->count())
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
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <strong>@lang('admin.error')</strong>
                        </div>
                    @endif
                    @if (Session::has('no_permission'))
                        <div class="alert alert-danger alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <strong>@lang('admin.no_permission')</strong>
                        </div>
                    @endif
                    @if (Session::has('cant_remove'))
                        <div class="alert alert-danger alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <ul>
                                @foreach (Session::get('cant_remove') as $cant_remove_cat_id)
                                    @php
                                        $title = DB::table($translates_table)
                                            ->select('title')
                                            ->where('parent_id', $cant_remove_cat_id)
                                            ->where('lang', $configuration->admin_lang)
                                            ->first()->title;
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
                                <th>@lang('admin.parent_category')</th>
                                <th class="text-center">
                                    <input type="checkbox" class="form-check-input multi-check" id="exampleCheck1">
                                    <label class="form-check-label" for="exampleCheck1">@lang('admin.check_all')</label>
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $key => $item)
                                <tr>
                                    @forelse($listing_columns as $listing_column)
                                        @if ($listing_column === 'sort')
                                            <td class="sort" id="sort{{ $key }}" data-id="{{ $item->id }}"
                                                data-ordering ="{{ $item->sort }}">
                                                {{ ++$key }}
                                            </td>
                                        @elseif($listing_column === 'status')
                                            <td>
                                                <div class="iradio">
                                                    <input type="checkbox" data-id="{{ $item->id }}"
                                                        data-table="{{ $main_table }}" data-column="status"
                                                        {{ $item->status ? 'checked' : '' }} class="js-switch change" />
                                                </div>
                                            </td>
                                        @elseif($listing_column === 'available')
                                            <td>
                                                <div class="iradio">
                                                    <input type="checkbox" data-id="{{ $item->id }}"
                                                        data-table="{{ $main_table }}" data-column="available"
                                                        {{ $item->available ? 'checked' : '' }} class="js-switch change" />
                                                </div>
                                            </td>
                                        @elseif($listing_column === 'new_product')
                                            <td>
                                                <div class="iradio">
                                                    <input type="checkbox" data-id="{{ $item->id }}"
                                                        data-table="{{ $main_table }}" data-column="new_product"
                                                        {{ $item->new_product ? 'checked' : '' }} class="js-switch change" />
                                                </div>
                                            </td>
                                        @elseif($listing_column === 'image')
                                            <td>
                                                <div style="width: 50px;height: 50px; overflow: hidden;">
                                                    <img src="{{ $item->image ? $item->image : '/uploads/no_photo.svg' }}"
                                                        style="height: 100%;width: 100%;object-fit: cover;">
                                                </div>
                                            </td>
                                        @elseif($listing_column === 'price')
                                            <td>{{ $item->$listing_column }} ₾</td>
                                        @else
                                            <td>{{ $item->$listing_column }}</td>
                                        @endif
                                    @empty
                                    @endforelse
                                    <td class="">
                                        @if ($item->category_id)
                                            <a href="{{ route('EditProductCategories', $item->category_id) }}"
                                                target="_blank" style="color: orange;">
                                                {{ $item->cat_title }}
                                            </a>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" name="id[]" value="{{ $item->id }}"
                                            class="form-check-input chk multi-check">
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm pull-right delete"
                                            data-id="{{ $item->id }}" data-table="{{ $main_table }}"
                                            data-check-childs-here="{{ $check_childs_here }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <a class="btn btn-primary btn-sm pull-right"
                                            href="{{ route('Edit' . $routes_suffix, [
                                                'id' => $item->id,
                                                'page' => Request::query('page') ? Request::query('page') : 1,
                                            ]) }}">
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
            @if ($items->count())
                <div>
                    {!! $items->appends(request()->query())->links() !!}
                </div>
            @endif
        </div>
    </div>
@endsection
@push('js')
    <script>
        $('#category-select').attr('name', 'category_id');
        $('#category-select').prepend("<option value=''>@lang('admin.category')</option>");

        @if (Request::get('category_id'))
            $("#category-select").val({{ Request::get('category_id') }});
        @else
            $("#category-select option:eq(0)").attr('selected', true);
        @endif

        $(document).ready(function() {
            $('#menu_toggle').trigger('click');
        });
    </script>
@endpush
