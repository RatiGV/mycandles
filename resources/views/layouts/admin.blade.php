<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $information ? $information->title : '' }}</title>
    <!-- Bootstrap -->
    <link href="/Administrator/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/Administrator/vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <link href="/Administrator/vendors/select2/dist/css/select2.min.css" rel="stylesheet">
    <link href="/Administrator/vendors/switchery/dist/switchery.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="/Administrator/vendors/nprogress/nprogress.css" rel="stylesheet">
    <link href="/Administrator/vendors/mjolnic-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css"
        rel="stylesheet">
    <link href="/Administrator/css/selectize.css" rel="stylesheet">
    <link href="/Administrator/css/selectize.bootstrap3.css" rel="stylesheet">
    <link href="/Administrator/css/selectize.legacy.css" rel="stylesheet">
    <link href="/Administrator/vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="/Administrator/vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="/Administrator/vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
    <link href="/Administrator/vendors/chosen/prism.css" rel="stylesheet">
    <link href="/Administrator/vendors/chosen/chosen.css" rel="stylesheet">
    <link href="{{ $information ? $information->favicon : '' }}" rel="icon" type="image/x-icon">
    <link href="/Administrator/build/css/newfont.css" rel="stylesheet">
    <link href="/Administrator/build/css/custom.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container" tyle="background: red ">
            <div class="col-md-3 left_col">
                <div class="left_col scroll-view">
                    <div class="navbar nav_title" style="border: 0;">
                        <a href="{{ route('AdminMainPage') }}" class="site_title">
                            <img class="logoAdmin" id="admin-logo-bg" src="{{ $information ? $information->logo : '' }}"
                                style="height: 80%; margin: 5px auto;">
                        </a>
                    </div>
                    <div class="clearfix"></div>
                    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                        <div class="menu_section">
                            <ul class="nav side-menu">
                                <li>
                                    <a href="{{ route('AdminMainPage') }}" title="@lang('admin.menu.index')">
                                        <i class="khvich-i fas fa-home"></i>
                                        <span class="menu-text">@lang('admin.menu.index')</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('Sliders') }}" title="@lang('admin.routes.Sliders')">
                                        <i class="khvich-i fas fa-image"></i>
                                        <span class="menu-text">@lang('admin.routes.Sliders')</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('Benefits') }}" title="@lang('admin.routes.Benefits')">
                                        <i class="khvich-i fas fa-bold"></i>
                                        <span class="menu-text">@lang('admin.routes.Benefits')</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('Banners') }}" title="@lang('admin.routes.Banners')">
                                        <i class="khvich-i fas fa-border-style"></i>
                                        <span class="menu-text">@lang('admin.routes.Banners')</span>
                                    </a>
                                </li>
                                {{-- <li>
                                    <a href="{{ route('Partners') }}" title="@lang('admin.routes.Partners')">
                                        <i class="khvich-i fas fa-handshake"></i>
                                        <span class="menu-text">@lang('admin.routes.Partners')</span>
                                    </a>
                                </li> --}}
                                {{-- <li>
                                    <a href="{{ route('Actors') }}" title="@lang('admin.routes.Actors')">
                                        <i class="khvich-i fab fa-autoprefixer"></i>
                                        <span class="menu-text">@lang('admin.routes.Actors')</span>
                                    </a>
                                </li> --}}
                                <li>
                                    <a title="@lang('admin.routes.Products')">
                                        <i class="khvich-i fab fa-product-hunt"></i>
                                        <span class="menu-text">@lang('admin.routes.Products')</apan>
                                            <span class="sidemn-down-icon fas fa-chevron-down"></span>
                                    </a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ route('Products') }}"> @lang('admin.routes.Products')</a></li>
                                        <li><a href="{{ route('ProductCategories') }}"> @lang('admin.categories')</a></li>
                                        <li><a href="{{ route('Brands') }}"> @lang('admin.routes.Brands')</a></li>
                                    </ul>
                                </li>
                                {{-- <li>
                                    <a title="@lang('admin.routes.News')">
                                        <i class="khvich-i fas fa-newspaper"></i>
                                        <span class="menu-text">@lang('admin.routes.News')</apan>
                                            <span class="sidemn-down-icon fas fa-chevron-down"></span>
                                    </a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ route('News') }}"> @lang('admin.routes.News')</a></li>
                                        <li><a href="{{ route('NewsCategories') }}"> @lang('admin.categories')</a></li>
                                        <li><a href="{{ route('Tags') }}"> @lang('admin.routes.Tags')</a></li>
                                    </ul>
                                </li> --}}
                                <li>
                                    <a href="{{ route('Textpages') }}" title="@lang('admin.routes.Textpages')">
                                        <i class="khvich-i fas fa-text-width"></i>
                                        <span class="menu-text">@lang('admin.routes.Textpages')</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('Faq') }}" title="@lang('admin.routes.Faq')">
                                        <i class="khvich-i fas fa-question"></i>
                                        <span class="menu-text">@lang('admin.routes.Faq')</span>
                                    </a>
                                </li>
                                {{-- <li>
                                    <a href="{{ route('Users') }}" title="@lang('admin.routes.Users')">
                                        <i class="khvich-i fas fa-users"></i>
                                        <span class="menu-text">@lang('admin.routes.Users')</span>
                                    </a>
                                </li> --}}
                                {{-- <li>
                                    <a href="{{ route('Orders') }}" title="@lang('admin.routes.Orders')">
                                        <i class="khvich-i fas fa-shopping-cart"></i>
                                        <span class="menu-text">@lang('admin.routes.Orders')</span>
                                    </a>
                                </li> --}}
                                {{-- <li>
                                    <a href="{{ route('payment-gateaway') }}" title="@lang('admin.routes.PaymentGateaways')">
                                        <i class="khvich-i fas fa-credit-card"></i>
                                        <span class="menu-text">@lang('admin.routes.PaymentGateaways')</span>
                                    </a>
                                </li> --}}
                                @if (Session::get('admin')->role === 1)
                                    {{-- <li>
                                        <a href="{{ route('Coupons') }}" title="@lang('admin.routes.Coupons')">
                                            <i class="khvich-i fas fa-percent"></i>
                                            <span class="menu-text">@lang('admin.routes.Coupons')</span>
                                        </a>
                                    </li> --}}
                                    {{-- <li>
                                        <a href="{{ route('Districts') }}" title="@lang('admin.routes.Districts')">
                                            <i class="khvich-i fas fa-map-marker-alt"></i>
                                            <span class="menu-text">@lang('admin.routes.Districts')</span>
                                        </a>
                                    </li> --}}
                                    <li>
                                        <a href="{{ route('EditInformations') }}" title="@lang('admin.routes.Informations')">
                                            <i class="khvich-i fas fa-info-circle"></i>
                                            <span class="menu-text">@lang('admin.routes.Informations')</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('Admins') }}" title="@lang('admin.routes.Admins')">
                                            <i class="khvich-i fas fa-users"></i>
                                            <span class="menu-text">@lang('admin.routes.Admins')</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('Logs') }}" title="@lang('admin.routes.Logs')">
                                            <i class="khvich-i fas fa-clipboard-list"></i>
                                            <span class="menu-text">@lang('admin.routes.Logs')</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('EditConfigurations') }}" title="@lang('admin.routes.Configurations')">
                                            <i class="khvich-i fas fa-cogs"></i>
                                            <span class="menu-text">@lang('admin.routes.Configurations')</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="top_nav">
                <div class="nav_menu">
                    <nav>
                        <div class="nav toggle">
                            <a id="menu_toggle"><i class="khvich-i fa fa-bars"></i></a>
                        </div>
                        <ul class="nav navbar-nav navbar-right">
                            <li class="">
                                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown"
                                    aria-expanded="false">
                                    <img src="/Administrator/images/user.png" alt="">
                                    {{ Session::get('admin')->name . ' ' . Session::get('admin')->surname }}
                                    <span class=" fa fa-angle-down"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-usermenu pull-right">
                                    <li>
                                        <a href="{{ route('AdminMainPage') }}">
                                            <i class="khvich-i fas fa-home pull-right"></i> @lang('admin.menu.index')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ env('APP_URL', '#') }}" target="_blank">
                                            <i class="khvich-i fas fa-desktop pull-right"></i> @lang('admin.go_to_site')
                                        </a>
                                    </li>
                                    @if (Session::get('admin')->role === 1)
                                        <li>
                                            <a href="{{ route('EditConfigurations') }}">
                                                <i class="khvich-i fas fa-cogs pull-right"></i> @lang('admin.routes.Configurations')
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a id="LogoutAdmin">
                                            <i class="khvich-i fas fa-sign-out-alt pull-right"></i> @lang('admin.logout')
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="right_col" role="main">
                <div class="">
                    <div class="page-title">
                        <div class="title_left">
                        </div>
                        <div class="title_right">
                            <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">

                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        @yield('content')
                    </div>
                </div>
            </div>
            <footer>
                <div class="clearfix"></div>
            </footer>
        </div>
    </div>

    <!-- meta title ფანჯარა -->
    <div id="mt-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center">
                        @lang('admin.nice_day')
                        <i class="far fa-smile"></i>
                    </h4>
                </div>
                <div class="modal-body">
                    <p>
                        <img src="/Administrator/images/mt.jpg" style="width: 100%; height: 100%;">
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- meta title ფანჯარა -->

