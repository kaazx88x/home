@extends('merchant.layouts.master')

@section('title', 'Online Orders')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-10">
        <h2>{{ $type_list[$type]['lang'] }}</h2>
        <ol class="breadcrumb">
            <li>
                @lang('localize.transaction')
            </li>
            <li>
                {{ $type_list[$type]['lang'] }}
            </li>
            <li class="active">
                <strong>{{ $status_list[$status] }}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="ibox float-e-margins border-bottom">
        <a class="collapse-link nolinkcolor">
            <div class="ibox-title ibox-title-filter">
                <h5>@lang('localize.search')</h5>
                <div class="ibox-tools">
                    <i class="fa fa-chevron-down"></i>
                </div>
            </div>
        </a>
        <div class="ibox-content ibox-content-filter" style="display:none;">
            <div class="row">
                <form class="form-horizontal" id="filter" action="{{ url( $route . '/transaction/product', [$type_list[$type]['name']]) }}" method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('localize.transaction_id')</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['id']}}" placeholder="@lang('localize.transaction_id')" class="form-control" id="id" name="id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('localize.productName')</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['name']}}" placeholder="@lang('localize.productName')" class="form-control" id="name" name="name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('localize.orderDate')</label>
                        <div class="col-sm-9">
                            <div class="input-daterange input-group">
                                <input type="text" class="form-control" name="start" id="sdate" placeholder="Start Date" value="{{$input['start']}}"/>
                                <span class="input-group-addon">@lang('localize.to')</span>
                                <input type="text" class="form-control" name="end" id="edate" placeholder="End Date" value="{{$input['end']}}"/>
                            </div>
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
                        <div class="col-sm-9 col-sm-offset-2">
                            <button type="submit" class="btn btn-block btn-outline btn-primary" id="filter">@lang('localize.search')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">

            @include('merchant.common.notifications')

            @if($input['status'] == 1)
            <form id="batch_update_form" action="{{ url( $route.'/transaction/product/batch/update', [$mer_id ,'accept_order']) }}" method="POST">
            {{ csrf_field() }}
            @endif

            <div class="ibox">
                 <div class="ibox-title" style="display: block;">
                    <div class="ibox-tools" style="margin-bottom:10px;">

                        @if($input['status'] == 1)
                        <div class="pull-left" style="margin-left:10px;">
                            <button type="button" class="btn btn-primary btn-sm" id="batch_update">
                                @lang('localize.accept_multiple_orders')
                            </button>
                        </div>
                        @endif

                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle"> Export All <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="/merchant/export/product_orders/{{ $type_list[$type]['name'] }}?action=export&export_as=csv{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
                                <li><a href="/merchant/export/product_orders/{{ $type_list[$type]['name'] }}?action=export&export_as=xlsx{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
                                <li><a href="/merchant/export/product_orders/{{ $type_list[$type]['name'] }}?action=export&export_as=xls{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
                            </ul>
                        </div>

                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-white btn-sm dropdown-toggle"> Export This Page <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="/merchant/export/product_orders/{{ $type_list[$type]['name'] }}?action=export_by_page&export_as=csv{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
                                <li><a href="/merchant/export/product_orders/{{ $type_list[$type]['name'] }}?action=export_by_page&export_as=xlsx{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
                                <li><a href="/merchant/export/product_orders/{{ $type_list[$type]['name'] }}?action=export_by_page&export_as=xls{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
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

                                    <th class="text-center text-nowrap">@lang('localize.#id')</th>
                                    <th class="text-center text-nowrap">@lang('localize.transaction_id')</th>
                                    <th width="30%" class="text-center text-nowrap">@lang('localize.product')</th>
                                    <th class="text-center text-nowrap">@lang('localize.customer')</th>
                                    <th class="text-nowrap">@lang('localize.quantity')</th>
                                    <th class="text-center">@lang('localize.amount')</th>
                                    <th nowrap class="text-center">@lang('localize.order_credit_total')</th>
                                    <th class="text-center">@lang('localize.merchant_charge')</th>
                                    <th class="text-center">@lang('localize.shipping_fees')</th>
                                    <th nowrap class="text-center">@lang('localize.merchant_earned_credit')</th>
                                    <th class="text-center">@lang('localize.date')</th>
                                    <th class="text-center">@lang('localize.action')</th>
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
                                        <td>
                                            <a href="{{ url('merchant/product/edit', [$order->pro_id]) }}" style="text-decoration:none; color: inherit;">{{ $order->pro_id.' - '.$order->pro_title_en }}</a>
                                            @if(!empty($order->order_attributes))
                                                <br><br>
                                                @foreach(json_decode($order->order_attributes, true) as $attribute => $item)
                                                <span><b> {{ $attribute }} : </b> {{ $item }}</span><br>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>{{ $order->cus_name }}</td>
                                        <td>{{ $order->order_qty }}</td>
                                        <td nowrap>{{ $order->currency }} {{ number_format(($order->order_vtokens - $order->cus_service_charge_value - $order->cus_platform_charge_value) * $order->currency_rate, 2) }}</td>
                                        <td>{{ number_format(($order->order_vtokens - $order->cus_service_charge_value - $order->cus_platform_charge_value), 4) }}</td>
                                        <td nowrap>
                                            <b>{{round($order->merchant_charge_percentage)}}% : </b> {{ number_format($order->merchant_charge_vtoken, 4) }}
                                        </td>
                                        <td nowrap>{{ number_format($order->total_product_shipping_fees_credit, 4) }}</td>
                                        <td>{{ number_format(($order->order_vtokens - $order->merchant_charge_vtoken - $order->cus_service_charge_value - $order->cus_platform_charge_value), 4) }}</td>
                                        {{-- <td>{{ Carbon\Carbon::createFromTimestamp(strtotime($order->order_date))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A') }}</td> --}}
                                        <td nowrap>
                                            {{trans('localize.transaction')}} <br>{{ \Helper::UTCtoTZ($order->order_date) }}
                                            @if($order->order_status == 5 || $order->order_status == 6)
                                            <hr style="margin:5px;">
                                            {{ ($order->order_status == 5)? trans('localize.cancelled_at') : trans('localize.refunded_at') }}<br>
                                            {{ \Helper::UTCtoTZ($order->updated_at) }}<br>
                                            @endif
                                        </td>
                                        {{--  @if($type == 3)
                                        <td>
                                            <p>{{ $order->serial_number }}</p>
                                            <hr style="margin:5px;">
                                            @if($order->code_status == 1)
                                                {{trans('localize.redemption')}} : <span class="text-info">@lang('localize.open')</span>
                                            @elseif($order->code_status == 2)
                                                <span class="text-nowrap">{{trans('localize.redemption')}} : <span class="text-navy">@lang('localize.redeemed')</span></span><br>
                                                {{ \Helper::UTCtoTZ($order->redeemed_at) }}
                                            @elseif($order->code_status == 3)
                                                @if($order->order_status != 5)
                                                <span class="text-warning">{{trans('localize.coupon_cancelled')}}</span><br>
                                                {{ \Helper::UTCtoTZ($order->cancelled_at) }}
                                                @else
                                                <span class="text-danger">{{trans('localize.order_cancelled')}}</span>
                                                @endif
                                            @endif
                                        </td>
                                        @endif  --}}
                                        <td class="text-center text-nowrap" width="10%">
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

                                            <p><button type="button" class="btn btn-white btn-block btn-sm" data-toggle="modal" data-id="{{ $order->order_id }}" data-post="data-php" data-action="details"><i class="fa fa-file-text-o"></i> @lang('localize.vieworder')</button></p>

                                            @if($type == 3)
                                            <p><button type="button" class="btn btn-success btn-sm btn-block" data-toggle="modal"  data-id="{{ $order->order_id }}" data-post="data-php" data-action="view_coupon">@lang('localize.view_coupon')</button></p>
                                            @elseif($type == 4)
                                            <p><button type="button" class="btn btn-success btn-sm btn-block" data-toggle="modal"  data-id="{{ $order->order_id }}" data-post="data-php" data-action="view_ticket">@lang('localize.view_ticket')</button></p>
                                            @elseif($type == 5)
                                            <p><button type="button" class="btn btn-success btn-xs btn-block" data-toggle="modal" data-id="{{ $order->order_id }}" data-post="data-php" data-action="view_ecard">@lang('localize.e-card.view')</button></p>
                                            @endif

                                            <p style="width:100%">
                                                @if($type == 1)
                                                    @if ($order->order_status == 1)
                                                        <button type="button" class="btn btn-primary btn-block btn-sm" data-toggle="modal" data-id="{{ $order->order_id  }}" data-post="data-php" data-action="accept"><i class="fa fa-check-square-o"></i> @lang('localize.acceptorder')</button>
                                                    @elseif ($order->order_status == 2)
                                                        <button type="button" class="btn btn-info btn-block btn-sm" data-toggle="modal" data-id="{{ $order->order_id  }}" data-post="data-php" data-action="shipment"><i class="fa fa-pencil-square-o"></i> {{ ($order->product_shipping_fees_type == 3)? trans('localize.arrange_for_self_pickup') : trans('localize.Update_Shipment_Info') }}</button>
                                                    @elseif ($order->order_status >= 3)
                                                        <button type="button" class="btn btn-success btn-block btn-sm" data-toggle="modal" data-id="{{ $order->order_id  }}" data-post="data-php" data-action="shipdetails"><i class="fa fa-truck"></i> @lang('localize.viewshipment')</button>

                                                        @if($order->product_shipping_fees_type == 3 && $order->order_status == 3)
                                                            <p><button type="button" class="btn btn-warning btn-sm btn-block" data-toggle="modal"  data-id="{{ $order->order_id }}" data-post="data-php" data-action="itemdelivered"><i class="fa fa-check"></i> {{trans('localize.product_is_received') }}</button></p>
                                                        @endif
                                                    @endif
                                                @endif
                                            </p>

                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <th class="text-right" colspan="{{ ($input['status'] == 1)? '7' : '6' }}">@lang('localize.subtotal')</th>
                                        <th class="text-center">{{ number_format($orders->sum('order_vtokens') - $orders->sum('cus_service_charge_value') - $orders->sum('cus_platform_charge_value'), 4) }}</th>
                                        <th class="text-center">{{ number_format($orders->sum('merchant_charge_vtoken'), 4) }}</th>
                                        <th class="text-center">{{ number_format($orders->sum('total_product_shipping_fees_credit'), 4) }}</th>
                                        <th class="text-center">{{ number_format($orders->sum('order_vtokens') - $orders->sum('merchant_charge_vtoken') - $orders->sum('cus_service_charge_value') - $orders->sum('cus_platform_charge_value'), 4) }}</th>
                                        <th colspan="4" rowspan="2"></th>
                                    </tr>
                                    <tr>
                                        <th class="text-right" colspan="{{ ($input['status'] == 1)? '7' : '6' }}">@lang('localize.total')</th>
                                        <th class="text-center">{{ number_format($charges->total_credit, 4) }}</th>
                                        <th class="text-center">{{ number_format($charges->merchant_charge, 4) }}</th>
                                        <th class="text-center">{{ number_format($charges->shipping_fees, 4) }}</th>
                                        <th class="text-center">{{ number_format($charges->merchant_earned, 4) }}</th>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="20">
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
<link href="/backend/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
<link href="/backend/css/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet">
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
            } else if (this_action == 'view_coupon') {
                get_code_number_listing(this_id, 'merchant', 'coupons');
            } else if (this_action == 'view_ticket') {
                get_code_number_listing(this_id, 'merchant', 'tickets');
            } else if (this_action == 'view_ecard') {
                get_code_number_listing(this_id, 'merchant', 'ecard');
            } else if (this_action == 'itemdelivered') {
                swal({
                    title: "{{ trans('localize.sure') }}",
                    text: "{{ trans('localize.this_order_will_mark_as_completed') }}",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#5cb85c",
                    confirmButtonText: "{{trans('localize.swal_ok')}}",
					cancelButtonText: "{{trans('localize.cancel')}}",
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                    }, function(isConfirm){
                        if (isConfirm) {
                            $('#spinner').show();
                            window.location.href = "/merchant/transaction/online/complete/"+this_id;
                        }
                    }
                );
            }
        });

        $('[data-toggle="tooltip"]').tooltip();

    });
</script>
@endsection
