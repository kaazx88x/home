@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.backend')
@endsection

@section('content')
<div class="ContentWrapper">

    @include('layouts.partials.status')

    <div class="panel-general panel-login active">
        <h4 class="panel-title">@lang('localize.admin_login')</h4>
        <div class="form-general">
            <form role="form" method="POST" action="{{ url('admin/login') }}">
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
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                    <img src="{{ asset('assets/images/icon/captcha.png') }}">
                    <input type="text" name="captcha" class="form-control" placeholder="@lang('localize.captcha')" style="width: 100%; height: 67px; font-size: 28px; text-align: center;" maxlength="6">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <p class="captcha">{!!captcha_img('flat')!!}</p>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">@lang('localize.login')</button>
            </form>
            <a href="{{ url('admin/password/reset') }}" class="btn btn-link btn-block ">@lang('localize.forgot')</a>
        </div>
    </div>
</div>
@endsection
@section('styles')
<style>
p.captcha > img {
    width:100% !important;
    height:100% !important;
    display:block !important;
    }
</style>
@endsection
