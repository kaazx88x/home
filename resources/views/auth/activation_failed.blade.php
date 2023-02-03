@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
    <div class="ContentWrapper">
        <div class="alert alert-danger text-center">
            <img src="{{ asset('assets/images/icon_error.png') }}">
            <h4>@lang('localize.error'), @lang('localize.invalid_token')</h4>
            <br/>
            <a class="btn btn-primary" href="{{ url('/') }}">@lang('localize.back')</a>
        </div>
    </div>
@endsection