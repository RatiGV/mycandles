@php
    $export_parameters = [
        'pay_status' => Request::get('pay_status'),
        'done' => Request::get('done'),
        'code' => Request::get('code'),
        'user_id' => Request::get('user_id'),
        'total_from' => Request::get('total_from'),
        'total_to' => Request::get('total_to'),
        'pay_type' => Request::get('pay_type'),
        'from' => Request::get('from'),
        'to' => Request::get('to')
    ];    
@endphp
@extends('layouts.admin')
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
    <!-- ფილტრი -->
    @include('Administrator.orders.filter')
    <!-- /ფილტრი -->
    <div class="x_panel">
        <div class="x_title">
            <h2>
                @lang('admin.routes.Orders') ({{ $orders->total() }})
            </h2>
            <a href="{{ route('ExportOrders',$export_parameters) }}"  class="btn btn-success btn-md pull-right"> 
                <i class="far fa-file-excel"></i> @lang('admin.export')
            </a>
            @if(Request::getQueryString())
                <a href="{{ route('Orders') }}"  class="btn btn-danger btn-md pull-right"> 
                    <i class="fas fa-undo"></i> @lang('admin.cancel')
                </a>
            @endif
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <table id="example" class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('admin.payed')</th>
                        <th>@lang('admin.done')</th>
                        <th>@lang('admin.code')</th>
                        <th>@lang('admin.user')</th>
                        <th>@lang('admin.total')</th>
                        <th>@lang('admin.pay_type')</th>
                        <th>@lang('admin.date')</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $key => $order)
                    <tr>
                        <th scope="row">{{ ++$key }}</th>
                        <th>
                            <div class="iradio">
                                <input type="checkbox" 
                                       data-id="{{ $order->id }}" 
                                       {{ $order->pay_status == 1 || $order->pay_status == 4 ? 'checked' : '' }} 
                                       class="js-switch change-pay-status" 
                                       {{ $order->payment_type == 1 ? 'disabled' : '' }}
                                />
                            </div>
                        </th>
                        <td>
                            <div class="iradio">
                                <input type="checkbox" 
                                       data-id="{{ $order->id }}" 
                                       data-table="orders"
                                       data-column="done"
                                       {{ $order->done ? 'checked' : '' }} 
                                       class="js-switch change" 
                                />
                            </div>
                        </td>
                        <td>{{ $order->code }}</td>
                        <td>{{ $order->first_name . ' ' .$order->last_name }}</td>
                        <td>{{ $order->total }} ₾</td>
                        <td>{{ $order->payment_type ==  1 ? trans('admin.online_pay') : trans('admin.bank_transfer') }}</td>
                        <td>{{ strftime( '%d %b, %Y, %H:%I:%S' , strtotime($order->created_at)) }}</td>
                        <td>
                            <a class="btn btn-primary btn-sm pull-right" 
                               href="{{ route('OrderDetails', $order->id) }}"
                            > 
                                <i class="fa fa-search"></i>
                            </a>
                            <a class="btn btn-success btn-sm pull-right" 
                               href="{{ route('GeneratePdfOrders',$order->id) }}" target="_blank"
                            >
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="alert alert-warning text-center">
                                    @lang('admin.product_not_found')
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($orders->count())
                <div>
                    {!! $orders->appends(request()->query())->links() !!}       
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
@push('js')
<script>
    
    // გადახდილია თუ არა, მხოლოდ საბანკო გადარიცხვიანი შეკვეთებისათვის
    $('.change-pay-status').change(function (e) {
        var id = $(this).data('id');

        $.ajax({
            url: "{{ route('StatusOrders') }}",
            type: 'post',
            dataType: 'json',
            data: {'id': id}
        }).done(function (data) {
            if (data.status) {
                new PNotify({
                    text: '{{ trans("admin.success") }}',
                    type: 'success',
                    styling: 'bootstrap3'
                });
            } else {
                new PNotify({
                    text: '{{ trans("admin.error") }}',
                    type: 'error',
                    styling: 'bootstrap3'
                });
            }
        });
    });
    
    $(document).ready(function() {
        $('#menu_toggle').trigger('click');
    });
        
</script>
@endpush