</body>

<!-- jQuery -->
<script src="/Administrator/vendors/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="/Administrator/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="/Administrator/vendors/fastclick/lib/fastclick.js"></script>
<!-- NProgress -->
<script src="/Administrator/vendors/nprogress/nprogress.js"></script>
<script src="/Administrator/vendors/iCheck/icheck.min.js"></script>
<script src="/Administrator/vendors/switchery/dist/switchery.min.js"></script>
<script src="/Administrator/vendors/select2/dist/js/select2.full.min.js"></script>
<script src="/Administrator/vendors/pnotify/dist/pnotify.js"></script>
<script src="/Administrator/vendors/pnotify/dist/pnotify.buttons.js"></script>
<script src="/Administrator/vendors/pnotify/dist/pnotify.nonblock.js"></script>
<script src="/Administrator/js/bootbox.min.js"></script>
<script src="/Administrator/js/ckeditor/ckeditor.js"></script>
<script src="/Administrator/js/standalone/selectize.js"></script>
<script src="{{ asset('Administrator/js/jquery-ui.min.js') }}"></script>
<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
<script src="/Administrator/vendors/chosen/chosen.jquery.js" type="text/javascript"></script>
<script src="/Administrator/vendors/chosen/prism.js"></script>
<script src="/Administrator/vendors/chosen/init.js"></script>

