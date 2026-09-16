     @extends('layouts.client')
     @section('content')
         <main class="main-content">
             <div class="breadcrumb-area breadcrumb-height" data-bg-image="{{ $info->top_banner }}">
                 <div class="container h-100">
                     <div class="row h-100">
                         <div class="col-lg-12">
                             <div class="breadcrumb-item">
                                 <h2 class="breadcrumb-heading">{{ trans('Cart') }}</h2>
                                 <ul>
                                     <li>
                                         <a href="{{ route('clientHome') }}">{{ trans('Home') }}</a>
                                     </li>
                                     <li>{{ trans('Cart') }}</li>
                                 </ul>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
             <div class="cart-area section-space-y-axis-100">
                 <div class="container">
                     <div class="row">
                         <div class="col-12">
                             <form action="javascript:void(0)">
                                 <div class="table-content table-responsive">
                                     <table class="table">
                                         <thead>
                                             <tr>
                                                 <th class="product_remove">#</th>
                                                 <th class="product-thumbnail">{{ trans('Image') }}</th>
                                                 <th class="cart-product-name">{{ trans('Product') }}</th>
                                                 <th class="product-price">{{ trans('Unit Price') }}</th>
                                                 <th class="product-quantity">{{ trans('Quantity') }}</th>
                                                 <th class="product-subtotal">{{ trans('Total') }}</th>
                                             </tr>
                                         </thead>
                                         <tbody>
                                             @if ($cart->isNotEmpty())
                                                 @foreach ($cart as $carty)
                                                     <tr class="cart-items">
                                                         <td class="product_remove">
                                                             <a href="#" class="product-cart-item-remove"
                                                                 data-id="{{ $carty->id }}">
                                                                 <i class="pe-7s-close" data-tippy="{{ trans('Remove') }}"
                                                                     data-tippy-inertia="true"
                                                                     data-tippy-animation="shift-away" data-tippy-delay="50"
                                                                     data-tippy-arrow="true"
                                                                     data-tippy-theme="sharpborder"></i>
                                                             </a>
                                                         </td>
                                                         <td class="product-thumbnail">
                                                             <a
                                                                 href="{{ route('clientProductsInner', $carty->associatedModel->slug ?? $carty->id . '-' . \Illuminate\Support\Str::slug($carty->name, '-', false)) }}">
                                                                 <img src="{{ $carty->attributes->image }}"
                                                                     alt="Cart Thumbnail">
                                                             </a>
                                                         </td>
                                                         <td class="product-name"><a
                                                                 href="{{ route('clientProductsInner', $carty->associatedModel->slug ?? $carty->id . '-' . \Illuminate\Support\Str::slug($carty->name, '-', false)) }}">{{ $carty->name }}</a>
                                                         </td>
                                                         <td class="product-price"><span
                                                                 class="amount">{{ getPrice($carty->price) }} GEL</span>
                                                         </td>
                                                         <td class="quantity">
                                                             <div class="cart-plus-minus">
                                                                 <input class="cart-plus-minus-box"
                                                                     value="{{ $carty->quantity }}" type="text">
                                                                 <div class="dec qtybutton qtybutton-cart" data-id="{{ $carty->id }}">
                                                                     <i class="fa fa-minus"></i>
                                                                 </div>
                                                                 <div class="inc qtybutton qtybutton-cart" data-id="{{ $carty->id }}">
                                                                     <i class="fa fa-plus"></i>
                                                                 </div>
                                                             </div>
                                                         </td>
                                                         <td class="product-subtotal"><span
                                                                 class="amount">{{ getPrice($carty->price * $carty->quantity) }}
                                                                 GEL</span></td>
                                                     </tr>
                                                 @endforeach
                                             @endif
                                         </tbody>
                                     </table>
                                 </div>
                                 <div class="row">
                                     <div class="col-md-5 ml-auto">
                                         <div class="cart-page-total">
                                             <h2>{{ trans('Cart Total') }}</h2>
                                             <ul>
                                                 <li>{{ trans('Total') }} <span class="cart-total-amount">{{ getPrice(\Cart::getTotal()) }}
                                                         GEL</span></li>
                                             </ul>
                                             <a href="{{ route('checkout') }}">{{ trans('site.proceed_checkout') }}</a>
                                         </div>
                                     </div>
                                 </div>
                             </form>
                         </div>
                     </div>
                 </div>
             </div>
         </main>
     @endsection
