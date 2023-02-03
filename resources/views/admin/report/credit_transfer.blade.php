@extends('admin.layouts.master')

@section('title', trans('localize.credit_transfer'))

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>@lang('localize.credit_transfer')</h2>
            <ol class="breadcrumb">
                <li>
                @lang('localize.report')
                </li>
                <li class="active">
                    <strong>@lang('localize.credit_transfer')</strong>
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
            <div class="ibox-content ibox-content-filter" style="display:block;">
                <div class="row">
                    <form class="form-horizontal" id="filter" action='/admin/report/credit-transfer' method="GET">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{trans('localize.Search_By')}}</label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" name="cid" placeholder="{{trans('localize.customer_id')}}" value="{{ (isset($data)) ? $data['cid'] : '' }}"/>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="cname" placeholder="{{trans('localize.customer_name')}}" value="{{ (isset($data)) ? $data['cname'] : '' }}"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{trans('localize.transaction_date')}}</label>
                            <div class="col-sm-9">
                                <div class="input-daterange input-group">
                                    <input type="text" class="form-control" name="start" id="sdate" placeholder="{{trans('localize.startDate')}}" value="{{ (isset($data)) ? $data['start'] : '' }}" required/>
                                    <span class="input-group-addon">{{trans('localize.to')}}</span>
                                    <input type="text" class="form-control" name="end" id="edate" placeholder="{{trans('localize.endDate')}}" value="{{ (isset($data)) ? $data['end'] : '' }}" required/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{trans('localize.account')}}</label>
                            <div class="col-sm-9">
                                <div class="i-checks">
                                    <label class="">
                                        <div class="iradio_square-green" style="position: relative;">
                                            <input type="radio" value="" name="account" {{ ((!isset($data)) || (isset($data) && ($data['account'] == ""))) ? 'checked' : '' }}>
                                            <ins class="iCheck-helper"></ins>
                                        </div>&nbsp; All
                                    </label>
                                    &nbsp;
                                    <label class="">
                                        <div class="iradio_square-green" style="position: relative;">
                                            <input type="radio" value="1" name="account" {{ (isset($data) && ($data['account'] == "1")) ? 'checked' : '' }}>
                                            <ins class="iCheck-helper"></ins>
                                        </div>&nbsp; Royal2u
                                    </label>
                                    &nbsp;
                                    <label class="">
                                        <div class="iradio_square-green" style="position: relative;">
                                            <input type="radio" value="2" name="account" {{ (isset($data) && ($data['account'] == "2")) ? 'checked' : '' }}>
                                            <ins class="iCheck-helper"></ins>
                                        </div>&nbsp; Early2u
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{trans('localize.country')}}</label>
                            <div class="col-sm-9">
                                {{--  <label style="cursor: pointer;"><input type="checkbox" class="i-checks" id="check_all" {{ (isset($data) && (count($data['countries']) == $countries->count())? 'checked' : '' ) }}>&nbsp; All</label>&nbsp;  --}}
                                <label style="cursor: pointer;"><input type="checkbox" class="i-checks input_checkbox" name="countries[]" value="0" {{ (isset($data['countries']) && (in_array("0", $data['countries']))? 'checked' : '' ) }}>&nbsp; No Country</label>&nbsp;
                                @foreach($countries as $country)
                                    <label style="cursor: pointer;"><input type="checkbox" class="i-checks input_checkbox" name="countries[]" value="{{ $country->co_id }}" {{ (isset($data['countries']) && (in_array($country->co_id, $data['countries']))? 'checked' : '' ) }}>&nbsp; {{ $country->co_name }}</label>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{trans('localize.sort')}}</label>
                            <div class="col-sm-9">
                                <select class="form-control" id="sortby" name="sortby" style="font-family:'FontAwesome', sans-serif;">
                                    <option value="new" {{ (isset($data) && ($data['sortby'] == "new")) ? 'selected' : '' }}>{{trans('localize.newest')}}</option>
                                    <option value="old" {{ (isset($data) && ($data['sortby'] == "old")) ? 'selected' : '' }}>{{trans('localize.oldest')}}</option>
                                    <option value="cusidAsc" {{ (isset($data) && ($data['sortby'] == "cusidAsc")) ? 'selected' : '' }}>{{trans('localize.customer_id')}} : &#xf162;</option>
                                    <option value="cusidDesc" {{ (isset($data) && ($data['sortby'] == "cusidDesc")) ? 'selected' : '' }}>{{trans('localize.customer_id')}}: &#xf163;</option>
                                    <option value="cusnameAsc" {{ (isset($data) && ($data['sortby'] == "cusnameAsc")) ? 'selected' : '' }}>{{trans('localize.customer_name')}} : &#xf15d;</option>
                                    <option value="cusnameDesc" {{ (isset($data) && ($data['sortby'] == "cusnameDesc")) ? 'selected' : '' }}>{{trans('localize.customer_name')}}: &#xf15e;</option>
                                    <option value="amountAsc" {{ (isset($data) && ($data['sortby'] == "amountAsc")) ? 'selected' : '' }}>{{trans('localize.amount')}} : &#xf162;</option>
                                    <option value="amountDesc" {{ (isset($data) && ($data['sortby'] == "amountDesc")) ? 'selected' : '' }}>{{trans('localize.amount')}}  : &#xf163;</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-9 col-sm-offset-2">
                                <div  class="col-sm-6">
                                    <button type="submit" class="btn btn-block btn-outline btn-primary" id="filter">
                                        <i class="fa fa-search"></i> @lang('localize.search')
                                    </button>
                                </div>
                                <div  class="col-sm-6">
                                    <button type="button" class="btn  btn-block btn-outline btn-primary btn-sm" onclick="location.href='/admin/report/credit-transfer'">
                                        <i class="fa fa-share-square-o"></i> @lang('localize.reset')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if (!empty($logs))
        <div class="row">
            <div class="col-lg-12">
                @include('merchant.common.success')
                @include('merchant.common.error')
                    <div class="ibox">
                        <div class="ibox-title" style="display: block;">
                        <div class="ibox-tools">

                            <div class="btn-group">
							@if($export_permission)
                                <button data-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle"> Export All <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a href="/admin/export/credit_transfer?action=export&export_as=csv{{ (!empty($data))? '&' . http_build_query($data) : '' }}">Csv</a></li>
                                    <li><a href="/admin/export/credit_transfer?action=export&export_as=xlsx{{ (!empty($data))? '&' . http_build_query($data) : '' }}">Xlsx</a></li>
                                    <li><a href="/admin/export/credit_transfer?action=export&export_as=xls{{ (!empty($data))? '&' . http_build_query($data) : '' }}">Xls</a></li>
                                </ul>
                            </div>
							
                            <div class="btn-group">
                                <button data-toggle="dropdown" class="btn btn-white btn-sm dropdown-toggle"> Export This Page <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a href="/admin/export/credit_transfer?action=export_by_page&export_as=csv{{ (!empty($data))? '&' . http_build_query($data) : '' }}">Csv</a></li>
                                    <li><a href="/admin/export/credit_transfer?action=export_by_page&export_as=xlsx{{ (!empty($data))? '&' . http_build_query($data) : '' }}">Xlsx</a></li>
                                    <li><a href="/admin/export/credit_transfer?action=export_by_page&export_as=xls{{ (!empty($data))? '&' . http_build_query($data) : '' }}">Xls</a></li>
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
                                        <th class="text-center">@lang('localize.date')</th>
                                        <th class="text-center">@lang('localize.customer_detail')</th>
                                        <th class="text-center">@lang('localize.amount')</th>
                                        <th class="text-center">@lang('localize.account')</th>
                                        <th class="text-center">@lang('localize.remarks')</th>
                                    </tr>
                                </thead>
                                @if ($logs->total())
                                    <tbody>
                                        @foreach($logs as $log)
                                        <tr class="text-center">
                                            <td>{{ \Helper::UTCtoTZ($log->created_at) }}</td>
                                            <td><a href="/admin/customer/view/{{ $log->cus_id }}" class="nolinkcolor" title="@lang('localize.customer_detail')" target="_blank">{{ $log->cus_id . ' - ' . $log->cus_name }}</a></td>
                                            <td>{{ ($log->credit_amount == 0.00)? '' : $log->credit_amount }}</td>
                                            <td>
                                                @if ($log->svi_wallet == 1)
                                                    Royal2u
                                                @else
                                                    Early2u
                                                @endif
                                            </td>
                                            <td>{{ $log->from }}</td>
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
        @endif
    </div>
