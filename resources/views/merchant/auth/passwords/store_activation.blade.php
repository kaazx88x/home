@extends('layouts.app')

@section('top-header')
@include('layouts.partials.merchant_top_header')
@endsection

@section('content')
<div class="auth-container">
    <div class="inner">
        <div class="logo-container">
            <img src="{{ asset('assets/images/logo.png') }}" class="logo">
        </div>
        @include('layouts.partials.status')
        <h4>@lang('localize.store_activation')</h4>

        <form role="form" method="POST" action="{{ url('/store/activation/'.$token) }}">
            {{ csrf_field() }}

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <i class="fa fa-envelope"></i>
                <input type="email" name="email" class="form-control" placeholder="@lang('localize.email')" value="{{ $email or old('email') }}">
                @if ($errors->has('email'))
                    <span class="help-block">
                        {{ $errors->first('email') }}
                    </span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <i class="fa fa-lock"></i>
                <input type="password" name="password" class="form-control" placeholder="@lang('localize.newpassword')">
                @if ($errors->has('password'))
                    <span class="help-block">
                        {{ $errors->first('password') }}
                    </span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <i class="fa fa-lock"></i>
                <input type="password" name="password_confirmation" class="form-control" placeholder="@lang('localize.confirmpassword')">
                @if ($errors->has('password_confirmation'))
                    <span class="help-block">
                        {{ $errors->first('password_confirmation') }}
                    </span>
                @endif
            </div>

            <div class="action">
                <button type="submit" class="btn btn-primary"><i class="fa fa-refresh"></i> @lang('localize.updatepassword')</button>
            </div>
        </form>
    </div>
</div>
@endsection
