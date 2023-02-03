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
                            <td align="center" style="font-size: 23px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 20px;" class="padding-copy">
                                {{ ucfirst($detail['limit_type']) }} Transaction Limit Alert Notification<br>
                                @if($detail['limit_type'] == 'single')
                                单笔交易限额提醒
                                @elseif($detail['limit_type'] == 'daily')
                                每日交易限额提醒
                                @elseif($detail['limit_type'] == 'weekly')
                                每周交易限额提醒
                                @elseif($detail['limit_type'] == 'monthly')
                                每月交易限额提醒
                                @elseif($detail['limit_type'] == 'yearly')
                                每年交易限额提醒
                                @endif

                                <p style="font-size: 15px;">
                                    This email is generated when the transaction made has reach it's limit.<br>
                                    当交易达到限额时，系统会自动发出此电子邮件。
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size: 12px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 10px;" width="100%">

                                @if($user_type == 'merchant')
                                <b>Merchant Name / 商家名称 :</b> {{ $merchant->merchantName() }} <br>
                                <b>Store Name / 商铺名称 :</b> {{ $store->stor_name }} <br>
                                @endif

                                <p>
                                @if($detail['block_type'] == 'amount')
                                    @if($detail['limit_type'] != 'single')
                                    <b>Current Amount / 当前数额 :</b> {{ $currency }}{{ $detail['current'] }} <br>
                                    @endif
                                    <b>Alert Amount / 警惕数额:</b> {{ $currency }}{{ $detail['alert_when'] }} <br>
                                @else
                                    @if($detail['limit_type'] != 'single')
                                    <b>Current Number of Transaction / 当前交易次数 :</b> {{ $detail['current'] }} <br>
                                    @endif
                                    <b>Transaction Limit / 交易限额 :</b> {{ $detail['alert_when'] }} transaction(s) / 次交易 <br>
                                @endif
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
