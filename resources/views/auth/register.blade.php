@extends('layouts.web.master')

@section('header')
@include('layouts.web.header.main')
@endsection

@section('content')
    <div class="ContentWrapper">
        @include('layouts.partials.status')
        <div class="panel-general panel-register">
            <h4 class="panel-title">@lang('localize.member_registration')</h4>

            <form method="post" action="{{ url('register') }}" id="register" accept-charset="UTF-8">
                {{ csrf_field() }}

                <div id="step-1">
                    <div class="step-title">
                        <span class="step-number">1</span>
                        <h5>@lang('localize.step') 1</h5>
                        <h4>@lang('localize.verifyPhone')</h4>
                    </div>

                    <div class="form-general">
                        <div class="form-group">
                            <div class="hint"><i class="fa fa-info-circle"></i> @lang('localize.mobileLabel')</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>@lang('localize.areacode')</label>
                                    <select class="form-control compulsary" name="areacode" id="areacode"></select>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label>@lang('localize.cellphone')</label>
                                    <input class="form-control phone number compulsary" id="phone" name="phone" placeholder="@lang('localize.phoneInput')" value="{{ old('phone') }}">
                                    <span class="mobilehint hint"><i class="fa fa-info-circle"></i> <span class="mobilehint-text">@lang('localize.mobileHint')</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group verification">
                            <label>@lang('localize.smsverificationcode')</label>
                            <div class="row">
                                <div class='col-sm-6'>
                                    <input class="form-control input compulsary" id="tac" name="tac" value="{{ old('tac') }}" maxlength="6">
                                </div>
                                <div class='col-sm-6'>
                                    <button type="button" class="btn btn-secondary btn-sm" id="send_tac">@lang('localize.getsmsverificationcode')</button>
                                </div>
                            </div>
                            <div id="tac_msg"></div>
                            {{--  <br/>
                            <div class="action">
                                <button type="button" class="btn btn-secondary btn-sm" id="send_tac">@lang('localize.getsmsverificationcode')</button>
                            </div>  --}}
                        </div>

                        <button id="step-1-next" type="button" class="btn btn-primary btn-block ">@lang('localize.next')</button>
                    </div>
                </div>

                <div id="step-2" style="display: none;">
                    <div class="step-title">
						<span class="step-number">2</span>
						<h5>@lang('localize.step') 2</h5>
						<h4>@lang('localize.loginInfo')</h4>
					</div>

					<div class="form-general">
						<div class="form-group">
							<label>@lang('localize.name_as_ic')</label>
							<input type="text" class="form-control compulsary" value="{{ old('cus_name') }}" name="cus_name" {{ ($errors->has('cus_name')) ? 'autofocus' : '' }}>
                        </div>

                        <div id="ic_number" class="form-group">
							<label>@lang('localize.ic_number')</label>
							<input type="text" class="form-control" value="{{ old('identity_card') }}" id="identity_card" name="identity_card" {{ ($errors->has('identity_card')) ? 'autofocus' : '' }}>
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.address')</label>
                            <input type="text" class="form-control compulsary" name="address1" placeholder="@lang('localize.address1Input')" value="{{ old('address1') }}">
                            <input type="text" class="form-control" name="address2" placeholder="@lang('localize.address2Input')" value="{{ old('address2') }}">
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">@lang('localize.country')</label>
                                    <select class="form-control compulsary input" id="country" name="country" onchange="get_states('#state', this.value)"></select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">@lang('localize.state')</label>
                                    <select class="form-control compulsary input" id="state" name="state">
                                    <option value="">@lang('localize.selectState')</option>
                                </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">@lang('localize.zipcode')</label>
                                    <input type="text" class="form-control compulsary" id="zipcode" name="zipcode" placeholder="@lang('localize.zipcode')" value="{{ old('zipcode') }}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">City</label>
                                    <input type="text" class="form-control compulsary" id="city" name="city" value="{{ old('city') }}">
                                </div>
                            </div>
                        </div>

						<div class="form-group">
							<label>@lang('localize.email')</label>
							<input type="text" class="form-control email" id="email" name="cus_email" value="{{ old('cus_email') }}" {{ ($errors->has('cus_email')) ? 'autofocus' : '' }}>
							<div class="hint"><i class="fa fa-info-circle"></i> @lang('localize.emailHint')</div>
						</div>

						<hr>

						<div class="form-group">
							<label>@lang('localize.password')</label>
                            <input type="password" class="form-control compulsary password" name="cus_password" id="password">
                            <div class="hint"><i class="fa fa-info-circle"></i> @lang('localize.passwordHint')</div>
						</div>
						<div class="form-group">
							<label>@lang('localize.confirmPassword')</label>
							<input type="password" class="form-control compulsary" name="cus_password_confirmation"  id="password_confirm">
						</div>

						<button id="step-2-next" type="button" class="btn btn-primary btn-block ">@lang('localize.next')</button>
					</div>
                </div>

                <div id="step-3" style="display: none;">
                    <div class="step-title">
						<span class="step-number">3</span>
						<h5>@lang('localize.step') 3</h5>
						<h4>@lang('localize.secure_code')</h4>
					</div>

					<div class="form-general">
                        <div class="form-group">
                            <label>@lang('localize.secure_code')</label>
                            <input id="payment_secure_code" type="password" class="form-control secure-code number compulsary" name="securecode" maxlength="6">
                            <div class="hint"><i class="fa fa-info-circle"></i> @lang('localize.securecodeHint')</div>
                        </div>
                        <div class="form-group">
                            <label>@lang('localize.confirm') @lang('localize.secure_code')</label>
                            <input id="payment_secure_code_confirmation" type="password" class="form-control secure-code number compulsary" name="securecode_confirmation" maxlength="6">
                        </div>
                        <p>@lang('localize.signup_term').</p>
                        <input type="hidden" id="serverdate">
                        <div class="row">
                            <div class="col-sm-2">
                                <input type="button" class="btn btn-default btn-block" id="back-step" value="@lang('localize.back')">
                            </div>
                            <div class="col-sm-10">
                                <input type="button" class="btn btn-primary btn-block" id="member-reg" value="@lang('localize.register')">
                            </div>
                        </div>
					</div>
                </div>

            </form>
        </div>

        <div id="login" class="panel-general panel-login">
            <a href="/login"><h4 class="panel-title">@lang('localize.member_login')</h4></a>
        </div>
    </div>
