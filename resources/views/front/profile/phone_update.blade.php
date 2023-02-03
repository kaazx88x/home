@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
	<div class="ListingTopbar">
        <h4 class="ListingCategory">@lang('localize.update_phone')</h4>
        <a href="/profile" class="back"><i class="fa fa-angle-left"></i></a>
    </div>

	<div class="ContentWrapper">

		@include('layouts.partials.status')

		<form method="post" role="form" method="POST" action="{{ url('/profile/phone/update') }}" accept-charset="UTF-8" id="update_form">
			{{ csrf_field() }}
			<div class="panel-general">
				<div class="form-general">
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<label>@lang('localize.areacode')</label>
								<select class="form-control compulsary" name="areacode" id="areacode">
									@foreach($countries as $key => $country)
										<option value="{{$country->phone_country_code}}" data-id="{{$country->co_id}}">{{'+' . $country->phone_country_code . ' - ' . $country->co_name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-sm-8">
							<div class="form-group">
								<label>@lang('localize.newphone')</label>
								<input class="form-control phone number compulsary" id="phone" name="phone" value="{{ old('phone') }}">
								<span class="mobilehint hint"><i class="fa fa-info-circle"></i> <span class="mobilehint-text">@lang('localize.mobileHint')</span></span>
							</div>
						</div>
					</div>

					<div class="form-group verification">
						<label>@lang('localize.verificationcode')</label>
						<div class="row">
							<div class='col-sm-6'>
								<input class="form-control input compulsary" id="tac" name="tac" value="{{ old('tac') }}" maxlength="6">
							</div>
							<div class='col-sm-6'>
								<button type="button" class="btn btn-secondary btn-sm" id="send_tac">@lang('localize.getsmsverificationcode')</button>
							</div>
						</div>
						<div id="tac_msg"></div>
					</div>
					<input type="hidden" id="serverdate">
					<input type="button" class="btn btn-block btn-primary" id="update_phone" value="{{ ucwords(strtolower(trans('localize.tac_phone_update'))) }}">
				</div>

			</div>
		</form>
	</div>
@endsection

@section('scripts')
    <script src="{{ asset('backend/js/custom.js') }}" type="text/javascript"></script>

	<script>
        $(document).ready(function() {
			$('.number').keydown(function (e) {-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});

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

            $('#send_tac').click(function(e) {
                var phone = $('#phone').val();
                // check phone val not null

                if (phone == '') {
                    swal("@lang('localize.swal_error')", "@lang('localize.phoneInput')", "error");
                    $('#phone').css('border', '1px solid red').focus();
                    return false;
                }

                if ($.isNumeric(phone) == false) {
                    swal("Error!", "Phone number is not numeric", "error");
                    $('#phone').css('border', '1px solid red').focus();
                    return false;
                }
                $('#phone').css('border', '');

                $.get( '/member_phone_check?phone=' + phone, function( data ) {
                    if (data > 0) {
                        swal({
                            title: window.translations.error,
                            text: window.translations.phone_exist,
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: "#d9534f",
                            confirmButtonText: window.translations.ok,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true,
                        });
                        $('#phone').css('border', '1px solid red').focus();
                        $('#phone').val('');
                        return false;
                    } else {
                        $('#phone').css('border', '');
                        phone = $('#areacode').val() + phone;
                        $('.loading').show();

                        $.get("/sms_verification", {phone: phone, action: 'tac_phone_update', type: 'sms'})
                        .done(function( data ) {
                            if (data.status == '0') {
                                $('#tac_msg').removeClass('tac-success');
                                $('#tac_msg').addClass('tac-denied');
                            } else {
                                $('#tac_msg').removeClass('tac-denied');
                                $('#tac_msg').addClass('tac-success');

                                // Call countdown
                                countdown(data.expired_at, '#send_tac');
                            }

                            $('#tac_msg').text(data.message);
                            $('.loading').hide();
                        });
                    }
                });
            });

			$('#update_phone').on('click', function() {
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
					phone = $('#areacode').val() + $('#phone').val();

                    $('.loading').show();
                    $.get("/check_tac", {action: 'tac_phone_update', tac: tac, phone: phone})
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