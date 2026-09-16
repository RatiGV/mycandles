@extends('layouts.admin')
@section('content')
<div class="col-md-3">
    <a href=" {{ route('Changelogs') }}"> 
        <div class="card-counter success">
            <i class="fas fa-user-clock"></i>
            <span class="count-numbers"></span>
            <span class="count-name">@lang('admin.routes.Changelogs')</span>
        </div>
    </a>
</div>
<div class="col-md-3">
    <a href=" {{ route('Operationlogs') }}">
        <div class="card-counter warning">
            <i class="fas fa-user-check"></i> 
            <span class="count-numbers"></span>
            <span class="count-name">@lang('admin.routes.Operationlogs')</span>
        </div>
    </a>
</div>
@endsection