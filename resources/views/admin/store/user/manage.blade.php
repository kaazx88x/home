@extends('admin.layouts.master')

@section('title', 'Manage Store Users')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>@lang('localize.manage_store_user')</h2>
        <ol class="breadcrumb">
            <li>
              @if ($merchant->mer_type == 0)
                    <a href="/admin/merchant/manage/online">{{trans('localize.Merchants')}} {{trans('localize.Online')}}</a>
                @elseif ($merchant->mer_type == 1)
                    <a href="/admin/merchant/manage/offline">{{trans('localize.Merchants')}} {{trans('localize.Offline')}}</a>
                @endif
            </li>
            <li>
                <a href="/admin/merchant/view/{{$mer_id}}">{{trans('localize.view')}} {{trans('localize.Merchants')}} </a>
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
                        @if($add_store_user_permission)
                        <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#create_user">@lang('localize.create_account')</button>
                        @endif
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
                                                    <li><a class="nolinkcolor" target="_blank" href="/admin/store/edit/{{ $mer_id }}/{{ $store->stor_id}}" data-toggle="tooltip" title="" data-original-title="{{ trans('localize.update_store') }}">{{ $store->stor_name }}</a></li>
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
                                        <a href="/admin/store/user/edit/{{ $mer_id }}/{{ $user->storeuser_id  }}" class="btn btn-white btn-block btn-sm"><i class="fa fa-pencil"></i> {{trans('localize.Edit')}}</a>
                                            @if($active_store_user_permission)
                                                @if($user->status == 0)
                                                <a href="/admin/store/user/status/{{ $user->storeuser_id  }}/1" class="btn btn-white btn-block btn-sm text-navy"><i class="fa fa-refresh"></i> {{trans('localize.set_to_active')}}</a>
                                                @elseif($user->status == 1)
                                                <a href="/admin/store/user/status/{{ $user->storeuser_id  }}/0" class="btn btn-white btn-block btn-sm text-warning"><i class="fa fa-refresh"></i> {{trans('localize.set_to_inactive')}}</a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10">
                                            <div class="row">
                                                <div class=" col-xs-6">
                                                    <span class="pagination">
                                                        {{trans('localize.Showing')}} {{$users->firstItem()}} {{trans('localize.to')}} {{$users->lastItem()}} {{trans('localize.of')}} {{$users->Total()}} {{trans('localize.Records')}}
                                                    </span>
                                                </div>
                                                <div class="col-xs-6 text-right">
                                                    {{$users->appends(Request::except('page'))->links()}}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            @else
                                <tr>
                                    <td colspan="10" class="text-center">@lang('localize.nodata')</td>
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
                <form id="create_user_submit" class="form-horizontal" action='/admin/store/user/add/{{ $mer_id }}' method="POST">
                {{ csrf_field() }}
                    <div class="form-group">
                        <label class="col-sm-3 control-label">@lang('localize.name') <span style="color:red;">*</span></label>
                        <div class="col-lg-9">
                            <input type="text" placeholder="@lang('localize.name')" name="name" id="name" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">@lang('localize.phone')</label>
                        <div class="col-lg-9">
                            <input type="number" placeholder="@lang('localize.phone')" name="phone" id="phone" class="form-control">
                        </div>
                    </div><div class="form-group">
                        <label class="col-sm-3 control-label">@lang('localize.username') <span style="color:red;">*</span></label>
                        <div class="col-lg-9">
                            <input type="text" placeholder="@lang('localize.username')" name="username" id="username" class="form-control">
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
                            <input type="email" placeholder="@lang('localize.email')" name="email" id="email" class="form-control">
                        </div>
                    </div>
                    <div class="form-group" id="passworddiv" style="display: none;">
                        <label class="col-lg-3 control-label">@lang('localize.password') <span style="color:red;">*</span></label>
                        <div class="col-lg-9">
                            <input type="password" placeholder="Enter password" name="password" id="password" class="form-control" >
                        </div>
                    </div>
                    <div class="form-group" id="confirmpassworddiv" style="display: none;">
                        <label class="col-lg-3 control-label">@lang('localize.confirmpassword') <span style="color:red;">*</span></label>
                        <div class="col-lg-9">
                            <input type="password" placeholder="Confirm password" name="password_confirmation" id="confirmpassword" class="form-control" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">@lang('localize.stores')</label>
                        <div class="col-lg-9">
                            <div class="row">
                                @foreach($stores as $key => $store)
                                    <div class="col-sm-12">
                                        <input type="checkbox" class="i-checks" name="store_list[]" value="{{ $store->stor_id }}" {{ ($store->assigned == 1)? 'checked' : '' }} >&nbsp;
                                        <label><a class="nolinkcolor" href="/admin/store/edit/{{ $store->stor_merchant_id }}/{{ $store->stor_id}}" data-toggle="tooltip" title="">{{ $store->stor_name }}</a></label>
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
<script src="/backend/js/custom.js"></script>
<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();

        $("#username").on({
            keydown: function(e) {
                if (e.which === 32)
                return false;
            },
            change: function(e) {
                this.value = this.value.replace(/\s/g, "");
                check_storeuser_username($(this), $(this).val(), '', e);
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
                    check_storeuser_email($(this), $(this).val(), '', e);
            }
        });

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
                swal('Error!',"{{trans('localize.name_error')}}",'error');
                return false;
            }

            if($('#username').val() == '') {
                swal('Error!',"{{trans('localize.username_error')}}",'error');
                return false;
            } else if (!usernameReg.test($('#username').val())) {
                swal("@lang('localize.error')", "@lang('localize.username_may_only_contain_letters_and_numbers')", "error");
                return false;
            }

            if($( "#setpassword" ).is(':checked') == false){
                if($('#email').val() == '') {
                    swal('Error!',"{{trans('localize.email_error')}}",'error');
                    return false;
                }
            }

            if($( "#setpassword" ).is(':checked') == true){
                $('#email').prop('disabled', true);

                if($('#password').val() == '') {
                    swal('Error!',"{{trans('localize.password_error')}}",'error');
                    return false;
                }

                if($('#confirmpassword').val() == '') {
                    swal('Error!',"{{trans('localize.confirmnewpassword')}}",'error');
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

    });
</script>
@endsection
