@extends('layouts.app')

@section('top-header') 
@include('layouts.partials.front_top_header') 
@endsection 

@section('content')
<div class="content">
    <div class="container">
        <div class="error-panel">
            <h1>500</h1>
            <img src="{{ asset('assets/images/error-500-icon.png') }}">
            <h4>@lang('localize.internal_server_error.title')</h4>
            <p>@lang('localize.internal_server_error.msg')</p>
        </div>
    </div>
</div>
@endsection