<script>
    let no = "@lang('admin.no')";
    let yes = "@lang('admin.yes')";
    let areYouSure = "@lang('admin.are_you_sure')";
    let notUploaded = "@lang('admin.not_uploaded')";

    @if (Session::has('last_edited_lang'))
        let activeLang = "{{ Session::get('last_edited_lang') }}";
    @else
        let activeLang = "{{ $configuration->admin_lang }}";
    @endif
</script>

@if (\Request::route()->getName() == 'AdminMainPage')
    <script src="/Administrator/js/charts/canvasjs.min.js"></script>
@endif

@if (Route::getCurrentRoute()->getActionMethod() == 'index')
    <script src="/Administrator/build/js/views/index.js"></script>
@endif

@if (Route::getCurrentRoute()->getActionMethod() == 'edit')
    <script src="/Administrator/build/js/views/edit.js"></script>
@endif

@if (Route::getCurrentRoute()->getActionMethod() == 'create')
    <script src="/Administrator/build/js/views/add.js"></script>
@endif

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#LogoutAdmin').click(function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('LogoutAdmin') }}",
            type: 'post',
            dataType: 'json',
            data: {}
        }).done(function(data) {
            if (data.status) {
                location.reload();
            }
        });

    });

    var CSRFToken = $('meta[name=csrf-token]').attr("content");

    var dataCkeditor = {
        filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
        filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token=' + CSRFToken,
        filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
        filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token=' + CSRFToken,
        filebrowserUploadMethod: "form"
    };

    @forelse($Localization as $key => $lang)

        if ($('#short_description_{{ $lang['prefix'] }}').length) {
            CKEDITOR.replace('short_description_{{ $lang['prefix'] }}', dataCkeditor);
        }

        if ($('#editor_{{ $lang['prefix'] }}').length) {
            CKEDITOR.replace('editor_{{ $lang['prefix'] }}', dataCkeditor);
        }
    @empty
    @endforelse

    $('.changeRadioButton').iCheck();
</script>

<script src="/Administrator/build/js/custom.js"></script>

@stack('js')

</body>

</html>
s
