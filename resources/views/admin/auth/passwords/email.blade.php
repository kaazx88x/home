@extends('layouts.web.master')

@section('header')
@include('layouts.web.header.backend')
@endsection

@section('content')

<div class="ContentWrapper">

    @include('layouts.partials.status')

    <div class="panel-general">
        <h4 class="panel-title">@lang('localize.forgot')</h4>
        <div class="form-general">
            <form role="form" method="POST" action="{{ url('admin/password/email') }}">
                {{ csrf_field() }}
                <div class="form-group">
                    <input type="username" name="username" class="form-control" placeholder="@lang('localize.username')" value="{{ old('username') }}">
                    @if ($errors->has('username'))
                        <span class="help-block">
                            {{ $errors->first('username') }}
                        </span>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary btn-block ">@lang('localize.send_reset_email')</button>
            </form>
            <a  href="{{ url('admin/login') }}" class="btn btn-link btn-block ">@lang('localize.back')</a>
        </div>
    </div>
</div>
@endsection