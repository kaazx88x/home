@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
    <div class="ContentWrapper">
        <div class="alert alert-success text-center">
            <img src="{{ asset('assets/images/icon/icon_success.png') }}">
            <h4>@lang('localize.member.account_activation.success')</h4>
            <p>@lang('localize.member.account_activation.msg')</p>
            <br/>
            <a class="btn btn-primary" href="{{ url('login') }}">@lang('localize.login')</a>
        </div>
    </div>
@endsection