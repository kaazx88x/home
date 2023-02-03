@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
	<div class="ListingTopbar">
        <h4 class="ListingCategory">@lang('localize.checkout_result')</h4>
        <a href="/carts" class="back"><i class="fa fa-angle-left"></i></a>
    </div>

    <div class="ContentWrapper">
        <div class="CheckoutStatus error">
            <img src="{{ asset('assets/images/icon_error.png') }}">
            <h4>@lang('localize.checkout_fail')</h4>
            <p>@lang('localize.transDetail_failed')</p>
            <a href="/carts" class="btn btn-primary">@lang('localize.return_checkout')</a>
        </div>
    </div>
@endsection

@section('script')
@endsection