@extends('layouts.client')
@section('content')
    <!-- Begin Slider Area -->
    <div class="slider-area">

        <!-- Main Slider -->
        @if ($sliders->isNotEmpty())
            <div class="swiper-container main-slider swiper-arrow with-bg_white">
                <div class="swiper-wrapper">
                    @foreach ($sliders as $slider)
                        <div class="swiper-slide animation-style-01">
                            <div class="slide-inner style-1 bg-height" data-bg-image="assets/images/slider/bg/1-1.jpg">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-6 order-2 order-lg-1 align-self-center">
                                            <div class="slide-content text-black">
                                                <h2 class="title">{{ $slider->title }}</h2>
                                                <p class="short-desc">{{ $slider->short_description }}</p>
                                                @if ($slider->url)
                                                    <div class="btn-wrap">
                                                        <a class="btn btn-custom-size xl-size btn-pronia-primary"
                                                            href="{{ $slider->url }}">{{ $slider->button_title ?: trans('Discover') }}</a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-8 offset-md-2 offset-lg-0 order-1 order-lg-2">
                                            <div class="inner-img">
                                                <div class="scene fill">
                                                    <div class="expand-width" data-depth="0.2">
                                                        <img src="{{ $slider->image }}" alt="Inner Image">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- Add Pagination -->
                <div class="swiper-pagination d-md-none"></div>

                <!-- Add Arrows -->
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        @endif
    </div>
    <!-- Slider Area End Here -->

    @if ($benefits->isNotEmpty())
        <!-- Begin Shipping Area -->
        <div class="shipping-area section-space-top-100">
            <div class="container">
                <div class="shipping-bg">
                    <div class="row shipping-wrap">
                        @foreach ($benefits as $benefit)
                            <div class="col-lg-4 col-md-6">
                                <div class="shipping-item">
                                    <div class="shipping-img">
                                        <img src="{{ $benefit->image }}" alt="{{ $benefit->alt }}">
                                    </div>
                                    <div class="shipping-content">
                                        <h2 class="title">{{ $benefit->title }}</h2>
                                        <p class="short-desc mb-0">{{ $benefit->short_description }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <!-- Shipping Area End Here -->
    @endif
    @if ($products->isNotEmpty())
        <!-- Begin Product Area -->
        <div class="product-area section-space-top-100">
            <div class="container">
                <div class="section-title-wrap">
                    <h2 class="section-title mb-0">{{ trans('Products') }}</h2>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <ul class="nav product-tab-nav tab-style-1" id="myTab" role="tablist">
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="featured" role="tabpanel"
                                aria-labelledby="featured-tab">
                                <div class="product-item-wrap row">
                                    @foreach ($products as $product)
                                        <div
                                            class="col-xl-3 col-md-4 col-sm-6 @if ($loop->iteration > 4) pt-4 @endif">
                                            <div class="product-item">
                                                <div class="product-img">
                                                    <a
                                                        href="{{ route('clientProductsInner', $product->slug ?? $product->id . '-' . \Illuminate\Support\Str::slug($product->trans->title, '-', false)) }}">
                                                        <img class="primary-img" src="{{ $product->image }}"
                                                            alt="{{ $product->trans->alt }}">
                                                        <img class="secondary-img" src="{{ $product->image }}"
                                                            alt="{{ $product->trans->alt }}">
                                                    </a>
                                                    <div class="product-add-action">
                                                        <ul>
                                                            <li>
                                                                <a href="#" class="add-to-wishlist-product" data-tippy="{{ trans('Add to wishlist') }}"
                                                                    data-tippy-inertia="true"
                                                                    data-tippy-animation="shift-away" data-tippy-delay="50"
                                                                    data-tippy-arrow="true" data-id="{{ $product->id }}" data-tippy-theme="sharpborder">
                                                                    <i class="pe-7s-like"></i>
                                                                </a>
                                                           <li>
    <a href="#quickModal"
       class="quickview-btn"
       data-id="{{ $product->id }}"
       data-bs-toggle="modal"
       data-bs-target="#quickModal"
       data-tippy="Quickview"
       data-tippy-inertia="true"
       data-tippy-animation="shift-away"
       data-tippy-delay="50"
       data-tippy-arrow="true"
       data-tippy-theme="sharpborder">
        <i class="pe-7s-look"></i>
    </a>
</li>





                                                            <li>
                                                                <a href="#" class="add-to-cart-products"
                                                                    data-tippy="{{ trans('Add to cart') }}" data-tippy-inertia="true"
                                                                    data-tippy-animation="shift-away" data-tippy-delay="50"
                                                                    data-tippy-arrow="true" data-tippy-theme="sharpborder"
                                                                    data-id="{{ $product->id }}">
                                                                    <i class="pe-7s-cart"></i>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="product-content">
                                                    <a class="product-name"
                                                        href="{{ route('clientProductsInner', $product->slug ?? $product->id . '-' . \Illuminate\Support\Str::slug($product->trans->title, '-', false)) }}">{{ $product->trans->title }}</a>
                                                    <div class="price-box pb-1">
                                                        <span class="new-price">{{ getPrice($product->price) }} GEL</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!-- Product Area End Here -->

    @if ($banners->isNotEmpty())
        <!-- Begin Banner Area -->
        <div class="banner-area section-space-top-90">
            <div class="container">
                <div class="row g-min-30 g-4">
                    @foreach ($banners as $banner)
                        @php
                            $classA = 'col-lg-8';
                            $classB = 'col-lg-4 col-md-6';

                            $i = $loop->iteration;

                            if ($i % 4 == 1 || $i % 4 == 0) {
                                $colClass = $classA;
                            } else {
                                $colClass = $classB;
                            }
                        @endphp

                        <div class="{{ $colClass }}">
                            <div class="banner-item img-hover-effect">
                                <div class="banner-img">
                                    <img src="{{ $banner->image }}" alt="{{ $banner->alt }}">
                                </div>
                                <div class="banner-content text-position-left">
                                    <h3 class="title">{{ $banner->title }}</h3>
                                    @if ($banner->url)
                                        <div class="button-wrap">
                                            <a class="btn btn-custom-size btn-pronia-primary" href="{{ $banner->url }}">
                                                {{ $banner->button_title }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Banner Area End Here -->
    @endif
    <!-- Begin Product Area -->
    <div class="section-space-top-100">
    </div>
    <!-- Product Area End Here -->


    @if ($blogs->isNotEmpty())
        <!-- Begin Blog Area -->
        <div class="blog-area section-space-bottom-100">
            <div class="container">
                <div class="section-title-wrap">
                    <h2 class="section-title mb-7">{{ trans('Latest Blog') }}</h2>
                    <p class="section-desc">
                    </p>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="swiper-container blog-slider">
                            <div class="swiper-wrapper">
                                @foreach ($blogs as $blog)
                                    <div class="swiper-slide">
                                        <div class="blog-item">
                                            <div class="blog-content">
                                                <div class="blog-meta">
                                                    <ul>
                                                        <li class="date">
                                                            {{ \Carbon\Carbon::parse($blog->created_at)->locale(locale())->translatedFormat('d F, Y') }}
                                                        </li>
                                                    </ul>
                                                </div>
                                                <h2 class="title">
                                                    <a
                                                        href="{{ route('clientBlogsInner', $blog->slug ?? $blog->id . '-' . \Illuminate\Support\Str::slug($blog->trans->title, '-', false)) }}">{{ $blog->trans->title }}</a>
                                                </h2>
                                                <p class="short-desc mb-7">{{ shorten($blog->trans->short_description) }}
                                                </p>
                                            </div>
                                            <div class="blog-img img-hover-effect">
                                                <a
                                                    href="{{ route('clientBlogsInner', $blog->slug ?? $blog->id . '-' . \Illuminate\Support\Str::slug($blog->trans->title, '-', false)) }}">
                                                    <img class="img-full" src="{{ $blog->image }}"
                                                        alt="{{ $blog->trans->alt }}">
                                                </a>
                                                <div class="inner-btn-wrap">
                                                    <a class="inner-btn"
                                                        href="{{ route('clientBlogsInner', $blog->slug ?? $blog->id . '-' . \Illuminate\Support\Str::slug($blog->trans->title, '-', false)) }}">
                                                        <i class="pe-7s-link"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <!-- Add Arrows -->
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Blog Area End Here -->
    @endif
    @if ($partners->isNotEmpty())
        <!-- Begin Brand Area -->
        <div class="brand-area section-space-bottom-100">
            <div class="container">
                <div class="brand-bg" data-bg-image="{{ asset('assets/images/brand/bg/1-1170x300.jpg') }}">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="swiper-container brand-slider">
                                <div class="swiper-wrapper">
                                    @foreach ($partners as $partner)
                                        <div class="swiper-slide">
                                            <a class="brand-item" href="{{ $partner->url }}">
                                                <img src="{{ $partner->image }}" alt="{{ $partner->alt }}">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Brand Area End Here -->
    @endif
@endsection
