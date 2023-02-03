@extends('layouts.web.master')

@section('header')
@include('layouts.web.header.backend')
@endsection

@section('content')
<div class="ContentWrapper">

    @include('layouts.partials.status')

    <div class="panel-general panel-login active">
        <h4 class="panel-title">@lang('localize.newpassword')</h4>
        <div class="form-general">
            <form role="form" method="POST" action="{{ url('store/password/reset') }}">
            {{ csrf_field() }}

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <img src="{{ asset('assets/images/icon/icon_account.png') }}">
                <input type="email" name="email" class="form-control" placeholder="@lang('localize.email')" value="{{ $email or old('email') }}">
                @if ($errors->has('email'))
                    <span class="help-block">
                        {{ $errors->first('email') }}
                    </span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <img src="{{ asset('assets/images/icon/icon_password.png') }}">
                <input type="password" name="password" class="form-control" placeholder="@lang('localize.newpassword')">
                @if ($errors->has('password'))
                    <span class="help-block">
                        {{ $errors->first('password') }}
                    </span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <img src="{{ asset('assets/images/icon/icon_password.png') }}">
                <input type="password" name="password_confirmation" class="form-control" placeholder="@lang('localize.confirmnewpassword')">
                @if ($errors->has('password_confirmation'))
                    <span class="help-block">
                        {{ $errors->first('password_confirmation') }}
                    </span>
                @endif
            </div>
            <button type="submit" class="btn btn-primary btn-block">@lang('localize.updatepassword')</button>
            <br/>
            <a href="{{ url('store/login') }}">@lang('localize.back')</a>
            </form>
        </div>
    </div>
</div>
@endsection
