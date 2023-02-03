@extends('layouts.web.master')
@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
<div class="ContentWrapper">
    @include('layouts.partials.status')
    <div class="panel-general">
        <h4 class="panel-title">@lang('localize.updatepassword')</h4>
        <div class="form-general">
            <form role="form" method="POST" action="{{ (empty($phone))? url('password/reset') : url('password/phone/reset', [$phone]) }}">
            {{ csrf_field() }}

            <input type="hidden" name="token" value="{{ $token }}">

            @if(empty($phone))
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="@lang('localize.email')" value="{{ old('email') }}" autocorrect="off" autocapitalize="off" autofocus="">
                @if ($errors->has('email'))
                    <span class="help-block">
                        {{ $errors->first('email') }}
                    </span>
                @endif
            </div>
            @endif

            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="@lang('localize.newpassword')" autocorrect="off" autocapitalize="off" autofocus="">
                @if ($errors->has('password'))
                    <span class="help-block">
                        {{ $errors->first('password') }}
                    </span>
                @endif
            </div>

            <div class="form-group">
                <input type="password" name="password_confirmation" class="form-control" placeholder="@lang('localize.confirmnewpassword')" autocorrect="off" autocapitalize="off" autofocus="">
            </div>

            <input type="submit" class="btn btn-primary btn-block" value="@lang('localize.updatepassword')">
        </form>
        </div>
    </div>
</div>
@endsection

