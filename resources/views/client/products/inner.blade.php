@extends('layouts.client')

@push('schema')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->trans->title,
            'description' => $product->trans->short_description ?: $product->trans->title,
            'image' => array_values(array_filter(array_merge([$product->image], $product->images->pluck('image')->toArray()))),
            'sku' => $product->code,
            'offers' => [
                '@type' => 'Offer',
                'url' => url()->current(),
                'priceCurrency' => 'GEL',
                'price' => (string) $product->price,
                'availability' => $product->available ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => trans('Home'),
                    'item' => route('clientHome'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => trans('Shop'),
                    'item' => route('clientProducts'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $product->trans->title,
                    'item' => url()->current(),
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

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
        <div class="single-product-area section-space-y-axis-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="single-product-img">
                            <div class="swiper-container single-product-slider">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <a href="{{ $product->image }}" class="single-img gallery-popup">
                                            <img class="img-full" src="{{ $product->image }}"
                                                alt="{{ $product->trans->alt }}" fetchpriority="high">
                                        </a>
                                    </div>
                                    @if ($product->images->isNotEmpty())
                                        @foreach ($product->images as $img)
                                            <div class="swiper-slide">
                                                <a href="{{ $img->image }}" class="single-img gallery-popup">
                                                    <img class="img-full gallery-slide-img" src="{{ $img->image }}" alt="Product Image" loading="lazy" decoding="async">
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
                                                    <img class="img-full gallery-slide-img" src="{{ $img->image }}" alt="Product Thumnail" loading="lazy" decoding="async">
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
                                <li class="wishlist-btn-wrap mb-4">
                                    <a class="custom-circle-btn add-to-wishlist-product @if((new \App\Services\WishlistService())->has($product->id)) in-wishlist @endif" data-id="{{ $product->id }}"
                                        href="#">
                                        <i class="pe-7s-like"></i>
                                    </a>
                                </li>
                            </ul>
                            @if ($product->code)
                                <div class="product-category">
                                    <span class="title">{{ trans('Product code') }}:</span>
                                    <ul>
                                        <li>
                                            <a href="javascript:void(0);">{{ $product->code }}</a>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                            @if ($categories->isNotEmpty())
                                <div class="product-category">
                                    <span class="title">{{ trans('Categories') }}:</span>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($benefits->isNotEmpty())
            <!-- Begin Shipping Area -->
            <div class="shipping-area">
                <div class="container">
                    <div class="shipping-bg">
                        <div class="row shipping-wrap">
                            @foreach ($benefits as $benefit)
                                <div class="col-lg-4 col-md-6">
                                    <div class="shipping-item">
                                        <div class="shipping-img">
                                            <img src="{{ $benefit->image }}" alt="{{ $benefit->alt }}" loading="lazy" decoding="async">
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

        <!-- Begin Product Area -->
        @if ($relateds->isNotEmpty())
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
                                                    alt="{{ $related->trans->alt }}" loading="lazy" decoding="async">
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
        @endif
        <!-- Product Area End Here -->

    </main>
@endsection
