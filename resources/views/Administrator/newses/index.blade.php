@extends('layouts.admin')
@section('content')
    <div class="col-md-3">
        <a href=" {{ route('News') }}">
            <div class="card-counter success">
                <i class="fa fa-newspaper"></i>
                <span class="count-numbers">{{ DB::table('news')->count() }}</span>
                <span class="count-name">@lang('admin.routes.News')</span>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href=" {{ route('NewsCategories') }}">
            <div class="card-counter warning">
                <i class="fa fa-list"></i>
                <span class="count-numbers">{{ DB::table('news_categories')->count() }}</span>
                <span class="count-name">@lang('admin.categories')</span>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href=" {{ route('Tags') }}">
            <div class="card-counter primary">
                <i class="fas fa-tags"></i>
                <span class="count-numbers">{{ DB::table('tags')->count() }}</span>
                <span class="count-name">@lang('admin.routes.Tags')</span>
            </div>
        </a>
    </div>
@endsection
