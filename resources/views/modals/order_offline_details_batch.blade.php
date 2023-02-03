<div>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <div class="row">
            <div class="col-sm-3"><img src="{{ url('/assets/images/meihome_logo.png') }}" alt="" style="width:auto; height:35px;"></div>
             @if($type_inv == "trans_ref")
             <div class="col-sm-7 text-center" style="vertical-align: middle;"><h3 class="text-uppercase">@lang('localize.transaction_reference')</h3></div>
             @elseif($type_inv == "mer_inv")
             <div class="col-sm-7 text-center" style="vertical-align: middle;"><h3 class="text-uppercase">@lang('localize.merchant_invoice')</h3></div>
             @else
             <div class="col-sm-7 text-center" style="vertical-align: middle;"><h3 class="text-uppercase">@lang('localize.tax_invoice')</h3></div>
             @endif
        </div>
    </div>
    <div class="modal-body" style="height:600px;overflow-y: auto;">
        <?php
        $id ="";
        ?>
        @if ($orders)
            @foreach($orders as $key => $order)
                @if($type_inv == "trans_ref")
                <div class="row">
                    <div class="col-sm-6">
                        <h4>{{ env('COMPANY_NAME') }}</h4>
                        {!! env('COMPANY_ADDRESS') !!}
                        <br><b>@lang('localize.gst_no') : </b> {{ env('GST') }}
                    </div>
                    <div class="col-sm-6">
                        <h4>{{trans('localize.merchant_detail')}}</h4>
                        <b>{{ $order->merchant_name }}</b>
                        {!! ($order->mer_address1) ? '<br>' . $order->mer_address1 : ''!!}
                        {!! ($order->mer_address2) ? '<br>' . $order->mer_address2 : '' !!}
                        {!! ($order->mer_city) ? '<br>' . $order->mer_city : '' !!}
                        <br>{{ $order->zipcode . ' ' . $order->mer_state . ', ' . $order->mer_country }}
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-xs-6">
                        <h4>{{trans('localize.payment_detail')}}</h4>
                        {{-- <strong>{{trans('localize.date')}}: </strong>{{ date('d F Y H:i:s', strtotime($order->created_at)) }}<br/> --}}
                        <b>{{trans('localize.date')}}: </b>{{ date_format($order->created_at, 'd/m/Y') }}<br/>
                        <b>{{trans('localize.offline_order_id')}}: </b>{{ $order->id }}<br/>
                        <b>{{trans('localize.status')}}: </b>
                        @if ($order->status == 0)
                            <td class="text-warning">{{trans('localize.unpaid')}}</td>
                        @elseif ($order->status == 1)
                            <td class="text-navy">{{trans('localize.paid')}}</td>
                        @elseif ($order->status == 2)
                            <td class="text-danger">{{trans('localize.cancel_by_member')}}</td>
                        @elseif ($order->status == 3)
                            <td class="text-danger">{{trans('localize.cancel_by_merchant')}}</td>
                        @elseif ($order->status == 4)
                            <td class="text-info">{{trans('localize.refunded')}}</td>
                        @endif<br/>
                    </div>
                    <div class="col-xs-6">
                        <h4>{{trans('localize.customer_detail')}}</h4>
                        <b>{{trans('localize.name')}}: </b>{{$order->cus_name}}<br/>
                        <b>{{trans('localize.email')}}: </b>{{(!empty($order->cus_email)) ? $order->cus_email : $order->email}}<br/>
                        <b>{{trans('localize.phone')}}: </b>{{$order->cus_phone}}<br/>
                    </div>
                </div>
                <br>
                <hr/>
                <div class="row">
                    <div class="col-sm-12">
                        <table border=0 width="100%">
                            <tr>
                                {{trans('localize.merchant_invoice_no')}} : {{$order->inv_no}}
                            </tr>
                            <tr>
                                <td colspan='5'>&nbsp;</td>
                            </tr>
                            <tr>
                                <th colspan='2'>&nbsp;</th>
                                <td class='text-center'><b>@lang('common.credit_name')</b></td>
                                <td class='text-center'><b>@lang('localize.amount') ({{ $order->currency }})</b></td>
                                <td></td>
                            </tr>
                            <tr>
                                <th style="padding-left:5px;">@lang('common.credit_name')</td>
                                <th>:</th>
                                <td class='text-center'>{{ $order->v_token }}</td>
                                <td class='text-center'>{{ $order->amount }}</td>
                                <td> * </td>
                            </tr>
                            @if($type == 'admin')
                                <tr><td colspan='5'><br/></td></tr>
                                <tr style="border-left:1pt solid #d1cfcf; border-right:1pt solid #d1cfcf; border-top:1pt solid #d1cfcf; padding:1px;">
                                    <th style="padding-left:5px;">{{trans('localize.platform_charges')}} {{$order->merchant_platform_charge_percentage}}%</th>
                                    <th>:</th>
                                    <td class='text-center'>{{ $order->merchant_platform_charge_token }}</td>
                                    <td class='text-center'>{{ round(($order->merchant_platform_charge_token * $order->currency_rate), 2) }}</td>
                                    <td> * </td>
                                </tr>
                                <tr style="border-left:1pt solid #d1cfcf; border-right:1pt solid #d1cfcf; padding:1px;">
                                    <th style="padding-left:5px;">{{trans('localize.gst')}} {{$order->customer_charge_percentage}}%</td>
                                    <th>:</th>
                                    <td class='text-center'>{{ $order->customer_charge_token }}</td>
                                    <td class='text-center'>{{ round(($order->customer_charge_token * $order->currency_rate), 2) }}</td>
                                    <td> * </td>
                                </tr>
                                <tr style="border-left:1pt solid #d1cfcf; border-right:1pt solid #d1cfcf; border-bottom:1pt solid #d1cfcf;">
                                    <th style="padding-left:5px;">{{trans('localize.order_credit_total')}}</td>
                                    <th>:</th>
                                    <td class='text-center'>{{ $order->order_total_token }}</td>
                                    <td class='text-center'>{{ round(($order->order_total_token * $order->currency_rate), 2) }}</td>
                                    <td></td>
                                </tr>
                                <tr><td colspan='5'><br/></td></tr>
                            @endif
                            <tr>
                                <th style="padding-left:5px;">{{trans('localize.merchant_charge')}} {{$order->merchant_charge_percentage}}%</td>
                                <th>:</th>
                                <td class='text-center'>{{ $order->merchant_charge_token }}</td>
                                <td class='text-center'>{{ round(($order->merchant_charge_token * $order->currency_rate), 2) }}</td>
                                <td> * </td>
                            </tr>
                            <tr><td colspan='5'><br/></td></tr>
                            <tr>
                                <th style="padding-left:5px;">{{trans('localize.merchant_earned_credit')}}</td>
                                <th>:</th>
                                <th class='text-center'>{{ $order->v_token - $order->merchant_charge_token }}</th>
                                <th class='text-center'>{{ round((($order->v_token - $order->merchant_charge_token) * $order->currency_rate), 2) }}</th>
                            </tr>
                            <tr><td colspan='5'><br/></td></tr>
                            <tr>
                                <th style="padding-left:5px;">{{trans('localize.remarks')}}</td>
                                <th>:</th>
                                <td colspan='2'>{{$order->remark}}</td>
                            </tr>
                        </table>
                        <br><br>
                        * GST inclusive if applicable
                        <br>This invoice is computer generated and no signature is required.
                    </div>
                </div>
                <br>
                <hr/>
                <?php
                    $id = $id.','.$order->id;
                    $id = ltrim($id, ',');

                ?>
                @elseif($type_inv == "mer_inv")
                <div class="row">
                    <table style="width: 100%">
                        <tr>
                            <td style="text-align: center;"><img src="{{  url('/assets/images/meihome_logo.png') }}" alt="" style="width:auto; height:120px;margin-top:5%"></td>
                            <td style="text-align: center;"><h4>{{ env('COMPANY_NAME') }}<span style="font-size:12px"> ({{ env('COMPANY_REG_NO') }})</span></h4><b>(@lang('localize.gst_id_no') :  {{ env('GST') }})<br>{!! env('COMPANY_ADDRESS') !!} <br> @lang('localize.tel') : {{ env('CONTACT_NO') }} </b></td>
                        </tr>
                    </table>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-sm-6">
                        <b>@lang('localize.merchant_id') : </b>{{ $order->mer_id }}<br>
                        <b>@lang('localize.merchant_name') : </b>{{ $order->merchant_name }}<br>
                        <b>@lang('localize.address') : </b>{{ ($order->mer_address1) ? '' . $order->mer_address1 : ''}}
                        {!! ($order->mer_address2) ? '<br>' . $order->mer_address2 : '' !!}
                        {!! ($order->mer_city) ? '<br>' . $order->mer_city : '' !!}
                        <br>{{ $order->zipcode . ' ' . $order->mer_state . ', ' . $order->mer_country }}<br>
                        <b>@lang('localize.contact_number') : </b>{{ $order->mer_phone }}<br>
                        <b>@lang('localize.gst_no') : </b>{{ $order->mer_gst_bank}}
                    </div>
                    <div class="col-sm-6" style="border:1px solid black;margin-top:5%">
                        <b>@lang('localize.invoice_no')  :</b> OFA{{ $order->tax_inv_no }}<br>
                        <b>@lang('localize.date')        :</b>{{ $order->created_at }}<br>
                        <b>@lang('localize.Page')        :</b>1<br>
                    </div>
                </div>
                <br>
                <br>
                @php
                    $total = round(($order->merchant_charge_token) * $order->currency_rate, 2);
                    $amount = round($total / 1.06, 2);
                    $tax = round($total - $amount, 2);
                @endphp
                <div class="row">
                    <div class="col-sm-12">
                        <table border=1 width="100%">
                            <tr>
                                <th colspan='3' class='text-center'><b>{{trans('localize.description')}}</b></th>
                                <td colspan='2'  class='text-center'><b>@lang('localize.amount') ({{ $order->currency }})</b></td>
                            </tr>
                            <tr>
                                <th colspan='3'>&nbsp;</th>
                                <td class='text-center' colspan="2"</td>
                            </tr>
                            <tr>
                                <th colspan="3" style="padding-left:5px;">{{ trans('localize.merchant_admin_fee') }}</td>
                                <td colspan="2" class="text-center">{{ number_format($total, 2) }}</td>
                            </tr>
                            <tr>
                                <th colspan='3'>&nbsp;</th>
                                <td class='text-center' colspan="2"></td>
                            </tr>
                            <tr>
                            <th colspan='3' style="padding-left:5px;">@lang('localize.merchant_invoice_no') : {{$order->inv_no}}</th>
                                <td class='text-center' colspan="2"</td>
                            </tr>
                            <tr>
                                <th colspan='3'>&nbsp;</th>
                                <td class='text-center' colspan="2"></td>
                            </tr>
                            <tr>
                                <th colspan='3'>&nbsp;</th>
                                <td class='text-center' colspan="2"></td>
                            </tr>
                            <tr>
                                <th colspan='3' style="padding-left:5px;">@lang('localize.grand_total') (@lang('localize.including_gst'))</th>
                                <td class='text-center' colspan="2"><b>{{ number_format($total, 2) }}</b></td>
                            </tr>
                        </table>
                        <br>
                        <br>
                        @if($order->created_at >= $date)
                        <table width="100%">
                            <tr>
                                <th colspan='5'></td>
                            </tr>
                            <tr>
                                <th style="padding-left:5px;" class="text-center"><u>{{trans('localize.GST_summary')}}</u></td>
                                <th style="padding-left:5px;" class="text-center"><u>@lang('localize.amount') ({{ $order->currency }})</u></td>
                                <th style="padding-left:5px;" class="text-center"><u>@lang('localize.tax') ({{ $order->currency }})</u></td>
                                <td colspan='2' width="30%"></td>
                            </tr>
                            <tr>
                                <th style="padding-left:5px;" class="text-center" style="text-align:center">{{trans('localize.sr')}}</td>
                                <th style="padding-left:5px;" class="text-center" style="text-align:center">{{ number_format($amount, 2) }}</td>
                                <th style="padding-left:5px;" class="text-center" style="text-align:center">{{ number_format($tax, 2) }}</td>
                                <td colspan='2' width="30%"></td>
                            </tr>
                        </table>
                        @endif

                        <br><br>
                        * GST inclusive if applicable
                        <br>This invoice is computer generated and no signature is required.
                    </div>
                </div>
                <br>
                <hr/>
                <?php
                    $id = $id.','.$order->id;
                    $id = ltrim($id, ',');
                ?>
                @else
                    <div class="row">
                        <table style="width: 100%">
                            <tr>
                                <td style="text-align: center;"><img src="{{  url('/assets/images/meihome_logo.png') }}" alt="" style="width:auto; height:120px;margin-top:5%"></td>
                                <td style="text-align: center;"><h4>{{ env('COMPANY_NAME') }}<span style="font-size:12px"> ({{ env('COMPANY_REG_NO') }})</span></h4><b>(@lang('localize.gst_id_no') :  {{ env('GST') }})<br>{!! env('COMPANY_ADDRESS') !!} <br> @lang('localize.tel') : {{ env('CONTACT_NO') }} </b></td>
                            </tr>
                        </table>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-xs-12">
                            <h2 class="text-uppercase" style="text-align:center">@lang('localize.tax_invoice')</h2>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-12">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width:50%">
                                    <b>@lang('localize.bill_to') : </b>{{$address['company_name']}}<br>
                                    <b>@lang('localize.address') : </b>{{$address['address']}}<br>
                                    <b>@lang('localize.contact_number') : </b>{{$address['tel']}}<br>
                                    <b>@lang('localize.gst_no') : </b>{{$address['gst_no']}}<br>
                                    </td>
                                    <td style="margin-top:5%;width:28.5%;margin-left:20.5%">
                                        <div style="border:1px solid black">
                                            <b>@lang('localize.invoice_no')  :</b> OFC{{ $order->tax_inv_no }}<br>
                                            <b>@lang('localize.date')        :</b>{{ $order->created_at }}<br>
                                            <b>@lang('localize.Page')        :</b>1<br>
                                        <div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <br>
                    @php
                        $total = round(($order->merchant_platform_charge_token + $order->customer_charge_token) * $order->currency_rate, 2);
                        $amount = round($total / 1.06, 2);
                        $tax = round($total - $amount, 2);
                    @endphp
                    <div class="row">
                        <div class="col-xs-12">
                            <table width="100%" style="border: 1px solid;border-collapse: collapse;">
                                <tr >
                                    <th colspan='3' class='text-center' style="border: 1px solid;text-align:center"><b>{{trans('localize.description')}}</b></th>
                                    <td colspan='2' class='text-center' style="border: 1px solid;text-align:center"><b>@lang('localize.amount') ({{ $order->currency }})</b></td>
                                </tr>
                                <tr>
                                    <th colspan='3' style="border: 1px solid;">&nbsp;</th>
                                    <td class='text-center' colspan="2" style="border: 1px solid;"></td>
                                </tr>
                                <tr>
                                    <th colspan="3" style="border: 1px solid;">{{ trans('localize.offline_commission') }}</td>
                                    <td colspan="2" class="text-center" style="border: 1px solid;border: 1px solid;text-align:center">{{ number_format($total, 2) }}</td>
                                </tr>
                                <tr>
                                    <th colspan='3' style="border: 1px solid;">&nbsp;</th>
                                    <td class='text-center' colspan="2" style="border: 1px solid;"></td>
                                </tr>
                                <tr>
                                <th colspan='3' style="padding-left:5px;border: 1px solid;">@lang('localize.merchant_invoice_no') : {{$order->inv_no}}</th>
                                    <td class='text-center' colspan="2" style="border: 1px solid;"></td>
                                </tr>
                                <tr>
                                    <th colspan='3' style="border: 1px solid;">&nbsp;</th>
                                    <td class='text-center' colspan="2" style="border: 1px solid;"></td>
                                </tr>
                                <tr>
                                    <th colspan='3' style="border: 1px solid;">&nbsp;</th>
                                    <td class='text-center' colspan="2" style="border: 1px solid;"></td>
                                </tr>
                                <tr>
                                    <th colspan='3' style="padding-left:5px;" style="border: 1px solid;">@lang('localize.grand_total') (@lang('localize.including_gst')) </th>
                                    <td colspan="2" style="border: 1px solid;text-align:center"><b>{{ number_format($total, 2) }}</b></td>
                                </tr>
                            </table>
                            <br>
                            <br>
                            @if($order->created_at >= $date)
                            <table width="100%">
                                <tr>
                                    <th colspan='5'></td>
                                </tr>
                                <tr>
                                    <th style="padding-left:5px;" class="text-center"><u>{{trans('localize.GST_summary')}}</u></td>
                                    <th style="padding-left:5px;" class="text-center"><u>@lang('localize.amount') ({{ $order->currency }})</u></td>
                                    <th style="padding-left:5px;" class="text-center"><u>@lang('localize.tax') ({{ $order->currency }})</u></td>
                                    <td colspan='2' width="30%"></td>
                                </tr>
                                <tr>
                                    <th style="padding-left:5px;" class="text-center">{{trans('localize.sr')}}</td>
                                    <th style="padding-left:5px;" class="text-center">{{ number_format($amount, 2) }}</td>
                                    <th style="padding-left:5px;" class="text-center">{{ number_format($tax, 2) }}</td>
                                    <td colspan='2' width="30%"></td>
                                </tr>
                            </table>
                            @endif

                            <br><br>
                            * GST inclusive if applicable
                            <br>This invoice is computer generated and no signature is required.
                        </div>
                    </div>
                    <?php
                    $id = $id.','.$order->id;
                    $id = ltrim($id, ',');
                    $add = $address['name'];
                    ?>
                @endif
            @endforeach
        @else
            <div class="alert alert-danger">
                Order you're tryin' to access is not valid because it's belong to other merchant. Please contact system admin for more details.
            </div>
        @endif
        @if($type_inv == "trans_ref")
        <div class="modal-footer">
            <a href="{{ url('pdf_order_trans_ref/'.$type_inv.'/'.$id.'/'.$type) }}" class="btn btn-primary">{{trans('localize.pdf')}}</a>
            <a href="{{ url('print_order_trans_ref/'.$type_inv.'/'.$id.'/'.$type) }}" class="btn btn-primary" onclick="window.open('{{ url('print_order_trans_ref/'.$type_inv.'/'.$id.'/'.$type) }}', 'newwindow', 'width=750, height=500'); return false;">{{trans('localize.print')}}</a>
            <button data-dismiss="modal" class="btn btn-default" type="button">{{trans('localize.close_')}}</button>
        </div>
        @elseif($type_inv == "mer_inv")
        <div class="modal-footer">
            <a href="{{ url('pdf_order_trans_ref/'.$type_inv.'/'.$id.'/'.$type) }}" class="btn btn-primary">{{trans('localize.pdf')}}</a>
            <a href="{{ url('print_order_trans_ref/'.$type_inv.'/'.$id.'/'.$type) }}" class="btn btn-primary" onclick="window.open('{{ url('print_order_trans_ref/'.$type_inv.'/'.$id.'/'.$type) }}', 'newwindow', 'width=750, height=500'); return false;">{{trans('localize.print')}}</a>
            <button data-dismiss="modal" class="btn btn-default" type="button">{{trans('localize.close_')}}</button>
        </div>
        @else
        <div class="modal-footer">
            <a href="{{ url('pdf_order_trans_ref/'.$type_inv.'/'.$id.'/'.$type.'/'.$add) }}" class="btn btn-primary">{{trans('localize.pdf')}}</a>
            <a href="{{ url('print_order_trans_ref/'.$type_inv.'/'.$id.'/'.$type.'/'.$add) }}" class="btn btn-primary" onclick="window.open('{{ url('print_order_trans_ref/'.$type_inv.'/'.$id.'/'.$type.'/'.$add) }}', 'newwindow', 'width=750, height=500'); return false;">{{trans('localize.print')}}</a>
            <button data-dismiss="modal" class="btn btn-default" type="button">{{trans('localize.close_')}}</button>
        </div>
        @endif
    </div>
</div>


