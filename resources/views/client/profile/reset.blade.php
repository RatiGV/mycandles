@extends('layouts.client')
@section('content')
    <main class="main-content">
        <div class="breadcrumb-area breadcrumb-height" data-bg-image="{{ $info->top_banner }}"></div>
        <div class="login-register-area section-space-y-axis-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        @if ($errors->has('email'))
                            <p class="alert alert-danger">{{ $errors->first('email') }}</p>
                        @endif
                        <form action="{{ route('password.email') }}" method="POST">
                            @csrf
                            <div class="login-form">
                                <h4 class="login-title">{{ trans('Reset password') }}</h4>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>{{ trans('Email Address') }}*</label>
                                        <input type="email" name="email" placeholder="{{ trans('Email Address') }}">
                                    </div>
                                    <div class="col-lg-12 pt-5">
                                        <button class="btn btn-custom-size lg-size btn-pronia-primary">{{ trans('Submit') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
