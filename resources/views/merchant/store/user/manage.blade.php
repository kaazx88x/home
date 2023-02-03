@extends('merchant.layouts.master')

@section('title', 'Manage Store Users')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>@lang('localize.manage_store_user')</h2>
        <ol class="breadcrumb">
            <li>
                @lang('localize.store_user')
            </li>
            <li class="active">
                <strong>{{trans('localize.manage')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    {{-- <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <form id="filter" action='/sitemerchant/store/manage' method="GET">
                <div class="col-sm-1">
                    <div class="form-group">
                        <input type="text" value="{{$input['id']}}" placeholder="{{trans('localize.#id')}}" class="form-control" id="id" name="id">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <input type="text" value="{{$input['name']}}" placeholder="{{trans('localize.mer_store_name')}}" class="form-control" id="name" name="name">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <select class="form-control" id="type" name="type">
                            <option value="" {{ ($input['type'] == "") ? 'selected' : '' }}>{{trans('localize.type')}}</option>
                            <option value="1" {{ ($input['type'] == "1") ? 'selected' : '' }}>{{trans('localize.offline_store')}}</option>
                            <option value="0" {{ ($input['type'] == "0") ? 'selected' : '' }}>{{trans('localize.online_store')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <select class="form-control" id="status" name="status">
                            <option value="" {{ ($input['status'] == "") ? 'selected' : '' }}>{{trans('localize.status')}}</option>
                            <option value="1" {{ ($input['status'] == "1") ? 'selected' : '' }}>{{trans('localize.active')}}</option>
                            <option value="0" {{ ($input['status'] == "0") ? 'selected' : '' }}>{{trans('localize.inactive')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <select class="form-control" id="sort" name="sort" style="font-family:'FontAwesome', sans-serif;">
                            <option value="name_asc" {{ ($input['sort'] == "name_asc") ? 'selected' : '' }}>{{trans('localize.productName')}} : &#xf15d;</option>
                            <option value="name_desc" {{ ($input['sort'] == "name_desc") ? 'selected' : '' }}>{{trans('localize.productName')}} : &#xf15e;</option>
                            <option value="new" {{($input['sort'] == "" || $input['sort'] == 'new') ? 'selected' : ''}}>{{trans('localize.newest')}}</option>
                            <option value="old" {{($input['sort'] == 'old') ? 'selected' : ''}}>{{trans('localize.oldest')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-block btn-outline btn-primary" id="filter">{{trans('localize.search')}}</button>
                </div>
            </form>
        </div>
    </div> --}}

    <div class="row">
        <div class="col-lg-12">
            @include('merchant.common.success')
            @include('merchant.common.error')
            @include('merchant.common.errors')
            <div class="ibox">
                <div class="ibox-content">
                    <div class="ibox-tools">
                        {{-- <button type="button" href="/sitemerchant/store/user/add" class="">Create New User</button> --}}
                        <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#create_user">@lang('localize.create_account')</button>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">{{trans('localize.name')}}</th>
                                    <th class="text-center">{{trans('localize.phone')}}</th>
                                    <th class="text-center">{{trans('localize.username')}}</th>
                                    <th class="text-center">{{trans('localize.email')}}</th>
                                    <th class="text-center">@lang('localize.store_permission')</th>
                                    <th class="text-center">{{trans('localize.status')}}</th>
                                    <th class="text-center">{{trans('localize.action')}}</th>
                                </tr>
                            </thead>
                            @if ($users->total())
                                <tbody>
                                    @foreach($users as $user)
                                    <tr class="text-center">
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td class="text-left">
                                            <ul>
                                                @foreach($user->assigned_store as $store)
                                                <li><a class="nolinkcolor" href="/merchant/store/edit/{{ $store->stor_id}}" data-toggle="tooltip" title="" data-original-title="{{ trans('localize.update_store') }}">{{ $store->stor_name }}</a></li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td>
                                            @if($user->status == 1)
                                            <label class="text-navy">@lang('localize.active')</label>
                                            @elseif($user->status == 0)
                                            <label class="text-warning">@lang('localize.inactive')</label>
                                            @endif
                                        </td>
                                        <td style="width:1%;">
                                            <a href="/merchant/store/user/edit/{{ $user->id }}" class="btn btn-white btn-block btn-sm"><i class="fa fa-pencil"></i> @lang('localize.edit')</a>
                                            @if($user->status == 0)
                                            <a href="/merchant/store/user/status/{{ $user->id }}/1" class="btn btn-white btn-block btn-sm text-navy"><i class="fa fa-refresh"></i> @lang('localize.set_to_active')</a>
                                            @elseif($user->status == 1)
                                            <a href="/merchant/store/user/status/{{ $user->id }}/0" class="btn btn-white btn-block btn-sm text-warning"><i class="fa fa-refresh"></i> @lang('localize.set_to_inactive')</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="12">
                                            <div class=" col-xs-6">
                                                <span class="pagination">
                                                    Showing {{$users->firstItem()}} to {{$users->lastItem()}} of {{$users->Total()}} Records
                                                </span>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <div class="col-xs-7 text-right">
                                                    {{$users->appends(Request::except('page'))->links()}}
                                                </div>
                                                <div class="col-xs-5 text-right pagination">
                                                    Go To Page
                                                    <input type='number' id='pageno' name='pageno' size="3" min="1" max="{{$users->lastPage()}}">
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="gotopage('page')">
                                                        <i class="fa fa-share-square-o"></i> Go
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            @else
                                <tr>
                                    <td colspan="12" class="text-center">@lang('localize.nodata')</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal inmodal" id="create_user" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <div class="row">
                    <div class="col-sm-3"><img src="{{ url('/assets/images/meihome_logo.png') }}" alt="" style="width:auto; height:35px;"></div>
                    <div class="col-sm-7 text-center" style="vertical-align: middle;"><h3>@lang('localize.create_account')</h3></div>
                </div>
            </div>
            <div class="modal-body">
                <form id="create_user_submit" class="form-horizontal" action='/merchant/store/user/add' method="POST">
                {{ csrf_field() }}
                    <div class="form-group">
                        <label class="col-sm-3 control-label">@lang('localize.name') <span style="color:red;">*</span></label>
                        <div class="col-lg-9">
                            <input type="text" placeholder="@lang('localize.enter_name')" name="name" id="name" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">@lang('localize.phone')</label>
                        <div class="col-lg-9">
                            <input type="number" placeholder="@lang('localize.enter_phone')" name="phone" id="phone" class="form-control">
                        </div>
                    </div><div class="form-group">
                        <label class="col-sm-3 control-label">@lang('localize.username') <span style="color:red;">*</span></label>
                        <div class="col-lg-9">
                            <input type="text" placeholder="@lang('localize.enter_username')" name="username" id="username" class="form-control" onchange="check_username_ajax(this.value);">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">&nbsp;</label>
                        <div class="col-lg-9">
                            <input type="checkbox" name="setpassword" value="setpassword" id='setpassword'> @lang('localize.manual_set_password')<br>
                        </div>
                    </div>


                    <div class="form-group" id="emaildiv">
                        <label class="col-lg-3 control-label">@lang('localize.email') <span style="color:red;">*</span></label>
                        <div class="col-lg-9">
                            <input type="email" placeholder="@lang('localize.enter_email')" name="email" id="email" class="form-control" onchange="check_email_ajax(this.value);">
                        </div>
                    </div>
                    <div class="form-group" id="passworddiv" style="display: none;">
                        <label class="col-lg-3 control-label">@lang('localize.password') <span style="color:red;">*</span></label>
                        <div class="col-lg-9">
                            <input type="password" placeholder="@lang('localize.password')" name="password" id="password" class="form-control" >
                        </div>
                    </div>
                    <div class="form-group" id="confirmpassworddiv" style="display: none;">
                        <label class="col-lg-3 control-label">@lang('localize.confirmpassword') <span style="color:red;">*</span></label>
                        <div class="col-lg-9">
                            <input type="password" placeholder="@lang('localize.confirmPassword')" name="password_confirmation" id="confirmpassword" class="form-control" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">@lang('localize.stores')</label>
                        <div class="col-lg-9">
                            <div class="row">
                                @foreach($stores as $key => $store)
                                    <div class="col-sm-12">
                                        <input type="checkbox" class="i-checks" name="store_list[]" value="{{ $store->stor_id }}" {{ ($store->assigned == 1)? 'checked' : '' }} >&nbsp;
                                        <label><a class="nolinkcolor" href="/merchant/store/user/edit/{{ $store->id}}" data-toggle="tooltip" title="">{{ $store->stor_name }}</a></label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">@lang('localize.closebtn')</button>
                <button type="button" id="submit" class="btn btn-primary">@lang('localize.create_account')</button>
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
<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();

        $("#setpassword").change(function() {
            if(this.checked) {
                $("#confirmpassworddiv").show();
                $("#passworddiv").show();
                $("#emaildiv").hide();
                $('#email').removeAttr('value');

            }else{
                $("#confirmpassworddiv").hide();
                $("#passworddiv").hide();
                $("#emaildiv").show();
                $('#confirmpassword').removeAttr('value');
                $('#password').removeAttr('value');
            }
        });

        $('#submit').click(function() {
            var usernameReg = /^[a-zA-Z0-9]*$/;

            if($('#name').val() == '') {
                swal("{{trans('localize.error')}}","{{trans('localize.name_error')}}",'error');
                return false;
            }

            if($('#username').val() == '') {
                swal("{{trans('localize.error')}}","{{trans('localize.username_error')}}",'error');
                return false;
            } else if (!usernameReg.test($('#username').val())) {
                swal("@lang('localize.error')", "@lang('localize.username_may_only_contain_letters_and_numbers')", "error");
                return false;
            }

            if($( "#setpassword" ).is(':checked') == false){
                if($('#email').val() == '') {
                    swal("{{trans('localize.error')}}","{{trans('localize.email_error')}}",'error');
                    return false;
                }
            }

            if($( "#setpassword" ).is(':checked') == true){
                $('#email').prop('disabled', true);

                if($('#password').val() == '') {
                    swal("{{trans('localize.error')}}","{{trans('localize.password_error')}}",'error');
                    return false;
                }

                if($('#confirmpassword').val() == '') {
                    swal("{{trans('localize.error')}}","{{trans('localize.confirmnewpassword')}}",'error');
                    return false;
                }
            } else {
                $('#confirmpassword').prop('disabled', true);
                $('#password').prop('disabled', true);
            }

            swal({
                title: "{{trans('localize.sure')}}",
                text: "{{trans('localize.confirm_create_new_store_user')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#5cb85c",
                confirmButtonText: "{{trans('localize.yes')}}",
                cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: true
            }, function(isConfirm) {
                if(isConfirm) {
                    $("#create_user_submit").submit();
                } else {
                    return false;
                }
            });

        });

        $("#pageno").change(function(){
            var input = $(this).val();
            var max = {{$users->lastPage()}};
            if(input > max){
                $(this).val(max);
            }
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
                        if (responseText > 0) {
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

    function check_username_ajax(username) {

        var cusername = $('#username');
        if (cusername.val().length < 4) {
            swal("{{trans('localize.error')}}", "{{trans('localize.username_validation_error')}}", "error");
            cusername.css('border', '1px solid red');
            cusername.focus();
            return false;
        } else {
            var passusername = 'username=' + cusername.val();
            $.ajax({
                type: 'get',
                data: passusername,
                url: '/store_usernamecheck',
                success: function (responseText) {
                    if (responseText) {
                        if (responseText > 0) {
                            swal("{{trans('localize.username')}}!", cusername.val()+"{{trans('localize.username_check_error')}}", "error");
                            cusername.css('border', '1px solid red');
                            cusername.val('');
                            cusername.focus();
                            return false;
                        } else {
                            cusername.css('border', '');
                        }
                    }
                }
            });
        }
    }

     function gotopage($page) {
        $val =  $("#pageno").val();

        var href = window.location.href.substring(0, window.location.href.indexOf('?'));
        var qs = window.location.href.substring(window.location.href.indexOf('?') + 1, window.location.href.length);
        var newParam = $page + '=' + $val;

        if (qs.indexOf($page + '=') == -1) {
            if (qs == '') {
                qs = '?'
            }
            else {
                qs = qs + '&'
            }
            qs = newParam;

        }
        else {
            var start = qs.indexOf($page + "=");
            var end = qs.indexOf("&", start);
            if (end == -1) {
                end = qs.length;
            }
            var curParam = qs.substring(start, end);
            qs = qs.replace(curParam, newParam);
        }
        window.location.replace(href + '?' + qs);
    }
</script>
@endsection
