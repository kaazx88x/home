@extends('admin.layouts.master')

@section('title', 'Order Offline')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>{{trans('localize.order')}} {{trans('localize.Offline')}}</h2>
        <ol class="breadcrumb">
            <li>
                {{trans('localize.transaction')}}
            </li>
            <li>
                <a href="/admin/transaction/offline">{{trans('localize.order')}} {{trans('localize.Offline')}}</a>
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
                <h5>{{trans('localize.Search_Filter')}}</h5>
                <div class="ibox-tools">
                        <i class="fa fa-chevron-down"></i>
                </div>
            </div>
        </a>
        <div class="ibox-content ibox-content-filter" style="display:none;">
            <div class="row">
                <form class="form-horizontal" id="filter" action='/admin/transaction/offline' method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.Search_By')}} ID</label>
                        <div class="col-sm-3">
                            <input type="text" value="{{$input['id']}}" placeholder="{{trans('localize.Search_By_Invoice_No')}}" class="form-control" id="id" name="id">
                        </div>
                        <div class="col-sm-2">
                            <input type="text" value="{{$input['cid']}}" placeholder="{{trans('localize.customer')}} ID" class="form-control" id="id" name="cid">
                        </div>
                        <div class="col-sm-2">
                            <input type="text" value="{{$input['mid']}}" placeholder="{{trans('localize.Merchants')}} ID" class="form-control" id="id" name="mid">
                        </div>
                        <div class="col-sm-2">
                            <input type="text" value="{{$input['sid']}}" placeholder="{{trans('localize.store')}} ID" class="form-control" id="sid" name="sid">
                        </div>
                    </div>
                    <div class="form-group">
                    <label class="col-sm-2 control-label">{{trans('localize.Search_By')}} {{trans('localize.invoice_no')}}</label>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">OFA</span>
                                <input type="text" class="form-control" placeholder="{{trans('localize.tax_invoice_no')}}" class="form-control" id="tax_inv_no" name="tax_inv_no" value="{{$input['tax_inv_no']}}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.Name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['search']}}" placeholder="{{trans('localize.Search_By')}} {{trans('localize.customer')}}/{{trans('localize.Merchants')}} {{trans('localize.Name')}}" class="form-control" id="search" name="search">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.date')}} {{trans('localize.range')}}</label>
                        <div class="col-sm-9">
                            <div class="input-daterange input-group">
                                <input type="text" class="form-control" name="start" id="sdate" placeholder="{{trans('localize.startDate')}}" value="{{$input['start']}}"/>
                                <span class="input-group-addon">To</span>
                                <input type="text" class="form-control" name="end" id="edate" placeholder="{{trans('localize.endDate')}}" value="{{$input['end']}}"/>
                                <span class="input-group-addon">For</span>
                                <select class="form-control" id="type" name="type">
                                    <option value="created_at" {{ ($input['type'] == 'created_at') ? 'selected' : '' }}>{{trans('localize.transaction_date')}}</option>
                                    @if ($input['status'] == '' || $input['status'] == 1)
                                        <option value="paid_date" {{ ($input['type'] == 'paid_date') ? 'selected' : '' }}>{{trans('localize.paid_date')}}</option>
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
                                <option value="new" {{($input['sort'] == "" || $input['sort'] == 'new') ? 'selected' : ''}}>{{trans('localize.Newest')}}</option>
                                <option value="old" {{($input['sort'] == 'old') ? 'selected' : ''}}>{{trans('localize.Oldest')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('localize.merchant_country')</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                            <label style="cursor: pointer;"><input type="checkbox" class="i-checks input_checkbox" name="merchant_countries[]" value="0" {{ (isset($input['merchant_countries']) && (in_array("0", $input['merchant_countries']))? 'checked' : '' ) }}>&nbsp; No Country</label>&nbsp;
                            @foreach($countries as $country)
                                <label style="cursor: pointer;"><input type="checkbox" class="i-checks input_checkbox" name="merchant_countries[]" value="{{ $country->co_id }}" {{ (isset($input['merchant_countries']) && (in_array($country->co_id, $input['merchant_countries']))? 'checked' : '' ) }}>&nbsp; {{ $country->co_name }}</label>
                            @endforeach
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('localize.customer_country')</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                            <label style="cursor: pointer;"><input type="checkbox" class="i-checks input_checkbox" name="customer_countries[]" value="0" {{ (isset($input['customer_countries']) && (in_array("0", $input['customer_countries']))? 'checked' : '' ) }}>&nbsp; No Country</label>&nbsp;
                            @foreach($countries as $country)
                                <label style="cursor: pointer;"><input type="checkbox" class="i-checks input_checkbox" name="customer_countries[]" value="{{ $country->co_id }}" {{ (isset($input['customer_countries']) && (in_array($country->co_id, $input['customer_countries']))? 'checked' : '' ) }}>&nbsp; {{ $country->co_name }}</label>
                            @endforeach
                            </p>
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
                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle">{{trans('localize.view_batch_inv')}}<span class="caret"></span></button>
                            <ul class="dropdown-menu" id="dropdown-view">
                                <li><a href="" id="view_trans_ref">{{trans('localize.view_trans_ref')}}</a></li>
                                <li><a href="" id="view_mer_inv">{{trans('localize.view_mer_inv')}}</a></li>

                            </ul>
                        </div>
                        @if($export_permission)
                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle"> Export All <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="/admin/export/order_offline?action=export&export_as=csv{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
                                <li><a href="/admin/export/order_offline?action=export&export_as=xlsx{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
                                <li><a href="/admin/export/order_offline?action=export&export_as=xls{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
                            </ul>
                        </div>

                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-white btn-sm dropdown-toggle"> Export This Page <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="/admin/export/order_offline?action=export_by_page&export_as=csv{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
                                <li><a href="/admin/export/order_offline?action=export_by_page&export_as=xlsx{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
                                <li><a href="/admin/export/order_offline?action=export_by_page&export_as=xls{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
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
                                    <th class="text-center">{{trans('localize.customer')}}</th>
                                    <th class="text-center">{{trans('localize.Merchants')}}</th>
                                    <th class="text-center">{{trans('localize.store')}}</th>
                                    <th class="text-center">{{trans('localize.amount')}}</th>
                                    <th class="text-center">{{trans('localize.order')}} {{ trans('common.credit_name') }} {{trans('localize.total')}}</th>
                                    <th class="text-center">{{trans('localize.charges')}}</th>
                                    <th class="text-center">{{trans('localize.Merchants')}} {{trans('localize.Earning')}} </th>
                                    @if ($input['status'] == 1)
                                        <th class="text-center text-nowrap">{{trans('localize.paid_date')}}</th>
                                    @endif
                                    <th class="text-center">{{trans('localize.date')}}</th>
                                    <th class="text-center">{{trans('localize.Action')}}</th>
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
                                        <td>
                                        {{$order->inv_no}}
                                        </td>
                                        <td class="text-left">
                                            @if(!empty($order->cus_name))
                                                <a href="/admin/customer/view/{{$order->cus_id}}" data-toggle="tooltip" title="View customer details" class="nolinkcolor">{{$order->cus_id}} - {{$order->cus_name}}</a>
                                            @else
                                                <p class="text-danger">{{trans('localize.Customer_not_found')}}</p>
                                            @endif
                                        </td>
                                        <td class="text-left">
                                            @if(!empty($order->mer_fname))
                                                <a href="/admin/merchant/view/{{$order->mer_id}}" data-toggle="tooltip" title="View merchant details" class="nolinkcolor">{{$order->mer_id}} - {{$order->mer_fname}}</a>
                                            @else
                                                <p class="text-danger">{{trans('localize.Merchant_not_found')}}</p>
                                            @endif
                                        </td>
                                        <td class="text-left">
                                            @if(!empty($order->stor_name))
                                                <a href="/admin/store/edit/{{$order->mer_id}}/{{$order->store_id}}" data-toggle="tooltip" title="View store details" class="nolinkcolor">{{$order->store_id}} - {{$order->stor_name}}</a>
                                            @else
                                                <p class="text-danger">{{trans('localize.store_not_found')}}</p>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">{{ $order->currency.' '. number_format($order->amount, 2) }}</td>
                                        <td>
                                            {{ number_format($order->order_total_token, 4) }}
                                                <br/><br/>
                                                Deduct From Wallet :
                                                <b>
                                                    @if ($order->wallet_id)
                                                        @if ($order->wallet_id == 99)
                                                            Hemma
                                                        @else
                                                            {{ $order->wallet_name }}
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </b>
                                        </td>
                                        <td class="text-left text-nowrap">
                                            <dl class="dl-horizontal" style="margin-bottom:0;">
                                                {{--<dt>Order {{ trans('localize.credit') }} Total</dt>--}}
                                                {{--<dd>{{ number_format($order->order_total_token, 4) }}</dd>--}}
                                                <dt>{{ trans('localize.platform_charges').' ('.round($order->merchant_platform_charge_percentage).'%)'}}</dt>
                                                <dd>{{ number_format($order->merchant_platform_charge_token, 4) }}</dd>
                                                <dt>{{ trans('localize.gst').' ('.round($order->customer_charge_percentage).'%)'}}</dt>
                                                <dd>{{ number_format($order->customer_charge_token, 4) }}</dd>
                                                <dt>{{ trans('localize.merchant_charge') .' ('.round($order->merchant_charge_percentage).'%)'}}</dt>
                                                <dd>{{ number_format($order->merchant_charge_token, 4) }}</dd>
                                                {{--<dt>Merchant Earning</dt>--}}
                                                {{--<dd>{{ number_format(($order->v_token - $order->merchant_charge_token), 4) }}</dd>--}}
                                            </dl>
                                        </td>
                                        <td>{{ number_format(($order->v_token - $order->merchant_charge_token), 4) }}</td>
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
                                                <div class="text-danger">{{trans('localize.cancelbymember')}}</div>
                                            @elseif ($order->status == 3)
                                                <div class="text-danger">{{trans('localize.cancelbymerchant')}}</div>
                                            @elseif ($order->status == 4)
                                                <div class="text-navy">{{trans('localize.refunded')}}</div>
                                            @endif
                                            <button type="button" class="btn btn-white btn-block btn-sm view-details" data-toggle="modal" data-id="{{ $order->id }}" data-post="data-php" data-action="details"><i class="fa fa-file-text-o"></i> {{trans('localize.view_details')}}</button>
                                            @if ($order->status == 1)
                                                @if($order_refund_permission)
                                                <button type="button" class="btn btn-default btn-block btn-sm" data-toggle="modal" data-id="{{ $order->id }}" data-post="data-php" data-action="refund">{{trans('localize.Refund')}}</button>
                                                @endif
                                            @endif
                                        </td>
                                        {{--<td>--}}
                                            {{--<button type="button" class="btn btn-white btn-block btn-sm" data-toggle="modal" data-id="{{ $order->id }}" data-post="data-php" data-action="details"><i class="fa fa-file-text-o"></i> View Details</button>--}}
                                            {{--@if ($order->status == 1)--}}
                                            {{--<button type="button" class="btn btn-default btn-block btn-sm" data-toggle="modal" data-id="{{ $order->id }}" data-post="data-php" data-action="refund">Refund</button>--}}
                                            {{--@endif--}}
                                        {{--</td>--}}
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <th class="text-center" colspan="6" style="text-align:right;">@lang('localize.subtotal')</th>
                                        <th class="text-center text-nowrap">{{ number_format($orders->sum('order_total_token'), 4) }}</th>
                                        <th class="text-nowrap">
                                            <dl class="dl-horizontal" style="margin-bottom:0;">
                                                <dt>{{ trans('localize.platform_charges') }}</dt>
                                                <dd>{{ number_format($orders->sum('merchant_platform_charge_token'), 4) }}</dd>
                                                <dt>{{ trans('localize.gst') }}</dt>
                                                <dd>{{ number_format($orders->sum('customer_charge_token'), 4) }}</dd>
                                                <dt>{{ trans('localize.merchant_charge') }}</dt>
                                                <dd>{{ number_format($orders->sum('merchant_charge_token'), 4) }}</dd>
                                            </dl>
                                        </th>
                                        <th class="text-center text-nowrap">{{ number_format($orders->sum('v_token') - $orders->sum('merchant_charge_token'), 4) }}</th>
                                        <th class="text-nowrap"></th>
                                        <th class="text-nowrap"></th>
                                        @if($input['status'] == 1)
                                            <th class="text-nowrap"></th>
                                        @endif
                                    </tr>
                                    <tr>
                                        <th class="text-center" colspan="6" style="text-align:right;">@lang('localize.total')</th>
                                        <th class="text-center text-nowrap">{{ number_format($total->v_credit, 4) }}</th>
                                        <th class="text-nowrap">
                                            <dl class="dl-horizontal" style="margin-bottom:0;">
                                                <dt>{{ trans('localize.platform_charges') }}</dt>
                                                <dd>{{ number_format($total->merchant_platform_charge_token, 4) }}</dd>
                                                <dt>{{ trans('localize.gst') }}</dt>
                                                <dd>{{ number_format($total->customer_charge_token, 4) }}</dd>
                                                <dt>{{ trans('localize.merchant_charge') }}</dt>
                                                <dd>{{ number_format($total->merchant_charge_token, 4) }}</dd>
                                            </dl>
                                        </th>
                                        <th class="text-center text-nowrap">{{ number_format($total->v_credit - $total->customer_charge_token - $total->merchant_platform_charge_token - $total->merchant_charge_token, 4) }}</th>
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
                                            <div class=" col-xs-3">
                                                <span class="pagination">
                                                    {{trans('localize.Showing')}} {{$orders->firstItem()}} {{trans('localize.to')}} {{$orders->lastItem()}} {{trans('localize.of')}} {{$orders->Total()}} {{trans('localize.Records')}}
                                                </span>
                                            </div>
                                            <div class="col-xs-6 text-center">
                                                    {{$orders->appends(Request::except('page'))->links()}}
                                            </div>
                                            <div class="col-xs-3 text-right pagination">
                                                {{trans('localize.Go_To_Page')}}
                                                <input type='number' id='pageno' name='pageno' size="3" min="1" max="{{$orders->lastPage()}}">
                                                <button type="button" class="btn btn-primary btn-sm" onclick="gotopage('page')">
                                                    <i class="fa fa-share-square-o"></i> {{trans('localize.Go')}}
                                                </button>
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
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        // daterange move to custom.js

        $('button').on('click', function(){
            var this_id = $(this).attr('data-id');
            var this_action = $(this).attr('data-action');
            var type = 'admin';

            if (this_action == 'details') {
                view_order_offline(this_id,type);
            } else if(this_action == 'refund') {
                swal({
                title: "Are you sure?",
                text: "Confirm to refund this order? This action cannot be undone",
                type: "warning",   showCancelButton: true,
                confirmButtonColor: "#5cb85c",
                confirmButtonText: "Refund",
                closeOnConfirm: false
                }, function(isConfirm) {
                    if (isConfirm) {
                        window.location.href = "/admin/transaction/refund_offline_order/"+this_id;
                    }
                });
            }
        });

        $("#pageno").change(function(){
            var input = $(this).val();
            var max = {{$orders->lastPage()}};
            if(input > max){
                $(this).val(max);
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

        $.getJSON( "{{ url('/backend/js/company.json') }}", function( data ) {
            $.each(data, function( index, value ) {
                $('#dropdown-view').append('<li><a href id="view_tax_inv_'+value.name+'">@lang("localize.view_tax_inv") ('+value.name+')</a></li>');

            $('#view_tax_inv_'+value.name).on("click", function(e) {
                if ($('input[name=inv_id]:checked').length > 0){
                    var type = 'admin';
                    e.preventDefault();
                    var sel = $('input[name=inv_id]:checked').map(function(_, el) {
                        return $(el).val();
                    }).get();

                    $.get( '/view_batch_inv/tax_inv/' + sel + '/' + type +'/'+value.name, function( data ) {
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