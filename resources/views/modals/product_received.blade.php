<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4>@lang('localize.product_is_received')</h4>
</div>
<div class="modal-body">
    <form class="form-horizontal" action="{{ url('admin/transaction/online/complete', [$order->order_id]) }}" method="post" id="submit">
    {{csrf_field()}}
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <label>@lang('localize.remarks')</label>
                <textarea type="text" class="form-control" name="remarks" id="remarks" placeholder="@lang('localize.Please_fill_remarks_field')" row="5" style="resize: none;" rows="5"></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <button type="submit" class="btn btn-primary btn-sm pull-right">@lang('localize.submit')</button>
    </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#submit').submit(function(e) {

            if(!$('#remarks').val()) {
                $('#remarks').css('border', '1px solid red').focus();
                swal("@lang('localize.error')", '@lang("localize.Please_fill_remarks_field")', 'error');
                e.preventDefault();
                return false;
            }
            $('#remarks').css('border', '');
        });
    })
</script>