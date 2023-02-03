@extends('layouts.web.master')

@section('header')
    @include('layouts.web.header.main')
@endsection

@section('content')
    <div class="AppContainer">
        <img src="{{ asset('assets/images/app/app_sample.png') }}" class="app_img">

        <div class="inner">
            <a href="{{ $app_link }}"><button class="btn btn-primary btn-block">Open in App</button></a>

            <div class="download-link">
                <h4>Doesn't have Meihome App? Download now at :</h4>
                <a href="https://play.google.com/store/apps/details?id=com.meihome.member" target="_blank"><img src="{{ asset('assets/images/app/googleplay.png') }}"></a>
                <a href="https://itunes.apple.com/app/id1252354583" target="_blank"><img src="{{ asset('assets/images/app/appstore.png') }}"></a>
            </div>
        </div>
    </div>
@endsection