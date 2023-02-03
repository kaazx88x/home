@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
	{{--  <a href="{{ url('profile/account') }}">  --}}
		<div class="username">
			<div class="avatar">
                <img class="lazyload" data-src="{{ env('IMAGE_DIR') . '/avatar/' . $customer->cus_id . '/' . $customer->cus_pic }}" src="{{ asset('assets/images/general-avatar.png') }}">
			</div>
			<div class="usercontent">
				<h4>{{ $customer->cus_name }}</h4>
				@if ($customer->phone_area_code && $customer->cus_phone)
				<p>
					{{ \Helper::securePhone($customer->phone_area_code . $customer->cus_phone) }}
					<a href="{{ url('/profile/phone') }}">
						<span class="text-{{ !$customer->cellphone_verified? 'danger' : 'success' }}">
							<small>
								<i class="fa fa-{{ !$customer->cellphone_verified? 'exclamation-circle' : 'check' }}" style="float: none; font-size: inherit; color: {{ !$customer->cellphone_verified? '#a94442' : '#6dd400' }}; padding: 1px;"></i>
								{{ !$customer->cellphone_verified? trans('localize.phone_not_verified') : trans('localize.verified') }}
							</small>
						</span>
					</a>
				</p>
				@endif

				@if ($customer->email)
				<p>
					{{ \Helper::secureEmail($customer->email) }}
					<span class="text-{{ !$customer->email_verified? 'danger' : 'success' }}">
						<small>
							<i class="fa fa-{{ !$customer->email_verified? 'exclamation-circle' : 'check' }}" style="float: none; font-size: inherit; color: {{ !$customer->email_verified? '#a94442' : '#6dd400' }}; padding: 1px;"></i>
							@if (!$customer->email_verified)
								<a href="{{ url('/profile/send/verification/email') }}" style="color: #a94442;">@lang('localize.email_not_verified') @lang('localize.resend_activation')</a>
							@else
								<a href="{{ url('/profile/email') }}" style="color: #6dd400;">@lang('localize.verified')</a>
							@endif
						</small>
					</span>
				</p>
				@endif
			</div>
			<i class="fa fa-angle-right"></i>
		</div>
	{{--  </a>  --}}

	<div class="ContentWrapper">
		<a class="transition" href="{{ url('profile/account') }}">
			<div class="panel-general panel-list">
				<img src="{{ asset('assets/images/icon/icon_account.png') }}">
				<span class="alert-hint {{ (!$customer->info) ? 'text-danger' : '' }}">
					@lang('localize.myaccount')
					@if (!$customer->info)
						<i class="fa fa-exclamation-circle" style="float: none; font-size: inherit; color: #a94442;"></i>
					@endif
				</span>
				<i class="fa fa-angle-right"></i>
			</div>
		</a>
		<a href="/profile/shippingaddress">
			<div class="panel-general panel-list">
				<img src="{{ asset('assets/images/icon/icon_shipping.png') }}">
				<span class="alert-hint {{ (count($customer->shipping) == 0) ? 'text-danger' : '' }}">
					@lang('localize.myaddress')
					@if (count($customer->shipping) == 0)
						<i class="fa fa-exclamation-circle" style="float: none; font-size: inherit; color: #a94442;"></i>
					@endif
				</span>
				<i class="fa fa-angle-right"></i>
			</div>
		</a>
		<a href="{{ url('profile/phone') }}">
			<div class="panel-general panel-list">
				<img src="{{ asset('assets/images/icon/icon_phone.png') }}">
				<span class="alert-hint {{ (!$customer->cellphone_verified) ? 'text-danger' : '' }}">
					@lang('localize.myphone')
					@if (!$customer->cellphone_verified)
						<i class="fa fa-exclamation-circle" style="float: none; font-size: inherit; color: #a94442;"></i>
					@endif
				</span>
				<i class="fa fa-angle-right"></i>
			</div>
		</a>
		<a href="{{ url('profile/email') }}">
			<div class="panel-general panel-list">
				<img src="{{ asset('assets/images/icon/icon_email.png') }}">
				<span class="alert-hint {{ (!$customer->email_verified) ? 'text-danger' : '' }}">
					@lang('localize.myemail')
					@if (!$customer->email_verified)
						<i class="fa fa-exclamation-circle" style="float: none; font-size: inherit; color: #a94442;"></i>
					@endif
				</span>
				<i class="fa fa-angle-right"></i>
			</div>
		</a>
		<a href="{{ url('profile/password') }}">
			<div class="panel-general panel-list">
				<img src="{{ asset('assets/images/icon/icon_password.png') }}">
				@lang('localize.mypassword')
				<i class="fa fa-angle-right"></i>
			</div>
		</a>
		<a href="{{ url('profile/securecode') }}">
			<div class="panel-general panel-list">
				<img src="{{ asset('assets/images/icon/icon_securecode.png') }}">
				<span class="alert-hint {{ (!$customer->payment_secure_code) ? 'text-danger' : '' }}">
					@lang('localize.mysecurecode')
					@if (!$customer->payment_secure_code)
						<i class="fa fa-exclamation-circle" style="float: none; font-size: inherit; color: #a94442;"></i>
					@endif
				<span>
				<i class="fa fa-angle-right"></i>
			</div>
		</a>
		<a href="{{ url('profile/security/question') }}">
			<div class="panel-general panel-list">
				<img src="{{ asset('assets/images/icon/icon_securecode.png') }}">
				<span class="alert-hint {{ (!$customer->question_1 || !$customer->answer_1) ? 'text-danger' : '' }}">
					@lang('localize.security_questions')
					@if (!$customer->question_1 || !$customer->answer_1)
						<i class="fa fa-exclamation-circle" style="float: none; font-size: inherit; color: #a94442;"></i>
					@endif
				<span>
				<i class="fa fa-angle-right"></i>
			</div>
		</a>
		<a href="{{ url('profile/order') }}">
			<div class="panel-general panel-list">
				<img src="{{ asset('assets/images/icon/icon_history.png') }}">
				@lang('localize.mybuys')
				<i class="fa fa-angle-right"></i>
			</div>
		</a>
		<a href="{{ url('profile/credit') }}">
			<div class="panel-general panel-list">
				<img src="{{ asset('assets/images/icon/icon_meicredit.png') }}">
				@lang('localize.vcoinLog')
				<i class="fa fa-angle-right"></i>
			</div>
		</a>
		<a href="{{ url('profile/limit') }}">
			<div class="panel-general panel-list">
				<img src="{{ asset('assets/images/icon/icon_more.png') }}">
				@lang('localize.my_transaction_limit')
				<i class="fa fa-angle-right"></i>
			</div>
		</a>
		<a href="{{ url('profile/logout') }}">
			<div class="panel-general panel-list">
				<img src="{{ asset('assets/images/icon/icon_logout.png') }}">
				@lang('localize.logout')
				<i class="fa fa-angle-right"></i>
			</div>
		</a>
	</div>
@endsection