@endsection

@section('styles')
    <link href="{{ asset('backend/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css" media="all">
@endsection

@section('scripts')
    <script src="{{ asset('backend/js/custom.js') }}" type="text/javascript"></script>
    <script src="{{ asset('backend/js/plugins/datapicker/bootstrap-datepicker.js') }}" type="text/javascript"></script>
    <script>
        $(document).ready(function() {

            $('.number').keydown(function (e) {-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});

            get_countries('#country', "{{ old('country', '0') }}", '#state', "{{ old('state', '0') }}");
            get_phoneAreacode('#areacode', "{{ old('areacode', '0') }}");

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

            $("#password").on({
                change: function(e) {
                    var passwordRegex = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])([a-zA-Z0-9]{6,})$/;

                    if(!passwordRegex.test($(this).val())) {
                        swal("@lang('localize.swal_error')", "@lang('localize.passwordHint')", "error");
                        $(this).css('border', '1px solid red').focus();
                    } else {
                        $(this).css('border', '');
                    }
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
                        {{--  swal("@lang()", "Phone number is already taken", "error");  --}}
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

                        $.get("/sms_verification", {phone: phone, action: 'tac_member_registration'})
                        .done(function( data ) {
                            if (data.status == '0') {
                                $('#tac_msg').removeClass('tac-success');
                                $('#tac_msg').addClass('tac-denied');
                            } else {
                                $('#tac_msg').removeClass('tac-denied');
                                $('#tac_msg').addClass('tac-success');

                                // Call countdown
                                countdown(data.expired_at);
                            }

                            $('#tac_msg').text(data.message);
                            $('.loading').hide();
                        });
                    }
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

                    $("#send_tac").html('<i class="fa fa-clock-o" aria-hidden="true"></i> ' + minutes + "m " + seconds + "s @lang('localize.time_left_to_try_again')").prop('disabled', true);

                    if (distance < 0) {
                        clearInterval(x);
                        $("#send_tac").html("{{trans('localize.getsmsverificationcode')}}").prop('disabled', false);
                    }
                }, 1000);
            }

            $('#step-1-next').on('click', function () {
                var isValid = true
                $('#step-1 :input').each(function() {
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
                    phone = $('#areacode').val() + $('#phone').val();
                    tac = $('#tac').val();
                    console.log(tac)
                    $('.loading').show();
                    $.get("/check_tac", {phone: phone, action: 'tac_member_registration', tac: tac})
                    .done(function( data ) {
                        if (data == 1) {
                            $('#login').hide();
                            $('#step-2').show().siblings("div").hide();
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

            $('#step-2-next').on('click', function () {
                var isValid = true
                $('#step-2 :input').each(function() {
                    if ($(this).hasClass('compulsary')) {
                        if (!$(this).val()) {
                            $(this).attr('placeholder', '@lang("localize.fieldrequired")').css('border', '1px solid red').focus();
                            isValid = false;
                            return false;
                        }

                        if ($(this).hasClass('password')) {
                            var passwordRegex = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])([a-zA-Z0-9]{6,})$/;

                            if(!passwordRegex.test($(this).val())) {
                                swal("@lang('localize.swal_error')", "@lang('localize.passwordHint')", "error");
                                $(this).css('border', '1px solid red').focus();
                                isValid = false;
                                return false;
                            }

                            if($('#password').val().length < 6) {
                                swal("@lang('localize.error')", "@lang('localize.password_min')", 'error');
                                $('#password').css('border', '1px solid red').focus();
                                $('#password_confirm').css('border', '1px solid red').focus();
                                isValid = false;
                                return false;
                            }
                        }
                    }
                    $(this).css('border', '');
                });

                if($('#password').val() != $('#password_confirm').val()) {
                    swal("@lang('localize.error')", "@lang('localize.matchpassword')", 'error');
                    $('#password').css('border', '1px solid red').focus();
                    $('#password_confirm').css('border', '1px solid red').focus();
                    isValid = false;
                    return false;
                }

                if (isValid) {
                    $('#step-3').show().siblings("div").hide();
                }
            });

            $('#back-step').on('click', function () {
                $('#step-2').show().siblings("div").hide();
            });

            $('#member-reg').click(function(event) {
                var isValid = true
                $('#register :input').each(function() {
                    if ($(this).hasClass('compulsary')) {
                        if (!$(this).val()) {
                            $(this).attr('placeholder', '@lang("localize.fieldrequired")').css('border', '1px solid red').focus();
                            isValid = false;
                            return false;
                        }

                        if ($(this).hasClass('password')) {
                            if($('#password').val().length < 6) {
                                swal("@lang('localize.error')", "@lang('localize.password_min')", 'error');
                                $('#password').css('border', '1px solid red').focus();
                                $('#password_confirm').css('border', '1px solid red').focus();
                                isValid = false;
                                return false;
                            }
                        }

                        if ($(this).hasClass('secure-code')) {
                            if($(this).val().length < 6) {
                                swal("@lang('localize.error')", "@lang('localize.secure_code_min')", 'error');
                                $('#payment_secure_code').css('border', '1px solid red').focus();
                                $('#payment_secure_code_confirmation').css('border', '1px solid red').focus();
                                isValid = false;
                                return false;
                            }
                        }
                    }
                    $(this).css('border', '');
                });

                if($('#password').val() != $('#password_confirm').val()) {
                    swal("@lang('localize.error')", "@lang('localize.matchpassword')", 'error');
                    $('#password').css('border', '1px solid red').focus();
                    $('#password_confirm').css('border', '1px solid red').focus();
                    isValid = false;
                    return false;
                }

                if($('#payment_secure_code').val() != $('#payment_secure_code_confirmation').val()) {
                    swal("@lang('localize.error')", "@lang('localize.securecode_notmatch')", 'error');
                    $('#payment_secure_code').css('border', '1px solid red').focus();
                    $('#payment_secure_code_confirmation').css('border', '1px solid red').focus();
                    isValid = false;
                    return false;
                }

                if (isValid) {
                    $('#register').submit();
                }
            });
        });
    </script>
@endsection
