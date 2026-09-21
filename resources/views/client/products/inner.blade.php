@extends('layouts.client')
@section('content')
    <main class="main-content">
        <div class="breadcrumb-area breadcrumb-height" data-bg-image="{{ $info->top_banner }}">
            <div class="container h-100">
                <div class="row h-100">
                    <div class="col-lg-12">
                        <div class="breadcrumb-item">
                            <h2 class="breadcrumb-heading">{{ trans('Shop') }}</h2>
                            <ul>
                                <li>
                                    <a href="{{ route('clientHome') }}">{{ trans('Home') }}</a>
                                </li>
                                <li>{{ trans('Shop') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="single-product-area section-space-top-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="single-product-img">
                            <div class="swiper-container single-product-slider">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <a href="{{ $product->image }}" class="single-img gallery-popup">
                                            <img class="img-full" src="{{ $product->image }}"
                                                alt="{{ $product->trans->alt }}">
                                        </a>
                                    </div>
                                    @if ($product->images->isNotEmpty())
                                        @foreach ($product->images as $img)
                                            <div class="swiper-slide">
                                                <a href="{{ $img->image }}" class="single-img gallery-popup">
                                                    <img class="img-full" src="{{ $img->image }}" alt="Product Image">
                                                </a>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="thumbs-arrow-holder">
                                <div class="swiper-container single-product-thumbs">
                                    <div class="swiper-wrapper">
                                        <a href="javascript:void(0);" class="swiper-slide">
                                            <img class="img-full" src="{{ $product->image }}"
                                                alt="{{ $product->trans->alt }}">
                                        </a>
                                        @if ($product->images->isNotEmpty())
                                            @foreach ($product->images as $img)
                                                <a href="javascript:void(0);" class="swiper-slide">
                                                    <img class="img-full" src="{{ $img->image }}" alt="Product Thumnail">
                                                </a>
                                            @endforeach
                                        @endif
                                    </div>
                                    <!-- Add Arrows -->
                                    <div class=" thumbs-button-wrap d-none d-md-block">
                                        <div class="thumbs-button-prev">
                                            <i class="pe-7s-angle-left"></i>
                                        </div>
                                        <div class="thumbs-button-next">
                                            <i class="pe-7s-angle-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 pt-5 pt-lg-0">
                        <div class="single-product-content">
                            <h2 class="title">{{ $product->trans->title }}</h2>
                            @if ($product->price > 0)
                                <div class="price-box">
                                    <span class="new-price">GEL {{ getPrice($product->price) }}</span>
                                </div>
                            @endif
                            <p class="short-desc">{{ $product->trans->short_description }}</p>
                            <ul class="quantity-with-btn">
                                @if (false)
                                <li class="quantity">
                                    <div class="cart-plus-minus">
                                        <input class="cart-plus-minus-box" value="1" type="text">
                                        <div class="dec qtybutton"><i class="fa fa-minus"></i></div>
                                        <div class="inc qtybutton"><i class="fa fa-plus"></i></div>
                                    </div>
                                </li>
                                @endif
                                @if (false)
                                <li class="add-to-cart">
                                    <a class="btn btn-custom-size lg-size btn-pronia-primary add-cart-product-inner"
                                        href="#" data-id="{{ $product->id }}">{{ trans('Add to cart') }}</a>
                                </li>
                                @endif
                                <li class="wishlist-btn-wrap">
                                    <a class="custom-circle-btn add-to-wishlist-product @if((new \App\Services\WishlistService())->has($product->id)) in-wishlist @endif" data-id="{{ $product->id }}"
                                        href="#">
                                        <i class="pe-7s-like"></i>
                                    </a>
                                </li>
                            </ul>
                            @if ($benefits->isNotEmpty())
                                <ul class="service-item-wrap">
                                    @foreach ($benefits as $benefit)
                                        <li class="service-item">
                                            <div class="service-img">
                                                <img src="{{ $benefit->image }}" alt="{{ $benefit->alt }}">
                                            </div>
                                            <div class="service-content">
                                                <span class="title">{{ $benefit->title }}</span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="product-category">
                                <span class="title">{{ trans('Product code') }}:</span>
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);">{{ $product->code }}</a>
                                    </li>
                                </ul>
                            </div>
                            @if ($categories->isNotEmpty())
                                <div class="product-category">
                                    <span class="title">Categories :</span>
                                    <ul>
                                        @foreach ($categories as $category)
                                            <li>
                                                <a
                                                    href="{{ route('clientProducts') }}?category={{ $category->trans->title }}">{{ $category->trans->title }}
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if ($product->trans->description)
                                <div class="product-description">
                                    {!! $product->trans->description !!}
                                </div>
                            @endif
                            <div class="product-category social-link align-items-center pb-0">
                                <span class="title pe-3">Share:</span>
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);" data-tippy="Pinterest" data-tippy-inertia="true"
                                            data-tippy-animation="shift-away" data-tippy-delay="50" data-tippy-arrow="true"
                                            data-tippy-theme="sharpborder">
                                            <i class="fa fa-pinterest-p"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" data-tippy="Twitter" data-tippy-inertia="true"
                                            data-tippy-animation="shift-away" data-tippy-delay="50" data-tippy-arrow="true"
                                            data-tippy-theme="sharpborder">
                                            <i class="fa fa-twitter"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" data-tippy="Tumblr" data-tippy-inertia="true"
                                            data-tippy-animation="shift-away" data-tippy-delay="50"
                                            data-tippy-arrow="true" data-tippy-theme="sharpborder">
                                            <i class="fa fa-tumblr"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" data-tippy="Dribbble" data-tippy-inertia="true"
                                            data-tippy-animation="shift-away" data-tippy-delay="50"
                                            data-tippy-arrow="true" data-tippy-theme="sharpborder">
                                            <i class="fa fa-dribbble"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="product-tab-area section-space-top-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <ul class="nav product-tab-nav tab-style-2 pt-0" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="active tab-btn" id="description-tab" data-bs-toggle="tab" href="#description"
                                    role="tab" aria-controls="description" aria-selected="true">
                                    {{ trans('Description') }}
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content product-tab-content">
                            <div class="tab-pane fade show active" id="description" role="tabpanel"
                                aria-labelledby="description-tab">
                                <div class="product-description-body">
                                    <p class="short-desc mb-0">{!! $product->trans->description !!}</p>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Begin Product Area -->
        <div class="product-area section-space-y-axis-90">
            <div class="container">
                <div class="row">
                    <div class="section-title-wrap without-tab">
                        <h2 class="section-title">{{ trans('Related Products') }}</h2>
                        <p class="section-desc">
                        </p>
                    </div>
                    <div class="col-lg-12">
                        <div class="swiper-container product-slider">
                            <div class="swiper-wrapper">
                                @foreach ($relateds as $related)
                                    <div class="swiper-slide product-item">
                                        <div class="product-img">
                                            <a
                                                href="{{ route('clientProductsInner', $related->slug ?? $related->id . '-' . \Illuminate\Support\Str::slug($related->trans->title, '-', false)) }}">
                                                <img class="primary-img" src="{{ $related->image }}"
                                                    alt="{{ $related->trans->alt }}">
                                            </a>
                                            <div class="product-add-action">
                                                <ul>
                                                    <li>
                                                        <a href="#" class="add-to-wishlist-product @if((new \App\Services\WishlistService())->has($related->id)) in-wishlist @endif"
                                                            data-tippy="{{ trans('Add to wishlist') }}"
                                                            data-id="{{ $related->id }}" data-tippy-inertia="true"
                                                            data-tippy-animation="shift-away" data-tippy-delay="50"
                                                            data-tippy-arrow="true" data-tippy-theme="sharpborder">
                                                            <i class="pe-7s-like"></i>
                                                        </a>
                                                    </li>
                                                   <li class="quuickview-btn" data-bs-toggle="modal" data-bs-target="#quickModal">
                                                        <a href="#quickModal"
                                                          class="quickview-btn"
                                                           data-id="{{ $related->id }}"
                                                           data-tippy="Quickview"
                                                           data-tippy-inertia="true"
                                                           data-tippy-animation="shift-away"
                                                           data-tippy-delay="50"
                                                           data-tippy-arrow="true"
                                                           data-tippy-theme="sharpborder">
                                                  <i class="pe-7s-look"></i>
                                                        </a>
                                                  </li>


                                                    @if (false)
                                                    <li>
                                                        <a href="#" class="add-to-cart-products"
                                                            data-tippy="{{ trans('Add to cart') }}"
                                                            data-tippy-inertia="true" data-id="{{ $related->id }}"
                                                            data-tippy-animation="shift-away" data-tippy-delay="50"
                                                            data-tippy-arrow="true" data-tippy-theme="sharpborder">
                                                            <i class="pe-7s-cart"></i>
                                                        </a>
                                                    </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="product-content">
                                            <a class="product-name"
                                                href="{{ route('clientProductsInner', $related->slug ?? $related->id . '-' . \Illuminate\Support\Str::slug($related->trans->title, '-', false)) }}">{{ $related->trans->title }}</a>
                                            @if ($related->price > 0)
                                                <div class="price-box pb-1">
                                                    <span class="new-price">{{ getPrice($related->price) }} GEL</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Product Area End Here -->

    </main>
@endsection
