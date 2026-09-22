     @extends('layouts.client')
     @section('content')
         <main class="main-content">
             <div class="breadcrumb-area breadcrumb-height" data-bg-image="{{ $info->top_banner }}">
                 <div class="container h-100">
                     <div class="row h-100">
                         <div class="col-lg-12">
                             <div class="breadcrumb-item">
                                 <h2 class="breadcrumb-heading">{{ trans('Shop') }}</h2>
                                 <ul>
                                     <li>
                                         <a href="{{ route('clientHome') }}">{{ trans('Home') }}</a>
                                     </li>
                                     <li>{{ trans('Shop') }}</li>
                                 </ul>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
             <div class="shop-area section-space-y-axis-100">
                 <div class="container">
                     <div class="row">
                         <div class="col-xl-3 col-lg-4 order-2 order-lg-1 pt-5 pt-lg-0">
                             <div class="sidebar-area">
                                 <div class="widgets-searchbox">
                                     <form id="widgets-searchbox" action="{{ route('clientProducts') }}">
                                         <input class="input-field" type="text" name="search"
                                             value="{{ request()->get('search') }}" placeholder="{{ trans('Search') }}">
                                         <div class="widgets-item widgets-filter">
                                             <h2 class="widgets-title mb-4">{{ trans('Price') }}</h2>
                                             @php
                                                 $range = request()->get('priceRange');

                                                 $defaultMin = \App\Models\Product::min('price');
                                                 $defaultMax = \App\Models\Product::max('price');

                                                 $from = $defaultMin;
                                                 $to = $defaultMax;

                                                 if ($range) {
                                                     [$from, $to] = explode(';', $range);
                                                 }
                                             @endphp
                                             <div class="price-filter">
                                                 <input type="text" class="pronia-range-slider" name="priceRange"
                                                     value="{{ $from }};{{ $to }}" data-type="double"
                                                     data-min="{{ $defaultMin }}" data-from="{{ $from }}"
                                                     data-to="{{ $to }}" data-max="{{ $defaultMax }}"
                                                     data-grid="false" />

                                                 <input type="hidden" name="category" value="{{ request('category') }}">
                                             </div>
                                         </div>
                                         <div class="d-grid mt-4">
                                             <button class="btn lg-size btn-pronia-primary" type="submit">
                                                 <i class="fa fa-search me-2"></i> {{ trans('Filter') }}
                                             </button>
                                         </div>
                                     </form>
                                 </div>
                                 <div class="widgets-area">
                                     <div class="widgets-item pt-0">
                                         <h2 class="widgets-title mb-4">{{ trans('Categories') }}</h2>
                                         <ul class="widgets-category">
                                             <li>
                                                 <a href="{{ route('clientProducts') }}" class="{{ request('category') ? '' : 'active' }}">
                                                     <i class="fa fa-chevron-right"></i>
                                                     {{ trans('All') }} <span>({{ \App\Models\Product::count() }})</span>
                                                 </a>
                                             </li>
                                             @forelse($categories as $countCategories)
                                                 <li>
                                                     <a
                                                         href="{{ route('clientProducts') }}?category={{ $countCategories->trans->title }}&search={{ request()->get('search') }}&sort={{ request()->get('sort') }}&priceRange={{ request()->get('priceRange') }}"
                                                         class="{{ request('category') === $countCategories->trans->title ? 'active' : '' }}">
                                                         <i class="fa fa-chevron-right"></i>
                                                         {{ $countCategories->trans->title }}
                                                         <span>({{ $countCategories->products_count }})</span>
                                                     </a>
                                                 </li>
                                             @empty
                                             @endforelse
                                         </ul>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="col-xl-9 col-lg-8 order-1 order-lg-2">
                             <div class="product-topbar">
                                 <ul>
                                     <li class="product-view-wrap">
                                         <ul class="nav" role="tablist">
                                             <li class="grid-view" role="presentation">
                                                 <a class="active" id="grid-view-tab" data-bs-toggle="tab" href="#grid-view"
                                                     role="tab" aria-selected="true">
                                                     <i class="fa fa-th"></i>
                                                 </a>
                                             </li>
                                             <li class="list-view" role="presentation">
                                                 <a id="list-view-tab" data-bs-toggle="tab" href="#list-view" role="tab"
                                                     aria-selected="true">
                                                     <i class="fa fa-th-list"></i>
                                                 </a>
                                             </li>
                                         </ul>
                                     </li>
                                     <li class="short">
                                         <form action="{{ route('clientProducts') }}" id="sortForm">
                                             <input type="hidden" name="search" value="{{ request('search') }}">
                                             <input type="hidden" name="priceRange" value="{{ request('priceRange') }}">
                                             <input type="hidden" name="category" value="{{ request('category') }}">
                                            <input type="hidden" name="page" value="{{ request('page') }}">
                                            
                                             <select class="nice-select" name="sort"
                                                 onchange="document.getElementById('sortForm').submit()">
                                                 <option value="">{{ trans('Sort by') }}</option>
                                                 <option value="price_desc"
                                                     {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                                                     {{ trans('Sort by High Price') }}
                                                 </option>
                                                 <option value="price_asc"
                                                     {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                                                     {{ trans('Sort by Low Price') }}
                                                 </option>
                                             </select>
                                         </form>
                                     </li>

                                 </ul>
                             </div>
                             <div class="tab-content">
                                 <div class="tab-pane fade show active" id="grid-view" role="tabpanel"
                                     aria-labelledby="grid-view-tab">
                                     <div class="product-grid-view row g-y-20">
                                         @forelse($products as $product)
                                             <div class="col-md-4 col-sm-6">
                                                 <div class="product-item">
                                                     <div class="product-img">
                                                         <a
                                                             href="{{ route('clientProductsInner', $product->slug ?? $product->id . '-' . \Illuminate\Support\Str::slug($product->trans->title, '-', false)) }}">
                                                             <img class="primary-img" src="{{ $product->image }}"
                                                                 alt="{{ $product->trans->alt }}" loading="lazy" decoding="async">
                                                         </a>
                                                         <div class="product-add-action">
                                                             <ul>
                                                                 <li>
                                                                     <a href="#" class="add-to-wishlist-product @if((new \App\Services\WishlistService())->has($product->id)) in-wishlist @endif"
                                                                         data-tippy="{{ trans('Add to wishlist') }}"
                                                                         data-tippy-inertia="true"
                                                                         data-id="{{ $product->id }}"
                                                                         data-tippy-animation="shift-away"
                                                                         data-tippy-delay="50" data-tippy-arrow="true"
                                                                         data-tippy-theme="sharpborder">
                                                                         <i class="pe-7s-like"></i>
                                                                     </a>
                                                                 </li>
                                                                 @if (false)
                                                                 <li>
                                                                     <a href="#" class="add-to-cart-products"
                                                                         data-tippy="{{ trans('Add to cart') }}"
                                                                         data-tippy-inertia="true"
                                                                         data-tippy-animation="shift-away"
                                                                         data-tippy-delay="50" data-tippy-arrow="true"
                                                                         data-tippy-theme="sharpborder"
                                                                         data-id="{{ $product->id }}">
                                                                         <i class="pe-7s-cart"></i>
                                                                     </a>
                                                                 </li>
                                                                 @endif
                                                             </ul>
                                                         </div>
                                                     </div>
                                                     <div class="product-content">
                                                         <a class="product-name"
                                                             href="{{ route('clientProductsInner', $product->slug ?? $product->id . '-' . \Illuminate\Support\Str::slug($product->trans->title, '-', false)) }}">{{ $product->trans->title }}</a>
                                                         @if ($product->price > 0)
                                                             <div class="price-box pb-1">
                                                                 <span class="new-price">
                                                                     {{ getPrice($product->price) }} GEL</span>
                                                             </div>
                                                         @endif
                                                     </div>
                                                 </div>
                                             </div>
                                         @empty
                                         @endforelse
                                     </div>
                                 </div>
                                 <div class="tab-pane fade" id="list-view" role="tabpanel"
                                     aria-labelledby="list-view-tab">
                                     <div class="product-list-view row g-y-30">
                                         @forelse($products as $product)
                                             <div class="col-12">
                                                 <div class="product-item">
                                                     <div class="product-img">
                                                         <a
                                                             href="{{ route('clientProductsInner', $product->slug ?? $product->id . '-' . \Illuminate\Support\Str::slug($product->trans->title, '-', false)) }}">
                                                             <img class="primary-img" src="{{ $product->image }}"
                                                                 alt="{{ $product->trans->alt }}" loading="lazy" decoding="async">
                                                         </a>
                                                     </div>
                                                     <div class="product-content">
                                                         <a class="product-name"
                                                             href="{{ route('clientProductsInner', $product->slug ?? $product->id . '-' . \Illuminate\Support\Str::slug($product->trans->title, '-', false)) }}">{{ $product->trans->title }}</a>
                                                         @if ($product->price > 0)
                                                             <div class="price-box pb-1">
                                                                 <span class="new-price">{{ getPrice($product->price) }}
                                                                     GEL</span>

                                                             </div>
                                                         @endif
                                                         <p class="short-desc mb-0">
                                                             {{ $product->trans->short_description }}
                                                         </p>
                                                         <div class="product-add-action">
                                                             <ul>
                                                                 <li>
                                                                     <a href="#" class="add-to-wishlist-product @if((new \App\Services\WishlistService())->has($product->id)) in-wishlist @endif"
                                                                         data-id="{{ $product->id }}"
                                                                         data-tippy="{{ trans('Add to wishlist') }}"
                                                                         data-tippy-inertia="true"
                                                                         data-tippy-animation="shift-away"
                                                                         data-tippy-delay="50" data-tippy-arrow="true"
                                                                         data-tippy-theme="sharpborder">
                                                                         <i class="pe-7s-like"></i>
                                                                     </a>
                                                                 </li>
                                                                 @if (false)
                                                                 <li>
                                                                     <a href="#" class="add-to-cart-products"
                                                                         data-tippy="{{ trans('Add to cart') }}"
                                                                         data-tippy-inertia="true"
                                                                         data-tippy-animation="shift-away"
                                                                         data-tippy-delay="50" data-tippy-arrow="true"
                                                                         data-tippy-theme="sharpborder"
                                                                         data-id="{{ $product->id }}">
                                                                         <i class="pe-7s-cart"></i>
                                                                     </a>
                                                                 </li>
                                                                 @endif
                                                             </ul>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                         @empty
                                         @endforelse
                                     </div>
                                 </div>
                             </div>
                             {{ $products->links() }}
                         </div>
                     </div>
                 </div>
             </div>
         </main>
     @endsection
