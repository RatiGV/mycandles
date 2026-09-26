@php
    $title = !request()->segment(3) ? trans('menu.' . $metaTitle) : $metaTitle;
    $cart = \Cart::getContent();
@endphp

<!DOCTYPE html>
<html lang="{{ locale() }}">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ $info->favicon }}" />

    <title>@include('layouts.title') - {{ $info->translate->slogan }}</title>

    <meta name="author" content="Smart Web" />

    <link rel="canonical" href="{{ url()->current() }}" />
    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
        <link rel="alternate" hreflang="{{ $localeCode }}"
            href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" />
    @endforeach
    <link rel="alternate" hreflang="x-default"
        href="{{ LaravelLocalization::getLocalizedURL(LaravelLocalization::getDefaultLocale(), null, [], true) }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="{{ $info->translate->slogan }}" />
    <meta property="og:locale" content="{{ locale() }}" />
    <meta property="og:title" content="@include('layouts.meta-title')" />
    <meta property="og:description" content="@include('layouts.meta-description')" />
    <meta property="og:image" content="{{ $metaImage ? url($metaImage) : '' }}" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="@include('layouts.meta-title')" />
    <meta name="twitter:description" content="@include('layouts.meta-description')" />
    <meta name="twitter:image" content="{{ $metaImage ? url($metaImage) : '' }}" />

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $info->translate->slogan,
            'url' => url('/'),
            'logo' => $info->logo,
            'contactPoint' => array_filter([
                '@type' => 'ContactPoint',
                'telephone' => $info->phone,
                'contactType' => 'customer service',
            ]),
            'sameAs' => array_values(array_filter([$info->facebook, $info->twitter])),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $info->translate->slogan,
            'url' => url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/products') . '?search={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @stack('schema')

    <!-- CSS
    ============================================ -->

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}?v=3" />

    <!-- Style CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v=3">

    <!-- Non-critical CSS (icon fonts, sliders, popups, toast, custom font) loaded async -->
    <link rel="preload" href="{{ asset('assets/css/vendor-bundle.css') }}?v=3" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('assets/css/vendor-bundle.css') }}?v=3"></noscript>
    @stack('css')

    <script>
        window.GET_PRODUCT_INFO_URL = "{{ route('getProductInfo') }}";
    </script>

    <script>
       window.ADD_TO_CART_URL = "/ajax-add-cart";
    </script>

    <script>
        window.WISHLIST_MESSAGES = {
            added: "{{ trans('Added to wishlist') }}",
            removed: "{{ trans('Removed from wishlist') }}",
            alreadyAdded: "{{ trans('Already in wishlist') }}",
            unavailable: "{{ trans('This product is unavailable') }}",
            error: "{{ trans('Something went wrong') }}"
        };
    </script>

    @include('partials.tracking')
</head>

