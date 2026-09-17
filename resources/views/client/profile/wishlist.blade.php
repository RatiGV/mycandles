@extends('layouts.client')
@section('content')
    <main class="main-content">
        <div class="breadcrumb-area breadcrumb-height" data-bg-image="{{ $info->top_banner }}">
            <div class="container h-100">
                <div class="row h-100">
                    <div class="col-lg-12">
                        <div class="breadcrumb-item">
                            <h2 class="breadcrumb-heading">{{ trans('Wishlist') }}</h2>
                            <ul>
                                <li>
                                    <a href="{{ route('clientHome') }}">{{ trans('Home') }}</a>
                                </li>
                                <li>{{ trans('Wishlist') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="wishlist-area section-space-y-axis-100">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <form action="javascript:void(0)">
                            <div class="table-content table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="product-thumbnail">{{ trans('Image') }}</th>
                                            <th class="cart-product-name">{{ trans('Product') }}</th>
                                            <th class="product-price">{{ trans('Unit Price') }}</th>
                                            @if (false)
                                                <th class="cart_btn">{{ trans('Add to cart') }}</th>
                                            @endif
                                            <th class="product_remove">#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($wishlists as $wishlist)
                                        <tr class="wishlist-items">
                                            <td class="product-thumbnail">
                                                <a href="{{ route('clientProductsInner', $wishlist->slug ?? $wishlist->id . '-' . \Illuminate\Support\Str::slug($wishlist->trans->title, '-', false)) }}">
                                                    <img src="{{ $wishlist->image }}"
                                                        alt="Wishlist Thumbnail">
                                                </a>
                                            </td>
                                            <td class="product-name"><a href="{{ route('clientProductsInner', $wishlist->slug ?? $wishlist->id . '-' . \Illuminate\Support\Str::slug($wishlist->trans->title, '-', false)) }}">{{ $wishlist->trans->title }}</a></td>
                                            <td class="product-price"><span class="amount">{{ getPrice($wishlist->price) }} GEL</span></td>
                                            @if (false)
                                                <td class="cart_btn"><a href="#" class="add-to-cart-products" data-id="{{ $wishlist->id }}">{{ trans('Add to cart') }}</a></td>
                                            @endif
                                            <td class="product_remove">
                                                <a href="#" class="remove-from-wishlist" data-id="{{ $wishlist->id }}">
                                                    <i class="pe-7s-close" data-tippy="{{ trans('Remove') }}" data-tippy-inertia="true"
                                                        data-tippy-animation="shift-away" data-tippy-delay="50"
                                                        data-tippy-arrow="true" data-tippy-theme="sharpborder"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
