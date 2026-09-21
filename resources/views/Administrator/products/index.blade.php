@extends('layouts.admin')
@section('content')
<div class="col-md-3">
    <a href="{{ route('Products') }}">
        <div class="card-counter success">
            <i class="fab fa-product-hunt"></i>
            <span class="count-numbers">{{ DB::table('products')->count() }}</span>
            <span class="count-name">@lang('admin.routes.Products')</span>
        </div>
    </a>
</div>
<div class="col-md-3">
    <a href="{{ route('ProductCategories') }}">
        <div class="card-counter warning">
            <i class="fa fa-list"></i>
            <span class="count-numbers">{{ DB::table('product_categories')->count() }}</span>
            <span class="count-name">@lang('admin.categories')</span>
        </div>
    </a>
</div>
@endsection
