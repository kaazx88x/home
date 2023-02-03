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
                                Merchant Withdrawal Receipt
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">
                                <table style="font-size: 12px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" width="100%" cellpadding="5">
                                    <tr>
                                        <td width="60%"><b>Paid To :</b> {{ $bank_acc_name }}</td>
                                        <td width="40%"><b>Receipt Number :</b> {{ $receipt_no }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Bank Name :</b> {{ $bank_name }}</td>
                                        <td><b>Payment Date :</b> {{ $payment_date }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Account Number :</b> {{ $bank_acc_no }}</td>
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
                                        <th>Mei Point</th>
                                        <th>Currency Rate</th>
                                        <th>Amount Paid</th>
                                    </tr>
                                    <tr><td colspan="3"><hr/></td></tr>
                                    <tr>
                                        <td align="center">{{ $credit }}</td>
                                        <td align="center">{{ $currency }}</td>
                                        <td align="center">{{ $currency_code . ' ' . $amount }}</td>
                                    </tr>
                                    <tr><td colspan="3"><br></td></tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">
                                <table style="font-size: 12px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" width="100%" cellpadding="5">
                                    <tr>
                                        <td width="60%"><b>Withdraw By :</b> {{ $withdraw_by }}</td>
                                        <td width="40%">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><b>Withdrawal Date :</b> {{ $withdraw_date }}</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td><b>Payment Approve By :</b></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>{{ $approve_by }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table><br/><br/>* This receipt is computer generated and no signature is required.
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
