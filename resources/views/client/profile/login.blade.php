@extends('layouts.client')
@section('content')
    <main class="main-content">
        <div class="breadcrumb-area breadcrumb-height"
            data-bg-image="{{ $info->top_banner }}">
            <div class="container h-100">
                <div class="row h-100">
                    <div class="col-lg-12">
                        <div class="breadcrumb-item">
                            <h2 class="breadcrumb-heading">{{ trans('Sign in') }}</h2>
                            <ul>
                                <li>
                                    <a href="{{ route('clientHome') }}">{{ trans('Home') }}</a>
                                </li>
                                <li>{{ trans('Sign in') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="login-register-area section-space-y-axis-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        @if ($errors->has('email'))
                            <p class="alert alert-danger">{{ $errors->first('email') }}</p>
                        @endif
                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="login-form">
                                <h4 class="login-title">{{ trans('Sign in') }}</h4>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>{{ trans('Email Address') }}*</label>
                                        <input type="email" name="email" placeholder="{{ trans('Email Address') }}">
                                    </div>
                                    <div class="col-lg-12">
                                        <label>{{ trans('Password') }}</label>
                                        <input type="password" name="password" placeholder="{{ trans('Password') }}">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="forgotton-password_info">
                                            <a href="{{ route('clientReset') }}"> {{ trans('Forgot password?') }}</a>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 pt-5">
                                        <button class="btn btn-custom-size lg-size btn-pronia-primary">{{ trans('Sign in') }}</button>
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
