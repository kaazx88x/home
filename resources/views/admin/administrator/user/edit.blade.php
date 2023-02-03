@extends('admin.layouts.master')
@section('title', 'Edit Admin')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{ trans('localize.edit_admin_user') }}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin">{{ trans('localize.dashboard') }}</a>
            </li>
            <li>
                <a href="/admin/administrator/user">{{ trans('localize.admin_users') }}</a>
            </li>
            <li class="active">
                <strong>{{ trans('localize.edit') }}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox float-e-margins border-bottom">
        <div class="ibox-title ibox-title-filter">
            <div class="row">
                <div class="col-sm-3 col-sm-offset-9">
                    @if($reset_permission)
                    <button type="button" class="btn btn-white btn-sm btn-block" data-toggle="modal" data-target="#password_reset_modal">{{ trans('localize.reset_password') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('admin.common.success')
    @include('admin.common.errors')
    @include('admin.common.error')

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ trans('localize.general_info') }}</h5>
                </div>

                <div class="ibox-content">
                    <form id="update_admin" class="form-horizontal" action="{{ url('admin/administrator/user/edit', [\Helper::encrypt($admin->adm_id)]) }}" method="POST">
                         {{ csrf_field() }}
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.firstname') }}</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{ trans('localize.firstname') }}" class="form-control compulsary" name='fname' value='{{ old('fname', $admin->adm_fname) }}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.lastname') }}</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{ trans('localize.lastname') }}" class="form-control compulsary" name='lname' value='{{ old('lname', $admin->adm_lname) }}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.email') }}</label>
                            <div class="col-lg-10">
                                <input type="email" placeholder="{{ trans('localize.email') }}" class="form-control compulsary email" name='email' value='{{ old('email', $admin->email) }}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.phone') }}</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{ trans('localize.phone') }}" class="form-control compulsary phonenum" name='phone' value='{{ old('phone', $admin->adm_phone) }}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.username') }}</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{ trans('localize.username') }}" class="form-control compulsary" name='username' value='{{ old('username', $admin->username) }}'>
                                <span class="help-block m-b-none">{{ trans('localize.email_will_be_sent_to_this_user_for_creating_password') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.role') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <select name="role" class="form-control compulsary">
                                    <option value="">-- {{ trans('localize.choose_administrator_role') }} --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{$role->id}}" {{ (old('role', ($admin->role->first())? $admin->role->first()->id : 0 ) == $role->id)? 'selected' : '' }}>{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.countries_access') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <div class="row">
                                @foreach($countries as $country)
                                <div class="col-lg-12 m-xxs">
                                    <div class="i-checks">
                                        <input type="checkbox" class="i-checks" name="countries[]" value="{{ $country->co_id }}" {{ ($admin->assignedCountries->keyBy('country_id')->has($country->co_id)? 'checked' : '') }}> {{ $country->co_name }}
                                    </div>
                                </div>
                                @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.status') }} </label>
                            <div class="col-lg-10">
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="1" name="status" {{ (old('status', $admin->status) == 1) ? 'checked' : '' }} > <i></i> {{ trans('localize.active') }}
                                    </label>
                                </div>
                                <div class="i-checks">
                                    <label> <input type="radio" value="0" name="status" {{ (old('status', $admin->status) == 0) ? 'checked' : '' }}> <i></i> {{ trans('localize.inactive') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                @if($edit_permission)
                                <button class="btn btn-sm btn-primary form_submit" type="button">{{ trans('localize.update') }}</button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal inmodal" id="password_reset_modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content animated flipInY">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h6 class="modal-title">{{trans('localize.reset_password')}}</h6>
                <small class="font-bold">{{trans('localize.reset_admin_reset_msg1')}} <span class="text-danger">{{trans('localize.reset_admin_reset_msg2')}}</span>. {{trans('localize.reset_admin_reset_msg3')}}</small>
            </div>
            <div class="modal-body">
                <form id="resetpassword" action="{{ url('admin/administrator/user/reset_password', [\Helper::encrypt($admin->adm_id)]) }}" class="form-horizontal" method="POST">
                {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.new_password')}}</label>
                        <input type="password" class="form-control" id="password" name="password" onchange="check_length(this.value);">
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.confirm_new_password')}}</label>
                        <input type="password" class="form-control" id="confirmpassword" name="password_confirmation" onchange="check_password(this.value);">
                    </div>
                    <input type="hidden" name="adm_id" value="{{$admin->adm_id}}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="reset_password" class="btn btn-primary"><i class="fa fa-send"></i> {{trans('localize.reset_password')}}</button>
            </div>
        </div>
    </div>
</div>
@endsection


@section('style')
<link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/iCheck/icheck.min.js"></script>
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script src="/backend/js/plugins/sweetalert/sweetalert.min.js"></script>
<script src="/backend/js/custom.js"></script>

<script>
$(document).ready(function() {

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    $('.col-lg-10').on('keydown', '.phonenum', function(e){-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});

    $('.email').change(function (e) {
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

        if(!emailReg.test($(this).val())) {
            swal("{{trans('localize.error')}}", "{{trans('localize.email_invalid')}}", "error");
            $(this).css('border', '1px solid red').focus();
            isValid = false;
            return false;
        } else {
            $(this).css('border', '');
        }
    });

    $('.phonenum').change(function (e) {
        if( !$.isNumeric($(this).val()) ) {
            swal("{{trans('localize.error')}}", "{{trans('localize.Phone_number_must_be_numeric_value')}}", "error");
            $(this).css('border', '1px solid red').focus();
            $(this).val('');
            return false;
        } else {
            $(this).css('border', '');
        }
    });

    $('.form_submit').click(function() {
        var isValid = true;

        $(':input').each(function(e) {
            if ($(this).hasClass('compulsary')) {
                if (!$(this).val()) {
                    $(this).attr('placeholder', '{{trans('localize.fieldrequired')}}').css('border', '1px solid red').focus();
                    isValid = false;
                    return false;
                }
            }

            $(this).css('border', '');
        });

        if (isValid) {
            $("#update_admin").submit();
        }
    });

    $('#reset_password').click(function() {
        if($('#password').val() == '') {
            $('#password').attr('placeholder', '{{trans('localize.fieldrequired')}}');
            $('#password').css('border', '1px solid red');
            return false;
        } else {
            $('#password').css('border', '');
        }

            if($('#confirmpassword').val() == '') {
            $('#confirmpassword').attr('placeholder', '{{trans('localize.fieldrequired')}}');
            $('#confirmpassword').css('border', '1px solid red');
            return false;
        } else {
            $('#confirmpassword').css('border', '');
        }

        swal({
            title: "{{trans('localize.sure')}}",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d9534f",
            confirmButtonText: "{{trans('localize.reset_password')}}",
            cancelButtonText: "{{trans('localize.cancel')}}",
            closeOnConfirm: false
        }, function(){
                $("#resetpassword").submit();
            }
        );

    });

});

function check_length(password) {
    var password = $('#password').val();
        if (password.length <  6)
        {
            $('#password').css('border', '1px solid red');
            swal("{{trans('localize.error')}}", "{{trans('localize.minpassword')}}", "error");
        }
        else {
            $('#password').css('border', '');
        }
    }

function check_password(confirmpassword) {
    var password = $('#password').val();
    if (confirmpassword !=  password)
    {
        swal("{{trans('localize.error')}}", "{{trans('localize.matchpassword')}}", "error");
        $('#confirmpassword').val('');
        $('#confirmpassword').css('border', '1px solid red');
        return false;
    } else {
        $('#confirmpassword').css('border', '');
        return true;
    }
}
</script>
@endsection
