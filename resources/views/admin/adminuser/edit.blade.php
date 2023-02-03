@extends('admin.layouts.master')
@section('title', 'Edit Admin')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Edit')}} {{trans('localize.Admin_Users')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin">{{trans('localize.dashboard')}}</a>
            </li>
            <li>
                <a href="/admin/admin-user">{{trans('localize.Admin_Users')}}</a>
            </li>
            <li class="active">
                <strong>{{trans('localize.Edit')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox float-e-margins border-bottom">
        <div class="ibox-title ibox-title-filter">
            <div class="row">
                <div class="col-sm-3 col-sm-offset-9">
                    <button type="button" class="btn btn-white btn-sm btn-block" data-toggle="modal" data-target="#password_reset_modal">{{trans('localize.Reset_Password')}}</button>
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
                    <h5>{{trans('localize.General_Info')}}</h5>
                </div>

                <div class="ibox-content">
                    <form id="update_admin" class="form-horizontal" action='/admin/admin-user/edit/{{$admin->adm_id}}' method="POST">
                         {{ csrf_field() }}
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.First_Name')}}</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{trans('localize.First_Name')}}" class="form-control compulsary" name='fname' value='{{ old('fname', $admin->adm_fname) }}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Last_Name')}}</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{trans('localize.Last_Name')}}" class="form-control compulsary" name='lname' value='{{ old('lname', $admin->adm_lname) }}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Email')}}</label>
                            <div class="col-lg-10">
                                <input type="email" placeholder="{{trans('localize.Email')}}" class="form-control compulsary email" name='email' value='{{ old('email', $admin->email) }}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Phone')}}</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{trans('localize.Phone')}}" class="form-control compulsary phonenum" name='phone' value='{{ old('phone', $admin->adm_phone) }}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Username')}}</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{trans('localize.Username')}}" class="form-control compulsary username" name='username' value='{{ old('username', $admin->username) }}'>
                                <span class="help-block m-b-none">{{trans('localize.alphanumeric_only_maximum_100_length')}}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Role')}}</label>
                            <div class="col-lg-10">
                                <select name="role" class="form-control compulsary">
                                    <option value="0">-- {{trans('localize.Choose_Administrator_Role')}} --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{$role->id}}" {{ (old('role', $admin->role_id) == $role->id)? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$role->name)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Status')}} </label>
                            <div class="col-lg-10">
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="1" name="status" {{ (old('status', $admin->status) == 1) ? 'checked' : '' }} > <i></i> {{trans('localize.Active')}}
                                    </label>
                                </div>
                                <div class="i-checks">
                                    <label> <input type="radio" value="0" name="status" {{ (old('status', $admin->status) == 0) ? 'checked' : '' }}> <i></i> {{trans('localize.Inactive')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <button class="btn btn-sm btn-primary form_submit" type="button">{{trans('localize.Update')}}</button>
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
                <h6 class="modal-title">{{trans('localize.Reset_Password')}}</h6>
                <small class="font-bold">{{trans('localize.This_form_will')}} <span class="text-danger">{{trans('localize.override_this_existing_administrator_password')}}</span>. {{trans('localize.This_feature_is_only_for_superuser_administrator')}}</small>
            </div>
            <div class="modal-body">
                <form id="resetpassword" action="/admin/admin-user/reset_password" class="form-horizontal" method="POST">
                {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.New_Password')}}</label>
                        <input type="password" class="form-control" id="password" name="password" onchange="check_length(this.value);">
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.Confirm_New_Password')}}</label>
                        <input type="password" class="form-control" id="confirmpassword" name="password_confirmation" onchange="check_password(this.value);">
                    </div>
                    <input type="hidden" name="adm_id" value="{{$admin->adm_id}}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="reset_password" class="btn btn-primary"><i class="fa fa-send"></i> {{trans('localize.Reset_Password')}}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script src="/backend/js/plugins/sweetalert/sweetalert.min.js"></script>
<script src="/backend/js/custom.js"></script>

<script>
    $(document).ready(function() {
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

        $(".username").on({
            keydown: function(e) {
                if (e.which === 32)
                return false;
            },
            change: function(e) {
                var usernameReg = /^[a-zA-Z0-9]*$/;
                this.value = this.value.replace(/\s/g, "");

                if (!usernameReg.test(this.value)) {
                    swal({
                        title: window.translations.error,
                        text: window.translations.username_invalid_character,
                        type: "error",
                        showCancelButton: false,
                        confirmButtonColor: "#d9534f",
                        confirmButtonText: window.translations.ok,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                    });
                    $(this).css('border', '1px solid red').focus();
                    e.preventDefault();
                    return false;
                }

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
                confirmButtonText: "{{trans('localize.Reset_Password')}}",
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
