@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
	<div class="ListingTopbar">
        <h4 class="ListingCategory">@lang('localize.security_questions')</h4>
        <a href="/profile" class="back"><i class="fa fa-angle-left"></i></a>
    </div>

    <div class="ContentWrapper">
    @include('layouts.partials.status')
        <div class="panel-general">
            <div class="form-general">
                <h4 class="form-title">@lang('localize.security_questions')</h4>
                <form action="{{ url('/profile/security/question') }}" id="update-security" method="POST" accept-charset="UTF-8">
			    {{ csrf_field() }}

                    <div class="form-group">
                        <label class="control-label">@lang('localize.security_question_1')</label>
                        <select id="question-1" class="form-control security-question compulsary" name="security_question_1">
                            <option value="0" class="hidden">@lang('localize.select_security_questions')</option>
                            @foreach($questions as $question)
                                <option value="{{ $question->id }}">{{ $question->question }}</option>
                            @endforeach
                        </select>
                        <input class="form-control input security-answer compulsary" id="answer-1" name="security_answer_1" placeholder="@lang('localize.answer')" autocomplete="off" value="">
                    </div>

                    <div class="form-group">
                        <label class="control-label">@lang('localize.security_question_2')</label>
                        <select id="question-2" class="form-control security-question compulsary" name="security_question_2">
                            <option value="0" class="hidden">@lang('localize.select_security_questions')</option>
                            @foreach($questions as $question)
                                <option value="{{ $question->id }}">{{ $question->question }}</option>
                            @endforeach
                        </select>
                        <input class="form-control input security-answer compulsary" id="answer-2" name="security_answer_2" placeholder="@lang('localize.answer')" autocomplete="off" value="">
                    </div>

                    <div class="form-group">
                        <label class="control-label">@lang('localize.security_question_3')</label>
                        <select id="question-3" class="form-control security-question compulsary" name="security_question_3">
                            <option value="0" class="hidden">@lang('localize.select_security_questions')</option>
                            @foreach($questions as $question)
                                <option value="{{ $question->id }}">{{ $question->question }}</option>
                            @endforeach
                        </select>
                        <input class="form-control input security-answer compulsary" id="answer-3" name="security_answer_3" placeholder="@lang('localize.answer')" autocomplete="off" value="">
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

                    <div class="form-group">
                        <button type="submit" class="btn btn-block btn-primary">@lang('localize.update')</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('backend/js/plugins/validate/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('backend/js/custom.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready( function() {
            $('.security-question').change(function() {
                var q1 = $('#question-1').val();
                var q2 = $('#question-2').val();
                var q3 = $('#question-3').val();

                refreshquestions(q1,q2,q3);
            });

            $('#code_by_phone').on('click', function(event) {
                $('.loading').show();
                event.preventDefault();

                $.get("/sms_verification", {type: 'sms', action: 'tac_security_question_update'})
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

				$.get("/sms_verification", {type: 'email', action: 'tac_security_question_update'})
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

            $("#update-security").validate({

                rules: {
                    security_question_1: {
                        required:true,
                        min: 1
                    },
                    security_question_2: {
                        required:true,
                        min: 1
                    },
                    security_question_3: {
                        required:true,
                        min: 1
                    },
                    security_answer_1: "required",
                    security_answer_2: "required",
                    security_answer_3: "required",
                    tac: "required",
                },

                messages: {
                    security_question_1: {
                        required: "{{ trans('localize.required') }}",
                        min: "{{ trans('localize.required') }}"
                    },
                    security_question_2: {
                        required: "{{ trans('localize.required') }}",
                        min: "{{ trans('localize.required') }}"
                    },
                    security_question_3: {
                        required: "{{ trans('localize.required') }}",
                        min: "{{ trans('localize.required') }}"
                    },
                    security_answer_1: "{{ trans('localize.required') }}",
                    security_answer_2: "{{ trans('localize.required') }}",
                    security_answer_3: "{{ trans('localize.required') }}",
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

            function refreshquestions(q1, q2, q3)
            {
                $("#question-1 option").removeClass("hidden");
                $("#question-1 option[value=" + q2 + "]").addClass("hidden");
                $("#question-1 option[value=" + q3 + "]").addClass("hidden");

                $("#question-2 option").removeClass("hidden");
                $("#question-2 option[value=" + q1 + "]").addClass("hidden");
                $("#question-2 option[value=" + q3 + "]").addClass("hidden");

                $("#question-3 option").removeClass("hidden");
                $("#question-3 option[value=" + q1 + "]").addClass("hidden");
                $("#question-3 option[value=" + q2 + "]").addClass("hidden");
            }

        });
    </script>
@endsection
