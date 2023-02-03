@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
	<div class="ListingTopbar">
        <h4 class="ListingCategory">@lang('localize.verifyPhone')</h4>
        <a href="/profile/phone" class="back"><i class="fa fa-angle-left"></i></a>
    </div>

	<div class="ContentWrapper">
		<div class="panel-general panel-list" >
			<img src="{{ asset('assets/images/icon/icon_phone.png') }}">
			{{ \Helper::securePhone($customer->phone_area_code . $customer->cus_phone) }}
		</div>

		@include('layouts.partials.status')
		<form method="post" role="form" method="POST" action="{{ route('phone.verify.submit') }}" accept-charset="UTF-8" id="phone-verify">
			{{ csrf_field() }}
			<div class="panel-general">
				<div class="form-general verification">
					<div class="form-group">
						<label>@lang('localize.verificationcode')</label>
						<input type="text" class="form-control compulsary" id="tac" name="tac" maxLength='6'>
						<div id="tac_msg"></div>
						<div class="action">
							<button type="button" class="btn btn-secondary btn-sm" id="code_by_phone">@lang('localize.sent_to_phone')</button>
						</div>
					</div>
					<input type="hidden" id="serverdate">
					<button type="submit" class="btn btn-block btn-primary">@lang('localize.submit')</button>
				</div>

			</div>
		</form>
	</div>
@endsection

@section('scripts')
<script src="{{ asset('backend/js/plugins/validate/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('backend/js/custom.js') }}" type="text/javascript"></script>

<script>
	$(document).ready(function() {

		$('#code_by_phone').on('click', function() {
			$('.loading').show();

			$.get("/sms_verification", {phone: '', action: 'tac_phone_verification'})
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

		$("#phone-verify").validate({

			rules: {
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

function GetServerDate()
{
    var svserverDate;
    $.ajax({
        async: false,
        url: '/getDate',
        dataType: 'json',
        type: 'get',
        success: function (res) {
            svserverDate = res.dateData;
            $('#serverdate').val(svserverDate);
        }
    });
}

function countdown(expired_date)
{
    GetServerDate();
    var serverDate = $('#serverdate').val();
    var nowx = new Date(serverDate).getTime();
    var expiredDate = new Date(expired_date).getTime();
    var dateDiffTime = expiredDate - nowx;
    var fiveMinTime = (5 * 60 * 1000);

    var expired = new Date().getTime() + dateDiffTime;

    if (dateDiffTime > fiveMinTime)
        expired = new Date().getTime() + fiveMinTime;

    var x = setInterval(function() {
        var now = new Date().getTime();
        var distance = expired - now;
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        $("#code_by_phone").html('<i class="fa fa-clock-o" aria-hidden="true"></i> ' + minutes + "m " + seconds + "s @lang('localize.time_left_to_try_again')").prop('disabled', true);

        if (distance < 0) {
            clearInterval(x);
            $("#code_by_phone").html("{{trans('localize.getsmsverificationcode')}}").prop('disabled', false);
        }
    }, 1000);
}
</script>
@endsection