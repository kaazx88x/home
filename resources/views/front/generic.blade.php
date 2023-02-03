@extends('layouts.master')

@section('top-header')
@include('layouts.partials.front_top_header')
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-6 col-xs-offset-3 generic alert alert-{{ isset($type) ? $type: '' }}">
            <center><strong style="font-size:2em;">{{ $title }}</strong></center>
            <br>
            <p class="text-center" style="font-size:1.25em;">
                {!! $msg !!}
            </p>
        </div>
    </div>
</div>
@endsection