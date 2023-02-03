<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4>@lang('localize.shipmentdetail')</h4>
</div>
<div class="modal-body">
    <form class="form-horizontal" id="submit_form" action="{{ url('update_shipment', [$order_id]) }}" method="post">
        {{csrf_field()}}
        {{--  <input type="hidden" name="id" value="{{ $order_id }}">  --}}
        {{--  <input type="hidden" name="status" value="3">  --}}

        <div class="form-group">
            @if($order->product_shipping_fees_type < 3)
            <div class="col-lg-6 i-checks text-left">
                <label><input type="radio" value="courier" name="shipment" class="shipment" checked> <i></i> @lang('localize.courier') </label>
            </div>
            @else
            <div class="col-lg-6 i-checks text-left">
                <label><input type="radio" value="cod" name="shipment" class="shipment" checked> <i></i> @lang('localize.self_pickup') </label>
            </div>
            @endif
        </div>

        <div class="form-group" id="courier_list">
            <label>@lang('localize.corier')</label>
            <select class="form-control" id="courier" name="courier">
                <option value="" selected>-- @lang('localize.selectcorier') --</option>
                @foreach ($couriers as $courier)
                <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="trackno"> @lang('localize.trackingno').</label>
            <textarea type="text" class="form-control" id="trackno" name="trackno" placeholder="@lang('localize.Enter_Tracking_No')" row="5" style="resize: none;" rows="5"></textarea>
        </div>

        <div class="form-group">
            <button type="button" id="submit_button" class="btn btn-default btn-success btn-block">@lang('localize.submit')</button>
        </div>

    </form>
</div>

<script>
    $(document).ready(function() {

        var shipment_type = parseInt("{{ $order->product_shipping_fees_type }}");
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        if(shipment_type == 3) {
            $('#courier_list').hide();
            $('.trackno').text("@lang('localize.remarks')");
            $('#trackno').attr("placeholder", "@lang('localize.Please_fill_remarks_field')").val('Self pickup');
        } else {
            $('#courier_list').show();
            $('.trackno').text("@lang('localize.trackingno').");
            $('#trackno').attr("placeholder", "@lang('localize.Enter_Tracking_No')");
        }

        {{--  $('.shipment').on('ifChecked', function(event) {
            if(this.value == 'cod') {
                $('#courier_list').hide();
                $('.trackno').text("@lang('localize.remarks')");
                $('#trackno').attr("placeholder", "@lang('localize.Please_fill_remarks_field')");
            } else {
                $('#courier_list').show();
                $('.trackno').text("@lang('localize.trackingno').");
                $('#trackno').attr("placeholder", "@lang('localize.Enter_Tracking_No')");
            }
        });  --}}

        $('#submit_button').click(function() {

            var shipment = $("input[name='shipment']:checked").val();
            var message = '@lang("localize.Please_fill_remarks_field")';
            if(shipment == 'courier') {
                if($('#courier').val() == '') {
                    swal("{{trans('localize.error')}}!",'{{trans("localize.Please_select_courier")}}','error');
                    return false;
                }

                message = "@lang('localize.Enter_Tracking_No')";
            }

            if($('#trackno').val() == '') {
                swal("@lang('localize.error')",message,'error');
                return false;
            }

            $('#spinner').show();
            $("#submit_form").submit();
        });
    });
</script>
