@extends('admin.layouts.master')
@section('title', 'Admin Users')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Admin_Users')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin">{{trans('localize.dashboard')}}</a>
            </li>
            <li class="active">
                <strong>{{trans('localize.manage')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
             @include('admin.common.success')
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{trans('localize.Administrator_List')}}</h5>
                    <div class="ibox-tools">
                        <a href="/admin/admin-user/edit/{{$adm_id}}" class="btn btn-primary btn-xs">{{trans('localize.My_Account')}}</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center text-nowrap">{{trans('localize.Name')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Email')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Phone')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Username')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Status')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Role')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Last_Login_Date')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Last_Login_IP')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Action')}}</th>
                                </tr>
                            </thead>
                            @if ($adminusers)
                                <tbody>
                                    @foreach($adminusers as $adminuser)
                                    <tr class="text-center text-nowrap">
                                        <td>{{$adminuser->adm_fname}} {{$adminuser->adm_lname}}</td>
                                        <td>{{$adminuser->email}}</td>
                                        <td>{{$adminuser->adm_phone}}</td>
                                        <td>{{$adminuser->username}}</td>
                                        <td><span class="label label-{{$adminuser->status?'primary':'default'}}" style="display:block">{{$adminuser->status? trans('localize.Active') : trans('localize.Inactive') }}</span></td>
                                        <td><span class="label label-{{($adminuser->name == 'superuser')? 'primary' : 'info' }}" style="display:block">{{ ucwords(str_replace('_',' ',$adminuser->name)) }}</span></td>
                                        {{-- <td>{{$adminuser->last_login_date}}</td> --}}
                                        <td>{{ ($adminuser->last_login_date) ? \Helper::UTCtoTZ($adminuser->last_login_date) : '' }}</td>
                                        <td>{{$adminuser->last_login_ip}}</td>
                                        <td><a href="/admin/admin-user/edit/{{$adminuser->adm_id}}" class="btn btn-white btn-sm btn-block"><i class="fa fa-pencil"></i> {{trans('localize.Edit')}} </a></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="9">
                                            <div class=" col-xs-6">
                                                <span class="pagination">
                                                    {{trans('localize.Showing')}} {{$adminusers->firstItem()}} {{trans('localize.to')}} {{$adminusers->lastItem()}} {{trans('localize.of')}} {{$adminusers->Total()}} {{trans('localize.Records')}}
                                                </span>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                {{$adminusers->appends(Request::except('page'))->links()}}
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            @else
                                <tr>
                                    <td colspan="9" class="text-center">@lang('localize.nodata')</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection