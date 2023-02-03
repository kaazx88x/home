@extends('merchant.layouts.master')

@section('title', 'Edit Store User')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>@lang('localize.edit_store_user')</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/merchant/store/user/manage">@lang('localize.store_user')</a>
            </li>
            <li class="active">
                <strong>@lang('localize.edit')</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInUp">
    <div class="ibox float-e-margins border-bottom">
        <div class="ibox-title ibox-title-filter">
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-2 col-sm-offset-10">
                        <button type="button" class="btn btn-white btn-sm btn-block" data-toggle="modal" data-target="#password_reset_modal">@lang('localize.reset_password')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.common.errors')
    @include('admin.common.error')
    @include('admin.common.success')
    @include('admin.common.status')
    <div class="row">
        <form action="{{url('merchant/store/user/edit')}}" method="POST" class="form">
            {{ csrf_field() }}
            <div class="col-lg-12 m-b">
                <div class="col-md-2 pull-right">
                    <button class="btn btn-block btn-primary" type="submit">@lang('localize.update')</button>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>@lang('localize.store_user_information')</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <label class="control-label">@lang('localize.username') <span class='text-danger'>*</span></label>
                            <input type="text" class="form-control" name="username" id="username" value="{{ old('username', $user->username) }}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.email')</label>
                            <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $user->email) }}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.name') <span class='text-danger'>*</span></label>
                            <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $user->name) }}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.phone') </label>
                            <input type="number" class="form-control" name="phone" id="phone" value="{{ old('phone', $user->phone) }}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">@lang('localize.stores') </label>
                            @foreach($stores->chunk(4) as $row)
                            <div class="row">
                                @foreach($row as $key => $store)
                                    <div class="col-md-3 m-b-xs">
                                        <input type="checkbox" class="i-checks" name="store_list[]" value="{{ $store->stor_id }}" {{ (!$user->assigned_stores->where('store_id', $store->stor_id)->isEmpty())? 'checked' : '' }} >
                                        <label><a class="nolinkcolor" href="/merchant/store/edit/{{ $store->stor_id }}" data-toggle="tooltip" title="">{{ $store->stor_name }}</a></label>
                                    </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="exist" value="{{ ($stores->count() > 0)? 1 : 0 }}">
                        <input type="hidden" name="user_id" value="{{$user->id}}">
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="col-md-2 pull-right">
                    <button class="btn btn-block btn-primary" type="submit">@lang('localize.update')</button><br>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal inmodal" id="password_reset_modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content animated flipInY">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Reset Password</h4>
                <small class="font-bold">This form will <span class="text-danger">override exsisting store user password</span> and email the new password based on details below.</small>
            </div>
            <div class="modal-body">
                <form id="resetpassword" action="/merchant/store/user/reset_password" class="form-horizontal" method="POST">
                {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label">@lang('localize.store_user_information')</label>
                        <p class="form-control-static">
                            <span>@lang('localize.username') : {{$user->username}}</span><br>
                            <span>@lang('localize.email') : {{$user->email}}</span><br>
                            <span>@lang('localize.name') : {{$user->name}}</span><br>
                            <span>@lang('localize.phone') : {{$user->phone}}</span><br>
                        </p>
                    </div>
                    <div class="form-group">
                        <label class="control-label">@lang('localize.new_password')</label>
                        <input type="password" class="form-control" id="password" name="password" onchange="check_length(this.value);">
                    </div>
                    <div class="form-group">
                        <label class="control-label">@lang('localize.confirm_password')</label>
                        <input type="password" class="form-control" id="confirmpassword" name="password_confirmation" onchange="check_password(this.value);">
                    </div>
                    <input type="hidden" name="user_id" value="{{$user->id}}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="reset_password" class="btn btn-primary"><i class="fa fa-envelope-o"></i> @lang('localize.reset_password')</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
<link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script src="/backend/js/plugins/iCheck/icheck.min.js"></script>
<script src="/backend/js/custom.js"></script>
<script>

    $(document).ready(function() {

        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
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
                confirmButtonText: "Reset Password",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function(){
                    $("#resetpassword").submit();
                }
            );

        });
    });

    $("#username").on({
        keydown: function(e) {
            if (e.which === 32)
            return false;
        },
        change: function(e) {
            this.value = this.value.replace(/\s/g, "");
            check_storeuser_username($(this), $(this).val(), '{{ $user->id }}', e);
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
                 check_storeuser_email($(this), $(this).val(), '{{ $user->id }}', e);
        }
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
