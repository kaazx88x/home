@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
	<div class="ListingTopbar">
        <h4 class="ListingCategory">@lang('localize.cart')</h4>
        <a href="javascript:void(0)" class="back goBack"><i class="fa fa-angle-left"></i></a>
    </div>

    <div class="ContentWrapper">
        @include('front.common.errors')
        @if(!$carts)
            <div class="Empty">
                <img src="{{ asset('assets/images/cart_empty.png') }}">
                <p>@lang('localize.shopping_cart_contains'): 0 @lang('localize.product')</p>
                <a href="/products" class="btn btn-primary">@lang('localize.conShopping')</a>
            </div>
        @else
            <h5 class="table-head">@lang('localize.shopping_cart_contains'): {{ count($carts) }} @lang('localize.product')</h5>

            <table class="table-cart">
                <thead>
                    <tr>
                        <th>@lang('localize.details')</th>
                        <th class="text-center">@lang('localize.quantity')</th>
                        <th class="text-center">@lang('localize.subtotal')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($carts as $key => $cart)
                    <?php
                        $rate = $cart['cart']->currency_rate;
                        $max_qty = ($cart['pricing']->quantity);
                        $image = (!empty($cart['main_image'])) ? $cart['main_image']->image : '';
                        if (!str_contains($image, 'http://'))
                            $image = env('IMAGE_DIR').'/product/'.$cart['product']->pro_mr_id.'/'.$image;
                    ?>
                    <tr>
                        <td class="details">
                            <div class="product-img">
                                <img src="{{ $image }}">
                            </div>
                            <div class="product-detail">
                            <h4><a href="/products/detail/{{ $cart['product']->pro_id }}"> {{ $cart['product']->title }} </a></h4>
                            <p>
                                @if($cart['cart']->attributes)                               {{-- {{$cart['cart']->title}} --}}
                                    <small>
                                        <p><strong>{!!$cart['pricing']->attributes_name!!} </strong> </p>
                                    </small>
                                    @if($cart['cart']->attribute_changes == 1)
                                        <br>
                                        <div class="well well-sm" style="border: 1px solid #eb9000; background-color: rgba(255, 153, 0, 0);">
                                        <p>
                                            <h5><small><b>@lang('localize.attribute_error_desc') :</b></small></h5>
                                            <small>{!! $cart['pricing']->attributes_name !!}</small><br>
                                            <h5><small><b>@lang('localize.attribute_error_desc_2')</b></small></h5>
                                            <a href="/carts/update/attribute?cart_id={{ $cart['cart']->id }}&pricing_id={{ $cart['pricing']->id }}" type="button"><small>@lang('localize.attribute_error_desc_4')</small></a>
                                        </p>
                                        </div>
                                    @endif
                                    <input class="attribute_status" type="hidden" id="attribute_status_{{ $key }}" value="{{ $cart['cart']->attribute_changes }}">
                                @else
                                    <input class="attribute_status" type="hidden" value="0">
                                @endif
                            </p>
                            <span class="price">{{$co_cursymbol}} {{ number_format($cart['pricing']->product_price, 2) }}</span>
                                @if(round($cart['pricing']->product_price, 2) <> round($cart['cart']->product_price,2))
                                    <p>
                                        <small><label class="{{ ($cart['pricing']->product_price < $cart['cart']->product_price)? 'label label-success' : 'label label-warning' }}">@lang('localize.price_has_been_updated_from') {{$co_cursymbol . ' ' . $cart['cart']->product_price}}</label></small>
                                    </p>
                                @endif
                                <p>
                                    <a href="javascript:void(0)" class="delete cart_remove" data-id="{{$cart['cart']->id}}"><small><i class="fa fa-trash-o"></i> @lang('localize.delete')</small></a>
                                    @php
                                    $expired = false;
                                    if($cart['product']->pro_type == 3 && !empty($cart['product']->end_date) && \Carbon\Carbon::now('UTC') >= \Carbon\Carbon::parse($cart['product']->end_date))
                                        $expired = true;
                                    @endphp

                                    @if($expired)
                                    <small class="text-danger">@lang('localize.expired') : {{ \Helper::UTCToTZ($cart['product']->end_date) }}</small>
                                    @endif
                                </p>
                            </div>
                        </td>
                        <td class="quantity">
                            <div class="quantity-selector">
                                <button type="button" id="sub" class="rmv_qty" data-key="{{ $key }}">-</button>

                                <input type="number" class="number data-id-{{ $key }}" id="qty_{{$key}}" data-key="{{ $key }}" data-id="{{ $cart['cart']->id }}" data-min="1" data-max="{{ $max_qty }}" data-price="{{ $cart['pricing']->product_price }}" value="{{ $cart['cart']->quantity }}" data-value="{{ $cart['cart']->quantity }}"/>

                                <button type="button" id="add" class="add_qty" data-key="{{ $key }}">+</button>
                            </div>
                            <input type="hidden" id="quantity_check_{{$key}}" class="quantity_check" orderqty-id="{{$cart['cart']->quantity}}" proqty-id="{{$cart['pricing']->quantity}}" proname-id="{{$cart['product']->pro_title_en}}">

                            @if($cart['cart']->quantity > $cart['pricing']->quantity)
                                @if($cart['pricing']->quantity == 0)
                                    <p><small><label class="text-danger">@lang('localize.out_of_stock')</label></small></p>
                                @else
                                    <p><small><label class="text-warning">{{ $cart['pricing']->quantity }} @lang('localize.unit_left')</label></small></p>
                                @endif
                            @endif
                        </td>
                        <td class="price text-nowrap">
                            {{$co_cursymbol}} <span id="subtotal_{{$key}}" class="sub_total">{{ number_format( $cart['pricing']->product_price * $cart['cart']->quantity, 2) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
            <div class="CheckoutAction">
                <p><em>*@lang('localize.shipping_taxes_calculated_at_checkout')*</em></p>
                <p>@lang('localize.total') :
                    <span class="subtotal">
                        {{$co_cursymbol}} <span id="total">{{ number_format(($grandtotal_price == 0)? 0 : $grandtotal_price, 2) }}</span>
                    </span>
                </p>
                <a class="next-btn"><button class="btn btn-primary">@lang('localize.checkout')</button></a>
            </div>
        @endif

    </div>
@stop

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/js/owlcarousel/owl.carousel.css') }}">
<style>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {

        var platform_charge = parseFloat("{{ $commission['platform_charge'] / 100 }}");
        $('.number').keydown(function (e) {-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});

        $('.number').on('change', function(event) {
            var item_key = $(this).attr('data-key');
            var cart_id = $(this).attr('data-id');
            var max_qty = $(this).attr('data-max');
            var min_qty = $(this).attr('data-min');
            var price = parseFloat($(this).attr('data-price'));
            var qty = parseInt($(this).val());
            var qty_before = parseInt($(this).attr('data-value'));
            var subtotal = parseFloat(price * qty).toFixed(2);

            if(qty < min_qty) {
                swal('Minimum quantity limit');
                event.preventDefault();
                $(this).val(qty_before);
                return;
            }

            if(qty > max_qty) {
                swal('Maximum quantity limit');
                event.preventDefault();
                $(this).val(qty_before);
                return;
            }

            $(this).attr('data-value', qty);
            $.ajax({
                type: 'post',
                data: { 'id': cart_id, 'qty': qty },
                url: '{{url('/carts/update')}}',
                success: function (responseText) {
                    if (responseText) {
                        if (responseText == "success") {
                            $('#qty_'+item_key).val(qty);
                            $('#subtotal_'+item_key).html(number_seperator(subtotal));
                            $('#total').myfunction();
                            $('#quantity_check_'+item_key).attr({'orderqty-id': qty});
                        }
                    }
                }
            });
        });

        $('.add_qty').on('click', function(){
            var item_key = $(this).attr('data-key');
            var cart_id = $('.data-id-'+item_key).attr('data-id');
            var max_qty = $('.data-id-'+item_key).attr('data-max');
            var price = parseFloat($('.data-id-'+item_key).attr('data-price')).toFixed(2);
            var qty = parseInt($('#qty_'+item_key).val())+1;
            var subtotal = parseFloat(price * qty).toFixed(2);

            if(qty <= max_qty) {
                $('.data-id-'+item_key).attr('data-value', qty);
                $.ajax({
                    type: 'post',
                    data: { 'id': cart_id, 'qty': qty },
                    url: '{{url('/carts/update')}}',
                    success: function (responseText) {
                        if (responseText) {
                            if (responseText == "success") {
                                $('#qty_'+item_key).val(qty);
                                $('#subtotal_'+item_key).html(number_seperator(subtotal));
                                $('#total').myfunction();
                                $('#quantity_check_'+item_key).attr({'orderqty-id': qty});
                            }
                        }
                    }
                });
            } else {
                swal('Maximum quantity limit');
            }
        });

        $('.rmv_qty').on('click', function(){
            var item_key = $(this).attr('data-key');
            var cart_id = $('.data-id-'+item_key).attr('data-id');
            var price = parseFloat($('.data-id-'+item_key).attr('data-price')).toFixed(2);
            var qty = parseInt($('#qty_'+item_key).val())-1;
            var subtotal = parseFloat(price * qty).toFixed(2);

            if(qty >= 1) {
                $('.data-id-'+item_key).attr('data-value', qty);
                $.ajax({
                    type: 'post',
                    data: { 'id': cart_id, 'qty': qty },
                    url: '{{url('/carts/update')}}',
                    success: function (responseText) {
                        if (responseText) {
                            if (responseText == "success") {
                                $('#qty_'+item_key).val(qty);
                                $('#subtotal_'+item_key).html( number_seperator(subtotal) );
                                $('#total').myfunction();
                                $('#quantity_check_'+item_key).attr({'orderqty-id': qty});
                            }
                        }
                    }
                });
            } else {
                swal('Minimum quantity limit');
            }
        });

        $.fn.myfunction = function() {
            var subtotal = 0;
            var merchantcharge = 0;
            var total = 0.00;
            $('span.sub_total').each(function( index ) {
                var sub = parseFloat($(this).text().replace(',', '')).toFixed(2);
                subtotal = (parseFloat(subtotal) + parseFloat(sub)).toFixed(2);
                total = (parseFloat(subtotal) + parseFloat(merchantcharge)).toFixed(2);
            });
            $('#total').html(number_seperator(total));
            return this;
        };

        $('.next-btn').on('click', function() {

            var pass = 1;
            var message = '';
            $('input.quantity_check').each(function( index ) {
                var order_qty = $(this).attr('orderqty-id');
                var pro_qty = $(this).attr('proqty-id');
                var pro_name = $(this).attr('proname-id');
                if(parseInt(pro_qty) < parseInt(order_qty))
                {
                    pass = 0;
                    message = 'Insufficient <b>'+pro_name+'</b> quantity.<br>Please decrease the item quantity in order to proceed';
                    if(pro_qty == 0)
                            message = '<b>'+pro_name+'</b> is <font color="red">out of stock</font>. <br>Please remove the item in order to proceed';
                }
            });

            if(pass == 0)
            {
                swal({ title: "Sorry!", text: message, type: "warning", html:true,confirmButtonColor: "#ed1c24", });
            } else {
                $('.loading').show();
                window.location.replace("/carts/checkout");
            }
        });
    });

    function number_seperator(number) {
        number += '';
        var x = number.split('.');
        var x1 = x[0];
        var x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }
</script>
@endsection
