@extends('layouts.front_email')
@section('content')
<tr>
    <td bgcolor="#ffffff" align="center" style="padding: 15px;">
        <table border="0" cellpadding="0" cellspacing="0" width="500" class="responsive-table">
            <tr>
                <td>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666; text-align:left;" class="padding-copy">
                                <p>Hello,</p>
                                <p>Thank you for choosing {{ config('app.name') }}</p>
                                <p>We have received your enquiries, we will respond to your enquiries immediately!</p>
                            </td>
                        </tr>
                        <tr>
                            <td align="left" style="padding: 20px 0 0 0; font-size: 14px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666; text-align:left" class="padding-copy">
                                <p>Enquiry details</p>
                                <p>
                                    Subject Heading : {{$details['subject']}} <br>
                                    Order reference : {{$details['order_reference']}} <br>
                                    Message : {{$details['message']}}<br>
                                </p>
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
