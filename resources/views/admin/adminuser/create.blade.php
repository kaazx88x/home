@extends('admin.layouts.master')
@section('title', 'Create Admin')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Create_New_Admin_User')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin">{{trans('localize.dashboard')}}</a>
            </li>
            <li>
                <a href="/admin/admin-user">{{trans('localize.Admin_Users')}}</a>
            </li>
            <li class="active">
                <strong>{{trans('localize.Create')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{trans('localize.General_Info')}}</h5>
                </div>

                <div class="ibox-content">
                    @include('admin.common.errors')
                    <form id="create_admin" class="form-horizontal" action='/admin/admin-user/add' method="POST">
                         {{ csrf_field() }}
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.First_Name')}} <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{trans('localize.First_Name')}}" class="form-control compulsary" name='fname' value='{{old('fname')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Last_Name')}} <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{trans('localize.Last_Name')}}" class="form-control compulsary" name='lname' value='{{old('lname')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Email')}} <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <input type="email" placeholder="{{trans('localize.Email')}}" class="form-control compulsary email" name='email' value='{{old('email')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Phone')}} <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{trans('localize.Phone')}}" class="form-control compulsary phonenum" name='phone' value='{{old('phone')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Username')}} <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{trans('localize.Username')}}" class="form-control compulsary username" name='username' value='{{old('username')}}'>
                                <span class="help-block m-b-none">{{trans('localize.alphanumeric_only_maximum_100_length')}}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Role')}} <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <select name="role" class="form-control compulsary">
                                    <option value="0">-- {{trans('localize.Choose_Administrator_Role')}} --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{$role->id}}" {{ (old('role') == $role->id)? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$role->name)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Status')}}</label>
                            <div class="col-lg-10">
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="1" name="status" {{old('status')=='1' || empty(old('status'))?'checked':''}} > <i></i> {{trans('localize.Active')}}
                                    </label>
                                </div>
                                <div class="i-checks">
                                    <label> <input type="radio" value="0" name="status" {{old('status')=='0'?'checked':''}}> <i></i> {{trans('localize.Inactive')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <button class="btn btn-sm btn-primary form_submit" type="button">{{trans('localize.Create')}}</button>
                                <span class="help-block m-b-none">{{trans('localize.Email_will_be_sent_to_this_user_for_creating_password')}}</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
    <script>
        $(document).ready( function() {
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
                    swal("{{trans('localize.error')}}", "{{trans('lcoalize.Phone_number_must_be_numeric_value')}}", "error");
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
                    $("#create_admin").submit();
                }
            });
        });
    </script>
@endsection