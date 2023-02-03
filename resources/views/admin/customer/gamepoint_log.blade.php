@extends('admin.layouts.master')

@section('title', 'Game Point Log')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Game Point Log</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin/customer/manage" class="nolinkcolor">Customer</a>
            </li>
            <li>
                Game Point Log
            </li>
            <li class="active">
                <strong>{{$cus_id}} - {{$customer->cus_name}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="ibox float-e-margins border-bottom">
        <div class="ibox-title ibox-title-filter">
            <h5>Search Filter</h5>
            <div class="ibox-tools">
                <a class="collapse-link">
                    <i class="fa fa-chevron-down"></i>
                </a>
            </div>
        </div>
        <div class="ibox-content ibox-content-filter" style="display:none;">
            <div class="row">
                <form class="form-horizontal" id="filter" action='/admin/customer/gamepoint/{{$cus_id}}' method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">ID</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['id']}}" placeholder="Search By ID" class="form-control" id="id" name="id">
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
                                <input type="text" class="form-control" name="start" id="sdate" placeholder="{{trans('localize.startDate')}}" value="{{$input['start']}}"/>
                                <span class="input-group-addon">{{trans('localize.to')}}</span>
                                <input type="text" class="form-control" name="end" id="edate" placeholder="{{trans('localize.endDate')}}" value="{{$input['end']}}"/>
                            </div>
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
                        <button type="button" class="btn btn-white btn-sm" data-toggle="modal"  data-id="{{$cus_id}}" data-post="data-php" data-action="export">
                            <i class="fa fa-share-square-o"></i> Export To CSV
                        </button>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('localize.#id')</th>
                                    <th class="text-center">@lang('localize.debit')</th>
                                    <th class="text-center">@lang('localize.credit')</th>
                                    <th class="text-center">Bidding ID</th>
                                    <th class="text-center">@lang('localize.remarks')</th>
                                    <th class="text-center">@lang('localize.date')</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($logs as $log)
                                <tr class="text-center">
                                    <td>{{$log->id}}</td>
                                    <td>{{ ($log->Debit_amount == 0)? '' : $log->Debit_amount }}</td>
                                    <td>{{ ($log->Credit_amount == 0)? '' : $log->Credit_amount }}</td>
                                    <td>{{ ($log->bidding_id == 0)? '' : $log->bidding_id }}</td>
                                    <td>{{ $log->remark }}</td>
                                    <td>{{ date('d/m/y H:i:s', strtotime($log->created_at)) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6">
                                        <ul class="pagination pull-right"></ul>
                                    </td>
                                </tr>
                            </tfoot>
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

        $('button').on('click', function(){
            var this_id = $(this).attr('data-id');
            var this_action = $(this).attr('data-action');

            if (this_action == 'export') {
                var params = window.location.search.split(/\?/);
                var url = '/admin/export/gamepoint_customer?action=export&cus_id=' + this_id + '&' + params[1];
                window.location.href = url;
            }
        });
    });

</script>
@endsection