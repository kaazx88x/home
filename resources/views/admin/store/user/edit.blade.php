@extends('admin.layouts.master')

@section('title', 'Edit Profile')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Edit')}} {{trans('localize.store_user')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin/store/user/{{$mer_id}}">{{trans('localize.manage')}} {{trans('localize.store_user')}}</a>
            </li>
            <li class="active">
                <strong>{{trans('localize.Edit')}}</strong>
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
                        @if($reset_password_permission)
                        <button type="button" class="btn btn-white btn-sm btn-block" data-toggle="modal" data-target="#password_reset_modal">{{trans('localize.Reset_Password')}}</button>
                        @endif
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
        <form action="{{url('admin/store/user/edit')}}" method="POST" class="form">
            {{ csrf_field() }}
            <div class="col-lg-12 m-b">
                <div class="col-md-2 pull-right">
                    <button class="btn btn-block btn-primary" type="submit">{{trans('localize.Update')}}</button>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{trans('localize.store_user')}} {{trans('localize.information')}}</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.Username')}}</label>
                            <input class="form-control" name="username" id="username" value="{{old('username', $user->username)}}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.email')}}</label>
                            <input type="email" class="form-control" name="email" id="email" value="{{old('email', $user->email)}}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.Name')}} <span class='text-danger'>*</span></label>
                            <input type="text" class="form-control" name="name" id="name" value="{{old('name', $user->name)}}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.Phone')}} </label>
                            <input type="text" class="form-control" name="phone" id="phone" value="{{old('phone', $user->phone)}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.Stores')}} </label>
                            @foreach($stores->chunk(4) as $row)
                            <div class="row">
                                @foreach($row as $key => $store)
                                    <div class="col-md-3 m-b-xs">
                                        <input type="checkbox" class="i-checks" name="store_list[]" value="{{ $store->stor_id }}" {{ (!$user->assigned_stores->where('store_id', $store->stor_id)->isEmpty())? 'checked' : '' }} >
                                        <label><a class="nolinkcolor" href="{{ url('admin/store/edit', [$user->mer_id, $store->stor_id]) }}" data-toggle="tooltip" title="">{{ $store->stor_name }}</a></label>
                                    </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="exist" value="{{ ($stores->count() > 0)? 1 : 0 }}">
                        <input type="hidden" name="user_id" value="{{$user->id}}">
                        <input type="hidden" name="mer_id" value="{{$user->mer_id}}">
                    </div>
                </div>
            </div>

            <div class="col-lg-12 m-b">
                <div class="col-md-2 pull-right">
                    <button class="btn btn-block btn-primary" type="submit">{{trans('localize.Update')}}</button><br>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal inmodal" id="password_reset_modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content animated flipInY">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">{{trans('localize.close_')}}</span></button>
                <h4 class="modal-title">{{trans('localize.Reset_Password')}}</h4>
                <small class="font-bold">{{trans('localize.This_form_will')}} <span class="text-danger">{{trans('localize.override_exsisting_store_user_password')}}</span> {{trans('localize.and_email_the_new_password_based_on_details_below')}}</small>
            </div>
            <div class="modal-body">
                <form id="resetpassword" action="/admin/store/user/reset_password" class="form-horizontal" method="POST">
                {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.store_user')}} {{trans('localize.details')}} </label>
                        <p class="form-control-static">
                            <span>{{trans('localize.Username')}} : {{$user->username}}</span><br>
                            <span>{{trans('localize.email')}} : {{$user->email}}</span><br>
                            <span>{{trans('localize.Name')}} : {{$user->name}}</span><br>
                            <span>{{trans('localize.Phone')}} : {{$user->phone}}</span><br>
                        </p>
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.New_Password')}}</label>
                        <input type="password" class="form-control" id="password" name="password" onchange="check_length(this.value);">
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.Confirm_New_Password')}}</label>
                        <input type="password" class="form-control" id="confirmpassword" name="password_confirmation" onchange="check_password(this.value);">
                    </div>
                    <input type="hidden" name="user_id" value="{{$user->id}}">
                    <input type="hidden" name="mer_id" value="{{$user->mer_id}}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="reset_password" class="btn btn-primary"><i class="fa fa-envelope-o"></i> {{trans('localize.reset_password')}}</button>
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
                confirmButtonText: "{{trans('localize.reset_password')}}",
				cancelButtonText: "{{trans('localize.cancel')}}",
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
