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
                            <td style="font-size: 32px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding-copy">Orders Is Completed!</td>
                        </tr>
                        <tr>
                            <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">
                                Order below is completed. The customer has received the order. {{ $merchant_earn_vtokens }} of Mei Point is credited into your merchant account.
                            </td>
                        </tr>
                        <tr>
                            <td>
                               <br>
                                <table style="font-size: 12px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" width="100%" cellpadding="5">
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Total Mei Point</th>
                                        <th>@lang('common.mall_name') Commission</th>
                                        <th>You Make</th>
                                    </tr>
                                    <tr><td colspan="6"><hr/></td></tr>

                                    <tr>
                                        <td>{{$transaction_id}}</td>
                                        <td>{{ $product_title }}</td>
                                        <td>{{ $order_qty }}</td>
                                        <td>{{ $order_vtokens }}</td>
                                        <td>{{ $order_commission }}</td>
                                        <td>{{ $merchant_earn_vtokens }}</td>
                                    </tr>
                                    <tr><td colspan="4"><br></td></tr>
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

@endsection
