@extends('merchant.layouts.master')

@section('title', 'Edit Profile')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>@lang('localize.setting')</h2>
        <ol class="breadcrumb">
            <li>
                @lang('localize.profile')
            </li>
            <li class="active">
                <strong>@lang('localize.edit')</strong>
            </li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
            @include('merchant.common.errors')
            @include('merchant.common.error')
            @include('merchant.common.success')
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Profile</h5>
                    <div class="ibox-tools">
                    </div>
                </div>
                <div class="ibox-content">
                    <form id="form_submit" action="{{url('store/profile/edit')}}" method="post">
                    {{ csrf_field() }}
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.username')}}</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">
                                    {{ $user->username }}
                                </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.name')}} <span class='text-danger'>*</span></label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" name="name" id="name" value="{{$user->name}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.email')}} <span class='text-danger'>*</span></label>
                            <div class="col-lg-10">
                                <input type="email" class="form-control" name="email" id="email" value="{{$user->email}}" onchange="check_email_ajax(this.value);">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.phone')}}</label>
                            <div class="col-lg-10">
                                <input type="number" class="form-control" name="phone" value="{{$user->phone}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-2 pull-right">
                                <button class="btn btn-primary btn-md btn-block pull-right" id="submit" ><a style="color:#fff" >{{trans('localize.update')}}</a></button>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('style')
<style>
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
@endsection

@section('script')
<script src="/backend/js/custom.js"></script>

<script>
    $(document).ready(function() {

        $('#submit').click(function() {
            if($('#name').val() == '') {
                swal('Error!',"{{trans('localize.name_error')}}",'error');
                return false;
            }

            if($('#email').val() == '') {
                swal('Error!',"{{trans('localize.email_error')}}",'error');
                return false;
            }

            $("#form_submit").submit();

        });
    });

    function check_email_ajax(email) {
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        var cemail = $('#email');

        if (!emailReg.test(cemail.val())) {
            swal("{{trans('localize.error')}}", "{{trans('localize.email_validation_error')}}", "error");
            cemail.css('border', '1px solid red');
            cemail.focus();
            return false;
        } else {
            var passemail = 'email=' + cemail.val();
            $.ajax({
                type: 'get',
                data: passemail,
                url: '/store_emailcheck',
                success: function (responseText) {
                    if (responseText) {
                        if (responseText == 1) {
                            swal("{{trans('localize.error')}}", cemail.val()+"{{trans('localize.email_check_error')}}", "error");
                            cemail.css('border', '1px solid red');
                            cemail.val('');
                            cemail.focus();
                            return false;
                        } else {
                            cemail.css('border', '');
                        }
                    }
                }
            });
        }
    }
</script>
@endsection
