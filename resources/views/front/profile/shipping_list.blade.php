@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
<div class="ListingTopbar">
	<h4 class="ListingCategory">@lang('localize.myaddress')</h4>
	<a href="{{ url('profile') }}" class="back"><i class="fa fa-angle-left"></i></a>
    </div>
    <div class="ContentWrapper">

        @include('layouts.partials.status')

        <p class="text-success">{{trans('localize.shippingListInfo', ['total' => $shipping->Total()])}}</p>
         @foreach($shipping as $ship)
         <div class="shipping-list" style="width:100%">
            <label class="radio radio-inline">
                <input type="radio" {{ ($ship['isdefault'] == 1)? 'checked' : '' }} >
                @if($ship['isdefault'] == 1)
                <ins></ins>
                @endif
                <span>
                    <address>
                        {{ implode(', ', array_filter([$ship->ship_address1, $ship->ship_address2, $ship->ship_postalcode, $ship->ship_city_name])) }}
                    </address>
                    <p>{{ $ship->ship_name }} {{ $ship->ship_phone }}</p>
                </span>
            </label>
            <div class="action">
                <a href="shippingaddress/edit/{{$ship->ship_id}}">{{trans('localize.edit')}}</a> | <a href="shippingaddress/delete/{{$ship->ship_id}}">{{trans('localize.delete')}}</a>
                @if($ship['isdefault'] == 0)
                <button onclick="location.href='shippingaddress/setdefault/{{$ship->ship_id}}';" class="btn btn-secondary btn-xs">{{ trans('localize.setasdefault')}}</button>
                @endif
            </div>
        </div>
         @endforeach

         @if($shipping->Total() < 5)
            <a class="add-new" href="{{ url('profile/shippingaddress/add') }}">{{trans('localize.addnewshipping')}}</a>
         @endif
    </div>

@endsection