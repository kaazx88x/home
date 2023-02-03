@extends('admin.layouts.master')

@section('title', 'Fund Request')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.manage')}} {{trans('localize.fund_request')}}</h2>
        <ol class="breadcrumb">
            <li>
                {{trans('localize.transaction')}}
            </li>
            <li>
                <a href="/admin/transaction/fund-request">{{trans('localize.fund_request')}}</a>
            </li>
            <li class="active">
                <strong>{{$status_list[$status]}}</strong>
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
                <form class="form-horizontal" id="filter" action='/admin/transaction/fund-request' method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.Merchants')}} ID</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['id']}}" placeholder="{{trans('localize.Search_By_Merchant_ID')}}" class="form-control" id="id" name="id">
                        </div>
                    </div>
                         <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.Merchants')}} {{trans('localize.Name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['name']}}" placeholder="{{trans('localize.Search_by_Merchant_Name')}}" class="form-control" id="name" name="name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.Merchants')}} {{trans('localize.email')}}</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['email']}}" placeholder="{{trans('localize.Search_by_Merchant_Email')}}" class="form-control" id="email" name="email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.date')}} {{trans('localize.range')}}  </label>
                        <div class="col-sm-9">
                            <div class="input-daterange input-group">
                                <input type="text" class="form-control" name="start" id="sdate" placeholder="{{trans('localize.startDate')}}" value="{{$input['start']}}"/>
                                <span class="input-group-addon">{{trans('localize.to')}}</span>
                                <input type="text" class="form-control" name="end" id="edate" placeholder="{{trans('localize.endDate')}}" value="{{$input['end']}}"/>
                                <span class="input-group-addon">{{trans('localize.For')}}</span>
                                <select class="form-control" id="type" name="type">
                                    <option value="created_at" {{ ($input['type'] == 'created_at') ? 'selected' : '' }}>{{trans('localize.request_date')}}</option>
                                    {{-- <option value="updated_at" {{ ($input['type'] == 'updated_at') ? 'selected' : '' }}>{{trans('localize.transaction_date')}}</option> --}}
                                    @if ($input['status'] == '' || $input['status'] == 1)
                                        <option value="updated_at" {{ ($input['type'] == 'updated_at') ? 'selected' : '' }}>{{trans('localize.paid_date')}}</option>
                                    @endif
                                </select>
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
                        <label class="col-sm-2 control-label">@lang('localize.merchant_country')</label>
                        <div class="col-sm-9">
                            <label style="cursor: pointer;"><input type="checkbox" class="i-checks input_checkbox" name="countries[]" value="0" {{ (isset($input['countries']) && (in_array("0", $input['countries']))? 'checked' : '' ) }}>&nbsp; No Country</label>&nbsp;
                            @foreach($countries as $country)
                                <label style="cursor: pointer;"><input type="checkbox" class="i-checks input_checkbox" name="countries[]" value="{{ $country->co_id }}" {{ (isset($input['countries']) && (in_array($country->co_id, $input['countries']))? 'checked' : '' ) }}>&nbsp; {{ $country->co_name }}</label>
                            @endforeach
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
            @include('admin.common.success')
            @include('admin.common.errors')
            @include('admin.common.error')
            <div class="ibox">
                 <div class="ibox-title" style="display: block;">
                    <div class="ibox-tools">
                        @if($export_permission)
                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle"> Export All <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="/admin/export/fund_request?action=export&export_as=csv{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
                                <li><a href="/admin/export/fund_request?action=export&export_as=xlsx{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
                                <li><a href="/admin/export/fund_request?action=export&export_as=xls{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
                            </ul>
                        </div>

                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-white btn-sm dropdown-toggle"> Export This Page <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="/admin/export/fund_request?action=export_by_page&export_as=csv{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
                                <li><a href="/admin/export/fund_request?action=export_by_page&export_as=xlsx{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
                                <li><a href="/admin/export/fund_request?action=export_by_page&export_as=xls{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-stripped">
                            <thead>
                                <tr>
                                    <th nowrap class="text-center">#</th>
                                    <th nowrap class="text-center">{{trans('localize.Merchants')}}</th>
                                    <th nowrap class="text-center">{{trans('localize.email')}}</th>
                                    <th nowrap class="text-center">@lang('localize.fund_withdrawal')</th>
                                    <th class="text-center">{{trans('localize.Current')}} {{trans('localize.balance')}} </th>
                                    <th class="text-center">{{trans('localize.request_date')}}</th>
                                    <th class="text-center">{{trans('localize.transaction_date')}}</th>
                                    <th class="text-center">{{trans('localize.paid_date')}}</th>
                                    <th class="text-center">{{trans('localize.Action')}}</th>
                                    <th class="text-center">{{trans('localize.Status')}}</th>
                                </tr>
                            </thead>
                            @if ($funds->total())
                                <tbody>
                                    @foreach($funds as $fund)
                                    <tr class="text-center">
                                        <td>{{$fund->wd_id}}</td>
                                        <td class="text-left">
                                            <p><a class="nolinkcolor" href="/admin/merchant/view/{{$fund->mer_id}}" data-toggle="tooltip" title="View merchant details">{{$fund->mer_id}} - {{$fund->mer_fname}} {{$fund->mer_lname}}</a></p>
                                        </td>
                                        <td>{{$fund->email}}</td>
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
                                        <td>{{$fund->mer_vtoken}}</td>
                                        {{-- <td>{{ Carbon\Carbon::createFromTimestamp(strtotime($fund->created_at))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A') }}</td> --}}
                                        <td>{{ \Helper::UTCtoTZ($fund->created_at) }}</td>
                                        {{-- <td>{{ Carbon\Carbon::createFromTimestamp(strtotime($fund->updated_at))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A') }}</td> --}}
                                        <td>{{ \Helper::UTCtoTZ($fund->updated_at) }}</td>
                                        {{-- <td>{{($fund->wd_status == 3 ) ? Carbon\Carbon::createFromTimestamp(strtotime($fund->wd_date))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A') : '' }}</td> --}}
                                        <td>{{($fund->wd_status == 3 ) ? \Helper::UTCtoTZ($fund->wd_date) : '' }}</td>
                                        <td nowrap width="10%">
                                            <p>
                                                <button type="button" class="btn btn-info btn-block btn-xs" data-toggle="modal" data-id="{{ $fund->mer_id }}" data-post="data-php" data-action="bank">{{trans('localize.Show_Bank_Info')}}</button>
                                            </p>

                                            @if($fund->wd_statement)
                                            <p>
                                                <button type="button" class="btn btn-primary btn-block btn-xs" onclick="get_fund_withdraw_statement({{ $fund->wd_id }}, '0')">@lang('localize.view_statement')</button>
                                            </p>
                                            @endif

                                            <p>
                                                @if ($fund->wd_status == 0)
                                                    @if($fund_approval_permission)
                                                    <button type="button" class="btn btn-warning btn-xs" data-id="{{ $fund->wd_id }}" data-action="accept"><i class="fa fa-check"></i> <span>{{trans('localize.approve')}}</span></button>
                                                    <button type="button" class="btn btn-danger btn-xs" data-id="{{ $fund->wd_id }}" data-action="decline"><i class="fa fa-ban"></i> <span>{{trans('localize.decline')}}</span></button>
                                                    @endif
                                                @elseif ($fund->wd_status == 1)
                                                    @if($fund_paid)
                                                    <button type="button" class="btn btn-primary btn-block btn-xs" data-id="{{ $fund->wd_id }}" data-action="paid"><i class="fa fa-check"></i> <span>{{trans('localize.Fund_Paid')}}</span></button>
                                                    @endif
                                                @endif
                                            </p>
                                        </td>
                                        <td class="text-center">
                                            @if ($fund->wd_status == 1)
                                            <span class="text-navy" style="font-size:13px;">{{trans('localize.approved')}}</span>
                                            @elseif ($fund->wd_status == 2)
                                                <span class="text-danger" style="font-size:13px;">{{trans('localize.declined')}}</span>
                                            @elseif ($fund->wd_status == 3)
                                                <span class="text-success" style="font-size:13px;">{{trans('localize.paid')}}</span>
                                            @else
                                                <span class="text-warning" style="font-size:13px;">{{trans('localize.pending')}}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10">
                                            <div class=" col-xs-6">
                                                <span class="pagination">
                                                    {{trans('localize.Showing')}} {{$funds->firstItem()}} {{trans('localize.to')}} {{$funds->lastItem()}} {{trans('localize.of')}} {{$funds->Total()}} {{trans('localize.Records')}}
                                                </span>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <div class="col-xs-7 text-right">
                                                    {{$funds->appends(Request::except('page'))->links()}}
                                                </div>
                                                <div class="col-xs-5 text-right pagination">
                                                    {{trans('localize.Go_To_Page')}}
                                                    <input type='number' id='pageno' name='pageno' size="3" min="1" max="{{$funds->lastPage()}}">
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="gotopage('page')">
                                                        <i class="fa fa-share-square-o"></i> {{trans('localize.Go')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            @else
                                <tr>
                                    <td colspan="10" class="text-center">@lang('localize.nodata')</td>
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
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script src="/backend/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="/backend/js/plugins/daterangepicker/daterangepicker.js"></script>
<script src="/backend/js/custom.js"></script>

<script>
    $(document).ready(function() {
        // daterange move to custom.js

        $('button').on('click', function(){
            var this_id = $(this).attr('data-id');
            var this_action = $(this).attr('data-action');
            if (this_action == 'bank') {
                view_merchant_bank_info(this_id);
            } else if (this_action == 'accept') {
                approve_fund(this_id);
            } else if (this_action == 'decline') {
                decline_fund(this_id);
            } else if (this_action == 'paid') {
                paid_fund(this_id);
            }
        });

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
