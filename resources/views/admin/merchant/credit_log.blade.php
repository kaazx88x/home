@extends('admin.layouts.master')

@section('title', trans('common.credit_name') . ' Log')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>@lang('common.credit_name') Log</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin/merchant/manage" class="nolinkcolor">{{trans('localize.Merchants')}}</a>
            </li>
            <li>
                @lang('common.credit_name') Log
            </li>
            <li class="active">
                <strong>{{$merchant->mer_id}} - {{$merchant->mer_fname}}</strong>
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
                <form class="form-horizontal" id="filter" action='/admin/merchant/credit/{{$merchant->mer_id}}' method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">ID</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['id']}}" placeholder="{{trans('localize.Search_By')}} ID" class="form-control" id="id" name="id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.remarks')}}</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['remark']}}" placeholder="{{trans('localize.Search_By')}} {{trans('localize.remarks')}}" class="form-control" id="remark" name="remark">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.date')}} {{trans('localize.range')}}</label>
                        <div class="col-sm-9">
                            <div class="input-daterange input-group">
                                <input type="text" class="form-control" name="start" id="sdate" placeholder="{{trans('localize.startDate')}}" value="{{$input['start']}}"/>
                                <span class="input-group-addon">{{trans('localize.to')}}</span>
                                <input type="text" class="form-control" name="end" id="edate" placeholder="{{trans('localize.endDate')}}" value="{{$input['end']}}"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.Status')}}</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="status" name="status">
                                @foreach ($status_list as $key => $stat)
                                    <option value="{{ $key }}" {{ (strval($key) == $input['status']) ? 'selected' : '' }}>{{ $stat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.sort')}}</label>
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
                        @if($admin_permission)
                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle"> Export All <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="/admin/export/credit_merchant?action=export&export_as=csv&mer_id={{ $merchant->mer_id }}{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
                                <li><a href="/admin/export/credit_merchant?action=export&export_as=xlsx&mer_id={{ $merchant->mer_id }}{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
                                <li><a href="/admin/export/credit_merchant?action=export&export_as=xls&mer_id={{ $merchant->mer_id }}{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
                            </ul>
                        </div>

                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-white btn-sm dropdown-toggle"> Export This Page <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="/admin/export/credit_merchant?action=export_by_page&export_as=csv&mer_id={{ $merchant->mer_id }}{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
                                <li><a href="/admin/export/credit_merchant?action=export_by_page&export_as=xlsx&mer_id={{ $merchant->mer_id }}{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
                                <li><a href="/admin/export/credit_merchant?action=export_by_page&export_as=xls&mer_id={{ $merchant->mer_id }}{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
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
                                                <a target="_blank" style="text-decoration:none; color: inherit;" href="{{ url('admin/transaction/product/orders') }}?tid={{ $log->order_id }}">{{ $log->order_id }}</a>
                                            @elseif (!empty($log->offline_order_id))
                                                <a target="_blank" style="text-decoration:none; color: inherit;" href="{{ url('admin/transaction/offline') }}?id={{ $log->offline_order_id }}">{{ $log->offline_order_id }}</a>
                                            @elseif (!empty($log->withdraw_id))
                                                <a target="_blank" style="text-decoration:none; color: inherit;" href="{{ url('admin/transaction/fund-request') }}?id={{ $log->withdraw_id }}">{{ $log->withdraw_id }}</a>
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
                                                    {{trans('localize.Showing')}} {{$logs->firstItem()}} {{trans('localize.to')}} {{$logs->lastItem()}} {{trans('localize.of')}} {{$logs->Total()}} {{trans('localize.Records')}}
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
        // daterange move to custom.js
    });

</script>
@endsection