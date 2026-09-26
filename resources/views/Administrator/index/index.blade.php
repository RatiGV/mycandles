@extends('layouts.admin')
@section('content')
<div class="x_panel">
    <div class="x_title">
        <h2>@lang('admin.modules')</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
                <a class="collapse-link">
                    <i class="fa fa-chevron-up"></i>
                </a>
            </li>
            <li>
                <a class="close-link">
                    <i class="fas fa-times"></i>
                </a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <div class="col-md-3">
            <a href="{{ route('Sliders') }}">
                <div class="card-counter success">
                    <i class="fa fa-image"></i>
                    <span class="count-numbers">{{ DB::table('sliders')->count() }}</span>
                    <span class="count-name">@lang('admin.routes.Sliders')</span>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('ProductsIndex') }}">
                <div class="card-counter danger">
                    <i class="fab fa-product-hunt"></i>
                    <span class="count-numbers">{{ DB::table('products')->count() }}</span>
                    <span class="count-name">@lang('admin.routes.Products')</span>
                </div>
            </a>
        </div>
        {{-- <div class="col-md-3">
            <a href="{{ route('Newses') }}">
                <div class="card-counter info">
                    <i class="fas fa-newspaper"></i>
                    <span class="count-numbers">{{ DB::table('news')->count() }}</span>
                    <span class="count-name">@lang('admin.routes.News')</span>
                </div>
            </a>
        </div> --}}
        <div class="col-md-3">
            <a href="{{ route('EditTextpages',1) }}">
                <div class="card-counter warning">
                    <i class="fas fa-barcode"></i>
                    <span class="count-numbers"></span>
                    <span class="count-name">@lang('admin.menu.about')</span>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('EditTextpages',2) }}">
                <div class="card-counter primary">
                    <i class="fas fa-clipboard-list"></i>
                    <span class="count-numbers"></span>
                    <span class="count-name">@lang('admin.menu.terms')</span>
                </div>
            </a>
        </div>
        {{-- <div class="col-md-3">
            <a href="{{ route('Users') }}">
                <div class="card-counter other">
                    <i class="fa fa-users"></i>
                    <span class="count-numbers">{{ DB::table('users')->count() }}</span>
                    <span class="count-name">@lang('admin.routes.Users')</span>
                </div>
            </a>
        </div> --}}
        {{-- <div class="col-md-3">
            <a href="{{ route('Orders') }}">
                <div class="card-counter danger">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="count-numbers">{{ DB::table('orders')->count() }}</span>
                    <span class="count-name">@lang('admin.routes.Orders')</span>
                </div>
            </a>
        </div> --}}
    </div>
</div>
@if(Session::get('admin')->role === 1)
<div class="x_panel">
    <div class="x_title">
        <h2>@lang('admin.info_and_configuration')</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
                <a class="collapse-link">
                    <i class="fa fa-chevron-up"></i>
                </a>
            </li>
            <li>
                <a class="close-link">
                    <i class="fas fa-times"></i>
                </a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        {{-- <div class="col-md-3">
            <a href="{{ route('Coupons') }}">
                <div class="card-counter other">
                    <i class="fas fa-percent"></i>
                    <span class="count-numbers">{{ DB::table('coupons')->count() }}</span>
                    <span class="count-name">@lang('admin.routes.Coupons')</span>
                </div>
            </a>
        </div> --}}
        {{-- <div class="col-md-3">
            <a href="{{ route('Districts') }}">
                <div class="card-counter warning">
                    <i class="fas fa-map-marker-alt"></i>
                    <span class="count-numbers">{{ DB::table('districts')->count() }}</span>
                    <span class="count-name">@lang('admin.routes.Districts')</span>
                </div>
            </a>
        </div> --}}
        <div class="col-md-3">
            <a href="{{ route('EditInformations') }}">
                <div class="card-counter success">
                    <i class="fas fa-info-circle"></i>
                    <span class="count-numbers"></span>
                    <span class="count-name">@lang('admin.routes.Informations')</span>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('Admins') }}">
                <div class="card-counter iasamani">
                    <i class="fas fa-users-cog"></i>
                    <span class="count-numbers">{{ DB::table('admins')->count() }}</span>
                    <span class="count-name">@lang('admin.routes.Admins')</span>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('Logs') }}">
                <div class="card-counter info">
                    <i class="fas fa-clipboard-list"></i>
                    <span class="count-numbers"></span>
                    <span class="count-name">@lang('admin.routes.Logs')</span>
                </div>
            </a>
        </div>
    </div>
