@extends('admin.layouts.master')

@section('title', trans('localize.generate_tax'))

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>@lang('localize.tax_invoice')</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin/merchant/manage" class="nolinkcolor">{{trans('localize.Merchants')}}</a>
            </li>
            <li>
                <a href="/admin/merchant/tax/{{ $merchant->mer_id }}" class="nolinkcolor">{{trans('localize.tax_invoice')}}</a>
            </li>
            <li class="active">
                <strong>@lang('localize.generate')</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="ibox float-e-margins border-bottom">
        <div class="ibox-content ibox-content-filter">
            @if ($invoices)
            <form class="form-inline" id="tax_generate" action='/admin/merchant/tax/save/{{$merchant->mer_id}}' method="POST">
                {{ csrf_field() }}
                <div class="form-group">
                    <div class="input-daterange input-group">
                        <input type="text" class="form-control" value="{{ $input['start'] }}" name="start" readonly/>
                        <span class="input-group-addon">{{trans('localize.to')}}</span>
                        <input type="text" class="form-control" value="{{ $input['end'] }}" name="end" readonly/>
                    </div>
                </div>
                <div class="form-group">
                    <label>For</label>
                    <input type="text" class="form-control" value="{{ ($input['type'] == '1') ? trans('localize.online_orders') : trans('localize.order_offline') }}" readonly/>
                    <input type="hidden" value="{{ $input['type'] }}" name="type"/>
                </div>
                <div class="pull-right">
                    <a href="/admin/merchant/tax/generate/{{ $merchant->mer_id }}" class="btn">{{trans('localize.reset')}}</a>
                    <button id="save" class="btn btn-secondary" type="submit">@lang('localize.save_tax')</button>
                </div>
            </form>
            @else
            <form class="form-inline" id="tax_generate" action='/admin/merchant/tax/generate/{{$merchant->mer_id}}' method="GET">
                <div class="form-group">
                    <label for="exampleInputEmail2" class="sr-only">{{trans('localize.date')}} {{trans('localize.range')}}</label>
                    <div class="input-daterange input-group">
                        <input type="text" class="form-control compulsary" name="start" id="sdate" placeholder="{{trans('localize.startDate')}}" value="{{$input['start']}}" data-date-end-date="-1d"/>
                        <span class="input-group-addon">{{trans('localize.to')}}</span>
                        <input type="text" class="form-control compulsary" name="end" id="edate" placeholder="{{trans('localize.endDate')}}" value="{{$input['end']}}" data-date-end-date="-1d"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail2" class="sr-only">{{trans('localize.type')}}</label>
                    <select class="form-control compulsary" id="type" name="type">
                        <option value="1" {{ (($input['type'] == "1") || ($merchant->mer_type == 0)) ? 'selected' : '' }}>{{trans('localize.online_orders')}}</option>
                        <option value="2" {{ (($input['type'] == "2") || ($merchant->mer_type == 1)) ? 'selected' : '' }}>{{trans('localize.order_offline')}}</option>
                    </select>
                </div>
                <div class="pull-right">
                    <button id="generate" class="btn btn-primary" type="button">@lang('localize.generate')</button>
                </div>
            </form>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            @include('merchant.common.success')
            @include('merchant.common.error')
            <div class="ibox">
                <div class="ibox-content">
                    {{--  <div class="ibox-title" style="display: block;">
                        <div class="ibox-tools">
                            <a href="/admin/merchant/tax/create/{{ $merchant->mer_id }}" class="btn btn-primary btn-sm">@lang('localize.add')</a>
                        </div>
                    </div>  --}}
                    @if ($invoices)
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('localize.transaction_date')</th>
                                    <th class="text-center">@lang('localize.invoice_no')</th>
                                    <th class="text-center">@lang('localize.description')</th>
                                    <th class="text-center">@lang('common.credit_name')</th>
                                    <th class="text-center">@lang('localize.tax_code')</th>
                                    <th class="text-center">@lang('localize.amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $inv)
                                <tr>
                                    <td rowspan="2" class="text-center" style="vertical-align:middle;">{{ \Helper::UTCtoTZ($inv->order_date, 'd/m/Y') }}</td>
                                    <td rowspan="2" class="text-center" style="vertical-align:middle;">{{ $inv->invoice_no }}</td>
                                    <td class="text-center">@lang('localize.merchant_charge') ({{ $inv->merchant_charge_percentage.'%' }})</td>
                                    <td class="text-center">{{ $inv->merchant_charge_credit }}</td>
                                    <td class="text-center">SR</td>
                                    <td class="text-center">{{ number_format($inv->merchant_charge_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-center">@lang('localize.gst')</td>
                                    <td class="text-center">{{ number_format($inv->gst_credit, 4) }}</td>
                                    <td class="text-center">&nbsp;</td>
                                    <td class="text-center">{{ number_format($inv->gst_amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('localize.total_before_gst')</th>
                                    <th class="text-center">@lang('localize.total_gst')</th>
                                    <th class="text-center">@lang('localize.total_after_gst')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">{{ number_format($invoices->sum('merchant_charge_amount'), 2) }}</td>
                                    <td class="text-center">{{ number_format($invoices->sum('gst_amount'), 2) }}</td>
                                    <td class="text-center">{{ number_format(($invoices->sum('merchant_charge_amount') + $invoices->sum('gst_amount')), 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @else
                    @lang('localize.table_no_record')
                    @endif
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

        $('#generate').on('click', function() {
            isValid = true;

            $('#tax_generate :input').each(function() {
                if ($(this).hasClass('compulsary')) {
                    if (!$(this).val()) {
                        $(this).css('border', '1px solid red').focus();
                        isValid = false;
                        return false;
                    }
                }

                $(this).css('border', '');
            });

            if (isValid) {
                $("#tax_generate").submit();
            }
        });
    });

</script>
@endsection