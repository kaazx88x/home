<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="row">
        <div class="col-sm-3"><img src="{{ url('/assets/images/meihome_logo.png') }}" alt="" style="width:auto; height:35px;"></div>
        <div class="col-sm-7 text-center" style="vertical-align: middle;">
        <h3>
            @if($order->order_type == 3)
                @lang('localize.coupon_code')
            @elseif($order->order_type == 4)
                @lang('localize.ticket_number')
            @elseif($order->order_type == 5)
                @lang('localize.e-card.serial_number')
            @endif
        </h3>
        </div>
    </div>
</div>

<div class="modal-body">
    <div class="row" id="table-display">
        <div class="table-responsive col-sm-12">
            <table class="table table-stripped table-hover">
                <thead>
                    <tr>
                        <th class="text-center" colspan="{{ $order->order_type == 4? '2' : '' }}">
                        @if($order->order_type == 3)
                            @lang('localize.coupon_code')
                        @elseif($order->order_type == 4)
                            @lang('localize.ticket_number')
                        @elseif($order->order_type == 5)
                            @lang('localize.e-card.name')
                        @endif
                        </th>

                        <th class="text-center">
                        @if($order->order_type == 5)
                            @lang('localize.e-card.validity')
                        @else
                            @lang('localize.date')
                        @endif
                        </th>

                        <th class="text-center">@lang('localize.status')</th>
                        @if($order->order_type <> 5 && $by == 'admin')
                        <th class="text-center">@lang('localize.action')</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($listing as $code)
                        @php
                        $expired = false;
                        if($code->status == 1 && !empty($code->valid_to) && \Carbon\Carbon::now('UTC') >= \Helper::UTCtoTZ($code->valid_to))
                            $expired = true;
                        @endphp
                        <tr class="text-center">
                            @if($order->order_type == 4)
                            <td>
                            <button type="button" class="btn btn-default btn-outline btn-sm btn-block" data-url="{{ url('') }}?ticket_number={{ $code->serial_number }}" data-action="show-qrcode"><i class="fa fa-qrcode fa-2x"></i></button>
                            </td>
                            @endif
                            <td>{{ $code->serial_number }}</td>
                            <td style="width:70%;">
                                @if($order->order_type == 5 && $code->valid_to)
                                {{ \Helper::UTCtoTZ($code->valid_to) }}
                                @else
                                    @if($code->status == 2)
                                        {{ \Helper::UTCtoTZ($code->redeemed_at) }}
                                    @elseif($code->status == 3)
                                        {{ \Helper::UTCtoTZ($code->updated_at) }}
                                    @elseif($code->status == 1 && $expired)
                                        {{ \Helper::UTCtoTZ($code->valid_to) }}
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($code->status == 1)
                                    @if($expired && $order->order_type <> 5)
                                        <span class="text-danger">@lang('localize.expired')</span>
                                    @else
                                        <span class="text-info">@lang('localize.open')</span>
                                    @endif
                                @elseif($code->status == 2)
                                    <span class="text-navy">@lang('localize.redeemed')</span>
                                @elseif($code->status == 3)
                                    <span class="text-warning">@lang('localize.cancelled')</span>
                                @endif
                            </td>

                            @if($order->order_type <> 5 && $by == 'admin')
                            <td>
                                @if($code->status == 1 && !$expired)
                                    @if($order->order_type == 3)
                                    <p><button type="button" class="btn btn-warning btn-xs btn-block" data-url="{{ url('admin/transaction/online/cancel/coupon', [$code->order_id, $code->serial_number]) }}"data-action="cancel_coupon">{{trans('localize.cancel_coupon')}}</button></p>
                                    @endif

                                    @if($order->order_type == 4)
                                    <p><button type="button" class="btn btn-warning btn-xs btn-block" data-url="{{ url('admin/transaction/online/cancel/ticket', [$code->order_id, $code->serial_number]) }}" data-action="cancel_ticket">{{trans('localize.cancel_ticket')}}</button></p>
                                    @endif
                                @endif
                            </td>
                            @endif

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row qrcode-display text-center" style="display:none;">
        <div class="col-sm-12" id="qr_output" style="padding-left:10px;"></div>
    </div>
