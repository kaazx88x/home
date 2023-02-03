@extends('layouts.front_email')
@section('content')
<tr>
    <td bgcolor="#ffffff" align="center" style="padding: 15px;">
        <table border="0" cellpadding="0" cellspacing="0" width="1000" class="responsive-table">
            <tr>
                <td>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <!-- COPY -->
                            <td style="font-size: 27px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding-copy">Your Order is on the way!</td>
                        </tr>
                        <tr>
                            <td align="left" style="padding: 15px 0 5px 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">
                                Here is your shipment details :
                            </td>
                        </tr>
                        <tr>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size: 12px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;">
                                @if($deliveryBy == 0)
                                <tr>
                                    <th style="width:10%;">Courier</th>
                                    <th>:</th>
                                    <td class="text-left">Self Pickup</td>
                                </tr>
                                <tr>
                                    <th>Remarks</th>
                                    <th>:</th>
                                    <td class="text-left">{{ $trackingNo }}</td>
                                </tr>
                                @else
                                <tr style="width:10%;">
                                    <th>Courier</th>
                                    <th>:</th>
                                    <td class="text-left">{{ $courierName }}</td>
                                </tr>
                                <tr>
                                    <th>Tracking Number</th>
                                    <th>:</th>
                                    <td class="text-left">{{ $trackingNo }}</td>
                                </tr>
                                <tr>
                                    <th>Tracking URL</th>
                                    <th>:</th>
                                    <td class="text-left">{{ $courierLink }}</td>
                                </tr>
                                @endif
                            </table>
                        </tr>
                        <tr>
                            <td>
                                <br>
                                <table style="font-size: 12px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" width="100%" cellpadding="5">
                                    <tr>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th nowrap>Mei Point</th>
                                        <th>Total</th>
                                        <th>Shipping Address</th>
                                    </tr>
                                    <tr><td colspan="5"><hr/></td></tr>
                                    @foreach ($checkouts as $checkout)
                                    <tr>
                                        @foreach ($checkout as $c)
                                        <td>{{ $c }}</td>
                                        @endforeach
                                    </tr>
                                      @endforeach
                                    <tr><td colspan="5"><br></td></tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!--[if (gte mso 9)|(IE)]>
        </td>
        </tr>
        </table>
        <![endif]-->
    </td>
</tr>
@stop
