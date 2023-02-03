@extends('admin.layouts.master')
@section('title', 'Admin Users')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{ trans('localize.admin_users') }}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin">{{ trans('localize.dashboard') }}</a>
            </li>
            <li class="active">
                <strong>{{ trans('localize.manage') }}</strong>
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
                    <h5>{{ trans('localize.administrator_list') }}</h5>
                    <div class="ibox-tools">
                        <a href="/admin/administrator/user/edit/{{$adm_id}}" class="btn btn-primary btn-xs">{{ trans('localize.my_account') }}</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center text-nowrap">{{ trans('localize.name') }}</th>
                                    <th class="text-center text-nowrap">{{ trans('localize.email') }}</th>
                                    <th class="text-center text-nowrap">{{ trans('localize.phone') }}</th>
                                    <th class="text-center text-nowrap">{{ trans('localize.username') }}</th>
                                    <th class="text-center text-nowrap">{{ trans('localize.status') }}</th>
                                    <th class="text-center text-nowrap">{{ trans('localize.role') }}</th>
                                    <th class="text-center text-nowrap">{{ trans('localize.attempt_fail') }}</th>
                                    <th class="text-center text-nowrap">{{ trans('localize.last_login_date') }}</th>
                                    <th class="text-center text-nowrap">{{ trans('localize.last_login_ip') }}</th>
                                    <th class="text-center text-nowrap">{{ trans('localize.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($adminusers as $adminuser)
                                <tr class="text-center text-nowrap">
                                    <td>{{$adminuser->adm_fname}} {{$adminuser->adm_lname}}</td>
                                    <td>{{$adminuser->email}}</td>
                                    <td>{{$adminuser->adm_phone}}</td>
                                    <td>{{$adminuser->username}}</td>
                                    <td><span class="label label-{{$adminuser->status?'primary':'default'}}" style="display:block">{{$adminuser->status? trans('localize.active') : trans('localize.inactive') }}</span></td>
                                    <td>
                                        @if($adminuser->role->first())
                                            {{ $adminuser->role->first()->display_name }}
                                        @else
                                            <span class="label label-danger" style="display:block">
                                                {{trans('localize.not_assigned')}}
                                            </span>
                                        @endif

                                    </td>
                                    <td>{{$adminuser->attempt_fail}}</td>
                                    {{-- <td>{{$adminuser->last_login_date}}</td> --}}
                                    <td>{{ ($adminuser->last_login_date) ? \Helper::UTCtoTZ($adminuser->last_login_date) : '' }}</td>
                                    <td>{{$adminuser->last_login_ip}}</td>
                                    <td>
                                    <a href="/admin/administrator/user/edit/{{$adminuser->adm_id}}" class="btn btn-white btn-sm btn-block"><i class="fa fa-pencil"></i> {{ trans('localize.edit') }} </a>
                                    <p>
                                        @if($admin_lock_user)
                                            @if ($adminuser->account_locked == 0)
                                                <a style="width:100%" class="btn btn-white btn-sm text-danger lock-btn" href="/admin/administrator/user/lock/{{$adminuser->adm_id}}/lock"><span><i class="fa fa-lock"></i> {{ trans('localize.lock_admin') }}</span></a>
                                            @else

                                                <a style="width:100%" class="btn btn-white btn-sm text-success" href="/admin/administrator/user/lock/{{$adminuser->adm_id}}/unlock"><span><i class="fa fa-unlock"></i> {{ trans('localize.unlock_admin') }}</span></a>
                                            @endif
                                        @endif
                                    </p>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            {{-- <tfoot>
                                <tr>
                                    <td colspan="7">
                                        <ul class="pagination pull-right"></ul>
                                    </td>
                                </tr>
                            </tfoot> --}}
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection