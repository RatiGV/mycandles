@extends('layouts.client')
@section('content')
    <main class="main-content">
        <div class="breadcrumb-area breadcrumb-height" data-bg-image="{{ $info->top_banner }}">
            <div class="container h-100">
                <div class="row h-100">
                    <div class="col-lg-12">
                        <div class="breadcrumb-item">
                            <h2 class="breadcrumb-heading">{{ trans('Sign up') }}</h2>
                            <ul>
                                <li>
                                    <a href="{{ route('clientHome') }}">{{ trans('Home') }}</a>
                                </li>
                                <li>{{ trans('Sign up') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="login-register-area section-space-y-axis-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 pt-5 pt-lg-0">
                        <form action="{{ route('register') }}" method="POST">
                            @csrf
                            <div class="login-form">
                                <h4 class="login-title">{{ trans('Sign up') }}</h4>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <label>{{ trans('First name') }}</label>
                                        <input type="text" name="name" placeholder="{{ trans('First name') }}">
                                        @error('name')
                                            <span>{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <label>{{ trans('Last name') }}</label>
                                        <input type="text" name="surname" placeholder="{{ trans('Last name') }}">
                                        @error('surname')
                                            <span>{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <label>{{ trans('Email Address') }}*</label>
                                        <input type="email" name="email" placeholder="{{ trans('Email Address') }}">
                                        @error('email')
                                            <span>{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <label>{{ trans('Phone') }}*</label>
                                        <input type="tel" name="phone" placeholder="{{ trans('Phone') }}">
                                        @error('phone')
                                            <span>{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label>{{ trans('Password') }}</label>
                                        <input type="password" name="password" placeholder="{{ trans('Password') }}">
                                        @error('password')
                                            <span>{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label>{{ trans('Confirm Password') }}</label>
                                        <input type="password" name="password_confirmation"
                                            placeholder="{{ trans('Confirm Password') }}">
                                    </div>
                                    <div class="col-12">
                                        <button type="submit"
                                            class="btn btn-custom-size lg-size btn-pronia-primary">{{ trans('Sign up') }}</button>
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
