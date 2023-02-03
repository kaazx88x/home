@extends('merchant.layouts.email')

@section('content')
<tr>
    <td class="content-block">
        Hello {{$merchant->name}},
    </td>
</tr>
<tr>
    <td class="content-block">
        Welcome to {{env('SITE_NAME')}} administrator site. We have created an account for you. Below is your login username.
    </td>
</tr>
<tr>
    <td class="content-block">
        <strong>{{$merchant->username}}</strong>
    </td>
</tr>
<tr>
    <td class="content-block">
        In order to access the administrator site of {{env('SITE_NAME')}}, you need to click the button below to reset your password.
    </td>
</tr>
<tr>
    <td class="content-block">
        <a href="{{ url('merchant/password/reset/'.$token) }}" class="btn-primary">Reset Password</a>
    </td>
</tr>
@endsection