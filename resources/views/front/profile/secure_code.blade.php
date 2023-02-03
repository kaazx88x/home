@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
	<div class="ListingTopbar">
        <h4 class="ListingCategory">@lang('localize.mysecurecode')</h4>
        <a href="/profile" class="back"><i class="fa fa-angle-left"></i></a>
    </div>

    <div class="ContentWrapper">
		@include('layouts.partials.status')
		<div class="panel-general panel-login">

			<div class="login-selection">
				<a id="update-old" href="javascript:void(0)" class="{{ (!old('type') || old('type') == 'old') ? 'active' : '' }}">@lang('localize.oldsecurecode')</a>
				<a id="update-tac" href="javascript:void(0)" class="{{ (old('type') == 'tac') ? 'active' : '' }}">@lang('localize.verificationcode')</a>
			</div>

			<div class="form-general">

				<form method="post" role="form" method="POST" action="{{ url('/profile/securecode') }}" accept-charset="UTF-8" id="update-code">
				{{ csrf_field() }}
					<div class="row">
                        <div class="col-sm-12 text-info form-group"><i class="fa fa-info-circle"></i> @lang('localize.securecodeHint')</div>
						<div class="col-sm-12" id="old-field" style="display: {{ (!old('type') || old('type') == 'old') ? 'block' : 'none' }}">
							<div class="form-group">
                                <label class="control-label">@lang('localize.oldsecurecode')</label>
                                <div class="securecode">
                                    <input type="password" class="form-control securecode" maxlength="1" id="old1" name="old">
                                    <input type="password" class="form-control securecode" maxlength="1" id="old2" name="old">
                                    <input type="password" class="form-control securecode" maxlength="1" id="old3" name="old">
                                    <input type="password" class="form-control securecode" maxlength="1" id="old4" name="old">
                                    <input type="password" class="form-control securecode" maxlength="1" id="old5" name="old">
                                    <input type="password" class="form-control securecode" maxlength="1" id="old6" name="old">
                                    <input id="old_securecode" type="hidden" name="old_securecode">
                                </div>
                            </div>
						</div>

						<div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">@lang('localize.newsecurecode')</label>
                                <div class="securecode">
                                    <input type="password" class="form-control securecode" maxlength="1" id="pass1" name="pass">
                                    <input type="password" class="form-control securecode" maxlength="1" id="pass2" name="pass">
                                    <input type="password" class="form-control securecode" maxlength="1" id="pass3" name="pass">
                                    <input type="password" class="form-control securecode" maxlength="1" id="pass4" name="pass">
                                    <input type="password" class="form-control securecode" maxlength="1" id="pass5" name="pass">
                                    <input type="password" class="form-control securecode" maxlength="1" id="pass6" name="pass">
                                    <input id="securecode" type="hidden" name="securecode">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">@lang('localize.confirmcode')</label>
                                <div class="securecode">
                                    <input type="password" class="form-control securecode" maxlength="1" id="confirm1" name="confirm">
                                    <input type="password" class="form-control securecode" maxlength="1" id="confirm2" name="confirm">
                                    <input type="password" class="form-control securecode" maxlength="1" id="confirm3" name="confirm">
                                    <input type="password" class="form-control securecode" maxlength="1" id="confirm4" name="confirm">
                                    <input type="password" class="form-control securecode" maxlength="1" id="confirm5" name="confirm">
                                    <input type="password" class="form-control securecode" maxlength="1" id="confirm6" name="confirm">
                                    <input id="securecode_confirmation" type="hidden" name="securecode_confirmation">
                                </div>
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
							<button type="button" id="submit-update" class="btn btn-block btn-primary" style="margin-right:14%;">@lang('localize.update')</button>
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
            text-align: center;
            border-top: 0px solid #eee !important;
            border-bottom: 1px solid #eee;
        }

        .panel-login .form-general .form-group .form-control {
            padding-left: 15px !important;
            text-align: center!;
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
                $('#old_securecode').val('');
            });

            $('#code_by_phone').on('click', function(event) {
                $('.loading').show();
                event.preventDefault();

                $.get("/sms_verification", {type: 'sms', action: 'tac_securecode_update'})
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

				$.get("/sms_verification", {type: 'email', action: 'tac_securecode_update'})
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

            $('#submit-update').click(function(event) {
                event.preventDefault();

                var old1 = $('#old1').val();
                var old2 = $('#old2').val();
                var old3 = $('#old3').val();
                var old4 = $('#old4').val();
                var old5 = $('#old5').val();
                var old6 = $('#old6').val();

                var pass1 = $('#pass1').val();
                var pass2 = $('#pass2').val();
                var pass3 = $('#pass3').val();
                var pass4 = $('#pass4').val();
                var pass5 = $('#pass5').val();
                var pass6 = $('#pass6').val();

                var confirm1 = $('#confirm1').val();
                var confirm2 = $('#confirm2').val();
                var confirm3 = $('#confirm3').val();
                var confirm4 = $('#confirm4').val();
                var confirm5 = $('#confirm5').val();
                var confirm6 = $('#confirm6').val();

                var old = old1+old2+old3+old4+old5+old6;
                var code = pass1+pass2+pass3+pass4+pass5+pass6;
                var confirmcode = confirm1+confirm2+confirm3+confirm4+confirm5+confirm6;

                if(!code || !confirmcode || ($('#type') == 'old' && !old)) {
                    swal("{{trans('localize.error')}}", "{{trans('localize.securecode_required')}}", "error");
                    return false;
                }

                if($('type') == 'tac' && !$('#tac').val()) {
                    $('#tac').css('border', '1px solid red').focus();
                    return false;
                } else {
                    $('#tac').css('border', '');
                }

                if (code == confirmcode) {
                    $('#securecode').val(code);
                    $('#securecode_confirmation').val(confirmcode);
                    $('#old_securecode').val(old);

                    swal({
                        title: "@lang('localize.sure')",
                        text: "@lang('localize.are_you_sure_submit')",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "@lang('localize.yes')",
                        cancelButtonText: "@lang('localize.cancel')",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function(isConfirm){
                            if (isConfirm)
                                $('#update-code').submit();
                        }
                    );
                }
                else{
                    swal("{{trans('localize.error')}}", "{{trans('localize.securecode_notmatch')}}", "error");
                    return false;
                }
            });

            $("input[name='old']").keyup(function () {
                if (this.value.length == this.maxLength) {
                var $next = $(this).next("input[name='old']");
                if ($next.length){
                        $(this).next("input[name='old']").val('');
                        $(this).next("input[name='old']").focus();
                }
                else
                    $(this).blur();
                }
            });

            $("input[name='confirm']").keyup(function () {
                if (this.value.length == this.maxLength) {
                var $next = $(this).next("input[name='confirm']");
                if ($next.length){
                        $(this).next("input[name='confirm']").val('');
                        $(this).next("input[name='confirm']").focus();
                }
                else
                    $(this).blur();
                }
            });

            $("input[name='pass']").keyup(function () {
                if (this.value.length == this.maxLength) {
                var $next = $(this).next("input[name='pass']");
                if ($next.length){
                    $(this).next("input[name='pass']").val('');
                    $(this).next("input[name='pass']").focus();
                }
                else
                    $(this).blur();
                }
            });

        });

        $('.securecode').keydown(function (e) {
            if (e.shiftKey || e.ctrlKey || e.altKey) {
                e.preventDefault();
            } else {
                var key = e.keyCode;
                if (!((key == 8) || (key == 46) || (key >= 35 && key <= 40) || (key >= 48 && key <= 57) || (key >= 96 && key <= 105))) {
                    e.preventDefault();
                }
            }
        });
    </script>
@endsection
