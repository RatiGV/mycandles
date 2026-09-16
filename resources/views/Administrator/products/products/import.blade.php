@extends('layouts.admin')
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2>@lang('admin.routes.' . $routes_suffix), @lang('admin.import')</h2>
            <a href="/Administrator/excel/products.xlsx" download="პროდუქტი" class="btn btn-primary btn-sm pull-right"> 
                <i class="fas fa-download"></i> @lang('admin.download_excel_file_example')
            </a>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="" role="tabpanel" data-example-id="togglable-tabs">
                <form id="news-form"  
                      class="form-horizontal form-label-left" 
                      method="post" enctype="multipart/form-data" 
                      action="{{ route('Upload'.$routes_suffix) }}"
                >
                    @csrf
                    <div id="myTabContent" class="tab-content">
                        <div class="form-group {{ $errors->has('file') ? 'bad' : '' }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                @lang('admin.select_excel_file') 
                                <i class="fas fa-question-circle"
                                   data-toggle="modal" 
                                   data-target="#excel-modal"
                                   style="cursor: pointer"
                                ></i>
                                <span class="required">*</span>
                            </label>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <input type="file" name="file" class="form-control col-md-7 col-xs-12" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 col-sm-7 col-xs-12 col-md-offset-3">
                            <button type="submit" class="btn btn-success">@lang('admin.upload')</button>
                            <a href="{{ route($routes_suffix) }}" class="btn btn-success">
                                @lang('admin.cancel')
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- ფაილის ფანჯარა -->
<div id="excel-modal" class="modal fade" role="dialog">
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
                    <img src="/Administrator/images/product_excel.jpg" style="width: 100%; height: 100%;">
                </p>
            </div>
        </div>
    </div>
</div>
<!-- /ფაილის ფანჯარა -->
@endsection
