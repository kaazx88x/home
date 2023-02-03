@extends('admin.layouts.master')
@section('title', 'Commission')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Commission')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin">{{trans('localize.dashboard')}}</a>
            </li>
            <li class="active">
                <strong>{{trans('localize.Commission')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    @include('admin.common.success')
    @include('admin.common.errors')
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{trans('localize.Commision_Info')}}</h5>
                </div>

                <div class="ibox-content">
                    <form class="form-horizontal" action='/admin/setting/commission' method="POST">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label class="col-lg-8 control-label">@lang('localize.Default_Online') @lang('localize.platform_charges') {{trans('localize.Rate')}}</label>
                            <div class="col-lg-4">
                                <div class="input-group m-b">
                                    <input type="number" placeholder="@lang('localize.Default_Online') @lang('localize.platform_charges') {{trans('localize.Rate')}}" class="form-control" name="platform_charge" value="{{($setting->platform_charge)?$setting->platform_charge : ''}}">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-8 control-label">@lang('localize.Default_Online') @lang('localize.gst') {{trans('localize.Rate')}}</label>
                            <div class="col-lg-4">
                                <div class="input-group m-b">
                                    <input type="number" placeholder="@lang('localize.Default_Online') @lang('localize.gst') {{trans('localize.Rate')}}" class="form-control" name="service_charge" value="{{($setting->service_charge)? $setting->service_charge : '' }}">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-8 control-label">@lang('localize.Default_Offline') @lang('localize.platform_charges') {{trans('localize.Rate')}}</label>
                            <div class="col-lg-4">
                                <div class="input-group m-b">
                                    <input type="number" placeholder="@lang('localize.Default_Offline') @lang('localize.platform_charges') {{trans('localize.Rate')}}" class="form-control" name="offline_platform_charge" value="{{($setting->offline_platform_charge)?$setting->offline_platform_charge : ''}}">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-8 control-label">@lang('localize.Default_Offline') @lang('localize.gst') {{trans('localize.Rate')}}</label>
                            <div class="col-lg-4">
                                <div class="input-group m-b">
                                    <input type="number" placeholder="@lang('localize.Default_Offline') @lang('localize.gst') {{trans('localize.Rate')}}" class="form-control" name="offline_service_charge" value="{{($setting->offline_service_charge)? $setting->offline_service_charge : '' }}">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-4 col-lg-offset-8">
                                @if($edit_permission)
                                <button class="btn btn-block btn-primary" type="submit">{{trans('localize.Update')}}</button>
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