
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
@extends('layouts.mail')
@section('content')
<table border="0" cellspacing="0" cellpadding="0" width="600" align="center" >
    <tr style='background: #393939!important;border-bottom: 1px solid #2B2B2B !important;'>
        <td style="height:20px;">&nbsp;</td>
    </tr>
    <tr style="background: rgba(34, 34, 34, 0.8) url('{{url('/')}}/images/diwaliblock-bkg.png') repeat scroll center bottom;box-shadow: 0 2px 15px #2B2B2B;">
        <td align="center"><img src="{{url('/')}}/assets/logo/svmall.png"  height="90" alt="'.$site_name.'" style="margin:0 30px 20px 40px;"/>          </td>
    </tr>
    <tr>
        <td style="padding: 1px 10px;border:1px solid #c79810;height:30px;"><b>Merchant Account created successfully for your mail id.</b></td>
    </tr>
    <tr>
        <td style=" margin:0 auto; font-size:16px;text-align:left; font-family:Arial, Helvetica, sans-serif; padding:10px 10px 10px;">
        <table  cellspacing="10">
            <tr>
            <th colspan="3" ><h4 style="color:#F60;" > Your Login Credentials Are: </h4> </th>
            </tr>
            <tr>
                <th>Username</th>
                <th>:</th>
                <td >{{ $firstname }}</td>
            </tr>
            <tr>
                <th>Password</th>
                <th>:</th>
                <td >{{ $password }}</td>
            </tr>
            </table>

             <div style="text-align: center;">
                <a href="{{ url('merchant') }}" style="font-size:18px; font-weight: bold; font-family:sans-serif; text-decoration: none; line-height:40px; width:100%; display:inline-block">
                    <table cellspacing="0" cellpadding="0" style="margin: 30px auto;" width="300">
                        <tr>
                            <td align="center" width="300" height="40" bgcolor="#c79810" style="-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; color: #ffffff; display: block; background: linear-gradient( #c79810, #f7d774 ) !important;">
                                <span style="color: #ffffff; font-size:20px; font-weight: bold; font-family:sans-serif; text-decoration: none; line-height:40px;">Login To Your Account Now</span>
                            </td>
                        </tr>
                    </table>
                </a>
            </div>

            <table cellspacing="10">
                <tr>
                <td colspan="3" >&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" >Your sincerely,<br/>MeiHome</td>
            </tr>
        </table>
        </td>
    </tr>
    <tr style='background: #393939!important;border-bottom: 1px solid #2B2B2B !important;'>
        <td style="height:50px;text-align:center;font-family:Arial, Helvetica, sans-serif; font-size:14px;">
        <a href="{{url('/')}}" style='color: #F7D774;text-decoration: none;'>&copy; Copyright {{ date("Y") }} MeiHome</a></td>
    </tr>
</table>
@endsection
