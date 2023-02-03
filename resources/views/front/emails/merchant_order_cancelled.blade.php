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
                            <td style="font-size: 32px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding-copy">Orders Is Canceled!</td>
                        </tr>
                        <tr>
                            <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">
                                Sorry to inform that your order as below has been canceled because you have not accept the order within time limit.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <br>
                                <table style="font-size: 12px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" width="100%" cellpadding="5">
                                    <tr>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Mei Point</th>
                                    </tr>
                                    <tr><td colspan="3"><hr/></td></tr>

                                    <tr>
                                        <td>{{ $product_title }}</td>
                                        <td>{{ $order_qty }}</td>
                                        <td>{{ $order_vtokens }}</td>
                                    </tr>
                                    <tr><td colspan="3"><br></td></tr>
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
