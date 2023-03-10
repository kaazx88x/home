@extends('layouts.front_email')
@section('content')
<tr>
    <td bgcolor="#ffffff" align="center" style="padding: 15px;">
        <table border="0" cellpadding="0" cellspacing="0" width="1000" class="responsive-table">
            <tr>
                <td>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        @php
                            \App::setLocale('en');
                        @endphp
                        <tr>
                            <!-- COPY -->
                            <td align="center"  style="font-size: 27px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding-copy">{{ trans('localize.topup_content.type.' . $type) }} Topup!</td>
                        </tr>
                        <tr>
                            <td align="center" style="padding: 15px 0 5px 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">
                                You have recently top-up your {{ trans('localize.topup_content.type.' . $type) }}
                            </td>
                        </tr>
                        <tr>
                             <td align="center" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">
                                <b>From Account</b>: {{ $from }}<br/>
                                <b>Topup Amount</b>: {{ $addedvalue }}<br/>
                                <b>Your Updated Mei Point</b>: <br/>
                                @foreach ($wallets as $cw)
                                    - {{ trans('localize.topup_content.wallet.' . $cw->wallet->name_en) . ' : ' . $cw->credit }}<br/>
                                @endforeach
                                <br/><br/>
                                Start using your Mei Point in <a href="{{ env('APP_URL') }}">@lang('common.mall_name')</a>.
                            </td>
                        </tr>
                        <tr>
                            <td><hr/></td>
                        </tr>
                        @php
                            \App::setLocale('cn');
                        @endphp
                        <tr>
                            <!-- COPY -->
                            <td align="center"  style="font-size: 27px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding-copy">{{ trans('localize.topup_content.type.' . $type) }}??????!</td>
                        </tr>
                        <tr>
                            <td align="center" style="padding: 15px 0 5px 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">
                                ????????????????????????:
                            </td>
                        </tr>
                        <tr>
                             <td align="center" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">
                                <b>?????????</b>: {{ $from }}<br/>
                                <b>????????????</b>: {{ $addedvalue }}<br/>
                                <b>?????????????????????</b>:<br/>
                                @foreach ($wallets as $cw)
                                    - {{ trans('localize.topup_content.wallet.' . $cw->wallet->name_en) . ' : ' . $cw->credit }}<br/>
                                @endforeach
                                <br/><br/>
                                ???????????????<a href="{{ env('APP_URL') }}">????????????</a>?????????????????????
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
@php
    \App::setLocale('en');
@endphp
@stop