<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="row">
        <div class="col-sm-3"><img src="{{ url('/assets/images/meihome_logo.png') }}" alt="" style="width:auto; height:35px;"></div>
        <div class="col-sm-7 text-center" style="vertical-align: middle;"><h3>@lang('localize.edit_pricing')</h3></div>
    </div>
</div>
<div class="modal-body">
    <form id="edit_pricing" class="form-horizontal" action='/product_pricing/edit' method="POST">
    {{ csrf_field() }}
        <div class="form-group">
            <label class="col-sm-3 control-label">@lang('localize.country')</label>
            <div class="col-lg-7">
                <p class="form-control-static"> {{$price->co_name}}</p>
            </div>
        </div>

        @if($price->pro_type == 2)
        <div class="form-group">
            <label class="col-lg-3 control-label">@lang('localize.coupon_value') <span style="color:red;">*</span></label>
            <div class="col-lg-7">
                <input type="number" placeholder="@lang('localize.coupon_value')" class="form-control" id="modal_coupon" name='coupon_value' value="{{ $price->coupon_value }}">
            </div>
        </div>
        @endif

        <div class="form-group">
            <label class="col-lg-3 control-label">@lang('localize.original_price') <span style="color:red;">*</span></label>
            <div class="col-lg-7">
                <div class="input-group m-b">
                    <span class="input-group-addon">{{$price->co_curcode}}</span>
                    <input type="text"  placeholder="@lang('localize.original_price')" class="form-control number modal_compulsary" id="modal_price" name='pro_price' value="{{$price->price}}">
                </div>
            </div>

        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">@lang('localize.price_after_discount')</label>
            <div class="col-lg-7">
                <input type="text" placeholder="@lang('localize.price_after_discount')" class="form-control" id="modal_dprice" name='pro_dprice' value="{{$price->discounted_price}}">
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">@lang('localize.discounted') (%)</label>
            <div class="col-lg-7">
                <input type="text" placeholder="@lang('localize.discounted') (%)" class="form-control" id='modal_dpercentage' name="pro_drate" readonly>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">@lang('localize.discounted_date_range')</label>
            <div class="col-lg-7">

                <input type="text" id="modal_daterange" name="modal_daterange" value="{{($daterange == "")? '' : $daterange}}" placeholder="Discounted date range" class="form-control" disabled/>
                <input type="hidden" name="start" value="{{$price->discounted_from}}" id="modal_sdate">
                <input type="hidden" name="end" value="{{$price->discounted_to}}" id="modal_edate">
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">@lang('localize.product_v_credit_value')</label>
            <div class="col-lg-7">
                <input type="number" placeholder="@lang('localize.product_v_credit_value')" class="form-control" id='modal_credit' readonly>
                <input type="hidden" name="currency_rate" id="modal_currency_rate">
                <input type="hidden" name="id" value="{{$price->id}}">
                <input type="hidden" name="mer_id" value="{{$mer_id}}">
                <input type="hidden" name="co_code" value="{{$price->co_code}}">
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">@lang('localize.delivery_days')</label>
            <div class="col-lg-7">
                <input type="text" placeholder="{{trans('localize.delivery_days')}}" class="form-control" name='pro_delivery' value="{{$price->delivery_days}}">
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">@lang('localize.shipping_fees_type') <span style="color:red;">*</span></label>
            <div class="col-lg-7">
                <label class="radio-inline"><input type="radio" name="shipping_fees_type" value="0" {{ ($price->shipping_fees_type == 0) ? 'checked' : '' }}>@lang('localize.shipping_fees_free')</label><br>
                <label class="radio-inline"><input type="radio" name="shipping_fees_type" value="1" {{ ($price->shipping_fees_type == 1) ? 'checked' : '' }}>@lang('localize.shipping_fees_product')</label><br>
                <label class="radio-inline"><input type="radio" name="shipping_fees_type" value="2" {{ ($price->shipping_fees_type == 2) ? 'checked' : '' }}>@lang('localize.shipping_fees_transaction')</label><br>
                <label class="radio-inline"><input type="radio" name="shipping_fees_type" value="3" {{ ($price->shipping_fees_type == 3) ? 'checked' : '' }}>@lang('localize.self_pickup')</label>
            </div>
        </div>

        <div id="modal_shipping_fees" class="form-group" style="{{ ($price->shipping_fees_type == 0 || $price->shipping_fees_type == 3 ) ? 'display:none' : '' }};">
            <label class="col-lg-3 control-label">@lang('localize.shipping_fees') <span style="color:red;">*</span></label>
            <div class="col-lg-7">
                <div class="input-group m-b">
                    <span class="input-group-addon">{{$price->co_curcode}}</span>
                    <input type="text" placeholder="{{trans('localize.shipping_fees')}}" class="form-control number {{ ($price->shipping_fees_type > 0 && $price->shipping_fees_type < 3 ) ? 'modal_compulsary' : '' }}" id='modal_pro_shipping_fees' name='shipping_fees' value="{{ $price->shipping_fees }}">
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button data-dismiss="modal" class="btn btn-default" type="button">{{trans('localize.close_')}}</button>
    <button type="button" class="btn btn-outline btn-primary pull-right" id="submit_edit">@lang('localize.edit_pricing')</button>
