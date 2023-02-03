@extends('layouts.front_email')
@section('content')
<tr>
    <td bgcolor="#ffffff" align="center" style="padding: 15px;">
        <table border="0" cellpadding="0" cellspacing="0" width="500" class="responsive-table">
            <tr>
                <td>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <!-- COPY -->
                            <td align="center" style="font-size: 25px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding-copy">Reset payment secure code</td>
                        </tr>
                        <tr>
                            <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">
                                We received a request to reset your payment secure code for your @lang('common.mall_name') account : {{$email}}. Kindly please change back your payment secure code for security reason. Please contact our administrator if your did not perform this action.
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <!-- BULLETPROOF BUTTON -->
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td align="center" style="padding-top: 25px;" class="padding">
                                            <table border="0" cellspacing="0" cellpadding="0" class="mobile-button-container">

                                                <tr>
                                                    <td align="center">
                                                        <span style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #000; text-decoration: none; border-radius: 3px; padding: 15px 25px; display: inline-block;">Temporary Payment secure code : {{$securecode}}</span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
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
