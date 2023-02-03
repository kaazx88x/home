@extends('admin.layouts.master')

@section('title', 'Online Orders')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{ $type_list[$type]['lang'] }}</h2>
        <ol class="breadcrumb">
            <li>
                @lang('localize.transaction')
            </li>
            <li>
                {{ $type_list[$type]['lang'] }}
            </li>
            <li class="active">
                <strong>{{ $status_list[$input['status']] }}</strong>
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
                <form class="form-horizontal" id="filter" action="{{ url('admin/transaction/product', [$type_list[$type]['name']]) }}" method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.Search_By')}}</label>
                        <div class="col-sm-2">
                            <input type="text" value="{{$input['oid']}}" placeholder="{{trans('localize.order')}} ID" class="form-control" id="id" name="oid">
                        </div>
                        <div class="col-sm-2">
                            <input type="text" value="{{$input['tid']}}" placeholder="{{trans('localize.transaction')}} ID" class="form-control" id="id" name="tid">
                        </div>
                        <div class="col-sm-2">
                            <input type="text" value="{{$input['pid']}}" placeholder="{{trans('localize.product')}} ID" class="form-control" id="pid" name="pid">
                        </div>
                        <div class="col-sm-1">
                            <input type="text" value="{{$input['cid']}}" placeholder="{{trans('localize.customer')}} ID" class="form-control" id="cid" name="cid">
                        </div>
                        <div class="col-sm-1">
                            <input type="text" value="{{$input['mid']}}" placeholder="{{trans('localize.Merchants')}} ID" class="form-control" id="mid" name="mid">
                        </div>
                        <div class="col-sm-1">
                            <input type="text" value="{{$input['sid']}}" placeholder="{{trans('localize.store')}} ID" class="form-control" id="sid" name="sid">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.productName')}}</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['name']}}" placeholder="{{trans('localize.Search_By')}} {{trans('localize.productName')}}" class="form-control" id="name" name="name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.transaction')}} {{trans('localize.date')}}</label>
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
                                <option value="name_asc" {{ ($input['sort'] == "name_asc") ? 'selected' : '' }}>{{trans('localize.productName')}} : &#xf15d;</option>
                                <option value="name_desc" {{ ($input['sort'] == "name_desc") ? 'selected' : '' }}>{{trans('localize.productName')}} : &#xf15e;</option>
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

            @include('admin.common.notifications')

            @if($input['status'] == 1)
            <form id="batch_update_form" action="{{ url('admin/transaction/product/batch/update', ['accept_order']) }}" method="POST">
            {{ csrf_field() }}
            @endif

            <div class="ibox">
                 <div class="ibox-title" style="display: block;">
                    <div class="ibox-tools" style="margin-bottom:10px;">

                        @if($input['status'] == 1)
                        <div class="pull-left" style="margin-left:10px;">
                            @if($accept_multiple_order_permission)
                            <button type="button" class="btn btn-primary btn-sm" id="batch_update">
                                @lang('localize.accept_multiple_orders')
                            </button>
                            @endif
                        </div>
                        @endif

                        @if($export_permission)
                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle"> Export All <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="/admin/export/product_orders/{{ $type_list[$type]['name'] }}?action=export&export_as=csv{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
                                <li><a href="/admin/export/product_orders/{{ $type_list[$type]['name'] }}?action=export&export_as=xlsx{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
                                <li><a href="/admin/export/product_orders/{{ $type_list[$type]['name'] }}?action=export&export_as=xls{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
                            </ul>
                        </div>

                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-white btn-sm dropdown-toggle"> Export This Page <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="/admin/export/product_orders/{{ $type_list[$type]['name'] }}?action=export_by_page&export_as=csv{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
                                <li><a href="/admin/export/product_orders/{{ $type_list[$type]['name'] }}?action=export_by_page&export_as=xlsx{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
                                <li><a href="/admin/export/product_orders/{{ $type_list[$type]['name'] }}?action=export_by_page&export_as=xls{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-stripped table-bordered">
                            <thead>
                                <tr>
                                    @if($input['status'] == 1)
                                    <th class="text-center text-nowrap">
                                        <div class="i-checks">
                                            <label>
                                                <input type="checkbox" id="check_all">
                                            </label>
                                        </div>
                                    </th>
                                    @endif

                                    <th nowrap class="text-center">#ID</th>
                                    <th nowrap class="text-center">{{trans('localize.transaction')}} ID</th>
                                    <th class="text-center">{{trans('localize.customer')}}</th>
                                    <th class="text-center">{{trans('localize.Merchants')}}</th>
                                    <th class="text-center">{{trans('localize.store')}}</th>
                                    <th width="30%" class="text-center">{{trans('localize.product')}}</th>
                                    <th nowrap class="text-center">{{trans('localize.amount')}}</th>
                                    <th nowrap class="text-center">{{trans('localize.order')}} Mei Point {{trans('localize.total')}}</th>
                                    <th nowrap class="text-center">{{trans('localize.charges')}}</th>
                                    <th nowrap class="text-center">{{trans('localize.Merchants')}} {{trans('localize.Earning')}}</th>
                                    <th nowrap class="text-center">{{trans('localize.date')}}</th>
                                    <th class="text-center">{{trans('localize.Action')}}</th>
                                </tr>
                            </thead>
                            @if ($orders->total())
                                <tbody>
                                    @foreach ($orders as $order)
                                    <tr class="text-center">

                                        @if($input['status'] == 1)
                                        <td>
                                            <div class="i-checks">
                                                <label>
                                                    <input type="checkbox" class="input_checkbox" name="order_id[]" value="{{ $order->order_id }}">
                                                </label>
                                            </div>
                                        </td>
                                        @endif

                                        <td>{{ $order->order_id }}</td>
                                        <td>{{ $order->transaction_id }}</td>
                                        <td><a href="/admin/customer/view/{{ $order->cus_id }}" class="nolinkcolor" data-toggle="tooltip" title="View customer details">{{$order->cus_id}} - {{ $order->cus_name }}</a></td>
                                        <td><a href="/admin/merchant/view/{{ $order->mer_id }}" class="nolinkcolor" data-toggle="tooltip" title="View merchant details">{{$order->mer_id}} - {{ $order->mer_fname }}</a></td>
                                        <td><a href="/admin/store/edit/{{ $order->mer_id }}/{{ $order->stor_id }}" class="nolinkcolor" data-toggle="tooltip" title="View store details">{{$order->stor_id}} - {{ $order->stor_name }}</a></td>
                                        <td>
                                                @if(!empty($order->pro_id))
                                                <a href="/admin/product/view/{{ $order->pro_mr_id }}/{{ $order->pro_id }}" class="nolinkcolor" data-toggle="tooltip" title="View product details">{{ $order->pro_id }} - {{ $order->pro_title_en }}</a>
                                                @if(!empty($order->order_attributes))
                                                    <br><br>
                                                    @foreach(json_decode($order->order_attributes, true) as $attribute => $item)
                                                    <span><b> {{ $attribute }} : </b> {{ $item }}</span><br>
                                                    @endforeach
                                                @endif
                                                @else
                                                    <span class="text-danger">{{trans('localize.product_not_found')}}</span>
                                                @endif
                                        </td>
                                        <td nowrap>{{ $order->currency }} {{number_format($order->order_vtokens * $order->currency_rate , 2)}}</td>
                                        <td>{{number_format($order->order_vtokens , 4)}}</td>
                                        <td class="text-left text-nowrap">
                                            <dl class="dl-horizontal" style="margin-bottom:0;">
                                                <dt>@lang('localize.platform_charges') ({{round($order->cus_platform_charge_rate)}}%)</dt>
                                                <dd>{{number_format(($order->cus_platform_charge_value) ? $order->cus_platform_charge_value : '0.00',4)}}</dd>
                                                <dt>@lang('localize.gst') ({{round($order->cus_service_charge_rate)}}%)</dt>
                                                <dd>{{number_format($order->cus_service_charge_value , 4)}}</dd>
                                                <dt>@lang('localize.merchant_charge') ({{round($order->merchant_charge_percentage)}}%)</dt>
                                                <dd>{{number_format($order->merchant_charge_vtoken, 4)}}</dd>
                                                <dt>@lang('localize.shipping_fees')</dt>
                                                <dd>{{number_format($order->total_product_shipping_fees_credit, 4)}}</dd>
                                            </dl>
                                        </td>
                                        <td>
                                            {{ number_format(($order->order_vtokens - $order->merchant_charge_vtoken - $order->cus_service_charge_value - $order->cus_platform_charge_value), 4) }}
                                        </td>
                                        <td nowrap>
                                            {{trans('localize.transaction')}} <br>{{ \Helper::UTCtoTZ($order->order_date) }}
                                            @if($order->order_status == 5 || $order->order_status == 6)
                                            <hr style="margin:5px;">
                                            {{ ($order->order_status == 5)? trans('localize.cancelled_at') : trans('localize.refunded_at') }}<br>
                                            {{ \Helper::UTCtoTZ($order->updated_at) }}<br>
                                            @endif
                                        </td>

                                        <td class="text-nowrap">

                                            <p>
                                            @if($type == 1)
                                                @if ($order->order_status == 1)
                                                    <span class="text-warning">@lang('localize.processing')</span>
                                                @elseif ($order->order_status == 2)
                                                    <span class="text-info">@lang('localize.packaging')</span>
                                                @elseif ($order->order_status == 3 && $order->product_shipping_fees_type < 3)
                                                    <span class="text-primary">@lang('localize.shipped')</span>
                                                @elseif ($order->order_status == 3 && $order->product_shipping_fees_type == 3)
                                                    <span class="text-primary">@lang('localize.arranged')</span>
                                                @elseif ($order->order_status == 4)
                                                    <span class="text-navy">@lang('localize.completed')</span>
                                                @elseif ($order->order_status == 5)
                                                    <span class="text-danger">@lang('localize.cancelled')</span>
                                                @elseif ($order->order_status == 6)
                                                    <span class="text-primary">@lang('localize.refunded')</span>
                                                @endif
                                            @elseif(in_array($type, [3,4,5]))
                                                @if ($order->order_status == 2)
                                                    <span class="text-warning">@lang('localize.pending')</span>
                                                @elseif ($order->order_status == 4)
                                                    <span class="text-navy">@lang('localize.completed')</span>
                                                @elseif ($order->order_status == 5)
                                                    <span class="text-danger">@lang('localize.cancelled')</span>
                                                @elseif ($order->order_status == 6)
                                                    <span class="text-primary">@lang('localize.refunded')</span>
                                                @endif
                                            @endif
                                            </p>

                                            <hr style="margin:5px;">

                                            <p><button type="button" class="btn btn-info btn-xs btn-block" data-toggle="modal" data-id="{{ $order->order_id }}" data-post="data-php" data-action="details">{{trans('localize.View_Order_Details')}}</button></p>

                                            @if($type == 3)
                                            <p><button type="button" class="btn btn-success btn-xs btn-block" data-toggle="modal" data-id="{{ $order->order_id }}" data-post="data-php" data-action="view_coupon">@lang('localize.view_coupon')</button></p>
                                            @elseif($type == 4)
                                            <p><button type="button" class="btn btn-success btn-xs btn-block" data-toggle="modal" data-id="{{ $order->order_id }}" data-post="data-php" data-action="view_ticket">@lang('localize.view_ticket')</button></p>
                                            @elseif($type == 5)
                                            <p><button type="button" class="btn btn-success btn-xs btn-block" data-toggle="modal" data-id="{{ $order->order_id }}" data-post="data-php" data-action="view_ecard">@lang('localize.e-card.view')</button></p>
                                            @endif

                                            @if($type == 1)
                                                @if ($order->order_status == 1)
                                                    @if($accept_order_permission)
                                                    <p><button type="button" class="btn btn-warning btn-xs btn-block" data-toggle="modal" data-id="{{ $order->order_id }}" data-post="data-php" data-action="accept">{{trans('localize.acceptorder')}}</button></p>
                                                    @endif
                                                @elseif ($order->order_status == 2)
													@if($update_shipping_info_permission)
                                                    <p><button type="button" class="btn btn-primary btn-xs btn-block" data-toggle="modal"  data-id="{{ $order->order_id }}" data-post="data-php" data-action="shipment">
                                                    {{ ($order->product_shipping_fees_type == 3)? trans('localize.arrange_for_self_pickup') : trans('localize.Update_Shipment_Info') }} </button></p>
													@endif
                                                @elseif ($order->order_status >= 3)
                                                    <p><button type="button" class="btn btn-success btn-xs btn-block" data-toggle="modal"  data-id="{{ $order->order_id }}" data-post="data-php" data-action="shipdetails">{{trans('localize.viewshipment')}}</button></p>

                                                    @if($order->product_shipping_fees_type == 3 && $order->order_status == 3)
                                                        <p><button type="button" class="btn btn-warning btn-xs btn-block" data-toggle="modal"  data-id="{{ $order->order_id }}" data-post="data-php" data-action="itemdelivered">{{trans('localize.product_is_received')}}</button></p>
                                                    @endif
                                                @endif
                                            @endif


                                            @if($order->order_type <> 5)
                                                @if($order->order_status < 4)
													@if($cancel_order_permission)
                                                    <p><button type="button" class="btn btn-danger btn-xs btn-block" data-id="{{ $order->order_id }}" data-action="cancel">{{trans('localize.cancel_order')}}</button></p>
													@endif
												@elseif($order->order_status == 4)
													@if($order_refund_permission)
                                                    <p><button type="button" class="btn btn-default btn-xs btn-block" data-id="{{ $order->order_id }}" data-action="refund">{{trans('localize.Refund')}}</button></p>
												@endif
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach

                                    <tr>
                                        <th colspan="{{ ($input['status'] == 1)? '8' : '7' }}" class="text-right">@lang('localize.subtotal')</th>
                                        <th class="text-center">{{ number_format($orders->sum('order_vtokens'), 4) }}</th>
                                        <th class="text-center text-nowrap">
                                            <dl class="dl-horizontal" style="margin-bottom:0;">
                                                <dt>@lang('localize.platform_charges')</dt>
                                                <dd>{{number_format($orders->sum('cus_platform_charge_value'), 4)}}</dd>
                                                <dt>@lang('localize.gst')</dt>
                                                <dd>{{number_format($orders->sum('cus_service_charge_value') , 4)}}</dd>
                                                <dt>@lang('localize.merchant_charge')</dt>
                                                <dd>{{number_format($orders->sum('merchant_charge_vtoken'), 4)}}</dd>
                                                <dt>@lang('localize.shipping_fees')</dt>
                                                <dd>{{number_format($orders->sum('total_product_shipping_fees_credit'), 4)}}</dd>
                                            </dl>
                                        </th>
                                        <th class="text-center">{{ number_format($orders->sum('order_vtokens') - $orders->sum('merchant_charge_vtoken') - $orders->sum('cus_service_charge_value') - $orders->sum('cus_platform_charge_value'), 4) }}</th>
                                        <th colspan="2" rowspan="2"></th>
                                    </tr>
                                    <tr>
                                        <th colspan="{{ ($input['status'] == 1)? '8' : '7' }}" class="text-right">@lang('localize.total')</th>
                                        <th class="text-center">{{ number_format($charges->total_credit, 4) }}</th>
                                        <th class="text-center text-nowrap">
                                            <dl class="dl-horizontal" style="margin-bottom:0;">
                                                <dt>@lang('localize.platform_charges')</dt>
                                                <dd>{{number_format($charges->transaction_fees, 4)}}</dd>
                                                <dt>@lang('localize.gst')</dt>
                                                <dd>{{number_format($charges->service_fees , 4)}}</dd>
                                                <dt>@lang('localize.merchant_charge')</dt>
                                                <dd>{{number_format($charges->merchant_charge, 4)}}</dd>
                                                <dt>@lang('localize.shipping_fees')</dt>
                                                <dd>{{number_format($charges->shipping_fees, 4)}}</dd>
                                            </dl>
                                        </th>
                                        <th class="text-center">{{ number_format($charges->merchant_earned, 4) }}</th>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="20">
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
                                    <td colspan="20" class="text-center">@lang('localize.nodata')</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($input['status'] == 1)
                </form>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
<link href="/backend/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
<link href="/backend/css/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/iCheck/icheck.min.js"></script>
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script src="/backend/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="/backend/js/plugins/daterangepicker/daterangepicker.js"></script>
<script src="/backend/js/custom.js"></script>
<script>
    $(document).ready(function() {

        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $('#check_all').on('ifToggled', function(event) {
            if(this.checked == true) {
                $('.input_checkbox').iCheck('check');
            } else {
                $('.input_checkbox').iCheck('uncheck');
            }
        });

        // daterange move to custom.js

        $('#batch_update').click(function(event) {
            event.preventDefault();

            if ($("#batch_update_form :checkbox:checked").is(":checked")) {
                swal({
                    title: "{{trans('localize.sure')}}",
                    text: "{{trans('localize.confirm_to_accept_selected_orders')}}?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#5cb85c",
                    confirmButtonText: "{{trans('localize.yes')}}",
					cancelButtonText: "{{trans('localize.cancel')}}",
                    closeOnConfirm: true
                }, function(isConfirm){
                    if(isConfirm) {
                        $('#spinner').show();
                        $('#batch_update_form').submit();
                    } else {
                        return false;
                    }
                });
            } else {
                swal("{{trans('localize.error')}}", "{{ trans('localize.please_tick_checkbox') }}", "error");
                return false;
            }
        });

        $('button').on('click', function(){
            var this_id = $(this).attr('data-id');
            var this_action = $(this).attr('data-action');

            if (this_action == 'shipment') {
                update_shipment(this_id);
            } else if (this_action == 'details') {
                view_order(this_id);
            } else if (this_action == 'shipdetails') {
                view_shipment(this_id);
            } else if (this_action == 'accept') {
                accept_order(this_id);
            } else if (this_action == 'cancel') {
                swal({
                    title: "{{trans('localize.sure')}}",
                    text: "{{trans('localize.Confirm_to_cancel_this_order_This_action_cannot_be_undone')}}",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "{{trans('localize.yes_cancel_it')}}",
					cancelButtonText: "{{trans('localize.cancel')}}",
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                    }, function(isConfirm){
                        if (isConfirm) {
                            $('#spinner').show();
                            window.location.href = "/admin/transaction/refund_online_order/"+this_id+"/cancel";
                        }
                    });
            }  else if (this_action == 'refund') {
                swal({
                    title: "{{trans('localize.sure')}}",
                    text: "{{trans('localize.Confirm_to_refund_this_order_This_action_cannot_be_undone')}}",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#5cb85c",
                    confirmButtonText: "{{trans('localize.Refund')}}",
					cancelButtonText: "{{trans('localize.cancel')}}",
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                    }, function(isConfirm){
                        if (isConfirm) {
                            $('#spinner').show();
                            window.location.href = "/admin/transaction/refund_online_order/"+this_id+"/refund";
                        }
                    });
            } else if (this_action == 'view_coupon') {
                get_code_number_listing(this_id, 'admin', 'coupons');
            } else if (this_action == 'view_ticket') {
                get_code_number_listing(this_id, 'admin', 'tickets');
            } else if (this_action == 'view_ecard') {
                get_code_number_listing(this_id, 'admin', 'ecard');
            } else if (this_action == 'itemdelivered') {
                $.get( "/admin/transaction/online/complete/" + this_id, function( data ) {
                    if(data != 0) {
                        $('#myModal-static').modal();
                        $('#myModal-static').on('shown.bs.modal', function(){
                            $('#myModal-static .load_modal').html(data);
                        });
                        $('#myModal-static').on('hidden.bs.modal', function(){
                            $('#myModal-static .modal-body').data('');
                        });
                    } else {
                        swal("@lang('localize.error')", "@lang('localize.invalid_operation')", "error");
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

        // $('.footable').footable();
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