<body>
    {{-- <div class="preloader-activate preloader-active open_tm_preloader">
        <div class="preloader-area-wrap">
            <div class="spinner d-flex justify-content-center align-items-center h-100">
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
            </div>
        </div>
    </div> --}}
    <div class="main-wrapper">
        <!-- Begin Main Header Area -->
        <header class="main-header-area">
            <div class="header-middle py-30">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <div class="header-middle-wrap position-relative">
                                <a href="{{ route('clientHome') }}" class="header-logo header-logo-round">
                                    <img src="{{ $info->logo }}" alt="Header Logo">
                                </a>

                                <div class="header-right">
                                    <ul>
                                        <li>
                                            <a href="#exampleModal" class="search-btn bt" data-bs-toggle="modal"
                                                data-bs-target="#exampleModal" title="Search Product" aria-label="Search Product">
                                                <i class="pe-7s-search"></i>
                                            </a>
                                        </li>
                                        <li class="d-none d-lg-block">
                                            <a href="{{ route('clientWishlist') }}" class="wishlist-nav-link">
                                                <i class="pe-7s-like"></i>
                                                <span class="wishlist-quantity">{{ count((new \App\Services\WishlistService())->ids()) }}</span>
                                            </a>
                                        </li>
                                        <li class="d-none d-lg-block header-lang-switch">
                                            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                                @if (locale() !== $localeCode)
                                                    <a class="ht-btn" rel="alternate"
                                                        hreflang="{{ $localeCode }}"
                                                        href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                                        {{ $localeCode === 'en' ? 'ENG' : $properties['native'] }}
                                                    </a>
                                                @endif
                                            @endforeach
                                        </li>
                                        <li class="mobile-menu_wrap d-block d-lg-none">
                                            <a href="#mobileMenu" class="mobile-menu_btn toolbar-btn pl-0">
                                                <i class="pe-7s-menu"></i>
                                            </a>
                                        </li>
                                    </ul>
                                    @if ($info->phone)
                                        <div class="header-contact header-contact-below d-none d-lg-flex">
                                            <i class="pe-7s-call"></i>
                                            <a href="tel:{{ $info->phone }}">{{ $info->phone }}</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-bottom d-none d-lg-block">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-menu position-relative">
                                <nav class="main-nav">
                                    <ul>
                                        <li>
                                            <a href="{{ route('clientHome') }}">{{ trans('Home') }}</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('clientProducts') }}">
                                                <span class="mm-text">{{ trans('Shop') }}</span>
                                            </a>
                                        </li>
                                        {{-- <li>
                                            <a href="{{ route('clientBlogs') }}">
                                                <span class="mm-text">{{ trans('Blog') }}</span>
                                            </a>
                                        </li> --}}
                                        <li>
                                            <a href="{{ route('clientAbout') }}">{{ trans('About us') }}</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('clientFaq') }}">{{ trans('FAQ') }}</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('clientContact') }}">{{ trans('Contact us') }}</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-sticky py-4 py-lg-0">
                <div class="container">
                    <div class="header-nav position-relative">
                        <div class="row align-items-center">
                            <div class="col-lg-3 col-6">

                                <a href="{{ route('clientHome') }}" class="header-logo header-logo-round header-logo-round-sm">
                                    <img src="{{ $info->logo }}" alt="Header Logo">
                                </a>

                            </div>
                            <div class="col-lg-6 d-none d-lg-block">
                                <div class="main-menu">
                                    <nav class="main-nav">
                                        <ul>
                                            <li>
                                                <a href="{{ route('clientHome') }}">
                                                    <span class="mm-text">{{ trans('Home') }}</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('clientProducts') }}">
                                                    <span class="mm-text">{{ trans('Shop') }}</span>
                                                </a>
                                            </li>
                                            {{-- <li>
                                                <a href="{{ route('clientBlogs') }}">
                                                    <span class="mm-text">{{ trans('Blog') }}</span>
                                                </a>
                                            </li> --}}
                                            <li>
                                                <a href="{{ route('clientAbout') }}">{{ trans('About us') }}</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('clientFaq') }}">{{ trans('FAQ') }}</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('clientContact') }}">{{ trans('Contact us') }}</a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="header-right">
                                    <ul>
                                        <li>
                                            <a href="#exampleModal" class="search-btn bt" data-bs-toggle="modal"
                                                data-bs-target="#exampleModal" title="Search Product" aria-label="Search Product">
                                                <i class="pe-7s-search"></i>
                                            </a>
                                        </li>
                                        <li class="d-none d-lg-block">
                                            <a href="{{ route('clientWishlist') }}" class="wishlist-nav-link">
                                                <i class="pe-7s-like"></i>
                                                <span class="wishlist-quantity">{{ count((new \App\Services\WishlistService())->ids()) }}</span>
                                            </a>
                                        </li>
                                        <li class="d-none d-lg-block header-lang-switch">
                                            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                                @if (locale() !== $localeCode)
                                                    <a class="ht-btn" rel="alternate"
                                                        hreflang="{{ $localeCode }}"
                                                        href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                                        {{ $localeCode === 'en' ? 'ENG' : $properties['native'] }}
                                                    </a>
                                                @endif
                                            @endforeach
                                        </li>
                                        <li class="mobile-menu_wrap d-block d-lg-none">
                                            <a href="#mobileMenu" class="mobile-menu_btn toolbar-btn pl-0">
                                                <i class="pe-7s-menu"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mobile-menu_wrapper" id="mobileMenu">
                <div class="offcanvas-body">
                    <div class="inner-body">
                        <div class="offcanvas-top">
                            <a href="#" class="button-close"><i class="pe-7s-close"></i></a>
                        </div>
                        <div class="header-contact offcanvas-contact">
                            <i class="pe-7s-call"></i>
                            <a href="tel:{{ $info->phone }}">{{ $info->phone }}</a>
                        </div>
                        <div class="offcanvas-user-info">
                            <ul class="dropdown-wrap">
                                <li>
                                    <a href="{{ route('clientWishlist') }}" class="wishlist-nav-link">
                                        <i class="pe-7s-like"></i>
                                        <span class="wishlist-quantity">{{ count((new \App\Services\WishlistService())->ids()) }}</span>
                                    </a>
                                </li>
                                <li class="header-lang-switch">
                                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                        @if (locale() !== $localeCode)
                                            <a class="ht-btn" rel="alternate"
                                                hreflang="{{ $localeCode }}"
                                                href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                                {{ $localeCode === 'en' ? 'ENG' : $properties['native'] }}
                                            </a>
                                        @endif
                                    @endforeach
                                </li>
                            </ul>
                        </div>
                        <div class="offcanvas-menu_area">
                            <nav class="offcanvas-navigation">
                                <ul class="mobile-menu">
                                    <li>
                                        <a href="{{ route('clientHome') }}">
                                            <span class="mm-text">{{ trans('Home') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('clientProducts') }}">
                                            <span class="mm-text">{{ trans('Shop') }}</span>
                                        </a>
                                    </li>
                                    {{-- <li>
                                        <a href="{{ route('clientBlogs') }}">
                                            <span class="mm-text">{{ trans('Blog') }}</span>
                                        </a>
                                    </li> --}}
                                    <li>
                                        <a href="{{ route('clientAbout') }}">
                                            <span class="mm-text">{{ trans('About us') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('clientFaq') }}">
                                            <span class="mm-text">{{ trans('FAQ') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('clientContact') }}">
                                            <span class="mm-text">{{ trans('Contact us') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModal"
                aria-hidden="true">
                <div class="modal-dialog modal-fullscreen">
                    <div class="modal-content modal-bg-dark">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                data-tippy="Close" data-tippy-inertia="true" data-tippy-animation="shift-away"
                                data-tippy-delay="50" data-tippy-arrow="true" data-tippy-theme="sharpborder">
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="modal-search">
                                <span
                                    class="searchbox-info">{{ trans('Start typing and press Enter to search or ESC to close') }}</span>
                                <form action="{{ route('clientProducts') }}" class="hm-searchbox">
                                    <input type="text" name="search" value="Search..."
                                        onblur="if(this.value==''){this.value='Search...'}"
                                        onfocus="if(this.value=='Search...'){this.value=''}" autocomplete="off">
                                    <button class="search-btn" type="submit" aria-label="searchbtn">
                                        <i class="pe-7s-search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="offcanvas-minicart_wrapper" id="miniCart">
                @include('partials.mini-cart')
            </div>
            <div class="global-overlay"></div>
        </header>
        <!-- Main Header Area End Here -->
        @yield('content')
        <!-- Begin Footer Area -->
        <div class="footer-area" data-bg-image="{{ $info->bottom_banner }}">
            <div class="footer-top section-space-top-100 pb-60">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="footer-widget-item">
                                <div class="footer-widget-logo">
                                    <a href="{{ route('clientHome') }}">
                                        <img src="{{ $info->logo }}" alt="Logo">
                                    </a>
                                </div>
                                <p class="footer-widget-desc">{{ $info->translate->slogan }}
                                </p>
                                <div class="social-link with-border">
                                    <ul>
                                        @if ($info->facebook)
                                            <li>
                                                <a href="{{ $info->facebook }}" data-tippy="Facebook"
                                                    data-tippy-inertia="true" data-tippy-animation="shift-away"
                                                    data-tippy-delay="50" data-tippy-arrow="true"
                                                    data-tippy-theme="sharpborder">
                                                    <i class="fa fa-facebook"></i>
                                                </a>
                                            </li>
                                        @endif
                                        @if ($info->twitter)
                                            <li>
                                                <a href="{{ $info->twitter }}" data-tippy="Twitter"
                                                    data-tippy-inertia="true" data-tippy-animation="shift-away"
                                                    data-tippy-delay="50" data-tippy-arrow="true"
                                                    data-tippy-theme="sharpborder">
                                                    <i class="fa fa-twitter"></i>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 pt-40">
                        </div>
                        <div class="col-lg-2 col-md-4 pt-40">
                            <div class="footer-widget-item">
                                <h3 class="footer-widget-title">{{ trans('Useful Links') }}</h3>
                                <ul class="footer-widget-list-item">
                                    <li>
                                        <a href="{{ route('clientAbout') }}">{{ trans('About us') }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('clientFaq') }}">{{ trans('FAQ') }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('clientContact') }}">{{ trans('Contact us') }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('clientTerms') }}">{{ trans('Terms and Conditions') }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 pt-40">
                            <div class="footer-widget-item">
                                <h3 class="footer-widget-title">{{ trans('My Account') }}</h3>
                                <ul class="footer-widget-list-item">
                                    {{-- @guest
                                        <li>
                                            <a href="{{ route('signin') }}">{{ trans('Sign in') }}</a>
                                        </li>
                                    @endguest
                                    @guest
                                        <li>
                                            <a href="{{ route('signup') }}">{{ trans('Sign up') }}</a>
                                        </li>
                                    @endguest --}}
                                    {{-- <li>
                                        <a href="{{ route('clientCart') }}">{{ trans('Cart') }}</a>
                                    </li> --}}
                                    <li>
                                        <a href="{{ route('clientWishlist') }}">{{ trans('Wishlist') }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-3 pt-40">
                            <div class="footer-contact-info">
                                <a class="number" href="tel:{{ $info->phone }}">{{ $info->phone }}</a>
                                <div class="address">
                                    <ul>
                                        <li>
                                            {{ $info->translate->address }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="copyright">
                                <span class="copyright-text">Created by
                                    <a href="https://ReadyWeb.ge/" rel="noopener" target="_blank">ReadyWeb</a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer Area End Here -->

      <!-- Begin Modal Area -->
<div class="modal quick-view-modal fade" id="quickModal" data-bs-backdrop="static" data-bs-keyboard="false"
     tabindex="-1" aria-labelledby="quickModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        data-tippy="Close" data-tippy-inertia="true" data-tippy-animation="shift-away"
                        data-tippy-delay="50" data-tippy-arrow="true" data-tippy-theme="sharpborder">
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-wrap row">
                    <div class="col-lg-6">
                        <div class="modal-img">
                            <div class="swiper-container modal-slider">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <a href="#" class="single-img quickview-link">
                                            <img class="img-full quickview-image"
                                                 src="{{ asset('assets/images/product/large-size/1-1-570x633.jpg') }}"
                                                 alt="" loading="lazy" decoding="async">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 pt-5 pt-lg-0">
                        <div class="single-product-content">

                            <h2 class="title"><a class="quickview-link quickview-title" href="#"></a></h2>

                           <div class="price-box">
                                <span class="new-price quickview-price"></span>
                            </div>


                            <p class="short-desc quickview-description"></p>
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
                                        <button type="button"
                                                class="btn btn-custom-size lg-size btn-pronia-primary add-cart-product-inner"
                                                data-id="">
                                            {{ trans('Add to cart') }}
                                        </button>
                                    </li>
                                @endif

                                <li class="wishlist-btn-wrap">
                                    <a class="custom-circle-btn" href="{{ route('clientWishlist') }}">
                                        <i class="pe-7s-like"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Area End Here -->


        <!-- Begin Scroll To Top -->
        <a class="scroll-to-top" href="">
            <i class="fa fa-angle-double-up"></i>
        </a>
        <!-- Scroll To Top End Here -->
    </div>
    <!-- JS Files
    ============================================ -->

    <script src="{{ asset('assets/js/vendor/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/jquery-migrate-3.3.2.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/jquery.waypoints.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/modernizr-3.11.2.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/wow.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/jquery.nice-select.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/parallax.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/tippy.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/ion.rangeSlider.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/mailchimp-ajax.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/jquery.counterup.js') }}"></script>

    <!--Main JS (Common Activation Codes)-->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @stack('js')
</body>

</html>
