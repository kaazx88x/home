@extends('admin.layouts.master')

@section('title', trans('localize.tax_invoice'))

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>@lang('localize.tax_invoice')</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin/merchant/manage" class="nolinkcolor">{{trans('localize.Merchants')}}</a>
            </li>
            <li class="active">
                @lang('localize.tax_invoice') Log
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="ibox float-e-margins border-bottom">

        <a class="collapse-link nolinkcolor">
            <div class="ibox-title ibox-title-filter">
                <h5>{{trans('localize.Search_Filter')}}</h5>
                <div class="ibox-tools">
                        <i class="fa fa-chevron-down"></i>
                </div>
            </div>
        </a>
        <div class="ibox-content ibox-content-filter" style="display:none;">
            <div class="row">
                <form class="form-horizontal" id="filter" action='/admin/merchant/tax/{{$merchant->mer_id}}' method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.created_at') . ' ' . trans('localize.range')}}</label>
                        <div class="col-sm-9">
                            <div class="input-daterange input-group">
                                <input type="text" class="form-control" name="start" id="sdate" placeholder="{{trans('localize.startDate')}}" value="{{$input['start']}}" />
                                <span class="input-group-addon">{{trans('localize.to')}}</span>
                                <input type="text" class="form-control" name="end" id="edate" placeholder="{{trans('localize.endDate')}}" value="{{$input['end']}}"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.type')}}</label>
                        <div class="col-sm-4">
                            <select class="form-control compulsary" id="type" name="type">
                                <option value="all" {{ ($input['type'] == "all") ? 'selected' : '' }}>{{trans('localize.all')}}</option>
                                <option value="1" {{ ($input['type'] == "1") ? 'selected' : '' }}>{{trans('localize.online_orders')}}</option>
                                <option value="2" {{ ($input['type'] == "2") ? 'selected' : '' }}>{{trans('localize.order_offline')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.invoice_no')}}</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="invoice_no" placeholder="{{ trans('localize.invoice_no') }}" value="{{ $input['invoice_no'] }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-9 col-sm-offset-2">
                            <button type="submit" class="btn btn-block btn-outline btn-primary" id="filter">{{trans('localize.search')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            @include('merchant.common.success')
            @include('merchant.common.error')
            <div class="ibox">
                <div class="ibox-content">
                    <div class="ibox-title" style="display: block;">
                        <div class="ibox-tools">
                            <a href="/admin/merchant/tax/generate/{{ $merchant->mer_id }}" class="btn btn-primary btn-sm">@lang('localize.generate_tax')</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('localize.invoice_no')</th>
                                    <th class="text-center">@lang('localize.type')</th>
                                    <th class="text-center">@lang('localize.startDate')</th>
                                    <th class="text-center">@lang('localize.endDate')</th>
                                    <th class="text-center">@lang('localize.created_at')</th>
                                    <th class="text-center">@lang('localize.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $inv)
                                <tr>
                                    <td class="text-center">{{ $inv->invoice_no }}</td>
                                    <td class="text-center">{{ ($inv->order_type == 1) ? trans('localize.online') : trans('localize.offline') }}</td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime($inv->start_date)) }}</td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime($inv->end_date)) }}</td>
                                    <td class="text-center">{{ \Helper::UTCtoTZ($inv->created_at) }}</td>
                                    <td class="text-center">
                                        <a href="/admin/merchant/tax/view/{{ $inv->merchant_id }}/{{ $inv->id }}" class="btn btn-white btn-sm">@lang('localize.view')</a>
                                        <a class="btn btn-sm btn-white" onclick="window.open('{{ url('admin/merchant/tax/view/'.$inv->merchant_id.'/'.$inv->id.'?action=print') }}', 'newwindow', 'width=750, height=500'); return false;">{{trans('localize.print')}}</a>
                                    </td>
                                <tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<link href="/backend/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
<link href="/backend/css/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="/backend/js/plugins/daterangepicker/daterangepicker.js"></script>
<script src="/backend/js/custom.js"></script>

<script>

     $(document).ready(function() {
        $('#sdate').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            format: "dd/mm/yyyy",
            minDate: 0,
        }).on('changeDate', function(){
            var minDate = $(this).datepicker('getDate');
            $('#edate').datepicker('setDate', minDate);
            $('#edate').datepicker('setStartDate', minDate);
        });

        $('#edate').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            format: "dd/mm/yyyy",
        }).on('changeDate', function(){
            var minDate = $(this).datepicker('getDate');
            $('#sdate').datepicker('setEndDate', minDate);
        });
    });

</script>
@endsection