@endsection

@section('style')
    <link href="/backend/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
    <link href="/backend/css/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet">
    <link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
@endsection

@section('script')
    <script src="/backend/js/plugins/datapicker/bootstrap-datepicker.js"></script>
    <script src="/backend/js/plugins/daterangepicker/daterangepicker.js"></script>
    <script src="/backend/js/plugins/iCheck/icheck.min.js"></script>
    <script src="/backend/js/custom.js"></script>

    <script>
        $(document).ready(function() {
            $('.i-checks').iCheck({
                radioClass: 'iradio_square-green',
                checkboxClass: 'icheckbox_square-green',
            });

            {{--  $('#check_all').on('ifToggled', function(event) {
                if(this.checked == true) {
                    $('.input_checkbox').iCheck('check');
                } else {
                    $('.input_checkbox').iCheck('uncheck');
                }
            });

            $('.input_checkbox').on('ifToggled', function(event) {
                if($(this).hasClass('checkbox_all')) {
                    if(this.checked == true) {
                        $('.input_checkbox').iCheck('check');
                    } else {
                        $('.input_checkbox').iCheck('uncheck');
                    }
                } else {
                    var check = 0;
                    var check_all = parseInt("{{ $countries->count() }}");

                    $('.input_checkbox').each(function () {
                        if(this.checked == true)
                            check++;
                    });

                    if(check == check_all)
                        $('.checkbox_all').iCheck('check');
                    else
                        $('.checkbox_all').iCheck('uncheck');
                }
            });  --}}

            // daterange move to custom.js
        });
    </script>
@endsection