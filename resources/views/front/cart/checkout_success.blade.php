@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
	<div class="ListingTopbar">
        <h4 class="ListingCategory">@lang('localize.checkout_result')</h4>
        <a href="/carts" class="back"><i class="fa fa-angle-left"></i></a>
    </div>

    <div class="ContentWrapper">
        <div class="CheckoutStatus success">
            <img src="{{ asset('assets/images/icon_success.png') }}">
            <h4>@lang('localize.checkout_success')</h4>
            <p>@lang('localize.thanksDetail')</p>
            <a href="/" class="btn btn-primary">@lang('localize.conShopping')</a>
        </div>
        <div class="panel-general">
            <div class="form-general">
                <div class="table-responsive">
                    <table class="table table-bordered cart_summary">
                        <thead>
                            <tr>
                                <th>@lang('localize.payerName')</th>
                                <th class="text-center">@lang('localize.transID')</th>
                                <th class="text-center">@lang('localize.orderStatus')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{$name}}</td>
                                <td class="text-center">{{$transaction_id}}</td>
                                <td class="text-center">
                                    <span class="order-status order-status-received">@lang('received')</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h3>@lang('localize.curDetail')</h3>
                <div class="table-responsive">
                    <table class="table table-bordered cart_summary">
                        <thead>
                            <tr>
                                <th class="text-center">@lang('localize.productName')</th>
                                <th class="text-center">@lang('localize.quantity')</th>
                                <th class="text-center">@lang('localize.subtotal')</th>
                                <th class="text-center">@lang('localize.remarks')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total = 0; ?>
                            @foreach($checkouts as $c)
                                <?php $attributes = ($c['json_attributes'] != null)? json_decode($c['json_attributes']) : null; ?>
                                <tr>
                                    @if ($c['payment_method'] == 1)
                                        <?php
                                            $total += $c['order_vc'];
                                        ?>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <p>{{$c['product_name']}}</p>
                                            @if($attributes != null)
                                                {!! $c['json_attributes_lang'] !!}
                                            @endif
                                        </td>

                                        <td class="text-center" style="vertical-align: middle;">{{$c['quantity']}}</td>
                                        <td class="text-center" style="vertical-align: middle;"><span class="product-coin text-center" style="width:100%; float: inherit;">{{$c['order_vc']}}</span></td>
                                        <td class="text-center" style="vertical-align: middle;">
                                            @if(!is_null($c['serial_number']))
                                            <p>
                                                @if($c['product_type'] == 2)
                                                <b>@lang('localize.coupon_code')</b>
                                                @elseif($c['product_type'] == 3)
                                                <b>@lang('localize.ticket_number')</b>
                                                @elseif($c['product_type'] == 4)
                                                <b>@lang('localize.e-card.serial_number')</b>
                                                @endif

                                                <br>
                                                @foreach(explode(",", $c['serial_number']) as $code)
                                                    @if(!empty($code))
                                                    {{ $code }}<br>
                                                    @endif
                                                @endforeach
                                            </p>
                                            @endif
                                        </td>
                                    @elseif ($c['payment_method'] == 2)
                                        <?php
                                            $total += $c['order_amt'];
                                        ?>
                                        <td class="text-center">
                                            {{$c['product_name']}}
                                            @if($attributes != null)
                                                @foreach($attributes as $key => $value)
                                                <p><b>{{$key}} : </b> {{$value}}</p>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="text-center">{{$c['quantity']}}</td>
                                        <td class="text-center">{{$c['currency_symbol']}} {{$c['order_amt']}}</td>
                                        <td class="text-center" style="vertical-align: middle;">
                                            @if(!is_null($c['serial_number']))
                                            <p>
                                                @if(strlen($c['serial_number']) > 12)
                                                <b>@lang('localize.ticket_number')</b>
                                                @else
                                                <b>@lang('localize.coupon_code')</b>
                                                @endif
                                                <br>
                                                @foreach(explode(",", $c['serial_number']) as $coupon)
                                                    @if(!empty($coupon))
                                                    {{ $coupon }}<br>
                                                    @endif
                                                @endforeach
                                            </p>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            <tr>
                                @if ($c['payment_method'] == 1)
                                    <td colspan="2" class="text-right"><strong>@lang('localize.totalcredit')</strong></td>
                                    <td class="text-center"><span class="product-coin text-center" style="width:100%; float: inherit;">{{$total}}</span></td>
                                    <td></td>
                                @elseif ($c['payment_method'] == 2)
                                    <td colspan="2" class="text-right"><strong>@lang('localize.total_price')</strong></td>
                                    <td class="text-center">{{$c['currency_symbol']}} {{$total}}</td>
                                    <td></td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('styles')
<style>
h3{
    color:#404040;
    font-family: "Open Sans", Microsoft Yahei, sans-serif;
}
</style>
@endsection