</div>
<script>
    $(document).ready(function() {
        $('.number').keydown(function (e) {-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});

        var price = {{$price->price}};
        var dprice = parseFloat({{ ($price->discounted_price == 0.00 ) ? '' : $price->discounted_price }});
        var rate = {{$price->co_rate}};

        var start = new Date("{{($price->discounted_from)? $price->discounted_from : date('Y-m-d')}}");
        var end = new Date("{{($price->discounted_to)? $price->discounted_to : date('Y-m-d')}}");

        calculate_price(rate,price,dprice);
        if( parseFloat("{{ ($price->discounted_price)? $price->discounted_price : 0 }}") > 0.00)
        {
            $('#modal_daterange').prop("disabled", false);
        } else {
            $('#modal_daterange').val('');
        }

        $('input[name="modal_daterange"]').daterangepicker({
            timePicker: true,
            timePickerIncrement: 1,
            autoUpdateInput : false,
            startDate: start,
            endDate: end,
            locale: {
                format: 'DD-MM-YYYY h:mm A'
            }
        });

        $('input[name="modal_daterange"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY HH:mm:ss') + ' - ' + picker.endDate.format('DD-MM-YYYY HH:mm:ss'));
            $('#modal_sdate').val($("#modal_daterange").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss'));
            $('#modal_edate').val($("#modal_daterange").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss'));
        });

        $('input[name="modal_daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#modal_sdate').val('');
            $('#modal_edate').val('');
        });

        $('#modal_price, #modal_dprice').change(function() {
            var price = parseFloat($('#modal_price').val());
            var dprice = parseFloat($('#modal_dprice').val());
            var rate = {{$price->co_rate}};

            if($('#modal_dprice').val() != '' ) {
                $('#modal_daterange').prop("disabled", false);
                $('#modal_daterange').addClass('modal_compulsary');
            } else {
                $('#modal_daterange').prop("disabled", true);
                $('#modal_daterange').val('');
                $('#modal_sdate').val('');
                $('#modal_edate').val('');
            }

            calculate_price(rate,price,dprice);
        });

        $('input[type=radio][name=shipping_fees_type]').change(function() {
            if ($(this).val() > 0 && $(this).val() < 3) {
                $('#modal_shipping_fees').show();
                $('#modal_pro_shipping_fees').addClass('modal_compulsary');
            } else {
                $('#modal_shipping_fees').hide();
                $('#modal_pro_shipping_fees').removeClass('modal_compulsary');
            }
        });

        $('#submit_edit').click(function() {
            var isValid = true;
            $(':input').each(function() {
                if ($(this).hasClass('modal_compulsary')) {
                    if (!$(this).val() || $(this).val() == 0) {
                        $(this).attr('placeholder', '{{trans('localize.fieldrequired')}}').css('border', '1px solid red').focus();
                        $(this).closest('.form-group').addClass('has-error');
                        isValid = false;
                        console.log($(this))
                    }
                }

                $(this).css('border', '');
                $(this).closest('.form-group').removeClass('has-error');
            });

            if($('#pro_dprice').val() != '') {
                if($('#daterange').val() == '') {
                    $('#daterange').attr('placeholder', '{{trans('localize.fieldrequired')}}');
                    $('#daterange').css('border', '1px solid red');
                    isValid = false;
                } else {
                    $('#daterange').css('border', '');
                }
            }

            if (!isValid) {
                return false;
            }

            swal({
                title: "{{trans('localize.sure')}}",
                text: "{{trans('localize.confirm_submit_pricing')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#5cb85c",
                confirmButtonText: "{{trans('localize.yes')}}",
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function(){
                $("#edit_pricing").submit();
            });
        });
    });

    function calculate_price(rate, price, dprice)
    {
        var calculate_price = 0;
        if(isNaN(dprice)) {
            dprice = 0;
            $('#modal_dpercentage').val('');
            $('#modal_dprice').val('');
            calculate_price = price;
        } else {
            var discounted_percentage = 100 - ((dprice/price) * 100);
            $('#modal_dpercentage').val(discounted_percentage.toFixed(2) + ' %');
            calculate_price = dprice;
        }

        if(dprice >= price) {
            swal("{{trans('localize.error')}}", "{{trans('localize.discounted_error_desc')}}", "error");
            $('#modal_dprice').css('border', '1px solid red');
            $('#modal_dprice').val('');
            $('#modal_dpercentage').val('');
            $('#modal_dprice').focus();
            return false;
        } else {
            $('#modal_dprice').css('border', '');
            $('#modal_dprice').css('border', '');
        }

        credit = calculate_price / rate;

        $('#modal_credit').val(credit.toFixed(2));
        $('#modal_currency_rate').val(rate);
    }
</script>