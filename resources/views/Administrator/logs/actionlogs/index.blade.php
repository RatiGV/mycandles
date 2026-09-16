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
                <h2>@lang('admin.routes.Changelogs')</h2>
                @if($items->count())
                    <a href="#!" class="btn btn-danger btn-sm pull-right multi-action-btn" data-action="1"> 
                        <i class="fa fa-trash"></i> @lang('admin.remove_checkeds')
                    </a>
                    <input type="hidden" name="action" id="action">
                @endif
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
                            <th>@lang('admin.user')</th>
                            <th>@lang('admin.ip')</th>
                            <th>@lang('admin.model')</th>
                            <th>@lang('admin.action')</th>
                            <th>@lang('admin.check')</th>
                            <th>@lang('admin.time')</th>
                            <th class="text-center">
                            <input type="checkbox" class="form-check-input multi-check" id="exampleCheck1">
                                <label class="form-check-label" for="exampleCheck1">@lang('admin.check_all')</label>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $key => $item)
                        <?php 
                            $model_name = $item->model_name . 's';

                            if(substr($item->model_name, -1) == 'y'){
                                $model_name = substr_replace($item->model_name,"",-1) . 'ies';
                            }elseif(substr($item->model_name, -1) == 's'){
                                $model_name = $item->model_name;
                            }
                        ?>
                            <tr>
                                <th scope="row">{{ $key+=1 }}</th>
                                <td>{{ $item->admin->name . ' ' . $item->admin->surname }}</td>
                                <td><code>{{ $item->ip_address }}</code></td>
                                <td>{{ trans('admin.routes.'.str_replace(' ', '',$model_name)) }}</td>
                                <td>{{ $item->action }}</td>
                                <td>
                                    <a href="{{ route($item->getRelateModelUrl($item->id), $item->model_id) }}">
                                        @lang('admin.check_model')
                                    </a>
                                </td>
                                <td>{{ $item->created_at }}</td>
                                <td class="text-center">
                                    <input type="checkbox" name="id[]" value="{{ $item->id }}" class="form-check-input chk multi-check">
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm pull-right delete" 
                                            data-id="{{ $item->id }}"
                                            data-table="changelogs"
                                            data-check-childs-here="{{ $check_childs_here }}"
                                    > 
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
        @if($items->count())
            <div>
                {!! $items->links() !!}       
            </div>
        @endif
    </div>
</div>
@endsection
@push('js')
<script>

    
       
</script>
@endpush
