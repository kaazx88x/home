@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.backend')
@endsection

@section('content-old')
<div class="auth-container">
    <div class="inner">
        <div class="logo-container">
            <img src="{{ asset('assets/images/logo.png') }}" class="logo">
        </div>
        <h4>@lang('localize.forgot')</h4>
        @include('layouts.partials.status')

        <form role="form" method="POST" action="{{ url('store/password/email') }}">
            {{ csrf_field() }}
            <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                <i class="fa fa-user"></i>
                <input type="username" name="username" class="form-control" placeholder="@lang('localize.username')" value="{{ old('username') }}">
                @if ($errors->has('username'))
                    <span class="help-block">
                        {{ $errors->first('username') }}
                    </span>
                @endif
            </div>

            <div class="action">
                <button type="submit" class="btn btn-primary">@lang('localize.send_reset_email')</button>
            </div>
        </form>
    </div>
    <div class="other-action">
        <a href="{{ url('store/login') }}">@lang('localize.back')</a>
    </div>
</div>
@endsection
@section('content')
<div class="page-title">
				<a href="{{ url('store/login') }}" class="back transition"><i class="fa fa-angle-left"></i></a>
				@lang('localize.forgot')
			</div>
			<div class="ContentWrapper">
				<div class="panel-general">
					<h4 class="panel-title">@lang('localize.forgot')</h4>
					<div class="form-general">

						@include('layouts.partials.status')

						 <form role="form" method="POST" action="{{ url('store/password/email') }}">
                            {{ csrf_field() }}
							<div class="form-group">
								<input type="username" name="username" class="form-control" placeholder="@lang('localize.username')" value="{{ old('username') }}">
                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        {{ $errors->first('username') }}
                                    </span>
                                @endif
							</div>

							<button class="btn btn-primary btn-block ">@lang('localize.send_reset_email')</button>
						</form>
						<a  href="{{ url('store/login') }}" class="btn btn-link btn-block ">@lang('localize.back')</a>
					</div>
				</div>
			</div>
@endsection
