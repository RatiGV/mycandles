            <div class="offcanvas-body">
                <div class="minicart-content">
                    <div class="minicart-heading">
                        <h4 class="mb-0">{{ trans('Cart') }}</h4>
                        <a href="#" class="button-close"><i class="pe-7s-close" data-tippy="Close"
                                data-tippy-inertia="true" data-tippy-animation="shift-away" data-tippy-delay="50"
                                data-tippy-arrow="true" data-tippy-theme="sharpborder"></i></a>
                    </div>
                    <ul class="minicart-list">
                        @if ($cart->isNotEmpty())
                            @foreach ($cart as $carty)
                                <li class="minicart-product">
                                    <a class="product-item_remove" href="#" data-id="{{ $carty->id }}"><i
                                            class="pe-7s-close" data-tippy="{{ trans('Remove') }}"
                                            data-tippy-inertia="true" data-tippy-animation="shift-away"
                                            data-tippy-delay="50" data-tippy-arrow="true"
                                            data-tippy-theme="sharpborder"></i></a>
                                    <a href="{{ route('clientProductsInner', $carty->associatedModel->slug ?? $carty->id . '-' . \Illuminate\Support\Str::slug($carty->name, '-', false)) }}"
                                        class="product-item_img">
                                        <img class="img-full" src="{{ $carty->attributes->image }}" alt="Product Image">
                                    </a>
                                    <div class="product-item_content">
                                        <a class="product-item_title"
                                            href="{{ route('clientProductsInner', $carty->associatedModel->slug ?? $carty->id . '-' . \Illuminate\Support\Str::slug($carty->name, '-', false)) }}">{{ $carty->name }}</a>
                                        <span class="product-item_quantity">{{ $carty->quantity }} x
                                            {{ getPrice($carty->price) }} GEL</span>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                <div class="minicart-item_total">
                    <span>{{ trans('Total') }}</span>
                    <span class="ammount">{{ getPrice(\Cart::getSubTotal()) }} GEL</span>
                </div>
                <div class="group-btn_wrap d-grid gap-2">
                    <a href="{{ route('clientCart') }}" class="btn btn-dark">Checkout</a>
                </div>
            </div>
