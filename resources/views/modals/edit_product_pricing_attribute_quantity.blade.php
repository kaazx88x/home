<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="row">
        <div class="col-sm-3"><img src="{{ url('/assets/images/meihome_logo.png') }}" alt="" style="width:auto; height:35px;"></div>
        <div class="col-sm-7 text-center" style="vertical-align: middle;"><h3>@lang('localize.edit_item')</h3></div>
    </div>
</div>
<div class="modal-body">
    <form id="edit_attribute" class="form-horizontal" action="{{ url('pricing_attribute_quantity/edit', [$mer_id, $pro_id, $pricing_id]) }}" method="POST">
    {{ csrf_field() }}
        <div class="form-group">
            <label class="col-lg-3 control-label">@lang('localize.quantity') <span style="color:red;">*</span></label>
            <div class="col-lg-9">
                <input type="number" placeholder="@lang('localize.quantity')" class="form-control number" id="modal_quantity" name='quantity' value="{{$quantity}}">
            </div>
        </div>
        <div class="form-group">
            {{--  <label class="col-lg-3 control-label">@lang('localize.operation')</label>  --}}
            <div class="col-lg-9 col-lg-offset-3">
                <div class="i-checks col-lg-4">
                    <label>
                        <input type="radio" name="operation" class="input_radio" value="override" checked> @lang('localize.override')
                    </label>
                </div>
                <div class="i-checks col-lg-4">
                    <label =>
                        <input type="radio" name="operation" class="input_radio" value="add"> @lang('localize.add')
                    </label>
                </div>
                <div class="i-checks col-lg-4">
                    <label>
                        <input type="radio" name="operation" class="input_radio" value="deduct"> @lang('localize.deduct')
                    </label>
                </div>
            </div>
        </div>
        @if(!$lists->isEmpty())
        @foreach ($lists as $attribute => $item)
            <div class="form-group">
                <label class="col-lg-3 control-label">{{ $attribute }}<span style="color:red;"> *</span></label>
                <div class="col-lg-9">
                    <select class="form-control" name="attribute[]">
                        @foreach ($item as $attribute_item)
                            <option value="{{ $attribute_item->id }}" {{ ($attribute_item->selected == 1)? 'selected' : '' }}>{{ $attribute_item->attribute_item }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endforeach
        @endif
    </form>
</div>
<div class="modal-footer">
    <button data-dismiss="modal" class="btn btn-default pull-left" type="button">{{trans('localize.close_')}}</button>
    <button type="button" class="btn btn-outline btn-primary pull-right" id="submit_edit">@lang('localize.update')</button>
</div>

<script>
    $(document).ready(function() {
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        {{--  $('.input_radio').on('ifChecked', function(event) {
            var quantity = parseInt($('#modal_quantity').val());
            if(this.value != 'override' && quantity <= 0) {
                swal("@lang('localize.error')", "@lang('localize.min_quantity_add_deduct', ['quantity' => '1'])", "warning");
            } else if(this.value == 'deduct' && quantity > parseInt("{{ $quantity }}")) {
                swal("@lang('localize.error')", "@lang('localize.max_quantity_deduct', ['quantity' => $quantity])", "warning");
            }
        });  --}}

        $(".number").on({
            keydown: function(e) {
                -1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()
            },
            change: function(e) {
                var operation = $('input[name=operation]:checked').val();
                if(operation != 'override' && this.value <= 0) {
                    swal("@lang('localize.error')", "@lang('localize.min_quantity_add_deduct', ['quantity' => '1'])", "warning");
                } else if(operation == 'deduct' && this.value > parseInt("{{ $quantity }}")) {
                    swal("@lang('localize.error')", "@lang('localize.max_quantity_deduct', ['quantity' => $quantity])", "warning");
                }
            }
        });

        $('#submit_edit').click(function() {

            var quantity = $('#modal_quantity').val();
            var operation = $('input[name=operation]:checked').val();

            if(quantity == '') {
                $('#modal_quantity').attr('placeholder', '{{trans('localize.fieldrequired')}}');
                $('#modal_quantity').css('border', '1px solid red');
                return false;
            } else if (operation != 'override' && parseInt(quantity) <= 0) {
                swal("@lang('localize.error')", "@lang('localize.min_quantity_add_deduct', ['quantity' => '1'])", "warning");
                return false;
            } else if (operation == 'deduct' && parseInt(quantity) > parseInt("{{ $quantity }}")) {
                swal("@lang('localize.error')", "@lang('localize.max_quantity_deduct', ['quantity' => $quantity])", "warning");
                return false;
            } else {
                $('#modal_quantity').css('border', '');
            }

            swal({
                title: "{{trans('localize.sure')}}",
                text: "Confirm Update?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#5cb85c",
                confirmButtonText: "{{trans('localize.yes')}}",
                closeOnConfirm: true
            }, function(){
                $('#spinner').show();
                $("#edit_attribute").submit();
            });
        });
    });
</script>