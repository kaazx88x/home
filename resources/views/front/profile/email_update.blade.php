@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
	<div class="ListingTopbar">
        <h4 class="ListingCategory">@lang('localize.myemail')</h4>
        <a href="/profile" class="back"><i class="fa fa-angle-left"></i></a>
    </div>

	<div class="ContentWrapper">

        @include('layouts.partials.status')

		<div class="panel-general panel-list">
            <div class="row">
                <div class="col-sm-6">
                    <img src="{{ asset('assets/images/icon/icon_email.png') }}">
			        {{ \Helper::secureEmail($customer->email) }}
                </div>
                <div class="col-sm-6">
                    @if(!$customer->email_verified && !empty($customer->email))
                        <a href="{{ route('email.verify.send') }}" class="btn btn-sm btn-primary">@lang('localize.resend_activation')</a>
                    @endif
                </div>
            </div>
		</div>

		<form method="post" role="form" method="POST" action="{{ url('/profile/email') }}" accept-charset="UTF-8" id="email-update">
			{{ csrf_field() }}
			<div class="panel-general">
				<div class="form-general">
					<div class="form-group">
						<label class="control-label">@lang('localize.newemail')</label>
						<input type="text" class="form-control email" id="email" name="email" value="{{ old('email') }}">
					</div>

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

					<input type="submit" class="btn btn-block btn-primary" value="@lang('localize.update')">
				</div>

			</div>
		</form>
	</div>
@endsection

@section('scripts')
    <script src="{{ asset('backend/js/plugins/validate/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('backend/js/custom.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready( function() {
            $('#code_by_phone').on('click', function(event) {
                $('.loading').show();
                event.preventDefault();

                $.get("/sms_verification", {type: 'sms', action: 'tac_email_update'})
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

				$.get("/sms_verification", {type: 'email', action: 'tac_email_update'})
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

            $("#email").on({
                keydown: function(e) {
                    if (e.which === 32)
                    return false;
                },
                change: function(e) {
                    this.value = this.value.replace(/\s/g, "");
                    if(this.value.length != 0)
                        check_member_email($(this), $(this).val(), '', e);
                }
            });

            $("#email-update").validate({

                rules: {
                    email: "required",
                    tac: "required",
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