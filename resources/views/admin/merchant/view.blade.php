@extends('admin.layouts.master')

@section('title', 'Edit Profile')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.view')}} {{trans('localize.Merchants')}}</h2>
        <ol class="breadcrumb">
            <li>
                @if ($merchant->mer_type == 0)
                    <a href="/admin/merchant/manage/online">{{trans('localize.Merchants')}} {{trans('localize.Online')}} </a>
                @elseif ($merchant->mer_type == 1)
                    <a href="/admin/merchant/manage/offline">{{trans('localize.Merchants')}} {{trans('localize.Offline')}} </a>
                @endif
            </li>
            <li class="active">
                <strong>{{trans('localize.view')}} {{trans('localize.Merchants')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInUp">
    @include('admin.common.notifications')
    <div class="ibox float-e-margins border-bottom">
        <div class="ibox-title ibox-title-filter">
            <div class="row">
                {{-- <div class="col-sm-2">
                    <a target="_blank" href="/admin/transaction/product/orders?mid={{$merchant->mer_id}}" class="btn btn-white btn-sm btn-block">{{trans('localize.Online_Order_Report')}}</a>
                </div> --}}
                <div class="col-sm-2">
                    <a target="_blank" href="/admin/transaction/offline?mid={{$merchant->mer_id}}" class="btn btn-white btn-sm btn-block">{{trans('localize.Offline_Order_Report')}}</a>
                </div>
                @if($mi_credit_report_permission)
                <div class="col-sm-2">
                    <a target="_blank" href="/admin/merchant/credit/{{$merchant->mer_id}}" class="btn btn-white btn-sm btn-block">@lang('common.credit_name') {{trans('localize.report')}}</a>
                </div>
                @endif
                <div class="col-sm-2">
                    <a target="_blank" href="/admin/transaction/fund-request?id={{$merchant->mer_id}}" class="btn btn-white btn-sm btn-block">{{trans('localize.fund_request')}} {{trans('localize.report')}}</a>
                </div>
                <div class="col-sm-2">
                    <a target="_blank" href="/admin/store/manage/{{$merchant->mer_id}}" class="btn btn-white btn-sm btn-block">{{trans('localize.manage')}} {{trans('localize.store')}} </a>
                </div>
                @if($merchant->mer_staus == 0)
                <div class="col-md-2">
                    <button type="button" class="btn btn-white btn-sm btn-block" data-href="{{ url('/merchant/resend/activation', [$merchant->mer_id, $merchant->email]) }}" id="resend_activation_email">{{trans('localize.resend_activation_email')}}</button>
                </div>
                @endif

                @if($edit_permission)
                <div class="col-sm-2">
                    <a href="/admin/merchant/edit/{{$merchant->mer_id}}" class="btn btn-primary btn-sm btn-block pull-right">{{trans('localize.Edit')}} {{trans('localize.Merchants')}} </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{trans('localize.Merchants')}} {{trans('localize.profile')}} </h5>
                </div>
                <div class="ibox-content">
                    <div class="form">
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.Merchants')}} ID</label>
                            <p class="form-control-static">{{$merchant->mer_id}}</p>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.Username')}}</label>
                            <p class="form-control-static">{{$merchant->username}}</p>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.First_Name')}}</label>
                            <p class="form-control-static">{{$merchant->mer_fname}}</p>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.Last_Name')}}</label>
                            <p class="form-control-static">{{$merchant->mer_lname}}</p>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.email')}}</label>
                            <p class="form-control-static">{{$merchant->email}}</p>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.Phone')}}</label>
                            <p class="form-control-static">{{$merchant->mer_phone}}</p>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.office_number')}}</label>
                            <p class="form-control-static">{{$merchant->mer_office_number}}</p>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.address')}}</label>
                            <p class="form-control-static">{{$merchant->mer_address1}}</p>
                            <br>
                            <p class="form-control-static">{{$merchant->mer_address2}}</p>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.country')}}</label>
                            @foreach ($country_details as $country)
                                @if($country->co_id == $merchant->mer_co_id)
                                    <p class="form-control-static">{{$country->co_name}}</p>
                                @endif
                            @endforeach
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.State')}}</label>
                            @foreach ($state_details as $state)
                                @if($state->id == $merchant->mer_state)
                                    <p class="form-control-static">{{$state->name}}</p>
                                @endif
                            @endforeach
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.city')}}</label>
                            <p class="form-control-static">{{$merchant->mer_city_name}}</p>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.Commision')}} (%)</label>
                            <p class="form-control-static">{{$merchant->mer_commission}}</p>
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('common.credit_name') {{trans('localize.balance')}}</label>
                            <p class="form-control-static"><a class="nolinkcolor btn btn-white btn-sm" href="/admin/merchant/credit/{{$merchant->mer_id}}">{{ ($merchant->mer_vtoken)? $merchant->mer_vtoken : '0.00' }}</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{trans('localize.guarantor_bank_acc')}}</h5>
                    <div class="ibox-tools">
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="form">
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.mer_acc_holder')}}</label>
                            <p class="form-control-static">{{$merchant->bank_acc_name}}</p>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.mer_acc_number')}}</label>
                            <p class="form-control-static">{{$merchant->bank_acc_no}}</p>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.mer_bank_name')}}</label>
                            <p class="form-control-static">{{$merchant->bank_name}}</p>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.mer_bank_country')}}</label>
                            @foreach ($country_details as $country)
                                @if($country->co_id == $merchant->bank_country)
                                    <p class="form-control-static">{{$country->co_name}}</p>
                                @endif
                            @endforeach
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.mer_bank_address')}}</label>
                            <p class="form-control-static">{{$merchant->bank_address}}</p>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.bank_swift')}}</label>
                            <p class="form-control-static">{{$merchant->bank_swift}}</p>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.if_europe')}}</label>
                            <p class="form-control-static">{{$merchant->bank_europe}}</p>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.gst_no')}}</label>
                            <p class="form-control-static">{{$merchant->bank_gst}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('style')
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
@endsection

@section('script')
<script>
$(document).ready(function() {
    $('#resend_activation_email').click(function() {
        var url = $(this).attr('data-href');
        swal({
            title: "{{trans('localize.sure')}}",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#5cb85c",
            confirmButtonText: "{{trans('localize.yes')}}",
			cancelButtonText: "{{trans('localize.cancel')}}",
            closeOnConfirm: true
        }, function(isConfirm){
                if (isConfirm) {
                    $('#spinner').show();
                    window.location.href = url;
                }
            }
        );
    });
});
</script>
@endsection