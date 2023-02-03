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
                            <td align="center" style="font-size: 32px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding-copy">One More Step</td>
                        </tr>
                        <tr>
                            <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">
                                Thank you for signing up an account with @lang('common.mall_name'). Before you start experiencing our shopping platform, please click the button below to activate your @lang('common.mall_name') Account. This account is registered by our administrator, please change your password for security reason.
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
                                                        <span style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #000; text-decoration: none; border-radius: 3px; padding: 15px 25px; display: inline-block;">Username : {{$username}}</span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td align="center">
                                                        <span style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #000; text-decoration: none; border-radius: 3px; padding: 15px 25px; display: inline-block;">Temporary Password : {{$password}}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align="center" style="border-radius: 3px;" bgcolor="#256F9C">
                                                        <a href="{{$activation_link}}" target="_blank" style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; border-radius: 3px; padding: 15px 25px; border: 1px solid #256F9C; display: inline-block;" class="mobile-button">Click Here To Activate</a>
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