</div>
@if (false)
<!-- ჯამური თანხები დღეების მიხედვით -->
<div class="x_panel" id="totals-by-days-panel">
    <div class="x_title">
        <h2>@lang('admin.charts.totals_by_days')</h2>
        <ul class="nav navbar-right panel_toolbox">
            <form action="" id="totals-by-days-form">
                @php
                    $year_month_val = Request::has('year_month') ? Request::get('year_month') : date('Y') . '-' . date('m');
                @endphp
                <input type="month"
                       name="year_month"
                       id="totals-by-days-input"
                       class="form-control"
                       value="{{ $year_month_val }}"
                >
            </form>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <div class="col-md-12 col-sm-912">
            <div id="totals-by-days-chart" style="height: 370px; width: 100%;"></div>
        </div>
    </div>
</div>
<!-- ჯამური თანხები /დღეების მიხედვით -->

<!-- ჯამური თანხები თვეების მიხედვით -->
<div class="x_panel" id="totals-by-months-panel">
    <div class="x_title">
        <h2>@lang('admin.charts.totals_by_months')</h2>
        <ul class="nav navbar-right panel_toolbox">
            <form action="" id="totals-by-months-form">
                @php
                    $year_val = Request::has('year') ? Request::get('year') : date('Y');
                @endphp
                <select name="year" class="form-control" id="totals-by-months-select">
                    <option value="">@lang('admin.year')</option>
                    @for($y=2010; $y<2030; $y++)
                        <option value="{{ $y }}" {{ $y == $year_val ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </form>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <div class="col-md-12 col-sm-912">
            <div id="totals-by-months-chart" style="height: 370px; width: 100%;"></div>
        </div>
    </div>
</div>
<!-- /ჯამური თანხები თვეების მიხედვით -->

<!-- ყველაზე გაყიდვადი 10 პროდუქტი -->
<div class="x_panel" id="top-prods-chart-panel">
    <div class="x_title">
        <h2>@lang('admin.charts.top_prods_by_date')</h2>
        <ul class="nav navbar-right panel_toolbox">
            <!--
            <form action="" id="top-prods-form" class="form-inline">
                @php
                    $from_val = Request::has('from') ? Request::get('from') : date('Y-m-d');
                    $to_val = Request::has('to') ? Request::get('to') : date('Y-m-d');
                @endphp
                <label>@lang('admin.from') : </label>
                <input type="date"
                       name="from"
                       id="top-prods-from"
                       class="form-control"
                       value="{{ $from_val }}"
                >
                <label>@lang('admin.to') : </label>
                <input type="date"
                       name="to"
                       id="top-prods-to"
                       class="form-control"
                       value="{{ $to_val }}"
                >
            </form>
            -->
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <div class="col-md-12 col-sm-912">
            <div id="top-prods-chart" style="height: 370px; width: 100%;"></div>
        </div>
    </div>
</div>
<!-- /ყველაზე გაყიდვადი 10 პროდუქტი -->

<!-- ყველაზე გაყიდვადი კატეგორიები და უბნები -->
<div class="row">
    <div class="col-md-6">
        <div class="x_panel" id="top-prods-chart-panel">
            <div class="x_title">
                <h2 style="font-size: 14px;">
                    @lang('admin.charts.count_percents_by_districts')
                </h2>
                <a href="javascript:void(0)" class="btn btn-primary btn-md pull-right">
                    @lang('admin.in_total')
                    {{ $sup_total_count }}
                    @lang('admin.order')
                </a>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="col-md-12 col-sm-12">
                    <div id="counts-by-districts-chart" style="height: 370px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="x_panel" id="top-prods-chart-panel">
            <div class="x_title">
                <h2 style="font-size: 14px;">
                    @lang('admin.charts.amount_percents_by_districts')
                </h2>
                <a href="javascript:void(0)" class="btn btn-primary btn-md pull-right">
                    @lang('admin.in_total')
                    {{ $sup_total_amount }} ₾
                </a>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="col-md-12 col-sm-12">
                    <div id="amounts-by-districts-chart" style="height: 370px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /ყველაზე გაყიდვადი კატეგორიები და უბნები -->
@endif
@endif
@endsection
@push('js')
<script>

    @if(false)
    window.onload = function () {

        /*********************** ჯამური თანხები დღეების მიხედვით ********************/
        $('#totals-by-days-input').on('change', function(){
            $('#totals-by-days-form').submit();
        });

        @if(Request::has('year_month'))
            $('html, body').animate({
                scrollTop: $('#totals-by-days-panel').offset().top
            }, 2000);
        @endif

        var salesByYearMonth = @json($data1);

        var chart1 = new CanvasJS.Chart("totals-by-days-chart", {
            animationEnabled: true,
            title: {
                text: "@lang('admin.charts.totals_by_days')"
            },
            axisY: {
                suffix: " ₾",
                titleFontSize: 24,
                includeZero: true
            },
            data: [{
                type: "column",
                indexLabelFontSize: 16,
                //xValueFormatString: "MMM D",
                yValueFormatString: "###.# ₾",
                dataPoints: salesByYearMonth
            }]
        });

        chart1.render();
        /*********************** /ჯამური თანხები დღეების მიხედვით ********************/


        /*********************** ჯამური თანხები თვეების მიხედვით ********************/
        $('#totals-by-months-select').on('change', function(){

            if($(this).val())
            {
                $('#totals-by-months-form').submit();
            }
        });

        @if(Request::has('year'))
            $('html, body').animate({
                scrollTop: $('#totals-by-months-panel').offset().top
            }, 2000);
        @endif

        var salesByYear = @json($data2);

        var chart2 = new CanvasJS.Chart("totals-by-months-chart", {
            animationEnabled: true,
            title: {
                text: "@lang('admin.charts.totals_by_months')"
            },
            axisY: {
                suffix: " ₾",
                titleFontSize: 24,
                includeZero: true
            },
            data: [{
                type: "line",
                indexLabelFontSize: 16,
                //xValueFormatString: "MMM D",
                yValueFormatString: "###.# ₾",
                dataPoints: salesByYear
            }]
        });

        chart2.render();
        /*********************** /ჯამური თანხები თვეების მიხედვით ********************/


        /*********************** ტოპ გაყიდვადი პროდუქტი დროის შუალედში ********************/

        var chart3 = new CanvasJS.Chart("top-prods-chart", {

            theme: "light1", // "light1", "light2", "dark1"
            animationEnabled: true,
            exportEnabled: true,
            title: {
                text: "@lang('admin.charts.top_prods_by_date')"
            },
            axisX: {
                margin: 10,
                labelPlacement: "inside",
                tickPlacement: "inside"
            },
            axisY2: {
                title: "(@lang('admin.charts.unit_data'))",
                titleFontSize: 14,
                includeZero: true,
                suffix: "@lang('admin.charts.unit')"
            },
            data: [{
                type: "bar",
                axisYType: "secondary",
                yValueFormatString: "#,###.##@lang('admin.charts.unit')",
                indexLabel: "{y}",
                dataPoints: @json($data3).reverse()
            }]
        });

        chart3.render();
        /*********************** /ტოპ გაყიდვადი პროდუქტი დროის შუალედში ********************/


        /*********************** შეკვეთების რაოდენობა უბნების მიხედვით ********************/
        var chart4 = new CanvasJS.Chart("counts-by-districts-chart", {
            theme: "light2",
            animationEnabled: true,
            title: {
                //text: "@lang('admin.charts.percents_by_districts')"
            },
            data: [{
                type: "pie",
                indexLabel: "{y}",
                yValueFormatString: "#,##0.00\"%\"",
                indexLabelPlacement: "inside",
                indexLabelFontColor: "#36454F",
                indexLabelFontSize: 18,
                indexLabelFontWeight: "bolder",
                showInLegend: true,
                legendText: "{label}",
                dataPoints: @json($data4, JSON_NUMERIC_CHECK)
            }]
        });

        chart4.render();
        /*********************** /შეკვეთების რაოდენობა უბნების მიხედვით ********************/


        /*********************** შეკვეთების რაოდენობა კატეგორიების მიხედვით ********************/
        var chart4 = new CanvasJS.Chart("amounts-by-districts-chart", {
            theme: "light2",
            animationEnabled: true,
            title: {
                //text: "@lang('admin.charts.percents_by_districts')"
            },
            data: [{
                type: "pie",
                indexLabel: "{y}",
                yValueFormatString: "#,##0.00\"%\"",
                indexLabelPlacement: "inside",
                indexLabelFontColor: "#36454F",
                indexLabelFontSize: 18,
                indexLabelFontWeight: "bolder",
                showInLegend: true,
                legendText: "{label}",
                dataPoints: @json($data5, JSON_NUMERIC_CHECK)
            }]
        });

        chart4.render();
        /*********************** შეკვეთების რაოდენობა /კატეგორიების მიხედვით ********************/


    };
    @endif


    $(document).ready(function() {
        $('#menu_toggle').trigger('click');
    });

</script>
@endpush
