@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
	<div class="ContentWrapper">
		@include('layouts.partials.status')
		<div class="panel-general panel-login active">
			<h4 class="panel-title">@lang('localize.resend_activation_email')</h4>

			<div class="form-general">
				<form role="form" method="POST" action="{{ url('/resend/activation', [$id]) }}">
					{{ csrf_field() }}

					<div class="form-group">
						<img src="{{ asset('assets/images/icon/icon_email.png') }}">
						<input type="email" name="email" class="form-control" placeholder="@lang('localize.email')" value="{{ old('email') }}" autocorrect="off" autocapitalize="off" autofocus="">
					</div>

					<input type="submit" class="btn btn-primary btn-block" value="@lang('localize.send')">
				</form>
			</div>
		</div>
	</div>
@endsection
