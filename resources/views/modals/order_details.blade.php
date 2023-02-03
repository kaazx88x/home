<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="row">
        <div class="col-sm-3"><img src="{{ url('/assets/images/meihome_logo.png') }}" alt="" style="width:auto; height:35px;"></div>
        <div class="col-sm-7 text-center" style="vertical-align: middle;"><h3 class="text-uppercase">@lang('localize.transaction_reference')</h3></div>
    </div>
</div>

<div class="modal-body">
    @if ($shipping)
        <div class="row">
            <div class="col-sm-6">
                <h4>{{ env('COMPANY_NAME') }}</h4>
                {{ env('COMPANY_ADDRESS') }}
                <br><b>@lang('localize.gst_no') : </b> {{ env('GST') }}
            </div>
            <div class="col-sm-6">
                <h4>{{trans('localize.merchant_detail')}}</h4>
                <b>{{ $shipping->merchant_name }}</b>
                {!! ($shipping->mer_address1) ? '<br>' . $shipping->mer_address1 : ''!!}
                {!! ($shipping->mer_address2) ? '<br>' . $shipping->mer_address2 : '' !!}
                {!! ($shipping->mer_city) ? '<br>' . $shipping->mer_city : '' !!}
                <br>{{ $shipping->zipcode . ' ' . $shipping->mer_state . ', ' . $shipping->mer_country }}
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-xs-6" style="border-right:1px solid #eee;">
                <h4>{{trans('localize.paymentdetail')}}</h4>
                <b>{{trans('localize.date')}}: </b>{{ \Helper::UTCtoTZ($shipping->order_created_at) }}<br/>
                <b>{{trans('localize.order_id')}}: </b>{{ $shipping->order_id }}<br/>
                <b>{{trans('localize.status')}}: </b>
                @if ($shipping->order_status == 1)
                    {{trans('localize.Processing')}}
                @elseif ($shipping->order_status == 2)
                    {{trans('localize.Packaging')}}
                @elseif ($shipping->order_status == 3)
                    {{trans('localize.Shipped')}}
                @elseif ($shipping->order_status == 4)
                    {{trans('localize.Completed')}}
                @elseif ($shipping->order_status == 5)
                    {{trans('localize.Canceled')}}
                @endif<br/>

                @if ($shipping->name)
                <b>{{trans('localize.courier')}}: </b>{{ $shipping->name }}<br/>
                <b>{{trans('localize.trackingno')}}: </b>{{ $shipping->order_tracking_no }}<br/>
                @endif
            </div>
            <div class="col-xs-6">
                <h4>{{trans('localize.shippingaddress')}}</h4>
                @if (!empty($address->ship_address1))
                {{ $address->ship_name }}
                <br/>
                {!! $address->ship_address1.', '.$address->ship_address2.',<br/>'.$address->ship_postalcode.' '.$address->ship_city_name.', '.(!empty($address->ship_state_id) ? $address->name : $address->ci_name).', '.$address->co_name !!}
                @else
                {{ $address->order_shipping_add }}
                <br/>
                @endif
                <br/>
                <strong>{{trans('localize.phone')}} : </strong>{{ $address->ship_phone }}
            </div>
        </div>
    <hr/>
    <div class="row">
        <div class="col-sm-12">
            <table border=0 width="100%">
                <tr>
                    <th width="35%" style="padding-left:5px;">{{trans('localize.merchant_invoice_no')}}</th>
                    <th width="5%">:</th>
                    <td colspan='3'>{{$shipping->transaction_id}}</td>
                </tr>
                <tr>
                    <td colspan='5'>&nbsp;</td>
                </tr>
                <tr>
                    <th colspan='2'>&nbsp;</th>
                    <td class='text-center'><b>@lang('common.credit_name')</b></td>
                    <td class='text-center'><b>@lang('localize.amount') ({{ $shipping->currency }})</b></td>
                    <td></td>
                </tr>
                <tr>
                    <th style="padding-left:5px;">Mei Point</td>
                    <th>:</th>
                    <td class='text-center'>
                        @if ($shipping->cus_platform_charge_value > 0 && $shipping->cus_service_charge_value > 0)
                            {{ number_format((($shipping->product_original_price * $shipping->order_qty) / $shipping->currency_rate), 4) }}
                        @else
                            {{ number_format($shipping->order_vtokens, 4) }}
                        @endif
                    </td>
                    <td class='text-center'>
                        @if ($shipping->cus_platform_charge_value > 0 && $shipping->cus_service_charge_value > 0)
                            {{ number_format(($shipping->product_original_price * $shipping->order_qty), 2) }}
                        @else
                            {{ number_format($shipping->order_vtokens * $shipping->currency_rate, 2) }}
                        @endif
                    </td>
                    <td> * </td>
                </tr>
                <tr>
                    <th style="padding-left:5px;">{{trans('localize.shipping_fees')}}</td>
                    <th>:</th>
                    <td class='text-center'>{{ ($shipping->total_product_shipping_fees_credit) ? $shipping->total_product_shipping_fees_credit : '0.00' }}</td>
                    <td class='text-center'>{{ number_format(($shipping->total_product_shipping_fees_credit * $shipping->currency_rate), 2) }}</td>
                    <td> * </td>
                </tr>
                @if($type == 'admin')
                    <tr><td colspan='5'><br/></td></tr>
                    <tr style="border-left:1pt solid #d1cfcf; border-right:1pt solid #d1cfcf; border-top:1pt solid #d1cfcf; padding:1px;">
                        <th style="padding-left:5px;">{{trans('localize.platform_charges')}} {{$shipping->cus_platform_charge_rate}}%</th>
                        <th>:</th>
                        <td class='text-center'>{{ ($shipping->cus_platform_charge_value) ? $shipping->cus_platform_charge_value : '0.00' }}</td>
                        <td class='text-center'>{{ number_format(($shipping->cus_platform_charge_value * $shipping->currency_rate), 2) }}</td>
                        <td> * </td>
                    </tr>
                    <tr style="border-left:1pt solid #d1cfcf; border-right:1pt solid #d1cfcf; padding:1px;">
                        <th style="padding-left:5px;">{{trans('localize.gst')}} {{$shipping->cus_service_charge_rate}}%</td>
                        <th>:</th>
                        <td class='text-center'>{{ $shipping->cus_service_charge_value }}</td>
                        <td class='text-center'>{{ number_format(($shipping->cus_service_charge_value * $shipping->currency_rate), 2) }}</td>
                        <td> * </td>
                    </tr>
                    <tr style="border-left:1pt solid #d1cfcf; border-right:1pt solid #d1cfcf; border-bottom:1pt solid #d1cfcf;">
                        <th style="padding-left:5px;">{{trans('localize.order_credit_total')}}</td>
                        <th>:</th>
                        <td class='text-center'>{{ $shipping->order_vtokens }}</td>
                        <td class='text-center'>{{ number_format(($shipping->order_vtokens * $shipping->currency_rate), 2) }}</td>
                        <td></td>
                    </tr>
                    <tr><td colspan='5'><br/></td></tr>
                @endif
                <tr>
                    <th style="padding-left:5px;">{{trans('localize.merchant_charge')}} {{$shipping->merchant_charge_percentage}}%</td>
                    <th>:</th>
                    <td class='text-center'>{{ $shipping->merchant_charge_vtoken }}</td>
                    <td class='text-center'>{{ number_format(($shipping->merchant_charge_vtoken * $shipping->currency_rate), 2) }}</td>
                    <td> * </td>
                </tr>
                <tr><td colspan='5'><br/></td></tr>
                <tr>
                    <th style="padding-left:5px;">{{trans('localize.merchant_earned_credit')}}</td>
                    <th>:</th>
                    <th class='text-center'>{{ number_format(($shipping->order_vtokens - $shipping->merchant_charge_vtoken - $shipping->cus_service_charge_value - $shipping->cus_platform_charge_value), 4) }}</th>
                    <th class='text-center'>{{ number_format((($shipping->order_vtokens - $shipping->merchant_charge_vtoken - $shipping->cus_service_charge_value - $shipping->cus_platform_charge_value) * $shipping->currency_rate), 2) }}</th>
                </tr>
            </table>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-sm-12 text-center">
            <h4>{{trans('localize.invoicedetail')}}</h4>
            <span>{{trans('localize.thisshipmentcontainfolowingitem')}}.</span>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 text-center">
            <br/>
            <h5>{{ $shipping->pro_title_en }}</h5>
            <table class="table">
                <tr style="border-bottom:1px solid #666;">

                    @if($shipping->order_type == 3)
                    <td  width="13%" align="center">{{trans('localize.type')}}</td>
                    @endif

                    @if(!empty($shipping->order_attributes))
                        @foreach(json_decode($shipping->order_attributes, true) as $attribute => $item)
                        <td align="center">{{ $attribute }}</td>
                        @endforeach
                    @endif

                    <td  width="13%" align="center">{{trans('localize.quantity')}}</td>
                    <td  width="13%" align="center" nowrap>{{trans('common.credit_name')}}</td>
                    @if (!empty($remarks))
                        <td  width="" align="center">{{trans('localize.remarks')}}</td>
                    @endif
                </tr>
                <?php $subtotal = $shipping->order_qty * $shipping->order_vtokens; ?>
                <tr>
                    @if($shipping->order_type == 3)
                    <td nowrap width="17%" align="center">
                    @lang('localize.coupon')
                    </td>
                    @endif

                    @if(!empty($shipping->order_attributes))
                        @foreach(json_decode($shipping->order_attributes, true) as $attribute => $item)
                        <td align="center">{{ $item }}</td>
                        @endforeach
                    @endif

                    <td  width="13%" align="center">{{ $shipping->order_qty }} </td>
                    <td  width="13%" align="center">{{ round($shipping->order_vtokens/$shipping->order_qty, 4) }} </td>
                    @if (!empty($remarks))
                        <td align="left">
                            <?php $remarks = json_decode($shipping->remarks); ?>
                            @if (!empty($remarks->name))
                                <small><a href="#">{{trans('localize.name')}} : {{$remarks->name}}</a></small>
                                <br>
                                <small><a href="#">{{trans('localize.idno')}} : {{$remarks->IDno}}</a></small>
                                <br>
                                <small><a href="#">{{trans('localize.handphone')}} : {{$remarks->phone}}</a></small>
                                <br>
                                @if (!empty($remarks->homephone))
                                    <small><a href="#">{{trans('localize.homephone')}} : {{$remarks->homephone}}</a></small>
                                    <br>
                                    <small><a href="#">{{trans('localize.idno')}} : {{$remarks->IDno}}</a></small>
                                    <br>
                                    <small><a href="#">{{trans('localize.handphone')}} : {{$remarks->phone}}</a></small>
                                    <br>
                                    @if (!empty($remarks->homephone))
                                        <small><a href="#">{{trans('localize.homephone')}} : {{$remarks->homephone}}</a></small>
                                        <br>
                                    @endif
                                    @if (!empty($remarks->address))
                                        <small><a href="#">{{trans('localize.address')}} : {{$remarks->address}}</a></small>
                                        <br>
                                    @endif
                                    <small><a href="#">{{trans('localize.email')}} : {{$remarks->email}}</a></small>
                                    <br>
                                @endif
                            </td>
                        @endif
                    @endif

                    </tr>
                </table>

                {{--  @if($shipping->order_type == 3 && !$shipping->coupons->isEmpty())
                <table class="table">
                    <tr style="border-bottom:1px solid #666;">
                        <td style-"width:60%;">@lang('localize.coupon_code')</td>
                        <td>@lang('localize.redemption') / @lang('localize.cancelled')</td>
                        <td>@lang('localize.status')</td>
                    </tr>
                    @foreach($shipping->coupons as $coupon)
                    <tr>
                        <td>{{ $coupon->serial_number }}</td>
                        <td style="width:40%;">
                            @if($coupon->status == 2)
                                {{ \Helper::UTCtoTZ($coupon->redeemed_at) }}
                            @elseif($coupon->status == 3)
                                {{ \Helper::UTCtoTZ($coupon->updated_at) }}
                            @endif
                        </td>
                        <td>
                            @if($coupon->status == 1)
                                @lang('localize.open')
                            @elseif($coupon->status == 2)
                                @lang('localize.redeemed')
                            @elseif($coupon->status == 3)
                                @lang('localize.cancelled')
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </table>
                @endif  --}}
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-sm-6"></div>
            <div class="col-sm-6">
                <?php $totamt = round($subtotal + ($subtotal * ($shipping->pro_inctax / 100))); ?>
                <div class="row">
                    <div class="col-sm-7 text-right">{{trans('localize.totalcredit')}}</div>
                    <div class="col-sm-5"><b><img src="{{ url('/assets/images/icon/icon_meicredit.png') }}" alt="" style="width:25px; height:25px;">{{ $shipping->order_vtokens }}</b></div>
                </div>
            </div>
            <br><br>
            * GST inclusive if applicable
            <br>This  invoice is computer generated and no signature is required.
        </div>
    @else
    <div class="row">
        <div class="col-sm-12 text-center">
            <div class="alert alert-danger">
                Order you're tryin' to access is not valid because it's belong to other merchant. Please contact system admin for more details.
            </div>
        </div>
    </div>
    @endif
</div>

<div class="modal-footer">
    @if ($shipping)
        <a href="{{ url('print_invoice/' . $shipping->order_id) }}" class="btn btn-primary" onclick="window.open('{{ url('print_invoice/' . $shipping->order_id) }}', 'newwindow', 'width=750, height=500'); return false;">{{trans('localize.print')}}</a>
    @endif
    <button data-dismiss="modal" class="btn btn-default" type="button">{{trans('localize.closebtn')}}</button>
</div>
