@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
<div class="ListingTopbar">
    <h4 class="ListingCategory">@lang('localize.mybuys')</h4>
    <a href="/profile" class="back"><i class="fa fa-angle-left"></i></a>
</div>

<div class="ContentWrapper">
    @if(!empty($orders))
        @foreach($orders as $or)
        <?php
            if ($or['order']->order_status == 1) {
                $orderstatus = "<span class='label label-warning'>" . trans('localize.processing_order') . "</span>";
            } else if ($or['order']->order_status == 2) {
                if(in_array($or['order']->order_type, [3,4,5]))
                    $orderstatus = "<span class='label label-primary'>" . trans('localize.pending') . "</span>";
                else
                    $orderstatus = "<span class='label label-primary'>" . trans('localize.packaging_order') . "</span>";
            } else if ($or['order']->order_status == 3) {
                $orderstatus = "<span class='label label-info'>" . trans('localize.shipped_order') . "</span>";
            } else if ($or['order']->order_status == 4) {
                $orderstatus = "<span class='label label-success'>" . trans('localize.completed') . "</span>";
            } else if ($or['order']->order_status == 5) {
                $orderstatus = "<span class='label label-danger'>" . trans('localize.canceled') . "</span>";
            } else if ($or['order']->order_status == 6) {
                $orderstatus = "<span class='label label-primary'>" . trans('localize.refunded') . "</span>";
            }
        ?>
        <h5 class="table-head">@lang('localize.id') : {{  $or['order']->order_id }}</h5>
        <table class="single-transaction">
            <tbody>
                <tr class="header">
                    <td colspan="2" class="status">
                        <div class="pull-left">@lang('localize.transID') : {{  $or['order']->transaction_id }}</div>
                        @if ($or['order']->order_status == 3)
                        @if($or['order']->order_courier_id > 0)
                        @lang('localize.trackingno') : <a href="{{ $or['order']->link }}" target="_blank"><span class="label label-default">{{ $or['order']->order_tracking_no }}</span></a>
                        @else
                        @lang('localize.remarks') : <span class="label label-default">{{ $or['order']->order_tracking_no }}</span>
                        @endif
                        <br/>
                        {{-- <a href="{{ $or['order']->link.$or['order']->order_tracking_no }}" target="_blank" class="btn">{{ $or['order']->name }}</a> --}}
                        <br/>
                        <button id="order_{{ $or['order']->order_id }}" onclick="accept({{ $or['order']->order_id }})" class="btn btn-success btn-xs">{{ $or['order']->product_shipping_fees_type == 3? trans('localize.product_is_received') : trans('localize.shipment_is_received') }} </button>
                        @else
                        {!! $orderstatus !!}
                        @endif
                    </td>
                </tr>
                <tr class="body">
                    <td>
                        <img class="product-img lazyload" data-src="{{ env('IMAGE_DIR') . '/product/' .$or['product']->pro_mr_id.'/'. $or['product']->image }}" src="{{ asset('assets/images/stock.png') }}">
                        <h4>
                            @if (isset($or['product']) && isset($or['product']->title))
                                <span>{{ $or['product']->title }}</span>
                            @endif
                            @if(!empty($or['order']->order_attributes))
                            <div class="variant">
                                <small>
                                <br>
                                {!!$or['attribute'] !!}
                                </small>
                            </div>
                            @endif

                            @if($or['order']->order_type == 3)
                            <br><button onclick="get_code_number_listing({{ $or['order']->order_id }})" class="btn btn-primary btn-sm" style="padding: 4px 7px;margin-top: 5px;">@lang('localize.view_coupon')</button>
                            @endif

                            @if($or['order']->order_type == 4)
                            <br><button onclick="get_code_number_listing({{ $or['order']->order_id }}, 'tickets')" class="btn btn-info btn-sm" style="padding: 4px 7px;margin-top: 5px;">@lang('localize.view_ticket')</button>
                            @endif

                            @if($or['order']->order_type == 5)
                            <br><button onclick="get_code_number_listing({{ $or['order']->order_id }}, 'ecard')" class="btn btn-primary btn-sm" style="padding: 4px 7px;margin-top: 5px;">@lang('localize.e-card.serial_number')</button>
                            @endif
                        </h4>
                        @if(!empty($or['order']->remarks))
                        <p>@lang('localize.remarks') : {{ $or['order']->remarks }}</p>
                        @endif

                        @if($or['order']->product_shipping_fees_type == 3)
                            @if($or['order']->order_status == 4)
                            <p>@lang('localize.remarks') : {{ $or['order']->order_tracking_no }}</p>
                            @elseif($or['order']->order_status < 4)
                            <p><span><i class="fa fa-exclamation"></i> @lang('localize.member_self_pickup_disclaimer')</span></p>
                            @endif
                        @endif
                    </td>
                    <td class="credit">
                        {{ $or['order']->order_vtokens }}<span> @lang('common.credit_name')</span>
                    </td>
                </tr>
                <tr class="footer">
                    <td colspan="2">
                        {{ \Helper::UTCtoTZ($or['order']->created_at) }}
                    </td>
                </tr>
            </tbody>
        </table>
        @endforeach

        <div class="PaginationContainer">
            {{ $orders->links() }}
        </div>
    @else
        <br/>
        <center>@lang('localize.table_no_record')</center>
    @endif
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content load_modal">
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endsection

@section('scripts')
<script src="{{ asset('assets/js/footable/footable.js') }}" type="text/javascript"></script>
<script type="text/javascript">

    function accept(id) {
        $("#order_"+id).remove();
        window.location.href = "/profile/accept_orders/"+id+"/4";
    }

    function get_code_number_listing(this_id, type = 'coupons') {

        var url = '/get_code_number_listing/' + this_id + '/member/' + type;

        $.get( url, function( data ) {
            if(data == 0) {
                swal("Error!", "Invalid Operation!", "error");
            } else {
                $('#myModal').modal();
                $('#myModal').on('shown.bs.modal', function(){
                    $('#myModal .load_modal').html(data);
                });
            }

            $('#myModal').on('hidden.bs.modal', function(){
                $('#myModal .modal-body').data('');
            });
        });
    }

    function paramPage($item, $val) {
        var href = window.location.href.substring(0, window.location.href.indexOf('?'));
        var qs = window.location.href.substring(window.location.href.indexOf('?') + 1, window.location.href.length);
        var newParam = $item + '=' + $val;

        if (qs.indexOf($item + '=') == -1) {
            if (qs == '') {
                qs = '?'
            }
            else {
                qs = qs + '&'
            }
            qs = newParam;
        }
        else {
            var start = qs.indexOf($item + "=");
            var end = qs.indexOf("&", start);
            if (end == -1) {
                end = qs.length;
            }
            var curParam = qs.substring(start, end);
            qs = qs.replace(curParam, newParam);
        }
        //alert(qs);
        window.location.replace(href + '?' + qs);
    }

    function paramSearchOrder($search) {
        var val = document.getElementById('searchorder').value;
        var item = document.getElementById('items').value;
        var href = window.location.href.substring(0, window.location.href.indexOf('?'));
        var qs = window.location.href.substring(window.location.href.indexOf('?') + 1, window.location.href.length);
        var newParam = $search + '=' + val;

        window.location.replace(href +'?items='+ item +'&' + newParam);
    }
</script>
<script>
    jQuery(function($){
        $('.responsive-table').footable({
            empty: "@lang('localize.table_no_record')"
        });
    });
</script>
@endsection
