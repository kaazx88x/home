@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
	<div class="ListingTopbar">
        <h4 class="ListingCategory">@lang('localize.myphone')</h4>
        <a href="/profile" class="back"><i class="fa fa-angle-left"></i></a>
    </div>

	<div class="ContentWrapper">

		@include('layouts.partials.status')

		<div class="panel-general panel-list" >
			<div class="row">
                <div class="col-sm-4">
					@if ($customer->phone_area_code && $customer->cus_phone)
                    <img src="{{ asset('assets/images/icon/icon_phone.png') }}">
					{{ \Helper::securePhone($customer->phone_area_code . $customer->cus_phone) }}
					@endif
                </div>
                <div class="col-sm-8 text-right">
                    @if(!$customer->cellphone_verified && !empty($customer->cus_phone) && !empty($customer->phone_area_code))
                        <a href="{{ route('phone.verify.send') }}" class="btn btn-sm btn-primary">@lang('localize.verify_phone')</a>
                    @endif
						<a href="javascript:void(0);" id="req_update" class="btn btn-sm btn-primary">@lang('localize.update_phone')</a>
                </div>
            </div>
		</div>

		<form method="post" role="form" method="POST" action="{{ url('/profile/phone') }}" accept-charset="UTF-8" id="update_form" style="display:none;">
			{{ csrf_field() }}
			<div class="panel-general">
				<div class="form-general">
					<div class="form-group verification">
						<div class="text-center">
							@if ($customer->email_verified)
							<button type="button" class="btn btn-secondary" id="code_by_email" style="margin-bottom: 5px;">@lang('localize.sent_to_current_email')</button>
							@endif

							@if ($customer->cellphone_verified)
							<button type="button" class="btn btn-secondary" id="code_by_phone" style="margin-bottom: 5px;">@lang('localize.sent_to_current_phone')</button>
							@endif
						</div>
						<label>@lang('localize.verificationcode')</label>
						<input type="text" class="form-control compulsary" id="tac" name="tac"  maxLength='6'>
						<div id="tac_msg"></div>
					</div>
					<input type="hidden" id="serverdate">
					<input type="button" class="btn btn-block btn-primary" id="next" value="{{ ucwords(strtolower(trans('localize.next'))) }}">
				</div>
			</div>
		</form>
	</div>
@endsection

@section('scripts')
    <script src="{{ asset('backend/js/custom.js') }}" type="text/javascript"></script>

	<script>
        $(document).ready(function() {
			$('#req_update').on('click', function() {
				$('#update_form').show();
			});

			$('.number').keydown(function (e) {-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});

			$("#phone").on({
                keydown: function(e) {
                    if (e.which === 32)
                    return false;
                },
                change: function(e) {
                    this.value = this.value.replace(/\s/g, "");
                    check_member_phone($(this), $(this).val(), '', e);
                }
            });


			$('#code_by_phone').on('click', function() {
				$('.loading').show();

				$.get("/sms_verification", {type: 'sms', action: 'tac_phone_verification'})
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

				$.get("/sms_verification", {type: 'email', action: 'tac_phone_verification'})
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

			$('#next').on('click', function() {
				var isValid = true
                $('#update_form :input').each(function() {
                    if ($(this).hasClass('compulsary')) {
                        if (!$(this).val()) {
                            $(this).attr('placeholder', '@lang("localize.fieldrequired")').css('border', '1px solid red').focus();
                            isValid = false;
                            return false;
                        }
                    }
                    $(this).css('border', '');
                });

				if (isValid) {
                    tac = $('#tac').val();

                    $('.loading').show();
                    $.get("/check_tac", {action: 'tac_phone_verification', tac: tac})
                    .done(function( data ) {
                        if (data == 1) {
                            $('#update_form').submit();
                        } else {
                            $('#tac_msg').removeClass('tac-success');
                            $('#tac_msg').addClass('tac-denied');
                            $('#tac').css('border', '1px solid red').focus();
                            $('#tac_msg').text("@lang('localize.tacNotMatch')");
                        }
                        $('.loading').hide();
                    });
                }
			});
		});
	</script>
@endsection