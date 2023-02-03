<!-- Mobile Sidebar -->
<div id="NavDrawer" class="drawer drawer--left">
    <div class="drawer__header">
        <div class="drawer__title h4">Menu</div>
    </div>
    <!-- begin mobile-nav -->
    <ul class="mobile-nav">
        <li class="mobile-nav__item">
            <a href="/" class="mobile-nav__link">{{trans('localize.home')}}</a>
        </li>
          @foreach ($nav_featured as $featured)
        <li class="mobile-nav__item" aria-haspopup="true">
            <div class="mobile-nav__has-sublist">
                    <div class="mobile-nav__toggle"><a href="javascript:void(0)" class="mobile-nav__link ">{{$featured['parent']->name}}</a></div>
                </div>
            <ul class="mobile-nav__sublist">
                @foreach ($featured['layer_one'] as $key => $layer_one)
                <li class="mobile-nav__item" aria-haspopup="true">
                    <div class="mobile-nav__has-sublist">
                        <div class="mobile-nav__toggle"><a href="javascript:void(0)" class="mobile-nav__link ">{{$layer_one->name}}</a></div>
                    </div>
                    <ul class="mobile-nav__sublist third-level">
                        @foreach ($layer_one['layer_two'] as $layer_two)
                            <li class="mobile-nav__item "><a href="/products/{{base64_encode($layer_one->id)}}" class="mobile-nav__link">{{ucwords(strtolower($layer_two->name))}}</a></li>
                        @endforeach

                    </ul>
                </li>
                @endforeach
            </ul>
        </li>
        @endforeach
    </ul>
</div>

<div id="CartDrawer" class="drawer drawer--right">
    <div class="drawer__header">
        <div class="drawer__title h4">
            @lang('localize.shopping_cart')
        </div>
    </div>
    <div id="CartContainer">
        <form action="/carts" method="post" novalidate="" class="cart ajaxcart">
            <div class="ajaxcart__inner">
                <?php
                    $total = 0;
                    $currency = $co_cursymbol;
                ?>
                @foreach($carts as $key => $cart)
                <div class="ajaxcart__product">

                    <?php
                        $total += ($cart['pricing']->product_price * $cart['cart']->quantity);
                        $image = (!empty($cart['main_image'])) ? $cart['main_image']->image : '';
                        if (!str_contains($image, 'http://')) {
                            // $image = env('IMAGE_DIR').'/product/'.$cart['product']->pro_mr_id.'/thumbnail_'.$image;
                            $image = env('IMAGE_DIR').'/product/'.$cart['product']->pro_mr_id.'/'.$image;
                        }
                    ?>
                     <div class="ajaxcart__row" data-line="1">
                        <div class="grid">
                            <div class="grid__item one-quarter">
                                <a href="/products/detail/{{$cart['product']->pro_id}}" class="ajaxcart__product-image">
                                    {{-- <img src="/web/images/loading.gif" data-src="{{ $image }}" alt="p10" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';" alt=""> --}}
                                    <img src="{{ $image }}" data-src="{{ $image }}" alt="p10" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';" alt="">
                                </a>
                            </div>
                            <div class="grid__item three-quarters">
                                <p>
                                    <a href="/products/detail/{{$cart['product']->pro_id}}" class="ajaxcart__product-name">{{$cart['product']->title}}</a>
                                </p>
                                <p>
                                    <span class="ajaxcart__product-meta">
                                    @if($cart['cart']->attributes)
                                        <small>
                                        @foreach(json_decode($cart['cart']->attributes_name) as $attribute_name => $attribute_item_name)
                                            <span><strong>{{ $attribute_name }}</strong> : {{ $attribute_item_name }}</span><br>
                                        @endforeach
                                        </small>
                                    @endif
                                    @if($cart['cart']->attribute_changes == 1)
                                    <br>
                                    <div class="well well-sm" style="border: 1px solid #eb9000; background-color: rgba(255, 153, 0, 0);">
                                        <h5><small><b>@lang('localize.attribute_error_desc') :</b></small></h5>
                                        <small>{!! $cart['pricing']->attributes_name !!}</small><br>
                                        <h5><small><b>@lang('localize.attribute_error_desc_2')</b></small></h5>
                                        <a href="/carts/update/attribute?cart_id={{ $cart['cart']->id }}&pricing_id={{ $cart['pricing']->id }} }}" type="button"><small>@lang('localize.attribute_error_desc_4')</small></a>
                                    </div>
                                    @endif
                                    </span>
                                </p>
                                <p>
                                    <a href="#" class="cart__remove" data-id="{{$cart['cart']->id}}"><small><i class="fa fa-trash-o"></i> @lang('localize.remove')</small></a>
                                </p>
                                <div class="grid--full display-table">
                                    <div class="grid__item">
                                        <span class="quantity">{{trans('localize.quantity')}} : {{$cart['cart']->quantity}}</span>
                                        @if($cart['pricing']->quantity < $cart['cart']->quantity)
                                            @if($cart['pricing']->quantity == 0)
                                                <p><small><label class="label label-danger">@lang('localize.out_of_stock')</label></small></p>
                                            @else
                                                <p><small><label class="label label-warning">{{$cart['pricing']->quantity}} @lang('localize.unit_left')</label></small></p>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="grid__item">
                                        <span class="money">@lang('localize.price') : {{$currency}} {{$cart['pricing']->product_price}}</span>
                                        @if(number_format($cart['pricing']->product_price, 2) != number_format($cart['cart']->product_price, 2))
                                            <p><small><label class="{{ ($cart['pricing']->product_price < $cart['cart']->product_price)? 'label label-success' : 'label label-warning' }}">@lang('localize.price_has_been_updated_from') {{$currency}} {{$cart['cart']->product_price}}</label></small></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="ajaxcart__footer">
                <div class="grid--full">
                    <div class="grid__item title-total">
                        <p>{{trans('localize.amount')}}</p>
                    </div>
                    <div class="grid__item price-total">
                        <p><span class="money">{{(!empty($currency))? $currency : ''}} {{ number_format($total, 2) }}</span></p>
                    </div>
                </div>
                <p class="text-center">@lang('localize.shipping_taxes_calculated_at_checkout')</p>
                <a href="/carts" class="btn btn--full cart__shoppingcart">{{ trans('localize.checkout') }} <i class="fa fa-angle-right"></i> </a>

            </div>
        </form>
    </div>
</div>