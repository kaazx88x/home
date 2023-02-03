@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
	<div class="ListingTopbar">
        <h4 class="ListingCategory">@lang('localize.mypassword')</h4>
        <a href="/profile" class="back"><i class="fa fa-angle-left"></i></a>
    </div>

    <div class="ContentWrapper">
		@include('layouts.partials.status')
		<div class="panel-general panel-login">

			<div class="login-selection">
				<a id="update-old" href="javascript:void(0)" class="{{ (!old('type') || old('type') == 'old') ? 'active' : '' }}">@lang('localize.update_via.old_password')</a>
				<a id="update-tac" href="javascript:void(0)" class="{{ (old('type') == 'tac') ? 'active' : '' }}">@lang('localize.update_via.verification_code')</a>
			</div>

			<div class="form-general">

				<form method="post" role="form" method="POST" action="{{ url('/profile/password') }}" accept-charset="UTF-8" id="form-update">
				{{ csrf_field() }}
					<div class="row">
                        <div class="col-sm-12 text-info form-group"><i class="fa fa-info-circle"></i> @lang('localize.passwordHint')</div>
						<div class="col-sm-12" id="old-field" style="display: {{ (!old('type') || old('type') == 'old') ? 'block' : 'none' }}">
							<div class="form-group{{ $errors->has('old_password') ? ' has-error' : '' }}">
								<label class="control-label">@lang('localize.oldpassword')</label>
								<input type="password" name="old_password" class="form-control" placeholder="@lang('localize.oldpassword')" id="old_password">
								@if ($errors->has('old_password'))
									<span class="help-block">
										{{ $errors->first('old_password') }}
									</span>
								@endif
							</div>
						</div>

						<div class="col-sm-12">
							<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
								<label class="control-label">@lang('localize.newpassword')</label>
								<input type="password" name="password" class="form-control" placeholder="@lang('localize.newpassword')" id="password">
								@if ($errors->has('password'))
									<span class="help-block">
										{{ $errors->first('password') }}
									</span>
								@endif
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
								<label class="control-label">@lang('localize.confirm_password')</label>
								<input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="@lang('localize.confirm_password')">
								@if ($errors->has('password_confirmation'))
									<span class="help-block">
										{{ $errors->first('password_confirmation') }}
									</span>
								@endif
							</div>
						</div>
						<div class="col-sm-12" id="tac-field" style="display: {{ (old('type') == 'tac') ? 'block' : 'none' }}">
							<div class="form-group verification">
								<label>@lang('localize.verificationcode')</label>
								<input type="text" class="form-control" id="tac" name="tac" maxlength="6">
								<div id="tac_msg"></div>
								<div class="action">
									@if ($customer->email_verified)
                                    <button type="button" class="btn btn-secondary btn-sm" id="code_by_email">@lang('localize.sent_to_email')</button>
                                    @endif

                                    @if($customer->cellphone_verified)
									<button type="button" id="code_by_phone" class="btn btn-secondary btn-sm">@lang('localize.sent_to_phone')</button>
									@endif
								</div>
							</div>
							<input type="hidden" id="serverdate">
						</div>

						<div class="col-sm-12">
							<input type="hidden" name="type" id="type" value="{{ old('type', 'old') }}">
							<input type="submit" class="btn btn-block btn-primary" value="@lang('localize.updatepassword')">
						</div>
					</div>
				</form>

			</div>
		</div>
	</div>
@endsection

@section('styles')
    <style>
        .panel-general {
            background-color: #fff;
            border-radius: 4px;
            padding: 0px 30px;
            margin-bottom: 15px;
        }

        .panel-login .login-selection {
            font-family: 'Poppins', "Microsoft Yahei", 微软雅黑, sans-serif;
            margin: 0px -30px 20px !important;
            background: #fdfdfd;
            text-align: left;
            border-top: 0px solid #eee !important;
            border-bottom: 1px solid #eee;
        }

        .panel-login .form-general .form-group .form-control {
            padding-left: 15px !important;
        }

        .help-block {
            text-align: left;
        }
    </style>
@endsection

@section('scripts')
    <script src="{{ asset('backend/js/plugins/validate/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('backend/js/custom.js') }}" type="text/javascript"></script>

    <script type="text/javascript">

        $(document).ready( function() {

            $('#update-old').on('click', function () {
                $('#update-old').addClass('active');
                $('#update-tac').removeClass('active');
                $('#old-field').show();
                $('#tac-field').hide();
                $('#type').val('old');
                $('#tac').val('');
            });

            $('#update-tac').on('click', function () {
                $('#update-old').removeClass('active');
                $('#update-tac').addClass('active');
                $('#old-field').hide();
                $('#tac-field').show();
                $('#type').val('tac');
                $('#old_password').val('');
            });

            $('#code_by_phone').on('click', function(event) {
                $('.loading').show();
                event.preventDefault();

                $.get("/sms_verification", {type: 'sms', action: 'tac_password_update'})
                .done(function( data ) {
                    if (data.status == '0') {
                        $('#tac_msg').removeClass('tac-success');
                        $('#tac_msg').addClass('tac-denied');
                    } else {
                        $('#tac_msg').removeClass('tac-denied');
                        $('#tac_msg').addClass('tac-success');

                        // Call countdown
                        countdown(data.expired_at, '#code_by_phone');
                    }

                    $('#tac_msg').text(data.message);
                    $('.loading').hide();
                });
            });

            $('#code_by_email').on('click', function() {
				$('.loading').show();

				$.get("/sms_verification", {type: 'email', action: 'tac_password_update'})
				.done(function( data ) {
					if (data.status == '0') {
						$('#tac_msg').removeClass('tac-success');
						$('#tac_msg').addClass('tac-denied');
					} else {
						$('#tac_msg').removeClass('tac-denied');
						$('#tac_msg').addClass('tac-success');

						// Call countdown
						countdown(data.expired_at, '#code_by_email');
					}

					$('#tac_msg').text(data.message);
					$('.loading').hide();
				});
			});

            $.validator.addMethod('pwcheck', function (value) {
                return /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])([a-zA-Z0-9]{6,})$/.test(value);
            }, '@lang("localize.passwordHint")');

            $("#form-update").validate({

                rules: {
                    old_password: {
                        required: function(element) {
                            return $('#type').val() == 'old';
                        }
                    },
                    password: {
                        required: true,
                        minlength: 6,
                        pwcheck: true
                    },
                    password_confirmation: {
                        required:true,
                        equalTo: '#password'
                    },
                    tac: {
                        required: function(element) {
                            return $('#type').val() == 'tac';
                        }
                    }
                },

                messages: {
                    password: {
                        required: "{{ trans('localize.required') }}",
                        minlength: "{{ trans('localize.password_min') }}"
                    },
                    password_confirmation: {
                        required: "{{ trans('localize.required') }}",
                        equalTo: "{{ trans('localize.password_not_match') }}"
                    },
                },

                highlight: function (element) {
                    $(element).parent().addClass('has-error');
                    $('.error').css('color', '#a95259');
                },
                unhighlight: function (element) {
                    $(element).parent().removeClass('has-error');
                },

                submitHandler: function(form) {
                form.submit();
                },

            });
        });
    </script>
@endsection
