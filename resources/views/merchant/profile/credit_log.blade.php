@extends('merchant.layouts.master')

@section('title', trans('common.credit_name') . ' Log')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>@lang('common.credit_name')</h2>
        <ol class="breadcrumb">
            <li>
                @lang('common.credit_name') Log
            </li>
            <li class="active">
                <strong>@lang('common.credit_name') Log</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <form id="filter" action="{{ url('merchant/credit/log') }}" method="GET">
                <div class="col-sm-2">
                    <div class="form-group">
                        <input type="text" value="{{$input['id']}}" placeholder="ID" class="form-control" id="id" name="id">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <div class="input-daterange input-group">
                            <input type="text" class="form-control" name="start" id="sdate" placeholder="{{trans('localize.startDate')}}" value="{{$input['start']}}"/>
                            <span class="input-group-addon">{{trans('localize.to')}}</span>
                            <input type="text" class="form-control" name="end" id="edate" placeholder="{{trans('localize.endDate')}}" value="{{$input['end']}}"/>
                        </div>
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
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-block btn-outline btn-primary" id="filter">{{trans('localize.search')}}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            @include('merchant.common.success')
            @include('merchant.common.error')
            <div class="ibox">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('localize.#id')</th>
                                    <th class="text-center">@lang('localize.type')</th>
                                    <th class="text-center">@lang('localize.debit')</th>
                                    <th class="text-center">@lang('localize.credit')</th>
                                    <th class="text-center">@lang('localize.remarks')</th>
                                    <th class="text-center">@lang('localize.date')</th>
                                </tr>
                            </thead>
                            @if ($logs->total())
                                <tbody>
                                    @foreach($logs as $log)
                                    <tr class="text-center">
                                        <td>
                                            @if (!empty($log->order_id))
                                                <a target="_blank" style="text-decoration:none; color: inherit;" href="{{ url('merchant/transaction/product') }}?id={{ $log->order_id }}">{{ $log->order_id }}</a>
                                            @elseif (!empty($log->offline_order_id))
                                                <a target="_blank" style="text-decoration:none; color: inherit;" href="{{ url('merchant/transaction/offline') }}?id={{ $log->offline_order_id }}">{{ $log->offline_order_id }}</a>
                                            @elseif (!empty($log->withdraw_id))
                                                <a target="_blank" style="text-decoration:none; color: inherit;" href="{{ url('merchant/fund/report') }}?id={{ $log->withdraw_id }}">{{ $log->withdraw_id }}</a>
                                            @endif
                                        </td>
                                        <td>
                                            @if (!empty($log->order_id))
                                                @lang('localize.order')
                                            @elseif (!empty($log->offline_order_id))
                                                @lang('localize.order_offline')
                                            @elseif (!empty($log->withdraw_id))
                                                @lang('localize.withdraw')
                                            @endif
                                        </td>
                                        <td>{{ ($log->debit_amount == 0.00)? '' : $log->debit_amount }}</td>
                                        <td>{{ ($log->credit_amount == 0.00)? '' : $log->credit_amount }}</td>
                                        <td>{{ $log->remark }}</td>

                                        {{-- <td>{{ Carbon\Carbon::createFromTimestamp(strtotime($log->created_at))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A') }}</td> --}}
                                        <td>{{ \Helper::UTCtoTZ($log->created_at) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6">
                                            <div class=" col-xs-6">
                                                <span class="pagination">
                                                    Showing {{$logs->firstItem()}} to {{$logs->lastItem()}} of {{$logs->Total()}} Records
                                                </span>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                {{$logs->appends(Request::except('page'))->links()}}
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">@lang('localize.nodata')</td>
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
    });

</script>
@endsection