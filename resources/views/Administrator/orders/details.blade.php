@extends('layouts.admin')
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_content">
            <div class="col-xs-12 col-sm-6 col-md-12">
                <div class="row">
                    <h2>@lang('admin.order_details')</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center" style="padding: 20px;"><i class="fas fa-hashtag"></i> @lang('admin.code')</th>
                                <th class="text-center" style="padding: 20px;"><i class="fas fa-user"></i> @lang('admin.user')</th>
                                <th class="text-center" style="padding: 20px;"><i class="fas fa-phone-square-alt"></i> @lang('admin.phone')</th>
                                <th class="text-center" style="padding: 20px;"><i class="fas fa-map-marker"></i> @lang('admin.address')</th>
                                <th class="text-center" style="padding: 20px;"><i class="fas fa-truck"></i> @lang('admin.delivery')</th>
                                <th class="text-center" style="padding: 20px;"><i class="fas fa-calendar-alt"></i> @lang('admin.date')</th>
                                <th class="text-center" style="padding: 20px;">
                                    @if($order->payment_type == 1)
                                        <i class="fas fa-credit-card"></i>
                                    @else
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    @endif
                                    @lang('admin.pay_type')
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center">
                                <td style="padding: 20px;">{{ $order->code }}</td>
                                <td style="padding: 20px;">{{ $user->first_name . ' ' . $user->last_name }}</td>
                                <td style="padding: 20px;">{{ $user->phone }}</td>
                                <td style="padding: 20px;">{{ $order->address }}</td>
                                <td style="padding: 20px;">
                                    @if($order->delivery == 1)
                                        <i class="fas fa-check-square" style="color: green;"></i>
                                    @else
                                        <i class="fas fa-window-close" style="color: red;"></i>
                                    @endif
                                </td>
                                <td style="padding: 20px;">{{ strftime( '%d %b, %Y, %H:%I:%S' , strtotime($order->created_at)) }}</td>
                                <td style="padding: 20px;">
                                    {{ $order->payment_type ==  1 ? trans('admin.online_pay') : trans('admin.bank_transfer') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <h2>@lang('admin.product')</h2>
                    <table class="table table-hover table-bordered">
                        <thead>
                        <th class="text-center" style="padding: 20px;">#</th>
                        <th class="text-center" style="padding: 20px;">@lang('admin.image')</th>
                        <th class="text-center" style="padding: 20px;">@lang('admin.title')</th>
                        <th class="text-center" style="padding: 20px;">@lang('admin.unit_price')</th>
                        <th class="text-center" style="padding: 20px;">@lang('admin.qty')</th>
                        <th class="text-center" style="padding: 20px;">@lang('admin.total')</th>
                        </thead>
                        @php
                            $product_total = 0;
                        @endphp
                        @foreach($order->products as $key => $product)
                            @php 
                                $product_info = \App\Models\Product::getItemInfo($product->product_id, App::getLocale());
                                $product_total += $product->price;
                            @endphp
                            <tr class="text-center">
                                <td>{{ ++$key }}</td>
                                <td>
                                    <div style="width: 40px;height: 40px; overflow: hidden; margin: 0px auto;">
                                        <img src="{{ $product_info->image }}" style="display: block;height: 100%;width: 100%;object-fit: cover;">
                                    </div>
                                </td>
                                <td>{{ $product_info->title }}</td>
                                <td>{{ $product->unit_price }} ₾</td>
                                <td>{{ $product->qty }}</td>
                                <td>{{ $product->unit_price * $product->qty }} ₾</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="7" style="border: 1px solid white; text-align: right; font-size: 20px; font-weight: bold;">
                                <h5>@lang('admin.sale') : <b>{{ $order->sale_percent }} %</b></h5>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" style="border: 1px solid white; text-align: right; font-size: 20px; font-weight: bold;">
                                <h5>@lang('admin.delivery_price') : <b>{{ $order->delivery_price }}</b> ₾</h5>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" style="border: 1px solid white; text-align: right; font-size: 20px; font-weight: bold;">
                                <h5>@lang('admin.product_price_without_sale') : <b>{{ $product_total }}</b> ₾</h5>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" style="border: 1px solid white; text-align: right; font-size: 20px; font-weight: bold;">
                                @php 
                                    $discounted_product_total = $product_total;
                                    
                                    if($order->sale_percent)
                                    {
                                        $discount = ($product_total / 100) * $order->sale_percent;
                                        $discounted_product_total = $discounted_product_total - $discount;
                                    }
                                @endphp
                                <h5>@lang('admin.product_price_with_sale') : <b>{{ $discounted_product_total }}</b> ₾</h5>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" style="border: 1px solid white; text-align: right; font-size: 20px; font-weight: bold;">
                                <h5>
                                    @lang('admin.total_price_without_sale') :
                                    <b>{{ $product_total +  $order->delivery_price }}</b> ₾
                                </h5>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" style="border: 1px solid white; text-align: right; font-size: 20px; font-weight: bold;">
                                <h5>
                                    @lang('admin.total_price_with_sale') :
                                    <b>{{ $order->total }}</b> ₾
                                </h5>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
