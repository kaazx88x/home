@extends('layouts.web.master')

@section('header')
@include('layouts.web.header.backend')
@endsection

@section('content')
<div class="ContentWrapper">

    @include('layouts.partials.status')

    <div class="panel-general panel-login active">
        <h4 class="panel-title">@lang('localize.merchant_login')</h4>

        <div class="login-selection">
            <a href="/merchant/login" class="active">@lang('localize.login_as_merchant')</a>
            <a href="/store/login" class="">@lang('localize.login_as_store_user')</a>
        </div>

        <div class="form-general">
            <form role="form" method="POST" action="{{ url('merchant/login') }}">
            {{ csrf_field() }}
            <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                <img src="{{ asset('assets/images/icon/icon_account.png') }}">
                <input type="text" name="username" class="form-control" placeholder="@lang('localize.username')" value="{{ old('username') }}">
                @if ($errors->has('username'))
                    <span class="help-block">
                        {{ $errors->first('username') }}
                    </span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <img src="{{ asset('assets/images/icon/icon_password.png') }}">
                <input type="password" name="password" class="form-control" placeholder="@lang('localize.password')">
                @if ($errors->has('password'))
                    <span class="help-block">
                        {{ $errors->first('password') }}
                    </span>
                @endif
            </div>
            <button type="submit" class="btn btn-primary btn-block">@lang('localize.login')</button>
            </form>
            <a href="{{ url('merchant/password/reset') }}" class="btn btn-link btn-block ">@lang('localize.forgot')</a>
            {{--  <div class="other-action">
                @lang('localize.noaccount') <a href="{{ url('merchant/register') }}" style="text-decoration:underline;">@lang('localize.register_now')</a>
            </div>  --}}
        </div>
    </div>
</div>
@endsection
