@extends('admin.layouts.master')

@section('title', trans('common.credit_name') . ' Log')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>@lang('common.credit_name') Log</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin/customer/manage" class="nolinkcolor">Customer</a>
            </li>
            <li>
                @lang('common.credit_name') Log
            </li>
            <li class="active">
                <strong>{{$cus_id}} - {{$customer->cus_name}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="ibox float-e-margins border-bottom">
        <a class="collapse-link nolinkcolor">
            <div class="ibox-title ibox-title-filter">
                <h5>@lang('localize.Search_Filter')</h5>
                <div class="ibox-tools">
                    <i class="fa fa-chevron-down"></i>
                </div>
            </div>
        </a>
        <div class="ibox-content ibox-content-filter" style="display:none;">
            <div class="row">
                <form class="form-horizontal" id="filter" action='/admin/customer/credit/{{$cus_id}}' method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Search by ID</label>
                        <div class="col-sm-4">
                            <input type="text" value="{{$input['id']}}" placeholder="Search By Order ID" class="form-control" id="id" name="id">
                        </div>
                        <div class="col-sm-5">
                            <input type="text" value="{{$input['ofid']}}" placeholder="Search By Offline Order ID" class="form-control" id="ofid" name="ofid">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Search</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['remark']}}" placeholder="Search by Remarks" class="form-control" id="remark" name="remark">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Date Range</label>
                        <div class="col-sm-9">
                            <div class="input-daterange input-group">
                                <input type="text" class="form-control" name="start" id="sdate" placeholder="Start Date" value="{{$input['start']}}"/>
                                <span class="input-group-addon">To</span>
                                <input type="text" class="form-control" name="end" id="edate" placeholder="End Date" value="{{$input['end']}}"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('localize.svi_wallet')</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="wallet" name="wallet">
                                <option value="">{{trans('localize.all')}}</option>
                                @foreach ($wallets as $key => $wallet)
                                    <option value="{{ $wallet->id }}" {{ (strval($wallet->id) == $input['wallet']) ? 'selected' : '' }}>{{ ucfirst($wallet->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('localize.status')</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="status" name="status">
                                @foreach ($status_list as $key => $stat)
                                    <option value="{{ $key }}" {{ (strval($key) == $input['status']) ? 'selected' : '' }}>{{ $stat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Sort By</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="sort" name="sort" style="font-family:'FontAwesome', sans-serif;">
                                <option value="new" {{($input['sort'] == "" || $input['sort'] == 'new') ? 'selected' : ''}}>{{trans('localize.newest')}}</option>
                                <option value="old" {{($input['sort'] == 'old') ? 'selected' : ''}}>{{trans('localize.oldest')}}</option>
                            </select>
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
                <div class="ibox-title" style="display: block;">
                    <div class="ibox-tools">
                        @if($export_permission)
                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle"> Export All <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="/admin/export/credit_customer?action=export&export_as=csv&cus_id={{ $cus_id }}{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
                                <li><a href="/admin/export/credit_customer?action=export&export_as=xlsx&cus_id={{ $cus_id }}{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
                                <li><a href="/admin/export/credit_customer?action=export&export_as=xls&cus_id={{ $cus_id }}{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
                            </ul>
                        </div>

                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-white btn-sm dropdown-toggle"> Export This Page <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="/admin/export/credit_customer?action=export_by_page&export_as=csv&cus_id={{ $cus_id }}{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
                                <li><a href="/admin/export/credit_customer?action=export_by_page&export_as=xlsx&cus_id={{ $cus_id }}{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
                                <li><a href="/admin/export/credit_customer?action=export_by_page&export_as=xls&cus_id={{ $cus_id }}{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('localize.#id')</th>
                                    <th class="text-center">@lang('localize.type')</th>
                                    <th class="text-center">@lang('localize.debit')</th>
                                    <th class="text-center">@lang('localize.credit')</th>
                                    <th class="text-center">From</th>
                                    {{-- <th class="text-center">Svi Wallet</th> --}}
                                    <th class="text-center">@lang('localize.remarks')</th>
                                    <th class="text-center">@lang('localize.wallet_type')</th>
                                    <th class="text-center">@lang('localize.date')</th>
                                </tr>
                            </thead>
                            @if ($logs->total())
                                <tbody>
                                    @foreach($logs as $log)
                                    <tr class="text-center">
                                        <td>
                                            @if (!empty($log->order_id))
                                                <button type="button" class="btn btn-outline btn-sm btn-link btn-block" data-toggle="modal"  data-id="{{ $log->order_id }}" data-post="data-php" data-action="order_details">{{ $log->order_id }}</button>
                                            @elseif (!empty($log->offline_order_id))
                                                <button type="button" class="btn btn-outline btn-sm btn-link btn-block" data-toggle="modal" data-id="345" data-post="data-php" data-action="order_offline_details">{{ $log->offline_order_id }}</button>
                                            @endif
                                        </td>
                                        <td>
                                            @if (!empty($log->order_id))
                                                Order
                                            @elseif (!empty($log->offline_order_id))
                                                Order offline
                                            @endif
                                        </td>
                                        <td>{{ ($log->debit_amount == 0.00)? '' : $log->debit_amount }}</td>
                                        <td>{{ ($log->credit_amount == 0.00)? '' : $log->credit_amount }}</td>
                                        <td>{{ $log->from }}</td>
                                        {{-- <td>{{ $log->svi_wallet }}</td> --}}
                                        <td>{{ $log->remark }}</td>
                                        <td>
                                            @if ($log->wallet_id)
                                                @if ($log->wallet_id == 0)
                                                    Hemma
                                                @else
                                                    {{ ($log->wallet) ? $log->wallet->name : '-' }}
                                                @endif
                                            @endif
                                        </td>
                                        {{-- <td>{{ Carbon\Carbon::createFromTimestamp(strtotime($log->created_at))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A') }}</td> --}}
                                        <td>{{ \Helper::UTCtoTZ($log->created_at) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="8">
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
                                    <td colspan="8" class="text-center">@lang('localize.nodata')</td>
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

        $('button').on('click', function(){
            var this_id = $(this).attr('data-id');
            var this_action = $(this).attr('data-action');

            if (this_action == 'order_details') {
                view_order(this_id);
            } else if (this_action == 'order_offline_details') {
                view_order_offline(this_id);
            }
        });
    });

</script>
@endsection