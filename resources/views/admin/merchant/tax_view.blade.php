@extends('admin.layouts.master')

@section('title', trans('localize.view_tax'))

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>@lang('localize.tax_invoice')</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin/merchant/manage" class="nolinkcolor">{{trans('localize.Merchants')}}</a>
            </li>
            <li>
                <a href="/admin/merchant/tax/{{ $invoices->mer_id }}" class="nolinkcolor">{{trans('localize.tax_invoice')}}</a>
            </li>
            <li class="active">
                <strong>@lang('localize.view')</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="row">
        <div class="col-lg-12">
            @include('merchant.common.success')
            @include('merchant.common.error')
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row">
                        @if ($invoices)
                        <div class="col-sm-12">
                            <a class="btn btn-sm btn-primary pull-right" onclick="window.open('{{ url('admin/merchant/tax/view/'.$invoices->mer_id.'/'.$invoices->id.'?action=print') }}', 'newwindow', 'width=750, height=500'); return false;">{{trans('localize.print')}}</a>
                        </div>
                        <div class="col-sm-12 text-uppercase text-center">
                            <h3>@lang('localize.tax_invoice')</h3>
                        </div>
                        <div class="col-sm-6 text-uppercase" style="margin-bottom:20px;">
                            {{ $invoices->merchant_name }}
                            {!! ($invoices->mer_address1) ? '<br>' . $invoices->mer_address1 : ''!!}
                            {!! ($invoices->mer_address2) ? '<br>' . $invoices->mer_address2 : '' !!}
                            {!! ($invoices->mer_city) ? '<br>' . $invoices->mer_city : '' !!}
                            <br>{{ $invoices->zipcode . ' ' . $invoices->mer_state . ', ' . $invoices->mer_country }}
                            <br><br>@lang('localize.gst_id_no') : {{ $invoices->bank_gst }}
                            <br>@lang('localize.phone') : {{ $invoices->mer_office_number }}
                        </div>
                        <div class="col-sm-6 text-uppercase">
                            @lang('localize.invoice_no') : {{ $invoices->invoice_no }}
                            <br>@lang('localize.id') : {{ $invoices->id }}
                            <br>@lang('localize.our_do_no') :
                            <br>@lang('localize.terms_') : C.O.D
                            <br>@lang('localize.date') :
                            <br>@lang('localize.Page') :
                        </div>
                        <div class="col-sm-12 table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">@lang('localize.transaction_date')</th>
                                        <th class="text-center">@lang('localize.merchant_invoice_no')</th>
                                        <th class="text-center">@lang('localize.description')</th>
                                        <th class="text-center">@lang('localize.amount') (RM) <br> <small>(@lang('localize.inclusive_of_gst'))</small></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoices->items as $inv)
                                    <tr>
                                        <td class="text-center" style="vertical-align:middle;">{{ \Helper::UTCtoTZ($inv->order_date, 'd/m/Y') }}</td>
                                        <td class="text-center" style="vertical-align:middle;">{{ $inv->order_invoice_no }}</td>
                                        <td class="text-center">@lang('localize.platform_charges')</td>
                                        <td class="text-center">{{ number_format($inv->merchant_charge_amount + $inv->gst_amount, 2) }}</td>
                                    </tr>

                                    @endforeach
                                </tbody>
                            </table>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">@lang('localize.sub_total') (@lang('localize.excluding_gst'))</th>
                                        <th class="text-center">@lang('localize.gst_payable') @ {{ round(\Config::get('settings.service_charge')) .'% on '. number_format($invoices->items->sum('merchant_charge_amount'), 2) }}</th>
                                        <th class="text-center">@lang('localize.total') (@lang('localize.inclusive_of_gst'))</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">{{ number_format($invoices->items->sum('merchant_charge_amount'), 2) }}</td>
                                        <td class="text-center">{{ number_format($invoices->items->sum('gst_amount'), 2) }}</td>
                                        <td class="text-center">{{ number_format(($invoices->items->sum('merchant_charge_amount') + $invoices->items->sum('gst_amount')), 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-12">
                            <a class="btn btn-sm btn-primary pull-right" onclick="window.open('{{ url('admin/merchant/tax/view/'.$invoices->mer_id.'/'.$invoices->id.'?action=print') }}', 'newwindow', 'width=750, height=500'); return false;">{{trans('localize.print')}}</a>
                        </div>
                        @else
                        @lang('localize.table_no_record')
                        @endif
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
        }).on('changeDate', function(){
            $('#edate').datepicker('setStartDate', new Date($(this).val()));
        });

        $('#edate').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
        }).on('changeDate', function(){
            $('#sdate').datepicker('setEndDate', new Date($(this).val()));
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