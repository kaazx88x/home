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
                            <td style="font-size: 32px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding-copy">Order is Completed! Customer has received the order.</td>
                        </tr>
                        <tr>
                            <td>
                                <br>
                                <table style="font-size: 12px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" width="100%" cellpadding="5">
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Mei Point</th>
                                        <th>Total</th>
                                        <th>Order Date</th>
                                    </tr>
                                    <tr><td colspan="7"><hr/></td></tr>
                                    @foreach ($checkouts as $od)
                                    <tr>
                                        <td>{{$od['trans_id']}}</td>
                                        <td>{{$od['name']}}</td>
                                        <td>{{$od['quantity']}}</td>
                                        <td>{{$od['vtoken']}}</td>
                                        <td>{{($od['vtoken'] * $od['quantity'])}}</td>
                                        <td>{{$od['date']}}</td>
                                    </tr>
                                    @endforeach
                                    <tr><td colspan="7"><br></td></tr>
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
