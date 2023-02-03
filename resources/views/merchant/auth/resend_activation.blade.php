@extends('layouts.app')

@section('top-header')
@include('layouts.partials.merchant_top_header')
@endsection

@section('content')
<div class="auth-container">
    <div class="inner">
        <!--div class="logo-container">
            <img src="{{ asset('assets/images/meihome_logo.png') }}" class="logo">
        </div-->
        @include('merchant.common.notifications')
        <h4 class="merchant">@lang('localize.resend_activation_email')</h4>

        <form role="form" method="POST" action="{{ url('/merchant/resend/activation', [$id]) }}">
            {{ csrf_field() }}
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="E-Mail Address" required="" value="{{ old('email') }}">
            </div>

            <div class="action">
                <button type="submit" class="btn btn-primary">@lang('localize.submit')</button>
            </div>
        </form>
    </div>
</div>
@endsection
