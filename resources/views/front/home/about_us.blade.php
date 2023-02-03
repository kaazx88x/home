@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
<div class="ListingTopbar">
    <h4 class="ListingCategory">@lang('localize.about_mall')</h4>
    <a href="javascript:void(0)" class="back goBack"><i class="fa fa-angle-left"></i></a>
</div>

<div class="ContentWrapper">
    <div class="panel-general about-list">
        <img src="assets/images/icon/icon_address.png">
        <div class="about-content">
            <h4>@lang('localize.address')</h4>
            <p>37, Jalan Setia Gemilang U13/BG, Seksyen U13, Setia Alam, 40170 Shah Alam, Selangor, Malaysia.</p>
        </div>
    </div>

    <div class="panel-general about-list">
        <img src="assets/images/icon/icon_phone.png">
        <div class="about-content">
            <h4>@lang('localize.contact_number')</h4>
            <p>0123583837</p>
        </div>
    </div>

    <div class="panel-general about-list">
        <img src="assets/images/icon/icon_email.png">
        <div class="about-content">
            <h4>@lang('localize.email_add')</h4>
            <p>info@meihome.asia</p>
        </div>
    </div>
</div>
@endsection
