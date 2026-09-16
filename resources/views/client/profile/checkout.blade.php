@extends('layouts.client')

@push('css')
<style>
    .payment-method-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 12px;
    }

    .payment-method-tile {
        border: 1px solid #e6e6e6;
        border-radius: 8px;
        padding: 14px;
        cursor: pointer;
        transition: border-color .2s ease, box-shadow .2s ease;
        background: #fff;
    }

    .payment-method-tile:hover {
        border-color: #0d6efd;
        box-shadow: 0 3px 10px rgba(13, 110, 253, 0.12);
    }

    .payment-method-tile input[type="radio"] {
        margin-right: 10px;
    }

    .payment-method-tile.active {
        border-color: #0d6efd;
        box-shadow: 0 3px 10px rgba(13, 110, 253, 0.12);
    }

    .payment-method-logo {
        max-height: 24px;
        width: auto;
    }

    .payment-method-title {
        font-weight: 600;
        margin: 8px 0 4px;
    }

    .payment-method-description {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
    }
</style>
@endpush

@section('content')
    @php
        $cartTotal = (float) \Cart::getTotal();
        $cartIsEmpty = $cart->isEmpty();
    @endphp

    <main class="main-content">
        <div class="breadcrumb-area breadcrumb-height" data-bg-image="{{ $info->top_banner }}">
            <div class="container h-100">
                <div class="row h-100">
                    <div class="col-lg-12">
                        <div class="breadcrumb-item">
                            <h2 class="breadcrumb-heading">{{ trans('Checkout') }}</h2>
                            <ul>
                                <li>
                                    <a href="{{ route('clientHome') }}">{{ trans('Home') }}</a>
                                </li>
                                <li>{{ trans('Checkout') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="checkout-area section-space-y-axis-100">
            <div class="container">
                @if ($cartIsEmpty)
                    <div class="alert alert-warning">
                        {{ trans('Your cart is empty. Add products before proceeding to checkout.') }}
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-6 col-12">
                        <form id="checkout-payment-form" action="javascript:void(0)">
                            @csrf
                            <div class="checkbox-form">
                                <h3>{{ trans('Checkout') }}</h3>

                                <div id="checkout-payment-alert" class="alert d-none"></div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="country-select clearfix">
                                            <label>{{ trans('Delivery') }} <span class="required">*</span></label>
                                            <select class="myniceselect nice-select wide" id="pickup" name="delivery">
                                                <option value="1">{{ trans('Courier service') }}</option>
                                                <option value="2">{{ trans('I will pick it up from the office') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="checkout-details">
                                    <div class="col-md-12 address-fields">
                                        <div class="country-select clearfix">
                                            <label>{{ trans('City') }} <span class="required">*</span></label>
                                            <select class="myniceselect nice-select wide" id="district_id" name="district_id">
                                                @foreach ($cities as $city)
                                                    <option value="{{ $city->id }}">{{ $city->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12 address-fields">
                                        <div class="checkout-form-list">
                                            <label>{{ trans('Address') }} <span class="required">*</span></label>
                                            <input id="address" name="address" placeholder="Street address" type="text">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="checkout-form-list">
                                            <label>{{ trans('Phone') }} <span class="required">*</span></label>
                                            <input id="phone" name="phone" type="text" value="{{ auth()->user()->phone }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-6 col-12">
                        <div class="your-order">
                            <h3>{{ trans('Your order') }}</h3>
                            <div class="your-order-table table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="cart-product-name">{{ trans('Product') }}</th>
                                            <th class="cart-product-total">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cart as $cartItem)
                                            <tr class="cart_item">
                                                <td class="cart-product-name">
                                                    {{ $cartItem->name }}
                                                    <strong class="product-quantity">× {{ (int) $cartItem->quantity }}</strong>
                                                </td>
                                                <td class="cart-product-total">
                                                    <span class="amount">{{ getPrice($cartItem->price * $cartItem->quantity) }} GEL</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="order-total">
                                            <th>{{ trans('Order Total) }}</th>
                                            <td><strong><span class="amount">{{ getPrice($cartTotal) }} GEL</span></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="payment-method mt-4">
                                <h5 class="mb-3">{{ trans('Payment Method') }}</h5>

                                @if (count($paymentMethods))
                                    <div class="payment-method-grid" id="payment-method-grid">
                                        @foreach ($paymentMethods as $method)
                                            <label class="payment-method-tile {{ $loop->first ? 'active' : '' }}">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <input
                                                            type="radio"
                                                            name="payment_method"
                                                            value="{{ $method['key'] }}"
                                                            data-provider="{{ $method['provider'] }}"
                                                            data-client-id="{{ $method['client_id'] ?? '' }}"
                                                            form="checkout-payment-form"
                                                            {{ $loop->first ? 'checked' : '' }}
                                                        >
                                                    </div>
                                                    <img src="{{ asset(ltrim($method['logo'], '/')) }}" alt="{{ $method['title'] }}" class="payment-method-logo">
                                                </div>
                                                <div class="payment-method-title">{{ $method['title'] }}</div>
                                                <p class="payment-method-description">{{ $method['description'] }}</p>
                                            </label>
                                        @endforeach
                                    </div>

                                    <div class="order-button-payment mt-4">
                                        <button
                                            id="proceed-to-payment-btn"
                                            class="btn btn-custom-size lg-size btn-pronia-primary"
                                            type="submit"
                                            form="checkout-payment-form"
                                            {{ $cartIsEmpty ? 'disabled' : '' }}
                                        >
                                            <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true" id="payment-btn-spinner"></span>
                                            Proceed to Payment
                                        </button>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        {{ trans('Payment methods are temporarily unavailable. Please contact support.') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('js')
@php
    $bogInstallmentMethod = collect($paymentMethods)->firstWhere('key', \App\Models\PaymentGateaway::NAME_BOG_INSTALLMENT);
    $bogInstallmentClientId = $bogInstallmentMethod['client_id'] ?? null;
@endphp
@if ($bogInstallmentClientId)
<script src="https://webstatic.bog.ge/bog-sdk/bog-sdk.js?client_id={{ urlencode((string) $bogInstallmentClientId) }}"></script>
@endif
<script>
    (function () {
        var form = document.getElementById('checkout-payment-form');
        if (!form) {
            return;
        }

        var alertBox = document.getElementById('checkout-payment-alert');
        var submitButton = document.getElementById('proceed-to-payment-btn');
        var spinner = document.getElementById('payment-btn-spinner');
        var methodGrid = document.getElementById('payment-method-grid');
        var initiateUrl = "{{ route('checkout.payment.initiate') }}";
        var cartTotal = {{ json_encode((float) $cartTotal) }};
        var bogInstallmentMethodKey = "{{ \App\Models\PaymentGateaway::NAME_BOG_INSTALLMENT }}";

        function setLoading(loading) {
            if (!submitButton || !spinner) {
                return;
            }

            submitButton.disabled = loading;
            spinner.classList.toggle('d-none', !loading);
        }

        function showAlert(message, type) {
            if (!alertBox) {
                return;
            }

            alertBox.className = 'alert alert-' + type;
            alertBox.textContent = message;
            alertBox.classList.remove('d-none');
        }

        function selectedPaymentInput() {
            return document.querySelector('input[name="payment_method"]:checked');
        }

        function submitCheckoutPayment(formData, options) {
            var requireRedirectUrl = !options || options.requireRedirectUrl !== false;

            return fetch(initiateUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(function (response) {
                    return response.json().then(function (data) {
                        return { ok: response.ok, status: response.status, data: data };
                    });
                })
                .then(function (result) {
                    if (!result.ok) {
                        var message = result.data && result.data.message ? result.data.message : 'Payment request failed.';

                        if (result.data && result.data.errors) {
                            var firstKey = Object.keys(result.data.errors)[0];
                            if (firstKey && result.data.errors[firstKey] && result.data.errors[firstKey][0]) {
                                message = result.data.errors[firstKey][0];
                            }
                        }

                        throw new Error(message);
                    }

                    if (requireRedirectUrl && !result.data.redirect_url) {
                        throw new Error('Payment redirect URL was not returned.');
                    }

                    return result.data;
                });
        }

        function openBogInstallmentCalculator(baseFormData) {
            var fallbackRedirectUrl = null;

            if (!window.BOG || !window.BOG.Calculator) {
                setLoading(false);
                showAlert('BOG installment SDK is unavailable.', 'danger');
                return;
            }

            window.BOG.Calculator.open({
                amount: Number(cartTotal.toFixed(2)),
                bnpl: false,
                onClose: function () {
                    setLoading(false);
                },
                onRequest: function (selected, successCb, closeCb) {
                    var requestData = new FormData();
                    var selectedPayload = selected || {};
                    var selectedMonth = Number(selectedPayload.month || 0);

                    baseFormData.forEach(function (value, key) {
                        requestData.append(key, value);
                    });

                    requestData.set('installment_selected', JSON.stringify(selectedPayload));
                    requestData.set('installment_month', selectedMonth > 0 ? String(selectedMonth) : '');
                    requestData.set('installment_loan_type', selectedMonth > 0 && selectedMonth < 6 ? 'ZERO' : 'STANDARD');
                    requestData.set('bog_type', 'ganvadeba');

                    submitCheckoutPayment(requestData, { requireRedirectUrl: false })
                        .then(function (data) {
                            fallbackRedirectUrl = data.redirect_url || null;
                            var gatewayOrderId = data && data.gateway_order_id ? String(data.gateway_order_id) : '';

                            if (!gatewayOrderId) {
                                throw new Error('Gateway order id was not returned.');
                            }

                            successCb(gatewayOrderId);
                        })
                        .catch(function (error) {
                            setLoading(false);
                            showAlert(error.message || 'Unable to start installment payment.', 'danger');
                            closeCb();
                        });
                },
                onComplete: function (payload) {
                    var redirectUrl = payload && payload.redirectUrl ? payload.redirectUrl : fallbackRedirectUrl;

                    if (redirectUrl) {
                        window.location.href = redirectUrl;
                        return false;
                    }

                    setLoading(false);
                    showAlert('Installment payment redirect URL was not returned.', 'danger');
                    return false;
                }
            });
        }

        if (methodGrid) {
            methodGrid.addEventListener('change', function (event) {
                if (!event.target || event.target.name !== 'payment_method') {
                    return;
                }

                methodGrid.querySelectorAll('.payment-method-tile').forEach(function (tile) {
                    tile.classList.remove('active');
                });

                var selectedTile = event.target.closest('.payment-method-tile');
                if (selectedTile) {
                    selectedTile.classList.add('active');
                }
            });
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            if (!submitButton || submitButton.disabled) {
                return;
            }

            alertBox.classList.add('d-none');

            var formData = new FormData(form);

            var selectedInput = selectedPaymentInput();
            if (!selectedInput || !formData.get('payment_method')) {
                showAlert('Please select a payment method.', 'danger');
                return;
            }

            setLoading(true);

            if (selectedInput.value === bogInstallmentMethodKey) {
                var installmentClientId = selectedInput.getAttribute('data-client-id') || '';
                if (!installmentClientId) {
                    setLoading(false);
                    showAlert('BOG installment client id is not configured.', 'danger');
                    return;
                }

                openBogInstallmentCalculator(formData);
                return;
            }

            submitCheckoutPayment(formData)
                .then(function (data) {
                    window.location.href = data.redirect_url;
                })
                .catch(function (error) {
                    showAlert(error.message || 'Unexpected error while starting payment.', 'danger');
                })
                .finally(function () {
                    setLoading(false);
                });
        });
    })();
</script>
@endpush
