@extends('layouts.client')
@section('content')
    <main class="main-content">
        <div class="breadcrumb-area breadcrumb-height" data-bg-image="{{ $info->top_banner }}">
            <div class="container h-100">
                <div class="row h-100">
                    <div class="col-lg-12">
                        <div class="breadcrumb-item">
                            <h2 class="breadcrumb-heading">{{ trans('My Account') }}</h2>
                            <ul>
                                <li>
                                    <a href="{{ route('clientHome') }}">{{ trans('Home') }}</a>
                                </li>
                                <li>{{ trans('My Account') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="account-page-area section-space-y-axis-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3">
                        <ul class="nav myaccount-tab-trigger" id="account-page-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="account-dashboard-tab" data-bs-toggle="tab"
                                    href="#account-dashboard" role="tab" aria-controls="account-dashboard"
                                    aria-selected="true">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="account-orders-tab" data-bs-toggle="tab" href="#account-orders"
                                    role="tab" aria-controls="account-orders" aria-selected="false">{{ trans('Orders') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="account-details-tab" data-bs-toggle="tab" href="#account-details"
                                    role="tab" aria-controls="account-details" aria-selected="false">{{ trans('Account Details') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="account-logout-tab"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    role="tab" aria-selected="false">{{ trans('Logout') }}</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-9">
                        <div class="tab-content myaccount-tab-content" id="account-page-tab-content">
                            <div class="tab-pane fade show active" id="account-dashboard" role="tabpanel"
                                aria-labelledby="account-dashboard-tab">
                                <div class="myaccount-dashboard">
                                    <p>Hello <b>{{ auth()->user()->name }}</b></p>
                                    <p>From your account dashboard you can view your recent orders, manage your shipping and
                                        billing addresses and <a href="#">edit your password and account details</a>.
                                    </p>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="account-orders" role="tabpanel"
                                aria-labelledby="account-orders-tab">
                                <div class="myaccount-orders">
                                    <h4 class="small-title">{{ trans('My Orders') }}</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <tbody>
                                                <tr>
                                                    <th>{{ trans('Order number') }}</th>
                                                    <th>{{ trans('Order date') }}</th>
                                                    <th>{{ trans('Order status') }}</th>
                                                    <th>{{ trans('Total') }}</th>
                                                    <th></th>
                                                </tr>
                                                @forelse ($orders as $order)
                                                    <tr>
                                                        <td><a class="account-order-id" href="#">#{{ $order->id }}</a></td>
                                                        <td>{{ \Carbon\Carbon::parse($order->created_at)->locale(locale())->translatedFormat('d F, Y') }}</td>
                                                        <td>On Hold</td>
                                                        <td>GEL {{ $order->total }}</td>
                                                        <td><a href="#" class="btn btn-dark"><span>View</span></a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="account-details" role="tabpanel"
                                aria-labelledby="account-details-tab">
                                <div class="myaccount-details">
                                    <form action="{{ route('updateSettings') }}" method="POST" class="myaccount-form">
                                        @csrf
                                        <div class="myaccount-form-inner">
                                            <div class="single-input single-input-half">
                                                <label>{{ trans('First name') }}*</label>
                                                <input type="text" name="name" value="{{ $user->name }}">
                                            </div>
                                            <div class="single-input single-input-half">
                                                <label>{{ trans('Last name') }}*</label>
                                                <input type="text" name="surname" value="{{ $user->surname }}">
                                            </div>
                                            <div class="single-input single-input-half">
                                                <label>{{ trans('Email') }}*</label>
                                                <input type="email" name="email" value="{{ $user->email }}">
                                            </div>
                                            <div class="single-input single-input-half">
                                                <label>{{ trans('Phone') }}*</label>
                                                <input type="text" name="phone" value="{{ $user->phone }}">
                                            </div>
                                            <div class="single-input">
                                                <label>{{ trans('Current Password (leave blank to leave unchanged)') }}</label>
                                                <input type="password" name="oldPassword">
                                            </div>
                                            <div class="single-input">
                                                <label>{{ trans('New Password') }}</label>
                                                <input type="password" name="password">
                                            </div>
                                            <div class="single-input">
                                                <label>{{ trans('Confirm New Password') }}</label>
                                                <input type="password" name="password_confirmation">
                                            </div>
                                            <div class="single-input">
                                                <button class="btn btn-custom-size lg-size btn-pronia-primary"
                                                    type="submit">
                                                    <span>{{ trans('Save') }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
