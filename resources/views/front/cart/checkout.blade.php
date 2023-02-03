@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
	<div class="ListingTopbar">
        <h4 class="ListingCategory">@lang('localize.checkout')</h4>
        <a href="/carts" class="back"><i class="fa fa-angle-left"></i></a>
    </div>

    @include('front.common.errors')

    <form action='/carts/checkout' method="POST" enctype="multipart/form-data" id="checkout_submit">
        {{ csrf_field() }}
        <div class="panel-general">
            <div class="panel-title">@lang('localize.shipAddress')</div>
            <div class="form-general">
                <div class="row">
                    <div class="col-sm-12">
                        {{--  Load shipping address  --}}
                        <div class="address"></div>

                        <div class="shipping-list panel-choose" data-id="new" style="width:100%; cursor: pointer; background-color: #fcfcfc; text-align: center;">
                            <label class="radio radio-inline">
                                <span>@lang('localize.addnewshipping')</span>
                            </label>
                        </div>
                        <input type="hidden" name="new_address" id="new_address" value="{{ old('new_address', 1) }}">
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="control-label">@lang('localize.name')</label>
                            <input class="form-control compulsary" type="text" name="name" id="name" value="{{old('name')}}">
                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">@lang('localize.areacode')</label>
                            <select class="form-control compulsary" name="areacode" id="areacode"></select>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group">
                            <label class="control-label">@lang('localize.phone')</label>
                            <input class="form-control compulsary" type="text" name="telephone" id="telephone" value="{{old('telephone')}}">
                            <span class="mobilehint hint" style="display : none;"><i class="fa fa-info-circle"></i> <span class="mobilehint-text">@lang('localize.mobileHint')</span></span>
                            @if ($errors->has('telephone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('telephone') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="control-label">@lang('localize.address')</label>
                            <input class="form-control compulsary" placeholder="{{trans('localize.address1Input')}}" type="text" name="address_1" id="address_1" value="{{old('address_1')}}">
                            @if ($errors->has('address_1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('address_1') }}</strong>
                                </span>
                            @endif
                            <input class="form-control" type="text" placeholder="{{trans('localize.address2Input')}}" name="address_2" id="address_2" value="{{old('address_2')}}">
                            @if ($errors->has('address_2'))
                                <span class="help-block">
                                    {{ $errors->first('address_2') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">@lang('localize.zipcode')</label>
                            <input class="form-control compulsary" type="text" name="postal_code" id="postal_code" value="{{old('postal_code')}}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">@lang('localize.country')</label>
                            <div class="custom_select">
                                <select class="form-control compulsary" name="country" id="country" onchange="get_states('#state', this.value)"></select>
                            </div>
                            @if ($errors->has('country'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('country') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">@lang('localize.state')</label>
                            <div class="custom_select">
                                <select class="form-control compulsary" name="state" id="state">
                                    <option value="0">{{trans('localize.selectCountry_first')}}</option>
                                </select>
                            </div>
                            @if ($errors->has('state'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('state') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">@lang('localize.city')</label>
                            <input class="form-control compulsary" type="text" name="city" id="city" value="{{old('city')}}">
                            @if ($errors->has('city'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('city') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <table class="table-cart">
            <thead>
                <tr>
                    <th>@lang('localize.details')</th>
                    <th class="text-center">@lang('localize.quantity')</th>
                    <th class="text-center">@lang('localize.product') @lang('localize.subtotal')</th>
                    <th class="text-center">@lang('localize.shipping_fees')</th>
                    <th class="text-center">@lang('localize.subtotal')</th>
                </tr>
            </thead>
            <tbody>
                <?php $attribute_missmatch = 0; ?>
                @foreach($carts as $key => $cart)
                <?php
                    $rate = $cart['cart']->currency_rate;
                    $max_qty = ($cart['pricing']->quantity);
                    $image = (!empty($cart['main_image'])) ? $cart['main_image']->image : '';
                    if (!str_contains($image, 'http://'))
                        $image = env('IMAGE_DIR').'/product/'.$cart['product']->pro_mr_id.'/'.$image;
                ?>
                <tr class="text-center">
                    <td class="details text-left">
                        <div class="product-img">
                            <img src="{{ $image }}">
                        </div>
                        <div class="product-detail">
                        <h4><a href="/products/detail/{{ $cart['product']->pro_id }}"> {{ $cart['product']->title }} </a></h4>
                        <p>
                            <small>
                                <span>
                                    <strong>@lang('localize.shipping_fees')</strong> :
                                    @if ($cart['pricing']->shipping_fees_type == 1)
                                        {{ number_format($cart['pricing']->shipping_fees , 2) }} - @lang('localize.shipping_fees_product')
                                    @elseif ($cart['pricing']->shipping_fees_type == 2)
                                        {{ number_format($cart['pricing']->shipping_fees , 2) }} - @lang('localize.shipping_fees_transaction')
                                    @else
                                        @lang('localize.shipping_fees_free')
                                    @endif
                                </span>
                            </small>
                            <br/>
                            @if($cart['cart']->attributes)
                            <small>
                                <span>{!!$cart['pricing']->attributes_name!!}</span>
                            </small>
                            @if($cart['cart']->attribute_changes == 1)
                            <br>
                            <?php $attribute_missmatch++; ?>
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
                        <span class="price text-nowrap">{{ $co_cursymbol . ' ' . number_format($cart['pricing']->product_price, 2) }}</span>
                        @if(round($cart['pricing']->product_price, 2) <> round($cart['cart']->product_price,2))
                            <p>
                                <small><label class="{{ ($cart['pricing']->product_price < $cart['cart']->product_price)? 'label label-success' : 'label label-warning' }}">@lang('localize.price_has_been_updated_from') {{$co_cursymbol}} {{$cart['cart']->product_price}}</label></small>
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
                    <td class="quantity">{{ $cart['cart']->quantity }}</td>
                    <td class="price text-nowrap">
                        {{$co_cursymbol}} <span id="subtotal_{{$key}}" class="sub_total">{{ number_format( $cart['pricing']->product_price * $cart['cart']->quantity, 2) }}</span>
                    </td>
                    <td class="price text-nowrap">
                        @if ($cart['pricing']->shipping_fees_type == 1)
                            {{$co_cursymbol . ' ' . number_format(($cart['pricing']->shipping_fees * $cart['cart']->quantity), 2) }}
                        @elseif ($cart['pricing']->shipping_fees_type == 2)
                            {{$co_cursymbol . ' ' . number_format($cart['pricing']->shipping_fees , 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="price text-nowrap">
                        {{$co_cursymbol}} <span id="subtotal_{{$key}}" class="subtotal">{{ number_format( $cart['pricing']->purchasing_price + $cart['pricing']->product_shippingfees, 2) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="checkout-detail text-nowrap">
                <tr class="currency-row">
                    <td colspan="4">@lang('localize.product') @lang('localize.total')</td>
                    <td><span class="currency">{{ $co_cursymbol . ' ' . number_format($grandtotal_price, 2) }}</span></td>
                </tr>
                <tr class="currency-row">
                    <td colspan="4">@lang('localize.shipping_fees') @lang('localize.total')</td>
                    <td><span class="currency">{{ $co_cursymbol . ' ' . number_format($shippingfees_total_price, 2) }}</span></td>
                </tr>
                <tr>
                    <td colspan="5" class="grand-total">
                        <label>@lang('localize.total')</label>
                        <span class="credit" style="color:#d70000;">{{ $co_cursymbol . ' ' . number_format(($grandtotal_price + $shippingfees_total_price), 2) }}</span>
                    </td>
                </tr>
                <tr class="credit-row">
                    <td colspan="4">@lang('common.credit_name')</td>
                    <td><span class="credit">{{ number_format(($grandtotal_credit), 4) }}</span></td>
                </tr>
                <tr class="credit-row">
                    <td colspan="4">@lang('localize.platform_charges')</td>
                    <td><span class="credit">{{ number_format($merchantcharge_total, 4) }}</span></td>
                </tr>
                <tr class="credit-row">
                    <td colspan="4">@lang('localize.gst')</td>
                    <td><span class="credit">{{ number_format($servicecharge_total, 4) }}</span></td>
                </tr>
                <tr class="credit-row">
                    <td colspan="4">@lang('localize.shipping_fees')</td>
                    <td><span class="credit">{{ number_format($shippingfees_total, 4) }}</span></td>
                </tr>
                <tr>
                    <td colspan="5" class="grand-total">
                        <label>@lang('common.credit_name') @lang('localize.total')</label>
                        <span class="credit" id="total_vc">{{ number_format($grandtotal_credit + $merchantcharge_total + $servicecharge_total + $shippingfees_total, 4) }}</span>
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="checkout-action">
            @if(count($carts) > 0)
                <input type="hidden" name="payment_type" id="credit" value="credit"/>
                <div>
                    <input class="form-control text-center compulsary" type="password" name="securecode" id="securecode" maxlength="6" placeholder="@lang('localize.6_digit_secure_code')">
                </div>
                <br/>
                <button class="btn btn-primary btn-block" id="btn_submit">{{trans('localize.place_order')}}</button>
            @endif
        </div>
    </form>
@stop

@section('scripts')
<script src="{{ asset('backend/js/custom.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function() {

        get_countries('#country', "{{ old('country', '0') }}", '#state', "{{ old('state', '0') }}");
        get_phoneAreacode('#areacode', "{{ old('areacode', '0') }}");

        //{{-- //$('input:radio[name=payment_type][value=credit]').trigger('click'); --}}
        var shippings = {!! json_encode($shippings) !!};
        if(shippings.length > 0) {
            var dataI="";
            $.each(shippings, function(k, v) {
                var default_class = '';
                if(v.isdefault === 1) {
                    $('.shipping-list').removeClass('panel-choose');
                    default_class = 'panel-choose';
                    load_shipping(k, shippings);
                }

                dataI += "<div class='shipping-list " + default_class + "' data-id='" + k + "' style='width:100%; cursor: pointer;'><label class='radio radio-inline'><span><address>" + ((v.ship_address1 !== null) ? v.ship_address1 + ', ' : '') + ((v.ship_address2 !== null) ? v.ship_address2 + ', ' : '') + ((v.ship_postalcode !== null) ? v.ship_postalcode + ', ' : '') + ((v.ship_city_name !== null) ? v.ship_city_name : '') + "</address><p>" + v.ship_name + " - " + ((v.areacode !== null) ? v.areacode : '') + v.ship_phone + " </p></span></label></div>";
            });

            $(".address").html(dataI);
        }

        $(document).on('click','.shipping-list', function() {
            var id = $(this).attr('data-id');
            $('.shipping-list').removeClass('panel-choose');
            $(this).addClass('panel-choose');
            if(id !== 'new') {
                load_shipping(id, shippings);
            } else {
                clear_shipping();
            }
        });

        $('#areacode').change(function() {
            var areacode = $(this).val();

            if (areacode == 60 || areacode == 886) {
                if(areacode == 60)
                    $('.mobilehint-text').text("@lang('localize.mobileHint-areacode.60')");

                if(areacode == 886)
                    $('.mobilehint-text').text("@lang('localize.mobileHint-areacode.886')");

                $('.mobilehint').show();
            } else {
                $('.mobilehint').hide();
            }
        });

        $('#telephone,#postal_code').keydown(function (e) {
            if (e.shiftKey || e.ctrlKey || e.altKey) {
                e.preventDefault();
            } else {
                var key = e.keyCode;
                if (!((key == 8) || (key == 46) || (key >= 35 && key <= 40) || (key >= 48 && key <= 57) || (key >= 96 && key <= 105))) {
                    e.preventDefault();
                }
            }
        });

        {{--  $.ajax({
            type: "GET",
            url: "shippingaddress/list",
            success: function(response){
                var dataI="";
                $.each(response.data, function(k, v) {
                    var default_class = (v.isdefault === 1)? 'panel-choose' : '';

                    dataI += "<div class='shipping-list "+default_class+"' data-id='"+k+"' style='width:100%'><label class='radio radio-inline'><span><address>"+v.ship_address1 +", "+v.ship_address2 +", "+v.ship_postalcode +", "+v.ship_city_name+"</address><p>"+v.ship_name+" - "+v.ship_phone+" </p></span></label></div>";
                });

                $(".address").html("<div style='cursor: pointer'>"+dataI+"</div>");
            }
        });  --}}

        {{--  $('input[type=radio][name=shipping_addr]').change(function() {
            if (this.value == 'no') {
                $('#checkout_submit').trigger("reset");
                $('#country').val("0");
                $('#state').empty();
                $('#state').append('<option value="0" selected>@lang('localize.selectCountry_first')</option>');
            }
        });  --}}

        $('#btn_submit').click(function(event) {
            var cid = parseInt("{{ $cid }}");
            var total = parseFloat($('#total_vc').html());
            var cus_vc = parseFloat("{{ $cus_vc }}");
            var pass = 1;
            $('#checkout_submit :input').each(function(e) {
                if ($(this).hasClass('compulsary')) {
                    if (!$(this).val() || $(this).val() == 0) {
                        $(this).attr('placeholder', '{{trans('localize.fieldrequired')}}').css('border', '1px solid red').focus();
                        pass = 0;
                        return false;
                    }
                }

                $(this).css('border', '');
            });

            if((!$('#securecode').val() || $('#securecode').val() == 0) && pass == 1) {
                $('#securecode').attr('placeholder', '{{trans('localize.securecode_required')}}').css('border', '1px solid red').focus();
                pass = 0;
                return false;
            } else {
                $('#securecode').css('border', '');
            }

            if(pass == 0)
            {
                event.preventDefault();
                return false;
            }

            if($('#credit').is(':checked')) {

                if (total > cus_vc) {
                    swal("@lang('localize.sorry')", "@lang('localize.checkouts.insufficient.credit')", "warning");
                    return false;
                } else if ($('#country').val() != cid) {
                    swal({
                        title: "@lang('localize.sorry')",
                        text: "@lang('localize.products_cant_be_shipped_error_desc')",
                        type: "warning",
                        confirmButtonText: "@lang('localize.ok')"
                    });
                    return false;
                } else if (parseInt("{{$attribute_missmatch}}") > 0) {
                    swal({
                        title: "@lang('localize.sorry')!",
                        text: "@lang('localize.product_options_not_updated_error_desc')",
                        type: "warning",
                        confirmButtonText: "@lang('localize.ok')"
                    });
                    return false;
                } else {
                    var message = '';
                    $('span.quantity_check').each(function( index ) {
                        var order_qty = $(this).attr('orderqty-id');
                        var pro_qty = $(this).attr('proqty-id');
                        var pro_name = $(this).attr('proname-id');
                        if(parseInt(pro_qty) < parseInt(order_qty))
                        {
                            pass = 0;
                            message = "@lang('localize.insufficient_product_quantity_error_desc', ['product_name' => '"+pro_name+"'])";
                            if(pro_qty == 0)
                                message = "@lang('localize.product_out_of_stock_error_desc', ['product_name' => '"+pro_name+"'])";
                        }
                    });

                    if(pass == 0)
                    {
                        swal({ title: "Sorry!", text: message, type: "warning", html:true,confirmButtonColor: "#ed1c24", });
                        event.preventDefault();
                        return false;
                    } else {
                        $('.loading').show();
                        $("#checkout_submit").submit();
                    }

                }
            }
        });

        function load_shipping(id, shippings) {
            $('#name').val(shippings[id].ship_name);
            $('#address_1').val(shippings[id].ship_address1);
            $('#address_2').val(shippings[id].ship_address2);
            $('#postal_code').val(shippings[id].ship_postalcode);
            $('select[name=areacode]').val(shippings[id].areacode);
            $('#telephone').val(shippings[id].ship_phone);
            $('#city').val(shippings[id].ship_city_name);
            $('select[name=country]').val(shippings[id].ship_country);
            get_countries('#country', shippings[id].ship_country, '#state', (shippings[id].ship_state_id !== 0) ? shippings[id].ship_state_id : '');
            $('#new_address').val(0);
        }

        function clear_shipping() {
            $('#name').val('');
            $('#address_1').val('');
            $('#address_2').val('');
            $('#postal_code').val('');
            $('#telephone').val('');
            $('#city').val('');
            $('select[name=areacode]').val('');
            $('select[name=country]').val('0');
            get_countries('#country', '0');
            get_states('#state', '0');
            $('#new_address').val(1);
        }

    });
</script>
@endsection
