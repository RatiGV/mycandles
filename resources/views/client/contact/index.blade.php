@extends('layouts.client')
@section('content')
    <main class="main-content">
        <div class="breadcrumb-area breadcrumb-height" data-bg-image="{{ $info->top_banner }}"></div>
        <div class="contact-form-area section-space-y-axis-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="contact-wrap">
                            <div class="contact-info text-white"
                                data-bg-image="{{ asset('assets/images/contact/1-1-370x500.jpg') }}">
                                <h2 class="contact-title">{{ trans('Contact') }}</h2>
                                <p class="contact-desc">
                                </p>
                                <ul class="contact-list">
                                    @if ($info->phone)
                                        <li>
                                            <i class="pe-7s-call"></i>
                                            <a href="tel:{{ $info->phone }}">{{ $info->phone }}</a>
                                        </li>
                                    @endif
                                    @if ($info->email)
                                        <li>
                                            <i class="pe-7s-mail"></i>
                                            <a href="mailto:{{ $info->email }}">{{ $info->email }}</a>
                                        </li>
                                    @endif
                                    @if ($info->translate->address)
                                        <li>
                                            <i class="pe-7s-map-marker"></i>
                                            <span>{{ $info->translate->address }}</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            @if ($info->longitude && $info->latitude)
                                <iframe
                                    src="https://maps.google.com/maps?q={{ $info->latitude }},{{ $info->longitude }}&hl=ge&z=14&amp;output=embed"
                                    class="contact-map-size"  allowfullscreen="true"
                                    loading="lazy"></iframe>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
