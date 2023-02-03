@extends('admin.layouts.master')
@section('title', 'Edit Admin')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{ trans('localize.edit_role') }}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin">{{ trans('localize.dashboard') }}</a>
            </li>
            <li>
                <a href="/admin/administrator/user">{{ trans('localize.administrator') }}</a>
            </li>
            <li>
                {{ trans('localize.role') }}
            </li>
            <li class="active">
                <strong>{{ trans('localize.edit') }}</strong>
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
                    @include('admin.common.notifications')
                    <form class="form-horizontal" action="{{ url('admin/administrator/role/edit', ['role_id' => $role->id]) }}" method="POST" id="role-edit-submit">
                         {{ csrf_field() }}
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.role_name') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{ trans('localize.role_name') }}" class="form-control" name="name" value="{{ old('name', $role->name)}} ">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.display_name') }}<span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{ trans('localize.display_name') }}" class="form-control" name="display_name" value="{{ old('display_name', $role->display_name) }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.description') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <textarea placeholder="{{ trans('localize.description') }}" class="form-control" name="description">{{ old('description', $role->description) }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.role_level') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <input type="number" placeholder="{{ trans('localize.role_level') }}" class="form-control" name="role_level" value="{{ old('role_level', $role->role_level) }}">
                            </div>
                        </div>
                         
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ trans('localize.permissions') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <div class="row">
                                    @foreach($groups as $name => $permissions)
                                    <?php $name_trim = str_replace(' ', '', $name); ?>
                                    <?php $key = 0;?>
                                    <div class="col-lg-12 border-size-md p-sm">
                                        @foreach($permissions->keyBy('id') as $index => $permission) 
                                        @if($key == 0)
                                        <?php $key ++;?>
                                        <h3>
                                            <div class="i-checks col-lg-12 border-bottom" style='padding-bottom:15px; padding-top:30px' >
                                                <div class='col-lg-4' stype='margin-left:30px'>{{ $name }} </div>
                                                <div class='col-lg-6'></div>
                                                <div class='col-lg-2'  style='padding-left:25px'><input type="checkbox"  class="i-checks parent_check" data-id="child-{{ $name_trim }}" {{ (array_key_exists($name, $role->permissions->groupBy('permission_group')->toArray()) && $role->permissions->groupBy('permission_group')[$name]->count() > 0 )? 'checked' : '' }}></div> 
                                            </div>
                                        </h3>
                                        @endif
                                        
                                        <div class="col-lg-12 border-bottom">
                                            <div class="form-group ">
                                                <div class="i-checks" style='padding-top:10px'>
                                                    
                                                    <div class="col-lg-4">
                                                        {{ $permission->display_name }}
                                                    </div>    
                                                    <div class="col-lg-6">
                                                        {{ $permission->description }}
                                                    </div>
                                                    
                                                    <div class="col-lg-2">
                                                    <input type="checkbox" class="i-checks child-{{ $name_trim }}" name="permissions[]" value="{{ $permission->id }}" {{ ($role->permissions->keyBy('id')->has($permission->id)? 'checked' : '') }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @endforeach
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-offset-10 col-lg-2">
                                @if($edit_permission)
                                <button class="btn btn-md btn-primary btn-block form_submit" type="submit">{{ trans('localize.update') }}</button>
                                @endif
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
<script src="/backend/js/plugins/validate/jquery.validate.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    $('.parent_check').on('ifToggled', function(event) {
        var child = $(this).attr('data-id');
        if(this.checked == true) {
            $('.'+child).iCheck('check');
        } else {
            $('.'+child).iCheck('uncheck');
        }
    });

    jQuery.validator.addMethod("alphanumeric", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
    });

    $("#role-edit-submit").validate({

        rules: {
            name: "required",
            display_name: "required",
            description: "required",
            role_level : "required",
        },

        messages: {
            name: "{{ trans('localize.required') }}",
            display_name: "{{ trans('localize.required') }}",
            description: "{{ trans('localize.required') }}",
            role_level: "{{ trans('localize.required') }}",
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
});
</script>

@endsection