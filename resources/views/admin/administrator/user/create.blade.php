@extends('admin.layouts.master')
@section('title', 'Create Admin')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.create_new_admin_user')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin">{{ trans('localize.dashboard') }}</a>
            </li>
            <li>
                <a href="/admin/administrator/user">{{ trans('localize.admin_users') }}</a>
            </li>
            <li class="active">
                <strong>{{ trans('localize.create') }}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ trans('localize.general_info') }}</h5>
                </div>

                <div class="ibox-content">
                    @include('admin.common.errors')
                    <form id="create_admin" class="form-horizontal" action='/admin/administrator/user/add' method="POST">
                         {{ csrf_field() }}
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.firstname') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{ trans('localize.firstname') }}" class="form-control compulsary" name='fname' value='{{old('fname')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.lastname') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{ trans('localize.lastname') }}" class="form-control compulsary" name='lname' value='{{old('lname')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.email') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <input type="email" placeholder="{{ trans('localize.email') }}" class="form-control compulsary email" name='email' value='{{old('email')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.phone') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{ trans('localize.phone') }}" class="form-control compulsary phonenum" name='phone' value='{{old('phone')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.username') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{ trans('localize.username') }}" class="form-control compulsary" name='username' value='{{old('username')}}'>
                                <span class="help-block m-b-none">{{ trans('localize.alphanumeric_only_maximum_100_length') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.role') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <select name="role" class="form-control compulsary">
                                    <option value="">-- {{ trans('localize.choose_administrator_role') }} --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{$role->id}}" {{ (old('role') == $role->id)? 'selected' : '' }}>{{ $role->display_name }}</option>
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
                                        <input type="checkbox" class="i-checks" name="countries[]" value="{{ $country->co_id }}"> {{ $country->co_name }}
                                    </div>
                                </div>
                                @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.status') }}</label>
                            <div class="col-lg-10">
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="1" name="status" {{old('status')=='1' || empty(old('status'))?'checked':''}} > <i></i> {{ trans('localize.active') }}
                                    </label>
                                </div>
                                <div class="i-checks">
                                    <label> <input type="radio" value="0" name="status" {{old('status')=='0'?'checked':''}}> <i></i> {{ trans('localize.inactive') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <div class="col-lg-2 col-lg-offset-2">
                                <button class="btn btn-md btn-block btn-primary form_submit" type="button">{{ trans('localize.create') }}</button>
                                <span class="help-block m-b-none">{{ trans('localize.email_will_be_sent_to_this_user_for_creating_password') }}</span>
                            </div>
                        </div>
                    </form>
                </div>
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

<script>
$(document).ready( function() {

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
            $("#create_admin").submit();
        }
    });
});
</script>
@endsection