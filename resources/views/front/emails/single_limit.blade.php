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
                            <td align="center" style="font-size: 32px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding-copy">
                                Transaction Over Limit
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">
                                <table style="font-size: 12px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" width="100%" cellpadding="5">
                                    <tr>
                                        <td width="60%"><b>Merchant Name :</b> {{ $merchant_name }}</td>
                                        <td width="40%">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><b>Store Name :</b> {{ $store_name }}</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <br>
                                <table style="font-size: 12px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" width="100%" cellpadding="5">
                                    <tr>
                                        <th>Order Type</th>
                                        <th>Order ID</th>
                                        <th>Transaction ID / Invoice No</th>
                                        <th>Mei Point</th>
                                        <th>Currency</th>
                                        <th>Amount</th>
                                    </tr>
                                    <tr><td colspan="6"><hr/></td></tr>
                                    <tr>
                                        <td align="center">{{ $trans_type }}</td>
                                        <td align="center">{{ $order_id }}</td>
                                        <td align="center">{{ $trans_no }}</td>
                                        <td align="center">{{ $credit }}</td>
                                        <td align="center">{{ $currency }}</td>
                                        <td align="center">{{ $currency_code . ' ' . number_format($amount, 2) }}</td>
                                    </tr>
                                    <tr><td colspan="6"><br></td></tr>
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
