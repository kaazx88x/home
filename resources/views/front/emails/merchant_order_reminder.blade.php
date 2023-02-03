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
                            <td style="font-size: 32px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding-copy">Orders Reminder!</td>
                        </tr>
                        <tr>
                            <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">
                                You still have {{$total}} order(s) pending on your acceptance.
                                <br/>Please login <a href="{{ url('merchant') }}" style='color: #F7D774;text-decoration: none;'>Merchant Portal</a> : Merchant Account to accept the order.
                                <br/>Please note that these orders will be canceled tomorrow if you haven't accepted.
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
