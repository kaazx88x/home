<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4>{{trans('localize.edit')}} {{trans('localize.limit')}}</h4>
</div>
<div class="modal-body">
    <form class="form-horizontal" id="submit_form" action="{{ url('admin/store/limit-action/edit', [$mer_id, $store_id, $action_id]) }}" method="post">
         {{csrf_field()}}

        @if($action->type == 2)
        <div class="form-group">
            <label class="checkbox-inline i-checks">
                <input type="checkbox" name="per_user" value="1" {{ $action->per_user == 1? 'checked' : '' }}>
                @lang('localize.per_user')
            </label>
        </div>
        @endif

        <div class="form-group">
            <label>@lang('localize.amount')</label>
            <input type="number" name="amount" id="mdl_amt" class="form-control text-center number_mdl {{ ($action->type == 1)? 'compulsary_mdl' : '' }}" value="{{ $action->amount }}">
        </div>

        @if($action->type != 1)
        <div class="form-group">
            <label>Number of Transaction</label>
            <input type="number" name="number_transaction" id="mdl_nmber" class="form-control text-center number_mdl" value="{{ $action->number_transaction }}">
        </div>
        @else
        <input type="hidden" id="mdl_nmber" value="">
        @endif

        <button type="button" id="edit-submit-btn" class="btn btn-default btn-success btn-block">{{trans('localize.submit')}}</button>
    </form>
</div>

<script>
$('.i-checks').iCheck({
    checkboxClass: 'icheckbox_square-green',
    radioClass: 'iradio_square-green',
});

$("#edit-submit-btn").click( function() {
    var isValid = true;

    $(':input').each(function(e) {
        if ($(this).hasClass('compulsary_mdl')) {
            if (!$(this).val()) {
                $(this).attr('placeholder', "@lang('localize.fieldrequired')").css('border', '1px solid red').focus();
                isValid = false;
                return false;
            }
        }

        if ($(this).hasClass('number_mdl')) {
            if ($(this).val() && $(this).val() < 1) {
                $(this).css('border', '1px solid red').focus();
                swal("@lang('localize.min_value', ['value' => '1'])", '', 'error');
                isValid = false;
                return false;
            }
        }

        $(this).css('border', '');
    });

    if(isValid) {
        if(!$("#mdl_amt").val() && !$("#mdl_nmber").val()) {
            $("#mdl_amt").css('border', '1px solid red').focus();
            $("#mdl_nmber").css('border', '1px solid red').focus();
            swal("Please insert either amount or number of transaction", '', 'error');
            return false;
        }

        $("#submit_form").submit();
    }
});
</script>
