@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
<div class="ContentWrapper">
    @include('layouts.partials.status')
    <div class="panel-general panel-login active">
        <h4 class="panel-title">@lang('localize.forgot_password')</h4>

        <div class="login-selection">
            <a id="reset-email" href="javascript:void(0)" class="{{ (!old('type') || old('type') == 'email') ? 'active' : '' }}">Reset by Email</a>
            <a id="reset-phone" href="javascript:void(0)" class="{{ (old('type') == 'phone') ? 'active' : '' }}">Reset by Phone</a>
        </div>

        <div class="form-general">
            <div id="email-div" style="display: {{ (!old('type') || old('type') == 'email') ? 'block' : 'none' }}">
                <form id="email-reset" role="form" method="POST" action="{{ url('password/email') }}">
                {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label">@lang('localize.email_add')</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="@lang('localize.email_error')" value="{{ old('email') }}" autocorrect="off" autocapitalize="off" autofocus="" required>
                    </div>

                    <input type="hidden" name="type" id="type" value="email"/>
                    <button type="button" class="btn btn-primary btn-block" id="reset_by_email">@lang('localize.reset_password')</button>
                </form>
            </div>

            <div id="phone-div" style="display: {{ (old('type') == 'phone') ? 'block' : 'none' }};">
                <form id="phone-reset" method="post" action="{{ url('password/phone') }}" accept-charset="UTF-8">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-5">
                                <img src="{{ asset('assets/images/icon/icon_country.png') }}">
                                <select class="form-control compulsary" name="areacode" id="areacode">
                                    @foreach($countries as $key => $country)
                                        <option value="{{$country->phone_country_code}}" data-id="{{$country->co_id}}">{{'+' . $country->phone_country_code . ' - ' . $country->co_name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-7">
                                <img src="{{ asset('assets/images/icon/icon_phone.png') }}">
                                <input type="number" class="form-control phone number compulsary" id="phone" name="phone" placeholder="Please Enter Phone Number" required value="{{ old('phone') }}">
                                <span class="mobilehint hint"><i class="fa fa-info-circle"></i> @lang('localize.mobileHint')</span>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group verification">
                                    <label>@lang('localize.smsverificationcode')</label>
                                    <input class="form-control input" id="tac" name="tac" value="{{ old('tac') }}" maxlength="6">
                                    <div id="tac_msg"></div>

                                    <div class="action">
                                        <button class="btn btn-secondary btn-sm" id="send_tac">@lang('localize.getsmsverificationcode')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="type" id="type" value="phone"/>
                    <input type="hidden" id="serverdate">
                    <button type="button" class="btn btn-primary btn-block" id="reset_by_phone">@lang('localize.reset_password')</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('backend/js/custom.js') }}" type="text/javascript"></script>

<script>
    $(document).ready(function() {

        $('.number').keydown(function (e) {-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});

        $('#reset-email').on('click', function () {
            $('#reset-email').addClass('active');
            $('#reset-phone').removeClass('active');
            $('#email-div').show().siblings("div").hide();
        });

        $('#reset-phone').on('click', function () {
            $('#reset-email').removeClass('active');
            $('#reset-phone').addClass('active');
            $('#phone-div').show().siblings("div").hide();
        });

        $('#areacode').change(function() {
            var areacode = $(this).val();

            if (areacode == 60) {
                $('.mobilehint').show();
            } else {
                $('.mobilehint').hide();
            }
        });

        $('#send_tac').click(function(event) {
            var phone = $('#phone').val();
            var areacode = $('#areacode').val();
            // check phone val not null
            event.preventDefault();

            if (phone == '') {
                swal("Error!", "@lang('localize.phoneInput')", "error");
                $('#phone').css('border', '1px solid red').focus();
                return false;
            }

            if ($.isNumeric(phone) == false) {
                swal("Error!", "Phone number is not numeric", "error");
                $('#phone').css('border', '1px solid red').focus();
                return false;
            }
            $('#phone').css('border', '');

            $('.loading').show();
            $.get('/check_member_verification/by_phone', {area: areacode, phone: phone })
            .success(function( response ) {
                if(response == 1) {
                    phone = areacode + phone;
                    $.get("/sms_verification", {phone: phone, action: 'tac_member_password_reset'})
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
                } else if(response == 0) {
                    swal("", "@lang('localize.only_active_account_and_verified_phone_reset')", "error");
                } else {
                    swal("Error!", "@lang('localize.invalid_operation')", "error");
                }

                $('.loading').hide();
            });
        });

        $('#reset_by_email').click(function() {
            var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
            var email = $('#email').val();

            if (!email) {
                $('#email').css('border', '1px solid red').focus();
                return false;
            }

            if(!emailReg.test(email)) {
                swal("Error!", "@lang('localize.email_validation_error')", "error");
                $('#email').css('border', '1px solid red').focus();
                return false;
            }
            $('#email').css('border', '');

            $.get('/check_member_verification/by_email', {email: email })
            .success(function( data ) {
                if(data == 1) {
                    $('#email-reset').submit();
                    $('.loading').show();
                    return false;
                } else if(data == 0) {
                    swal("", "@lang('localize.only_active_account_and_verified_email_reset')", "error");
                    return false;
                }

                swal("Error!", "@lang('localize.invalid_operation')", "error");
                return false;
            });
        });

        $('#reset_by_phone').click(function() {
            var phone = $('#phone').val();
            var area = $('#areacode').val();
            var tac = $('#tac').val();

            if (phone == '') {
                $('#phone').css('border', '1px solid red').focus();
                return false;
            }

            if (tac == '') {
                swal("Error!", "@lang('localize.tac_required')", "error");
                $('#tac').css('border', '1px solid red').focus();
                return false;
            }

            if ($.isNumeric(phone) == false) {
                swal("Error!", "Phone number is not numeric", "error");
                $('#phone').css('border', '1px solid red').focus();
                return false;
            }
            $('#phone').css('border', '');
            $('#tac').css('border', '');

            $('#phone-reset').submit();
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
</script>
@endsection