</div>
<div class="modal-footer">
    <div class="row">
        <div class="col-sm-4 pull-right">
            <button data-dismiss="modal" class="btn btn-default" type="button">@lang('localize.close_')</button>
        </div>

        <div class="col-sm-4 text-left qrcode-display" style="display:none;">
            <button class="btn btn-info btn-back" type="button">@lang('localize.back')</button>
        </div>

        {{--  <div class="col-sm-4 text-center qrcode-display" style="display:none;">
            <button class="btn btn-default btn-outline download-qrcode" type="button"><i class="fa fa-download"></i> @lang('localize.download_as_image')</button>
        </div>  --}}
    </div>

</div>

<img id='my-image' src="{{ url('assets/images/meihome_logo.png') }}" style="display:none;"/>

<script src="/backend/js/plugins/JQuery-qrcode/qrcode.js" type="text/javascript"></script>
<script src="/backend/js/html2canvas.js" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('button').on('click', function(){
        var url = $(this).attr('data-url');
        var this_action = $(this).attr('data-action');

        if (this_action == 'cancel_coupon') {
            swal({
                title: "{{trans('localize.sure')}}",
                text: "Confirm to cancel this coupon?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#5cb85c",
                confirmButtonText: "{{trans('localize.yes')}}",
                cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
                }, function(isConfirm){
                    if (isConfirm) {
                        $('#spinner').show();
                        window.location.href = url;
                    }
            });
        } else if(this_action == 'cancel_ticket') {
            swal({
                title: "{{trans('localize.sure')}}",
                text: "Confirm to cancel this ticket?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#5cb85c",
                confirmButtonText: "{{trans('localize.yes')}}",
                cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
                }, function(isConfirm){
                    if (isConfirm) {
                        $('#spinner').show();
                        window.location.href = url;
                    }
            });
        } else if(this_action == 'show-qrcode') {
            var options = {
                // render method: 'canvas', 'image' or 'div'
                render: 'image',

                // version range somewhere in 1 .. 40
                minVersion: 6,
                maxVersion: 15,

                // error correction level: 'L', 'M', 'Q' or 'H'
                ecLevel: 'L',

                // offset in pixel if drawn onto existing canvas
                left: 0,
                top: 0,

                // size in pixel
                size: 400,

                // code color or image element
                fill: '#000',

                // background color or image element, null for transparent background
                background: '#FFF',

                // content
                text: url,

                // corner radius relative to module width: 0.0 .. 0.5
                radius: 0.4,

                // quiet zone in modules
                quiet: 0,

                // modes
                // 0: normal
                // 1: label strip
                // 2: label box
                // 3: image strip
                // 4: image box
                mode: 4,

                mSize: 0.10,
                mPosX: 0.5,
                mPosY: 0.5,

                label: 'no label',
                fontname: 'sans',
                fontcolor: '#000',

                image: $('#my-image')[0]
            };

            $('#qr_output').empty().qrcode(options);
            $(".qrcode-display").show();
            $("#table-display").hide();
        }
    });

    $('.btn-back').on('click', function() {
        $(".qrcode-display").hide();
        $("#table-display").show();
    });

    $('.download-qrcode').on('click', function () {
        var element = $("#my-image");
        html2canvas(element, {
            onrendered: function (canvas) {
                $("#previewImage").append(canvas);

                var a = $("<a>").attr("href", canvas.toDataURL("image/jpeg").replace("image/jpeg", "image/octet-stream"))
                .attr("download", "qr_code.png")
                .appendTo("body");
                a[0].click();
                a.remove();
            }
        });
    });
});
</script>
