@extends('merchant.layouts.master')

@section('title', 'Order Offline')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>{{trans('localize.order_offline')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url( $route . '/transaction/offline') }}">@lang('localize.all')</a>
            </li>
            <li class="active">
                <strong>{{$status_list[$status]}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <form id="filter" action="{{ url($route . '/transaction/offline') }}" method="GET">
                <div class="col-sm-2">
                    <div class="form-group">
                        <input type="text" value="{{$input['id']}}" placeholder="{{trans('localize.invoice_no')}}" class="form-control" id="id" name="id">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="input-group">
                        <span class="input-group-addon">OFA</span>
                        <input type="text" class="form-control" placeholder="{{trans('localize.tax_invoice_no')}}" class="form-control" id="tax_inv_no" name="tax_inv_no" value="{{$input['tax_inv_no']}}">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <div class="input-daterange input-group">
                            <input type="text" class="form-control" name="start" id="sdate" placeholder="{{trans('localize.startDate')}}" value="{{$input['start']}}"/>
                            <span class="input-group-addon">{{trans('localize.to')}}</span>
                            <input type="text" class="form-control" name="end" id="edate" placeholder="{{trans('localize.endDate')}}" value="{{$input['end']}}"/>
                        </div>
                        <select class="form-control" id="type" name="type">
                            <option value="created_at" {{ ($input['type'] == 'created_at') ? 'selected' : '' }}>{{trans('localize.transaction_date')}}</option>
                            @if ($input['status'] == '' || $input['status'] == 1)
                                <option value="paid_date" {{ ($input['type'] == 'paid_date') ? 'selected' : '' }}>{{trans('localize.paid_date')}}</option>
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
                        <select class="form-control" id="store" name="store">
                            @foreach ($store_list as $key => $stat)
                                <option value="{{ $key }}" {{ (strval($key) == $input['store']) ? 'selected' : '' }}>{{ $stat }}</option>
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
                 <div class="ibox-title" style="display: block;">
                    <div class="ibox-tools">
                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle">{{trans('localize.view_batch_inv')}}<span class="caret"></span></button>
                            <ul class="dropdown-menu" id="dropdown-view">
                                <li><a href="" id="view_trans_ref">{{trans('localize.view_trans_ref')}}</a></li>
                                <li><a href="" id="view_mer_inv">{{trans('localize.view_mer_inv')}}</a></li>
                            </ul>
                        </div>
                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle"> Export All <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="/merchant/export/order_offline?action=export&export_as=csv{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
                                <li><a href="/merchant/export/order_offline?action=export&export_as=xlsx{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
                                <li><a href="/merchant/export/order_offline?action=export&export_as=xls{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
                            </ul>
                        </div>

                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-white btn-sm dropdown-toggle"> Export This Page <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="/merchant/export/order_offline?action=export_by_page&export_as=csv{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
                                <li><a href="/merchant/export/order_offline?action=export_by_page&export_as=xlsx{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
                                <li><a href="/merchant/export/order_offline?action=export_by_page&export_as=xls{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">
                                        <div class="i-checks">
                                            <label>
                                                <input type="checkbox" id="check_all">
                                            </label>
                                        </div>
                                    </th>
                                    <th class="text-center">#ID</th>
                                    <th class="text-center">{{trans('localize.tax_invoice_no')}}</th>
                                    <th class="text-center">{{trans('localize.merchant_invoice_no')}}</th>
                                    <th class="text-center">{{trans('localize.store_name') }}</th>
                                    <th class="text-center">{{trans('localize.customer')}}</th>
                                    <th class="text-center">{{trans('localize.amount')}}</th>
                                    <th class="text-center">@lang('common.credit_name')</th>
                                    <th class="text-center">@lang('localize.merchant_charge')</th>
                                    <th class="text-center">@lang('localize.merchant_earned_credit')</th>
                                    {{--<th class="text-center">{{trans('localize.balance')}}</th>--}}
                                    @if ($input['status'] == 1)
                                        <th class="text-center text-nowrap">{{trans('localize.paid_date')}}</th>
                                    @endif
                                    <th class="text-center text-nowrap">{{trans('localize.date')}}</th>
                                    <th class="text-center">{{trans('localize.action')}}</th>
                                </tr>
                            </thead>
                            @if ($orders->total())
                                <tbody>
                                    @foreach ($orders as $key => $order)
                                    <tr class="text-center">
                                        <th class="text-center text-nowrap">
                                             @if($order->status == 1 || $order->status == 4)
                                            <div class="i-checks">
                                                <label>
                                                    <input type="checkbox" class="input_checkbox" name="inv_id" value="{{ $order->id }}">
                                                </label>
                                            </div>
                                            @endif
                                        </th>
                                        <td>{{$order->id}}</td>
                                        <td>
                                            @if(!empty($order->tax_inv_no))
                                            OFA{{$order->tax_inv_no}}
                                            @else
                                            --
                                            @endif
                                        </td>
                                        <td>{{$order->inv_no}}</td>
                                        <td>{{ $order->stor_name }}</td>
                                        <td class="text-left">{{$order->cus_name}}</td>
                                        <td>{{ $order->currency.' '. number_format($order->amount, 2) }}</td>
                                        <td>{{ number_format($order->v_token, 4) }}</td>
                                        <td>
                                            {{ number_format($order->merchant_charge_token, 4) }}
                                            {{--<dl class="dl-horizontal" style="margin-bottom:0;">--}}
                                                {{--<dt>@lang('common.credit_name')</dt>--}}
                                                {{--<dd>{{ number_format($order->v_token, 4) }}</dd>--}}
                                                {{-- <dt>{{trans('localize.platform_charges').' ('.round($order->merchant_platform_charge_percentage).'%)'}}</dt>--}}
                                                {{--<dd>{{$order->merchant_platform_charge_token}}</dd>--}}
                                                {{--<dt>{{trans('localize.gst').' ('.round($order->customer_charge_percentage).'%)'}}</dt>--}}
                                                {{--<dd>{{$order->customer_charge_token}}</dd> --}}
                                                {{--<dt>{{trans('localize.merchant_charge').' ('.round($order->merchant_charge_percentage).'%)'}}</dt>--}}
                                                {{--<dd>{{ number_format($order->merchant_charge_token, 4) }}</dd>--}}
                                                {{-- <dt>{{trans('localize.order_credit_total')}}</dt>--}}
                                                {{--<dd>{{$order->order_total_token}}</dd> --}}
                                            {{--</dl>--}}
                                        </td>
                                        {{--<td>{{ number_format($order->v_token - $order->merchant_charge_token, 4) }}</td>--}}
                                        <td>{{ number_format($order->v_token - $order->merchant_charge_token, 4) }}</td>
                                        @if ($input['status'] == 1)
                                            {{-- <td>{{ Carbon\Carbon::createFromTimestamp(strtotime($order->paid_date))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A') }}</td> --}}
                                            <td>{{ \Helper::UTCtoTZ($order->paid_date) }}</td>
                                        @endif
                                        {{-- <td>{{ Carbon\Carbon::createFromTimestamp(strtotime($order->created_at))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A') }}</td> --}}
                                        <td nowrap>
                                            @lang('localize.transaction')<br>
                                            {{ \Helper::UTCtoTZ($order->created_at) }}

                                            @if($order->status == 4)
                                            <hr style="margin:5px;">
                                            @lang('localize.refunded_at')<br>
                                            {{ \Helper::UTCtoTZ($order->updated_at) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($order->status == 0)
                                                <div class="text-warning">{{trans('localize.unpaid')}}</div>
                                            @elseif ($order->status == 1)
                                                <div class="text-navy">{{trans('localize.paid')}}</div>
                                            @elseif ($order->status == 2)
                                                <div class="text-danger">{{trans('localize.cancel_by_member')}}</div>
                                            @elseif ($order->status == 3)
                                                <div class="text-danger">{{trans('localize.cancel_by_merchant')}}</div>
                                            @elseif ($order->status == 4)
                                                <div class="text-info">{{trans('localize.refunded')}}</div>
                                            @endif
                                            <button type="button" class="btn btn-white btn-block btn-sm" data-toggle="modal" data-id="{{ $order->id }}" data-post="data-php" data-action="details"><i class="fa fa-file-text-o"></i> {{trans('localize.view_details')}}</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <th class="text-center" colspan="5" style="text-align:right;">@lang('localize.subtotal')</th>
                                        {{--<th class="text-nowrap">{{number_format($v_token,4)}}</th>--}}
                                        <th class="text-nowrap text-center">{{ number_format($orders->sum('v_token'), 4) }}</th>
                                        <th class="text-center text-nowrap">{{ number_format($orders->sum('merchant_charge_token'), 4) }}</th>
                                        {{--<th class="text-nowrap">{{number_format(($merchant_charge_token), 4)}}</th>--}}
                                        <th class="text-center text-nowrap">{{ number_format($orders->sum('v_token') - $orders->sum('merchant_charge_token'), 4) }}</th>
                                        <th class="text-nowrap"></th>
                                        <th class="text-nowrap"></th>
                                        @if($input['status'] == 1)
                                            <th class="text-nowrap"></th>
                                        @endif
                                    </tr>
                                    <tr>
                                        <th class="text-center" colspan="5" style="text-align:right;">@lang('localize.total')</th>
                                        {{--<th class="text-nowrap">{{number_format($v_token,4)}}</th>--}}
                                        <th class="text-nowrap text-center">{{ number_format($total->v_credit, 4) }}</th>
                                        <th class="text-center text-nowrap">{{ number_format($total->merchant_charge_token, 4) }}</th>
                                        {{--<th class="text-nowrap">{{number_format(($merchant_charge_token), 4)}}</th>--}}
                                        <th class="text-center text-nowrap">{{ number_format($total->v_credit - $total->merchant_charge_token, 4) }}</th>
                                        <th class="text-nowrap"></th>
                                        <th class="text-nowrap"></th>
                                        @if($input['status'] == 1)
                                            <th class="text-nowrap"></th>
                                        @endif
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="100">
                                            <div class=" col-xs-6">
                                                <span class="pagination">
                                                    Showing {{$orders->firstItem()}} to {{$orders->lastItem()}} of {{$orders->Total()}} Records
                                                </span>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                {{$orders->appends(Request::except('page'))->links()}}
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            @else
                                <tr>
                                    <td colspan="100" class="text-center">@lang('localize.nodata')</td>
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
<link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/iCheck/icheck.min.js"></script>
<script src="/backend/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="/backend/js/plugins/daterangepicker/daterangepicker.js"></script>
<script src="/backend/js/custom.js"></script>

<script>
    $(document).ready(function() {
        // daterange move to custom.js
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $('button').on('click', function(){
            var this_id = $(this).attr('data-id');
            var this_action = $(this).attr('data-action');
            var type = 'merchant';

            if (this_action == 'details') {
                view_order_offline(this_id,type);
            }
        });

        $('#check_all').on('ifToggled', function(event) {
            if(this.checked == true) {
                $('.input_checkbox').iCheck('check');
            } else {
                $('.input_checkbox').iCheck('uncheck');
            }
        });

        $('#view_trans_ref').on("click", function(e) {
            if ($('input[name=inv_id]:checked').length > 0){
                var type = 'admin';
                e.preventDefault();
                var sel = $('input[name=inv_id]:checked').map(function(_, el) {
                    return $(el).val();
                }).get();

                $.get( '/view_batch_inv/trans_ref/' + sel + '/' + type, function( data ) {
                    $('#myModal').modal();
                    $('#myModal').on('shown.bs.modal', function(){
                        $('#myModal .load_modal').html(data);
                    });
                    $('#myModal').on('hidden.bs.modal', function(){
                        $('#myModal .modal-body').data('');
                    });
                });
            }else{
                swal({
                    title: "Please Select Multiple Invoice",
                    type: "error",
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "OK!",
                    closeOnConfirm: false
                    }, function(isConfirm){

                    });
            }
        })

        $('#view_mer_inv').on("click", function(e) {
            if ($('input[name=inv_id]:checked').length > 0){
                var type = 'admin';
                e.preventDefault();
                var sel = $('input[name=inv_id]:checked').map(function(_, el) {
                    return $(el).val();
                }).get();

                $.get( '/view_batch_inv/mer_inv/' + sel + '/' + type, function( data ) {
                    $('#myModal').modal();
                    $('#myModal').on('shown.bs.modal', function(){
                        $('#myModal .load_modal').html(data);
                    });
                    $('#myModal').on('hidden.bs.modal', function(){
                        $('#myModal .modal-body').data('');
                    });
                });
            }else{
                swal({
                    title: "Please Select Multiple Invoice",
                    type: "error",
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "OK!",
                    closeOnConfirm: false
                    }, function(isConfirm){

                    });
            }

        })

    });
</script>
@endsection