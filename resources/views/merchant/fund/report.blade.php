@extends('merchant.layouts.master')

@section('title', 'Fund Report')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>{{trans('localize.fund_request_report')}}</h2>
        <ol class="breadcrumb">
            <li>
                {{trans('localize.fund')}}
            </li>
            <li class="active">
                <strong>{{trans('localize.fund_request_report')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <form id="filter" action="{{ url('merchant/fund/report') }}" method="GET">
                <div class="col-sm-4">
                    <div class="form-group">
                        <div class="input-daterange input-group">
                            <input type="text" class="form-control" name="start" id="sdate" placeholder="{{trans('localize.startDate')}}" value="{{$input['start']}}"/>
                            <span class="input-group-addon">{{trans('localize.to')}}</span>
                            <input type="text" class="form-control" name="end" id="edate" placeholder="{{trans('localize.endDate')}}" value="{{$input['end']}}"/>
                        </div>
                        <select class="form-control" id="type" name="type">
                            <option value="wd_date" {{ ($input['type'] == 'created_at') ? 'selected' : '' }}>{{trans('localize.transaction_date')}}</option>
                            @if ($input['status'] == '' || $input['status'] == 1)
                                <option value="updated_at" {{ ($input['type'] == 'paid_date') ? 'selected' : '' }}>{{trans('localize.paid_date')}}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <select class="form-control" id="status" name="status">
                            @foreach ($status_list as $key => $stat)
                                <option value="{{ $key }}" {{ (strval($key) == $input['status']) ? 'selected' : '' }}>{{ $stat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <select class="form-control" id="sort" name="sort" style="font-family:'FontAwesome', sans-serif;">
                            <option value="new" {{($input['sort'] == "" || $input['sort'] == 'new') ? 'selected' : ''}}>{{trans('localize.newest')}}</option>
                            <option value="old" {{($input['sort'] == 'old') ? 'selected' : ''}}>{{trans('localize.oldest')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-2 col-sm-offset-2">
                    <button type="submit" class="btn btn-block btn-outline btn-primary" id="filter">{{trans('localize.search')}}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            @include('merchant.common.success')
            <div class="ibox">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">{{trans('localize.#id')}}</th>
                                    <th class="text-center" style="width:20%;">@lang('localize.fund_withdrawal')</th>
                                    <th class="text-center">{{trans('localize.request_date')}}</th>
                                    <th class="text-center">{{trans('localize.transaction_date')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.paid_date')}}</th>
                                    <th class="text-center">{{trans('localize.status')}}</th>
                                </tr>
                            </thead>
                            @if ($funds->total())
                                <tbody>
                                    @foreach($funds as $key => $fund)
                                    <tr>
                                        <td class="text-center">{{$fund->wd_id}}</td>
                                        <td>
                                            <dl class="dl-horizontal" style="margin-bottom:0;">
                                                <dt>@lang('localize.credit_requested')</dt>
                                                <dd class="text-nowrap">{{$fund->wd_submited_wd_amt}}</dd>
                                                <dt>@lang('localize.currency_rate')</dt>
                                                <dd class="text-nowrap">{{$fund->wd_currency}} {{$fund->wd_rate}}</dd>
                                                <dt>@lang('localize.withdraw_amount')</dt>
                                                <dd class="text-nowrap">{{$fund->wd_currency}} {{number_format($fund->wd_rate * $fund->wd_submited_wd_amt , 2 )}}</dd>
                                            </dl>
                                        </td>
                                        {{-- <td class="text-center">{{ Carbon\Carbon::createFromTimestamp(strtotime($fund->created_at))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A') }}</td> --}}
                                        {{-- <td class="text-center">{{ Carbon\Carbon::createFromTimestamp(strtotime($fund->updated_at))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A') }}</td> --}}
                                        <td class="text-center">{{ \Helper::UTCtoTZ($fund->created_at) }}</td>
                                        <td class="text-center">{{ \Helper::UTCtoTZ($fund->updated_at) }}</td>
                                        {{-- <td class="text-center">{{($fund->wd_status == 3 ) ? Carbon\Carbon::createFromTimestamp(strtotime($fund->wd_date))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A') : '' }}</td> --}}
                                        <td class="text-center">{{($fund->wd_status == 3 ) ? \Helper::UTCtoTZ($fund->wd_date) : '' }}</td>
                                        <td class="text-center" width="1%">
                                            <p>
                                                @if ($fund->wd_status == 1)
                                                    <span class="text-navy">@lang('localize.approved')</span>
                                                @elseif ($fund->wd_status == 2)
                                                    <span class="text-danger">@lang('localize.declined')</span>
                                                @elseif ($fund->wd_status == 3)
                                                    <span class="text-success">@lang('localize.paid')</span>
                                                @else
                                                    <span class="text-warning">@lang('localize.pending')</span>
                                                @endif
                                            </p>

                                            @if($fund->wd_statement)
                                            <button class="btn btn-xs btn-block btn-primary" onclick="get_fund_withdraw_statement({{ $fund->wd_id }}, {{ auth('merchants')->user()->mer_id }})">@lang('localize.view_statement')</button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7">
                                            <div class=" col-xs-6">
                                                <span class="pagination">
                                                    Showing {{$funds->firstItem()}} to {{$funds->lastItem()}} of {{$funds->Total()}} Records
                                                </span>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <div class="col-xs-7 text-right">
                                                    {{$funds->appends(Request::except('page'))->links()}}
                                                </div>
                                                <div class="col-xs-5 text-right pagination">
                                                    Go To Page
                                                    <input type='number' id='pageno' name='pageno' size="3" min="1" max="{{$funds->lastPage()}}">
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="gotopage('page')">
                                                        <i class="fa fa-share-square-o"></i> Go
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            @else
                                <tr>
                                    <td colspan="7" class="text-center">@lang('localize.nodata')</td>
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
        // daterange move to custom.js

        $("#pageno").change(function(){
            var input = $(this).val();
            var max = {{$funds->lastPage()}};
            if(input > max){
                $(this).val(max);
            }
        });
    });

    function gotopage($page) {
        $val =  $("#pageno").val();

        var href = window.location.href.substring(0, window.location.href.indexOf('?'));
        var qs = window.location.href.substring(window.location.href.indexOf('?') + 1, window.location.href.length);
        var newParam = $page + '=' + $val;

        if (qs.indexOf($page + '=') == -1) {
            if (qs == '') {
                qs = '?'
            }
            else {
                qs = qs + '&'
            }
            qs = newParam;

        }
        else {
            var start = qs.indexOf($page + "=");
            var end = qs.indexOf("&", start);
            if (end == -1) {
                end = qs.length;
            }
            var curParam = qs.substring(start, end);
            qs = qs.replace(curParam, newParam);
        }
        window.location.replace(href + '?' + qs);
    }
</script>
@endsection
