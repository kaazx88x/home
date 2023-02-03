@extends('admin.layouts.master')

@section('title', 'View Customer')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.view')}} {{trans('localize.customer')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="">{{trans('localize.customer')}}</a>
            </li>
            <li class="active">
                <strong>{{trans('localize.view')}} {{trans('localize.customer')}}</strong>
            </li>
        </ol>
    </div>
</div>

@include('admin.common.error')

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox float-e-margins border-bottom">
        <div class="ibox-title ibox-title-filter">
            <div class="row">
                @if($online_order_report)
                <div class="col-sm-2">
                    <a target="_blank" href="/admin/transaction/product/orders?cid={{$customer->cus_id}}" class="btn btn-white btn-sm btn-block">{{trans('localize.Online_Order_Report')}}</a>
                </div>
                @endif

                @if($offline_order_report)
                <div class="col-sm-2">
                    <a target="_blank" href="/admin/transaction/offline?cid={{$customer->cus_id}}" class="btn btn-white btn-sm btn-block">{{trans('localize.Offline_Order_Report')}}</a>
                </div>
                @endif

                <div class="col-sm-2">
                    <a target="_blank" href="/admin/transaction/product/ecards?cid={{$customer->cus_id}}" class="btn btn-white btn-sm btn-block">{{ trans('localize.e-card.report') }}</a>
                </div>

                @if($mi_credit_report_permission)
                <div class="col-sm-2">
                    <a target="_blank" href="/admin/customer/credit/{{$customer->cus_id}}" class="btn btn-white btn-sm btn-block">@lang('common.credit_name') {{trans('localize.report')}}</a>
                </div>
                @endif

                @if($edit_permission)
                <div class="col-sm-3 col-sm-offset-1">
                    <a href="/admin/customer/edit/{{$customer->cus_id}}" class="btn btn-primary btn-sm btn-block pull-right">{{trans('localize.Edit')}} {{trans('localize.customer')}}</a>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{trans('localize.customer')}} {{trans('localize.information')}}</h5>
                </div>
                <div class="ibox-content form-horizontal">
                    @include('merchant.common.errors')
                    <div class="form-group">
                        <label class="col-lg-3 control-label">{{trans('localize.Name')}}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{{ ($customer->cus_title != 0)? $select['person_titles'][$customer->cus_title].' '.$customer->cus_name : $customer->cus_name }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">{{trans('localize.Username')}}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{{$customer->username}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">{{trans('localize.ic_number')}}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{{$customer->identity_card}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">{{trans('localize.email')}}</label>
                        <div class="col-lg-9">
                        <p class="form-control-static">{{$customer->email}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">{{trans('localize.Phone')}}</label>
                        <div class="col-lg-9">
                        <p class="form-control-static">{{$customer->cus_phone}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">{{trans('localize.address')}}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{{$customer->cus_address1}}</p>
                            <br>
                            <p class="form-control-static">{{$customer->cus_address2}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">{{trans('localize.country')}}</label>
                        <div class="col-lg-9">
                            @foreach($country as $co)
                                @if($co->co_id == $customer->cus_country)
                                    <p class="form-control-static">{{$co->co_name}}</p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">{{trans('localize.state')}}</label>
                        <div class="col-lg-9">
                            @foreach($state as $st)
                                @if($st->id == $customer->cus_State)
                                    <p class="form-control-static">{{$st->name}}</p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">{{trans('localize.city')}}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{{$customer->cus_city_name}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label text-nowrap">@lang('common.credit_name') {{trans('localize.balance')}}</label>
                        <div class="col-lg-9">
                            <div class="row">
                                @foreach ($customer_wallets as $cw)
                                <div class="col-sm-3"><p class="form-control-static"><b>{{ ucfirst($cw->wallet->name) }} :</b> {{$cw->credit}}</p></div>
                                @endforeach
                                @if ($customer->special_wallet > 0)
                                <div class="col-sm-3"><p class="form-control-static"><b>Hemma :</b> {{$customer->special_wallet}}</p></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{trans('localize.Other_Information')}}</h5>
                </div>
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-lg-3 control-label">@lang('localize.job')</label>
                                <div class="col-lg-9">
                                    <p class="form-control-static">{{ ($customer->info && $customer->info->cus_job != 0)? $select['jobs'][$customer->info->cus_job] : '' }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label">@lang('localize.monthly_incomes')</label>
                                <div class="col-lg-9">
                                    <p class="form-control-static">{{ ($customer->info && $customer->info->cus_incomes != 0)? $select['incomes'][$customer->info->cus_incomes] : '' }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label">@lang('localize.education')</label>
                                <div class="col-lg-9">
                                    <p class="form-control-static">{{ ($customer->info && $customer->info->cus_education != 0)? $select['educations'][$customer->info->cus_education] : '' }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label">@lang('localize.gender')</label>
                                <div class="col-lg-9">
                                    <p class="form-control-static">
                                        @if ($customer->info && $customer->info->cus_gender == 1)
                                            {{trans('localize.male')}}
                                        @elseif ($customer->info && $customer->info->cus_gender == 2)
                                            {{trans('localize.female')}}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label">@lang('localize.dob')</label>
                                <div class="col-lg-9">
                                    <p class="form-control-static">{{ ($customer->info) ? date('d F Y', strtotime($customer->info->cus_dob)) : '' }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label">@lang('localize.nationality')</label>
                                <div class="col-lg-9">
                                    <p class="form-control-static">{{ ($customer->info && $customer->info->cus_nationality != null)? $select['nationality'][$customer->info->cus_nationality] : '' }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label">@lang('localize.race')</label>
                                <div class="col-lg-9">
                                    <p class="form-control-static">{{ ($customer->info && $customer->info->cus_race != 0)? $select['races'][$customer->info->cus_race] : '' }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label">@lang('localize.religion')</label>
                                <div class="col-lg-9">
                                    <p class="form-control-static">{{ ($customer->info && $customer->info->cus_religion != 0)? $select['religions'][$customer->info->cus_religion] : '' }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label">@lang('localize.marital_status')</label>
                                <div class="col-lg-9">
                                    <p class="form-control-static">{{ ($customer->info && $customer->info->cus_marital != 0)? $select['marital'][$customer->info->cus_marital] : '' }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label">@lang('localize.no_of_children')</label>
                                <div class="col-lg-9">
                                    <p class="form-control-static">{{ ($customer->info) ? $customer->info->cus_children : '' }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label">@lang('localize.hobby')</label>
                                <div class="col-lg-9">
                                    <p class="form-control-static">{{ ($customer->info) ? $customer->info->cus_hobby : '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

