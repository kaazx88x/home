@extends('merchant.layouts.master')

@section('title', 'Dashboard')

@section('content')
    @if($logintype == 'storeusers')

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content text-left p-md">
                    <h4>Hi! <span class="text-navy">{{ $storeuser->name }}</span></h4>
                    <p>You are log in as store user. Here you can manage product assosiated with assigned merchant store. Below is your assigned store.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach($stores as $store)
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    @if($store->stor_status == 0)
                    <span class="label label-warning pull-right">@lang('localize.inactive')</span>
                    @elseif($store->stor_status == 1)
                    <span class="label label-primary pull-right">@lang('localize.active')</span>
                    @endif
                    <h5>{{ $store->stor_name }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-12 form">
                            <div class="form-group">
                                <label>@lang('localize.phone')</label>
                                <p class="form-control-static">{{ $store->stor_phone }}</p>
                            </div>
                        </div>
                        <div class="col-sm-12 form">
                            <div class="form-group">
                                <label>@lang('localize.address')</label>
                                <p class="form-control-static">{{ $store->stor_address1 }}</p>
                                <p class="form-control-static">{{ $store->stor_address2 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @else
        @if ($merchant->mer_staus == 2)
            <div class="alert alert-warning">
                @lang('localize.merchant_account_pending')
            </div>
        @endif

        <div class="row">
            @if ($merchant->mer_type == 0)
            <a href="{{ url('merchant/product/manage') }}">
                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 text-center">
                    <div class="widget-head-color-box btn-default b-r-md">
                        <i class="fa fa-tags fa-3x"></i>
                    </div>
                    <div class="widget-text-box text-muted border-size-sm b-r-md p-xs">
                        @lang('localize.manage_product')
                    </div>
                </div>
            </a>
            @endif

            <a href="{{ url('merchant/store/manage') }}">
                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 text-center">
                    <div class="widget-head-color-box btn-primary b-r-md">
                        <i class="fa fa-building fa-3x"></i>
                    </div>
                    <div class="widget-text-box text-navy border-size-sm b-r-md p-xs">
                        @lang('localize.manage_stores')
                    </div>
                </div>
            </a>

            <a href="{{ ($merchant->mer_type == 0) ? url('merchant/transaction/product') : url('merchant/transaction/offline') }}">
                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 text-center">
                    <div class="widget-head-color-box btn-info b-r-md">
                        <i class="fa fa-dropbox fa-3x"></i>
                    </div>
                    <div class="widget-text-box text-info border-size-sm b-r-md p-xs">
                        @lang('localize.order_listing')
                    </div>
                </div>
            </a>

            <a href="{{ url('merchant/fund/report') }}">
                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 text-center">
                    <div class="widget-head-color-box btn-warning b-r-md">
                        <i class="fa fa-money fa-3x"></i>
                    </div>
                    <div class="widget-text-box text-warning border-size-sm b-r-md p-xs">
                        @lang('localize.withdraw_fund_request')
                    </div>
                </div>
            </a>
        </div>
    @endif
@stop

@section('style')
<style>
.widget-text-box {
    border :1px solid;
}
.widget-head-color-box {
    padding-top:25px;
    padding-bottom:25px;
}
</style>
@endsection