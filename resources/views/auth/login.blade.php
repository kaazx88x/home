@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
	<div class="ContentWrapper">
		@include('layouts.partials.status')
		<div class="panel-general panel-login active">
			<h4 class="panel-title">@lang('localize.member_login')</h4>

			<div class="login-selection">
				<a id="login-phone" href="javascript:void(0)" class="{{ (!old('type') || old('type') == 'phone') ? 'active' : '' }}">@lang('localize.loginByPhone')</a>
				<a id="login-email" href="javascript:void(0)" class="{{ (old('type') == 'email') ? 'active' : '' }}">@lang('localize.loginByEmail')</a>
			</div>

			<div class="form-general">
				<div id="email-div" style="display: {{ (old('type') == 'email') ? 'block' : 'none' }}">
					<form id="email-login" method="post" action="{{ url('/login') }}" accept-charset="UTF-8">
						{{ csrf_field() }}

						<div class="form-group">
							<img src="{{ asset('assets/images/icon/icon_email.png') }}">
							<input type="text" name="login" class="form-control" placeholder="@lang('localize.email')" class="form-control" autocorrect="off" autocapitalize="off" {{ ($errors->has('login')) ? 'autofocus' : '' }} required>
						</div>

						<div class="form-group">
							<img src="{{ asset('assets/images/icon/icon_password.png') }}">
							<input type="password" placeholder="@lang('localize.password')" name="password" class="form-control" autocorrect="off" autocapitalize="off" {{ ($errors->has('password')) ? 'autofocus' : '' }} required>
						</div>
						<input type="hidden" name="type" id="type" value="email"/>
						<button class="btn btn-primary btn-block ">@lang('localize.login')</button>
					</form>
				</div>

				<div id="phone-div" style="display: {{ (!old('type') || old('type') == 'phone') ? 'block' : 'none' }};">
					<form id="phone-login" method="post" action="{{ url('/login') }}" accept-charset="UTF-8">
						{{ csrf_field() }}

						<div class="form-group">
							<div class="row">
								<div class="col-sm-5">
									<img src="{{ asset('assets/images/icon/icon_country.png') }}">
									<select class="form-control compulsary" name="phone_area_code" id="areacode">
										@foreach($countries as $key => $country)
											<option value="{{$country->phone_country_code}}" data-id="{{$country->co_id}}">{{'+' . $country->phone_country_code . ' - ' . $country->co_name}}</option>
										@endforeach
									</select>
								</div>

								<div class="col-sm-7">
									<img src="{{ asset('assets/images/icon/icon_phone.png') }}">
									<input class="form-control phone number compulsary" id="phone" name="login" placeholder="@lang('localize.phoneInput')" required>
									<span class="mobilehint hint" style="display : none;"><i class="fa fa-info-circle"></i> <span class="mobilehint-text">@lang('localize.mobileHint')</span></span>
								</div>
							</div>
						</div>

						<div class="form-group">
							<img src="{{ asset('assets/images/icon/icon_password.png') }}">
							<input type="password" placeholder="@lang('localize.password')" name="password" class="form-control" autocorrect="off" autocapitalize="off" {{ ($errors->has('password')) ? 'autofocus' : '' }} required>
						</div>
						<input type="hidden" name="type" id="type" value="phone"/>
						<button class="btn btn-primary btn-block ">@lang('localize.login')</button>
					</form>
				</div>
				{{--  OLD  --}}



				<a href="{{ url('password/reset') }}" class="btn btn-link btn-block ">@lang('localize.forgot')</a>
			</div>
		</div>

		<div class="panel-general panel-register">
			<a href="/register"><h4 class="panel-title">@lang('localize.member_registration')</h4></a>
		</div>

	</div>
@endsection

@section('scripts')
	<script src="{{ asset('backend/js/custom.js') }}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {

			$('#login-email').on('click', function () {
				$('#login-email').addClass('active');
				$('#login-phone').removeClass('active');
				$('#email-div').show().siblings("div").hide();
			});

			$('#login-phone').on('click', function () {
				$('#login-email').removeClass('active');
				$('#login-phone').addClass('active');
				$('#phone-div').show().siblings("div").hide();
			});

			$('#areacode').change(function() {
                var areacode = $(this).val();

                if (areacode == 60 || areacode == 886) {
                    if(areacode == 60)
                        $('.mobilehint-text').text("@lang('localize.mobileHint-areacode.60')");

                    if(areacode == 886)
                        $('.mobilehint-text').text("@lang('localize.mobileHint-areacode.886')");

                    $('.mobilehint').show();
                } else {
                    $('.mobilehint').hide();
                }
            });

		});
	</script>
@endsection
