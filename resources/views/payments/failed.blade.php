@extends('layouts.client')

@section('content')
    <main class="main-content">
        <div class="breadcrumb-area breadcrumb-height" data-bg-image="{{ $info->top_banner }}">
            <div class="container h-100">
                <div class="row h-100">
                    <div class="col-lg-12">
                        <div class="breadcrumb-item">
                            <h2 class="breadcrumb-heading">Payment Failed</h2>
                            <ul>
                                <li><a href="{{ route('clientHome') }}">Home</a></li>
                                <li>Payment Failed</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-space-y-axis-100">
            <div class="container">
                <div class="alert alert-danger">
                    <h4 class="mb-2">Payment was not completed.</h4>
                    <p class="mb-1">{{ $message ?? 'Please try again or contact support if your card was charged.' }}</p>
                    @isset($payment)
                        <p class="mb-0">Order code: <strong>{{ $payment->order->code }}</strong></p>
                    @endisset
                </div>

                <a href="{{ route('checkout') }}" class="btn btn-custom-size lg-size btn-pronia-primary">
                    Return to Checkout
                </a>
            </div>
        </div>
    </main>
@endsection
