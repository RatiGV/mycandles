@extends('layouts.admin')
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2>@lang('admin.routes.PaymentGateaways')</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <strong>Saved successfully.</strong>
            </div>
            @endif
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <strong>Please review the highlighted fields.</strong>
            </div>
            @endif
            <form method="post" action="{{ route('payment-gateaway.update') }}">
                @csrf
                @foreach($banks as $bankKey => $bank)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-sm-6">
                                    <img src="{{ $bank['logo'] }}"
                                         alt="{{ $bank['title'] }} logo"
                                         class="img-responsive"
                                         style="height:34px;width:auto;display:inline-block;"
                                    >
                                </div>
                                <div class="col-sm-6 text-right">
                                    <strong>{{ $bank['title'] }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                @foreach($bank['sections'] as $sectionKey => $sectionLabel)
                                    @php
                                        $gatewayKey = $bank['gateways'][$sectionKey] ?? null;
                                        $gateway = $gatewayKey && isset($gateways[$gatewayKey]) ? $gateways[$gatewayKey] : null;
                                    @endphp
                                    <div class="col-md-6">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <strong>{{ $sectionLabel }}</strong>
                                            </div>
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label class="control-label">@lang('admin.status')</label>
                                                    <div class="iradio">
                                                        <input type="hidden" name="gateways[{{ $gatewayKey }}][status]" value="0">
                                                        <input type="checkbox"
                                                               name="gateways[{{ $gatewayKey }}][status]"
                                                               value="1"
                                                               class="js-switch"
                                                               {{ $gateway && $gateway->status ? 'checked' : '' }}
                                                        >
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Client</label>
                                                    <input type="text"
                                                           name="gateways[{{ $gatewayKey }}][client]"
                                                           class="form-control"
                                                           value="{{ $gateway ? $gateway->client : '' }}"
                                                           maxlength="255"
                                                    >
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Secret</label>
                                                    <input type="password"
                                                           name="gateways[{{ $gatewayKey }}][secret]"
                                                           class="form-control"
                                                           value=""
                                                           maxlength="255"
                                                           autocomplete="new-password"
                                                    >
                                                    <span class="help-block">@lang('admin.leave_empty_to_keep_existing_secret')</span>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Merchant Key</label>
                                                    <input type="text"
                                                           name="gateways[{{ $gatewayKey }}][merchant_key]"
                                                           class="form-control"
                                                           value="{{ $gateway ? $gateway->merchant_key : '' }}"
                                                           maxlength="255"
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">@lang('admin.save')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
