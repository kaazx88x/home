@extends('layouts.app')

@section('top-header') 
@include('layouts.partials.front_top_header') 
@endsection 

@section('content')
<div class="content">
    <div class="container">
        <div class="error-panel">
            <h1>404</h1>
            <img src="{{ asset('assets/images/error-404-icon.png') }}">
            <h4>@lang('localize.page_not_found')</h4>
            <a href="{{ url('/') }}" class="btn btn-primary">@lang('localize.back_to_home')</a>
        </div>
    </div>
</div>
@endsection
