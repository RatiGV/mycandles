@extends('layouts.client')

@section('content')
    <main class="main-content">
        <div class="breadcrumb-area breadcrumb-height" data-bg-image="{{ $info->top_banner }}">
            <div class="container h-100">
                <div class="row h-100">
                    <div class="col-lg-12">
                        <div class="breadcrumb-item">
                            <h2 class="breadcrumb-heading">Payment Success</h2>
                            <ul>
                                <li><a href="{{ route('clientHome') }}">Home</a></li>
                                <li>Payment Success</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-space-y-axis-100">
            <div class="container">
                <div class="alert alert-success">
                    <h4 class="mb-2">Thank you. Your payment was successful.</h4>
                    <p class="mb-1">Order code: <strong>{{ $payment->order->code }}</strong></p>
                    <p class="mb-0">Payment reference: <strong>{{ $payment->gateway_payment_id ?: 'N/A' }}</strong></p>
                </div>

                <a href="{{ route('myAccount') }}" class="btn btn-custom-size lg-size btn-pronia-primary">
                    Go to My Account
                </a>
            </div>
        </div>
    </main>
@endsection
