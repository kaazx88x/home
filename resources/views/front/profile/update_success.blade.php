@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
	<div class="ListingTopbar">
        <h4 class="ListingCategory">@lang('localize.updateAccount')</h4>
        <a href="/carts" class="back"><i class="fa fa-angle-left"></i></a>
    </div>

    <div class="ContentWrapper">
        <div class="CheckoutStatus success">
            <img src="{{ asset('assets/images/icon_success.png') }}">
            <h4>@lang('localize.update_success')</h4>
            <p>@lang('localize.thanks')</p>
            @if(\Helper::agent('mobile'))
                <a href="meihome://" class="btn btn-primary">@lang('localize.conShopping')</a>
            @else
                <a href="/" class="btn btn-primary">@lang('localize.conShopping')</a>
            @endif
        </div>
    </div>
@endsection

@section('styles')
<style>
h3{
    color:#404040;
    font-family: "Open Sans", Microsoft Yahei, sans-serif;
}
</style>
@endsection