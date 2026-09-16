@php
    $title = !request()->segment(3) ? trans('menu.' . $metaTitle) : $metaTitle;
@endphp

<!DOCTYPE html>
<html lang="{{ locale() }}">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>@include('layouts.title')</title>

    <meta name="author" content="Smart Web" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="@include('layouts.meta-title')" />
    <meta property="og:description" content="@include('layouts.meta-description')" />

    <meta property="og:title" content="Shop" />
    <meta property="og:description" content="Shop" />
    <meta property="og:image" content="" />

    <!-- CSS
    ============================================ -->

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/Pe-icon-7-stroke.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/ion.rangeSlider.min.css') }}" />

    <!-- Style CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/firago-font.css') }}">
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
            <div class="header-top bg-pronia-primary d-none d-lg-block">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-6">
                        </div>
                        <div class="col-6">
                            <div class="header-top-right">
                                <ul class="dropdown-wrap">
                                    <li class="dropdown">
                                        <button class="btn btn-link dropdown-toggle ht-btn" type="button"
                                            id="languageButton" data-bs-toggle="dropdown" aria-expanded="false">

                                            {{ LaravelLocalization::getCurrentLocaleNative() }}
                                        </button>

                                        <ul class="dropdown-menu" aria-labelledby="languageButton">
                                            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                                @if (locale() !== $localeCode)
                                                    <li>
                                                        <a class="dropdown-item" rel="alternate"
                                                            hreflang="{{ $localeCode }}"
                                                            href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                                            {{ $properties['native'] }}
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="header-middle py-30">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <div class="header-middle-wrap position-relative">
                                @if ($info->phone)
                                    <div class="header-contact d-none d-lg-flex">
                                        <i class="pe-7s-call"></i>
                                        <a href="tel:{{ $info->phone }}">{{ $info->phone }}</a>
                                    </div>
                                @endif
                                <a href="{{ route('clientHome') }}" class="header-logo">
                                    <img src="{{ $info->logo }}" alt="Header Logo">
                                </a>

                                <div class="header-right">
                                    <ul>
                                        <li>
                                            <a href="#exampleModal" class="search-btn bt" data-bs-toggle="modal"
                                                data-bs-target="#exampleModal">
                                                <i class="pe-7s-search"></i>
                                            </a>
                                        </li>
                                        <li class="dropdown d-none d-lg-block">
                                            <button class="btn btn-link dropdown-toggle ht-btn p-0" type="button"
                                                id="settingButton" data-bs-toggle="dropdown" aria-label="setting"
                                                aria-expanded="false">
                                                <i class="pe-7s-users"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="settingButton">
                                                @auth
                                                    <li><a class="dropdown-item" href="{{ route('myAccount') }}">My
                                                            account</a></li>
                                                    <li><a class="dropdown-item"
                                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                                                    </li>
                                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                        style="display: none;">
                                                        @csrf
                                                    </form>
                                                @endauth
                                                @guest
                                                    <li><a class="dropdown-item" href="{{ route('signin') }}">Login</a>
                                                    </li>
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('signup') }}">Register</a></li>
                                                @endguest
                                            </ul>
                                        </li>
                                        <li class="d-none d-lg-block">
                                            <a href="{{ route('clientWishlist') }}">
                                                <i class="pe-7s-like"></i>
                                            </a>
                                        </li>
                                        <li class="minicart-wrap me-3 me-lg-0">
                                            <a href="#miniCart" class="minicart-btn toolbar-btn">
                                                <i class="pe-7s-shopbag"></i>
                                                <span class="quantity">{{ $cart->count() }}</span>
                                            </a>
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
                                        <li>
                                            <a href="{{ route('clientBlogs') }}">
                                                <span class="mm-text">{{ trans('Blog') }}</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('clientAbout') }}">{{ trans('About us') }}</a>
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

                                <a href="{{ route('clientHome') }}" class="header-logo">
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
                                            <li>
                                                <a href="{{ route('clientBlogs') }}">
                                                    <span class="mm-text">{{ trans('Blog') }}</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('clientAbout') }}">{{ trans('About us') }}</a>
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
                                                data-bs-target="#exampleModal">
                                                <i class="pe-7s-search"></i>
                                            </a>
                                        </li>
                                        <li class="dropdown d-none d-lg-block">
                                            <button class="btn btn-link dropdown-toggle ht-btn p-0" type="button"
                                                id="stickysettingButton" data-bs-toggle="dropdown"
                                                aria-label="setting" aria-expanded="false">
                                                <i class="pe-7s-users"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="stickysettingButton">
                                                @auth
                                                    <li><a class="dropdown-item" href="{{ route('myAccount') }}">My
                                                            account</a>
                                                    @endauth
                                                </li>
                                                @guest
                                                    <li><a class="dropdown-item" href="{{ route('signin') }}">Login</a>
                                                    </li>

                                                    <li><a class="dropdown-item"
                                                            href="{{ route('signup') }}">Register</a></li>
                                                @endguest
                                            </ul>
                                        </li>
                                        <li class="d-none d-lg-block">
                                            <a href="{{ route('clientWishlist') }}">
                                                <i class="pe-7s-like"></i>
                                            </a>
                                        </li>
                                        <li class="minicart-wrap me-3 me-lg-0">
                                            <a href="#miniCart" class="minicart-btn toolbar-btn">
                                                <i class="pe-7s-shopbag"></i>
                                                <span class="quantity">{{ $cart->count() }}</span>
                                            </a>
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
                                <div class="col-6">
                                    <div class="header-top-right">
                                        <ul class="dropdown-wrap">
                                            <li class="dropdown">
                                                <button class="btn btn-link dropdown-toggle ht-btn" type="button"
                                                    id="languageButton" data-bs-toggle="dropdown"
                                                    aria-expanded="false">

                                                    {{ LaravelLocalization::getCurrentLocaleNative() }}
                                                </button>

                                                <ul class="dropdown-menu" aria-labelledby="languageButton">

                                                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                                        @if (locale() !== $localeCode)
                                                            <li>
                                                                <a class="dropdown-item" rel="alternate"
                                                                    hreflang="{{ $localeCode }}"
                                                                    href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                                                    {{ $properties['native'] }}
                                                                </a>
                                                            </li>
                                                        @endif
                                                    @endforeach

                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <li class="dropdown">
                                    <button class="btn btn-link dropdown-toggle ht-btn p-0" type="button"
                                        id="settingButtonTwo" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="pe-7s-users"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingButtonTwo">
                                        @auth
                                            <li><a class="dropdown-item" href="{{ route('myAccount') }}">{{ trans('My Account') }}</a>
                                            </li>
                                        @endauth
                                        @guest
                                            <li><a class="dropdown-item" href="{{ route('signin') }}">Login</a></li>
                                            <li><a class="dropdown-item" href="{{ route('signup') }}">Register</a></li>
                                        @endguest


                                    </ul>
                                </li>
                                <li>
                                    <a href="{{ route('clientWishlist') }}">
                                        <i class="pe-7s-like"></i>
                                    </a>
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
                                    <li>
                                        <a href="{{ route('clientBlogs') }}">
                                            <span class="mm-text">{{ trans('Blog') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('clientAbout') }}">
                                            <span class="mm-text">{{ trans('About us') }}</span>
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
                                <span class="searchbox-info">{{ trans('Start typing and press Enter to search or ESC to close') }}</span>
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
                <div class="offcanvas-body">
                    <div class="minicart-content">
                        <div class="minicart-heading">
                            <h4 class="mb-0">Shopping Cart</h4>
                            <a href="#" class="button-close"><i class="pe-7s-close" data-tippy="Close"
                                    data-tippy-inertia="true" data-tippy-animation="shift-away" data-tippy-delay="50"
                                    data-tippy-arrow="true" data-tippy-theme="sharpborder"></i></a>
                        </div>
                        <ul class="minicart-list">
                            @if ($cart->isNotEmpty())
                                @foreach ($cart as $carty)
                                    <li class="minicart-product">
                                        <a class="product-item_remove" href="#"><i class="pe-7s-close"
                                                data-tippy="Remove" data-tippy-inertia="true"
                                                data-tippy-animation="shift-away" data-tippy-delay="50"
                                                data-tippy-arrow="true" data-tippy-theme="sharpborder"></i></a>
                                        <a href="single-product-variable.html" class="product-item_img">
                                            <img class="img-full" src="{{ $carty->attributes->image }}"
                                                alt="Product Image">
                                        </a>
                                        <div class="product-item_content">
                                            <a class="product-item_title"
                                                href="single-product-variable.html">{{ $carty->name }}</a>
                                            <span class="product-item_quantity">{{ $carty->quantity }} x
                                                {{ $carty->price }} GEL</span>
                                        </div>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    <div class="minicart-item_total">
                        <span>Total</span>
                        <span class="ammount">{{ getPrice(\Cart::getSubTotal()) }} GEL</span>
                    </div>
                    <div class="group-btn_wrap d-grid gap-2">
                        <a href="{{ route('clientCart') }}" class="btn btn-dark">Checkout</a>
                    </div>
                </div>
            </div>
            <div class="global-overlay"></div>
        </header>
        <!-- Main Header Area End Here -->
        {{ $slot }}
        <!-- Begin Footer Area -->
        <div class="footer-area" data-bg-image="{{ asset('assets/images/footer/bg/1-1920x465.jpg') }}">
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
                                        <a href="{{ route('signup') }}">{{ trans('Sign up') }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 pt-40">
                            <div class="footer-widget-item">
                                <h3 class="footer-widget-title">{{ trans('My Account') }}</h3>
                                <ul class="footer-widget-list-item">
                                    <li>
                                        <a href="{{ route('signin') }}">{{ trans('Sign in') }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('clientCart') }}">{{ trans('Cart') }}</a>
                                    </li>
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
                                <span class="copyright-text">© 2021 Pronia Made with <i
                                        class="fa fa-heart text-danger"></i> by
                                    <a href="https://hasthemes.com/" rel="noopener" target="_blank">HasThemes</a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer Area End Here -->

       

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
</body>

</html>
