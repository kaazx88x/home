@extends('layouts.web.master')

@section('header')
@include('layouts.web.header.backend')
@endsection

@section('content')
{{--  <div class="container">
    <div class="row grid auth-panel" style="padding-top: 2em; padding-bottom: 3em;">
        <br><center><strong style="font-size:1.75em; color:{{ (isset($type) && $type == 'success') ? 'green': 'red' }}">{{ strtoupper($title) }}</strong></center>
        <br>
        <p class="text-center" style="font-size:1.25em;">
            {{ $msg }}
        </p>

        @if(isset($merchant))
        <br>
        <p class="text-center" style="font-size:1.25em;">
            @lang('localize.not_receiving_confirmation_email')
        </p>
        <br><center><button id="send_activation" class="btn btn2">@lang('localize.resend_activation_email')</button></center>
        @endif
    </div>
</div>  --}}
<div class="ContentWrapper">
    <div class="row grid auth-panel" style="padding-top: 2em; padding-bottom: 3em;">
        <br><center><strong style="font-size:1.75em; color:{{ (isset($type) && $type == 'success') ? 'green': 'red' }}">{{ strtoupper($title) }}</strong></center>
        <br>
        <p class="text-center" style="font-size:1.25em;">
            {{ $msg }}
        </p>

        @if(isset($merchant))
        <br>
        <p class="text-center" style="font-size:1.25em;">
            @lang('localize.not_receiving_confirmation_email')
        </p>
        <br><center><button id="send_activation" class="btn btn2">@lang('localize.resend_activation_email')</button></center>
        @endif
    </div>
</div>
@endsection

@if(isset($merchant))
    @section('scripts')
    <script>
    $(document).ready(function (){
        $('#send_activation').click(function() {
            $('#send_activation').attr("disabled", true);
            $.get("{{ url('merchant/resend/activation', [$merchant->mer_id,$merchant->email]) }}")
            .success(function() {
            swal("{{ trans('localize.merchant.resend_activation_email.success') }}", "{{ trans('localize.merchant.resend_activation_email.msg') }}", "success");
            $('#send_activation').attr("disabled", false);
            });
        });
    });
    </script>
    @endsection
@endif