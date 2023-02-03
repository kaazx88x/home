<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{trans('localize.tax_invoice')}}</title>
    <link href="/backend/css/bootstrap.min.css" rel="stylesheet">
</head>

<body onload="window.print()">
<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="row">
        <div class="col-lg-12">
            @include('merchant.common.success')
            @include('merchant.common.error')
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-xs-12 text-center" style="margin-bottom: 5px; border-bottom:2px solid;">
                            <div class="col-xs-3">
                            <img src="{{ asset('assets/images/meihome_logo.png') }}" style="float:right;height:120px; width:auto;">
                            </div>
                            <div class="col-xs-9" style="margin-left: -120px;">
                            <span style="font-size:2em"><b>{{ env('COMPANY_NAME') }}</b></span>
                            <small>({{ env('COMPANY_REG_NO') }})</small>
                            <br>37, Jalan Setia Gemilang U13/BG, Seksyen U13, Setia Alam,
                            <br>40170 Shah Alam, Selangor, Malaysia.
                            <br>Tel: 03-3359 2766 / 03-3359 2767
                            <br><b>{{trans('localize.gst_no')}} : </b>{{ env('GST') }}
                            <br><br>
                            </div>
                        </div>
                        <div class="col-xs-12 text-uppercase text-center">
                            <h3>@lang('localize.tax_invoice')</h3>
                        </div>
                        <div class="col-xs-6 text-uppercase" style="margin-bottom:20px;">
                            {{ $invoices->merchant_name }}
                            {!! ($invoices->mer_address1) ? '<br>' . $invoices->mer_address1 : ''!!}
                            {!! ($invoices->mer_address2) ? '<br>' . $invoices->mer_address2 : '' !!}
                            {!! ($invoices->mer_city) ? '<br>' . $invoices->mer_city : '' !!}
                            <br>{{ $invoices->zipcode . ' ' . $invoices->mer_state . ', ' . $invoices->mer_country }}
                            <br><br>@lang('localize.gst_id_no') : {{ $invoices->bank_gst }}
                            <br>@lang('localize.phone') : {{ $invoices->mer_office_number }}
                        </div>
                        <div class="col-xs-6 text-uppercase">
                            @lang('localize.invoice_no') : {{ $invoices->invoice_no }}
                            <br>@lang('localize.id') : {{ $invoices->id }}
                            <br>@lang('localize.our_do_no') :
                            <br>@lang('localize.terms_') : C.O.D
                            <br>@lang('localize.date') :
                            <br>@lang('localize.Page') :
                        </div>
                        @if ($invoices)
                        <div class="col-xs-12 table-responsive">
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
                        @else
                        @lang('localize.table_no_record')
                        @endif

                        <div class="col-xs-12 text-left">
                            <small><em>**@lang('localize.computer_generated_invoice')</em></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>