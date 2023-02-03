@extends('admin.layouts.master')
@section('title', 'Product Pricing')
@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.product')}} {{trans('localize.pricing')}}</h2>
        <ol class="breadcrumb">
            <li>
                {{trans('localize.product')}}
            </li>
            <li>
                {{trans('localize.pricing')}}
            </li>
            <li class="active">
                <strong>{{ $product['details']->pro_id }} - {{ $product['details']->pro_title_en }}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">

    @include('admin.common.notifications')

    <div class="row">
        <div class="tabs-container">

            @include('admin.product.nav-tabs', ['link' => 'pricing'])

            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="panel-body">

                        @if(!empty($items))
                        <div class="col-lg-12">
                            <div class="alert alert-info" role="alert">
                                <strong>{{trans('localize.information')}}:</strong>
                                <br/>{{trans('localize.prod_price_msg1')}}
                                <br/>{{trans('localize.prod_price_msg2')}}
                                <br/>{{trans('localize.prod_price_msg3')}}
                            </div>
                        </div>
                        @else
                        <div class="col-lg-12">
                            <div class="alert alert-danger" role="alert">
                                <strong>{{trans('localize.this_product_does_not_have_any_pricing')}}</strong>
                            </div>
                        </div>
                        @endif

                        @if(!empty($items))
                        <div class="col-lg-12 nopadding" id="button_back_view" style="display:none;">
                            <div class="col-md-2 col-md-offset-10">
                                <button type="button" class="btn btn-primary btn-block btn-md btn-outline" id="button_back">{{trans('localize.back')}}</button>
                            </div>
                        </div>

                        <div id="pricing_list" style="display:block;">
                            <form id="batch_status" action="{{ url('admin/product/pricing_status/batch_update', [$pro_id]) }}" method="POST">
                            {{ csrf_field() }}
                            @if($edit_permission)
                            <div class="col-lg-12 nopadding">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select class="form-control" id="status" name="status">
                                            <option value="">[{{trans('localize.batch_status_update')}}]</option>
                                            <option value="1">@lang('localize.set_to_active')</option>
                                            <option value="0">@lang('localize.set_to_inactive')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary btn-block btn-md btn-outline" id="update_status">@lang('localize.update')</button>
                                </div>
                                @if($product['details']->pro_type <> 4)
                                <div class="col-md-2 col-md-offset-6">
                                    <button type="button" class="btn btn-primary btn-block btn-md btn-outline" id="button_add">@lang('localize.add_pricing')</button>
                                </div>
                                @endif
                            </div>
                            @endif

                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-nowrap">
                                                    <div class="i-checks">
                                                        <label>
                                                            <input type="checkbox" id="check_all">
                                                        </label>
                                                    </div>
                                                </th>
                                                <th class="text-center text-nowrap">@lang('localize.country')</th>
                                                <th class="text-center text-nowrap">@lang('localize.currency_rate')</th>
                                                <th class="text-center text-nowrap">@lang('localize.price')</th>
                                                @if($product['details']->pro_type == 2)
                                                <th class="text-center text-nowrap">@lang('localize.coupon_value')</th>
                                                @endif
                                                <th class="text-center text-nowrap">@lang('localize.shipping_fees')</th>
                                                <th class="text-center text-nowrap">@lang('localize.discounted_price')</th>
                                                <th class="text-center text-nowrap">@lang('localize.discounted_date_range')</th>
                                                <th class="text-center text-nowrap">@lang('localize.delivery_days')</th>
                                                <th class="text-center text-nowrap">@lang('localize.last_updated')</th>
                                                <th class="text-center text-nowrap">@lang('localize.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($items as $ref => $item)
                                                <tr class="text-center border-size-lg" style="background-color: #{{ ($ref%2 == 0)? 'f9f9f9' : 'fff' }};">
                                                    <td colspan="">
                                                        <div class="i-checks">
                                                            <label>
                                                                <input type="checkbox" class="input_checkbox parent_check" data-id="{{ json_encode($item['price_id']) }}">
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td colspan="{{ ($product['details']->pro_type == 2)? '9' : '8' }}" class="text-center">
                                                        <h4><label>@lang('localize.quantity') :</label>&nbsp;{{ $item['quantity'] }}</h4>
                                                        @if($item['attributes'])
                                                            <hr class="hr-line-dashed" style="padding:0; margin:2px;">
                                                            {!! $item['attributes_list']!!}
                                                        @endif
                                                    </td>
                                                    <td>

                                                        @if($edit_permission)
                                                        @if($product['details']->pro_type <> 4)

                                                        <p>
                                                            <button type="button" class="btn btn-white btn-block btn-sm" data-id="{{ $item['price_id'] }}" data-merid="{{$mer_id}}" data-action="edit_pricing_attribute"><span><i class="fa fa-edit"></i> @lang('localize.edit_item')</span></button>
                                                        </p>
                                                        @endif
														                                                        @endif
                                                    </td>
                                                </tr>
                                                @foreach ($item['pricing'] as $key => $price)
                                                <tr class="text-center" style="background-color: #{{ ($ref%2 == 0)? 'f9f9f9' : 'fff' }};">
                                                    <td>
                                                        <div class="i-checks">
                                                            <label>
                                                                <input type="checkbox" class="input_checkbox" id="checkbox_{{$price->id}}" name="pricing_id[]" value="{{ $price->id }}">
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td>{{ $price->co_name }}</td>
                                                    <td>{{$price->co_cursymbol}} {{ $price->currency_rate }}</td>
                                                    <td class="text-nowrap">{{$price->co_cursymbol}} {{ number_format($price->price , 2) }}</td>
                                                    @if($product['details']->pro_type == 2)
                                                    <td>{{ $price->coupon_value }}</td>
                                                    @endif
                                                    <td class="text-nowrap">
                                                        @if ($price->shipping_fees_type == 1)
                                                            {{$price->co_cursymbol}} {{ number_format($price->shipping_fees , 2) }}<br/>@lang('localize.shipping_fees_product')
                                                        @elseif ($price->shipping_fees_type == 2)
                                                            {{$price->co_cursymbol}} {{ number_format($price->shipping_fees , 2) }}<br/>@lang('localize.shipping_fees_transaction')
                                                        @elseif ($price->shipping_fees_type == 3)
                                                            @lang('localize.self_pickup')
                                                        @else
                                                            @lang('localize.shipping_fees_free')
                                                        @endif
                                                    </td>
                                                    <td class="text-nowrap">{{ ($price->discounted_price == 0.00)? '' : $price->co_cursymbol.' '.number_format($price->discounted_price , 2) }}</td>
                                                    <td>
                                                        @if($price->discounted_from && $price->discounted_to )
                                                            </br>{{ ($price->discounted_to != null)? $price->discounted_to->format('e') : '' }}
                                                            </br>{{ ($price->discounted_from != null)? $price->discounted_from->format('d M y h:i A') : '' }}
                                                            </br>to
                                                            </br>{{ ($price->discounted_to != null)? $price->discounted_to->format('d M y h:i A') : '' }}

                                                        @endif
                                                    </td>
                                                    <td>{{ $price->delivery_days }}</td>
                                                    <td>{{ \Helper::UTCtoTZ($price->updated_at) }}</td>
                                                    <td>
                                                        <p>
                                                            @if($price->status == 0)
                                                                <label class="label label-danger" style="display:block;">{{trans('localize.Inactive')}}</label>

                                                                @if($edit_permission)

                                                                @if($product['details']->pro_type <> 4)

                                                                <button type="button" class="btn btn-white btn-block btn-sm text-danger" data-id="{{ $price->id  }}" data-merid="{{$mer_id}}" data-action="delete_product_pricing"><span><i class="fa fa-trash"></i> {{trans('localize.delete_pricing')}}</span></button>
                                                                @endif
                                                                <a href="/admin/product/toggle_pricing_status/{{$price->id}}/{{$mer_id}}" class="btn btn-white btn-block btn-sm text-navy {{ ($price->attribute_status == 0)? 'disabled' : '' }}"><span><i class="fa fa-refresh"></i> {{trans('localize.set_to_active')}}</span></a>
                                                                @endif
                                                            @elseif($price->status == 1)
                                                                <label class="label label-primary" style="display:block;">{{trans('localize.Active')}}</label>
                                                                @if($edit_permission)
                                                                <button type="button" class="btn btn-white btn-block btn-sm text-warning" data-id="{{ $price->id  }}" data-merid="{{$mer_id}}" data-action="set_to_inactive"><span><i class="fa fa-refresh"></i>  {{trans('localize.set_to_inactive')}}</span></button>
                                                                @endif
                                                            @endif
                                                        </p>
                                                        @if($edit_permission)
                                                        <p>
                                                            <button type="button" class="btn btn-white btn-block btn-sm" data-id="{{ $price->id  }}" data-merid="{{$mer_id}}" data-action="edit_product_pricing"><span><i class="fa fa-edit"></i> {{trans('localize.edit_pricing')}}</span></button>
                                                        </p>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            </form>
                        </div>
                        @endif

                        @if($edit_permission)
                        <div class="col-lg-12" style="margin-top: 18px; display:{{ (empty($items))? 'block' : 'none' }}" id="form_add_pricing">
                            <div class="ibox float-e-margins well">
                                <div class="ibox-title">
                                    <h5>{{trans('localize.add_pricing')}}</h5>
                                </div>
                                <div class="ibox-content">

                                    <form id="add_new_pricing" action="{{ url('admin/product/pricing/add', [$mer_id, $pro_id]) }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label">@lang('localize.country') <span style="color:red;">*</span></label>
                                            <div class="col-lg-7">
                                                <select class="form-control compulsary" name="country_id" id="country">
                                                    <option value="0" data-id="0">@lang('localize.selectCountry')</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{$country->co_id}}" data-id="{{$country->co_curcode}}" data-rate="{{$country->co_rate}}" data-symbol="{{$country->co_cursymbol}}" data-countrycode="{{$country->co_code}}">{{$country->co_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div id="field" style="display:none;">

                                            @if($product['details']->pro_type == 2)
                                            <div class="form-group">
                                                <label class="col-lg-3 control-label">@lang('localize.coupon_value') <span style="color:red;">*</span></label>
                                                <div class="col-lg-7">
                                                    <input type="number" placeholder="@lang('localize.coupon_value')" class="form-control number compulsary" id="coupon_value" name='coupon_value' value="">
                                                </div>
                                            </div>
                                            @endif

                                            <div class="form-group">
                                                <label class="col-lg-3 control-label">@lang('localize.original_price') <span style="color:red;">*</span></label>
                                                <div class="col-lg-7">
                                                    <div class="input-group m-b">
                                                        <span class="input-group-addon" id="co_code"></span>
                                                        <input type="text" placeholder="@lang('localize.original_price')" class="form-control number compulsary" id='pro_price' name='pro_price'>
                                                        <input type="hidden" name="co_code" id="code">
                                                    </div>
                                                </div>
                                            </div>

                                            @if($product['details']->pro_type <> 4)
                                            <div class="form-group">
                                                <label class="col-lg-3 control-label">@lang('localize.quantity') <span style="color:red;">*</span></label>
                                                <div class="col-lg-7">
                                                    <input type="text" placeholder="Quantity" class="form-control number compulsary" id='quantity' name='quantity'>
                                                    <label><span style="color:red;">[@lang('localize.quantity_override_disclaimer')]</span></label>
                                                </div>
                                            </div>
                                            @endif

                                            <div class="form-group">
                                                <label class="col-lg-3 control-label">@lang('localize.price_after_discount')</label>
                                                <div class="col-lg-7">
                                                    <input type="text" placeholder="@lang('localize.price_after_discount')" class="form-control number" id='pro_dprice' name='pro_dprice'>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 control-label">@lang('localize.discounted') (%)</label>
                                                <div class="col-lg-7">
                                                    <input type="text" placeholder="@lang('localize.discounted') (%)" class="form-control" id='discount_percentage' readonly>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">@lang('localize.discounted_date_range')</label>
                                                <div class="col-sm-7">
                                                    <input type="text" id="daterange" name="daterange" value="" placeholder="@lang('localize.discounted_date_range')" class="form-control" disabled/>
                                                    <input type="hidden" name="start" id="start">
                                                    <input type="hidden" name="end" id="end">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 control-label">@lang('localize.product_v_credit_value')</label>
                                                <div class="col-lg-7">
                                                    <input type="number" placeholder="@lang('localize.product_v_credit_value')" class="form-control" id='credit' readonly>
                                                    <input type="hidden" name="currency_rate" id="currency_rate">
                                                    <input type="hidden" name="discounted_rate" id="discounted_rate">
                                                    <input type="hidden" name="status" value="{{ $product['details']->pro_status}}">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 control-label">@lang('localize.delivery_days')</label>
                                                <div class="col-lg-7">
                                                    <input type="text" placeholder="{{trans('localize.delivery_days')}} : 2 - 5" class="form-control" name='pro_delivery'>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-3 control-label">@lang('localize.shipping_fees_type') <span style="color:red;">*</span></label>
                                                <div class="col-lg-7">
                                                    <label class="radio-inline"><input type="radio" name="shipping_fees_type" value="0" checked>@lang('localize.shipping_fees_free')</label>
                                                    <label class="radio-inline"><input type="radio" name="shipping_fees_type" value="1">@lang('localize.shipping_fees_product')</label>
                                                    <label class="radio-inline"><input type="radio" name="shipping_fees_type" value="2">@lang('localize.shipping_fees_transaction')</label>
                                                    <label class="radio-inline"><input type="radio" name="shipping_fees_type" value="3">@lang('localize.self_pickup')</label>
                                                </div>
                                            </div>

                                            <div id="shipping_fees" class="form-group" style="display:none;">
                                                <label class="col-lg-3 control-label">@lang('localize.shipping_fees') <span style="color:red;">*</span></label>
                                                <div class="col-lg-7">
                                                    <div class="input-group m-b">
                                                        <span class="input-group-addon" id="co_code2"></span>
                                                        <input type="text" placeholder="{{trans('localize.shipping_fees')}}" class="form-control number" id='pro_shipping_fees' name='shipping_fees'>
                                                    </div>
                                                </div>
                                            </div>

                                            @if(!$attributes->isEmpty())
                                                <br>
                                                <div class="text-center"><h3>@lang('localize.available_attributes')</h3></div>
                                                @foreach ($attributes as $attribute => $item)
                                                    <div class="form-group">
                                                        <label class="col-lg-3 control-label">{{$item->first()->title}}<span style="color:red;"> *</span></label>
                                                        <div class="col-lg-7">
                                                            <select class="form-control" name="attribute[]">
                                                                @foreach ($item as $attribute_item)
                                                                    <option value="{{ $attribute_item->id }}">{{$attribute_item->item}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif

                                            <div class="form-group">
                                                <div class="col-sm-7 col-sm-offset-3">
                                                    <button type="button" class="btn btn-block btn-outline btn-primary" id="pricing_add">@lang('localize.add_pricing')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
@stop

@section('style')
<link href="/backend/css/plugins/daterangepicker/custom-daterangepicker.css" rel="stylesheet">
<link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
<style>
.table > tbody > tr > td {
     vertical-align: middle;
}
.table > thead > tr > th {
     vertical-align: middle;
}
</style>
@endsection

@section('script')
<script src="/backend/js/plugins/iCheck/icheck.min.js"></script>
<script src="/backend/js/plugins/daterangepicker/moment.min.js"></script>
<script src="/backend/js/plugins/daterangepicker/custom-daterangepicker.js"></script>
<script src="/backend/js/custom.js"></script>

<script>
    $(document).ready(function() {
        $('.number').keydown(function (e) {-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});

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

        $('.parent_check').on('ifToggled', function(event) {
            var child = $(this).attr('data-id').slice(2, -2);
            if(this.checked == true) {
                $.each(child.split(","), function(key, value){
                    $('#checkbox_'+value).iCheck('check');
                });
            } else {
                $.each(child.split(","), function(key, value){
                    $('#checkbox_'+value).iCheck('uncheck');
                });
            }
        });

        $('input[name="daterange"]').daterangepicker({
            timePicker: true,
            timePickerIncrement: 1,
            autoUpdateInput : false,
            locale: {
                format: 'DD-MM-YYYY h:mm A'
            }
        });

        $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY HH:mm:ss') + ' - ' + picker.endDate.format('DD-MM-YYYY HH:mm:ss'));
            $('#start').val($("#daterange").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss'));
            $('#end').val($("#daterange").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss'));
        });

        $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#start').val('');
            $('#end').val('');
        });

        var rate = 0;
        $('#country').change(function() {
            var currency_code = $(this).find(':selected').data('id');
            var currency_symbol = $(this).find(':selected').data('symbol');
            var country_code = $(this).find(':selected').data('countrycode');
            rate = $(this).find(':selected').data('rate');

            $('#currency_rate').val(rate);

            var price = parseFloat($('#pro_price').val());
            var dprice = parseFloat($('#pro_dprice').val());

            if(price && dprice !== '') {
                calculate_price(rate,price,dprice);
            }

            $('#co_code, #co_code2').text(currency_code);
            $('#code').val(country_code);

            $('#field').show();
            $('#button').show();

            if(parseInt(currency_code) === 0) {
                $('#field').hide();
                $('#button').hide();
            }
        });

        $('#pro_price, #pro_dprice').change(function() {
            var price = parseFloat($('#pro_price').val());
            var dprice = parseFloat($('#pro_dprice').val());

            if($('#pro_dprice').val() != '' ) {
                $('#daterange').prop("disabled", false);
                $('#daterange').addClass('compulsary');
            } else {
                $('#daterange').prop("disabled", true);
                $('#daterange').val('');
                $('#start').val('');
                $('#end').val('');
            }

            calculate_price(rate,price,dprice);

        });

        $('input[type=radio][name=shipping_fees_type]').change(function() {
            if ($(this).val() > 0 && $(this).val() < 3) {
                $('#shipping_fees').show();
                $('#pro_shipping_fees').addClass('compulsary');
            } else {
                $('#shipping_fees').hide();
                $('#pro_shipping_fees').removeClass('compulsary');
            }
        });

        $('#button_add').click(function() {
            $('#button_back_view').show();
            $('#pricing_list').hide();
            $('#form_add_pricing').show();
        });

         $('#button_back').click(function() {
            $('#button_back_view').hide();
            $('#pricing_list').show();
            $('#form_add_pricing').hide();
        });

        $('#pricing_add').click(function(event) {
            var isValid = true;
            $(':input').each(function() {
                if ($(this).hasClass('compulsary')) {
                    if (!$(this).val() || $(this).val() == 0) {
                        $(this).attr('placeholder', "{{ trans('localize.fieldrequired') }}").css('border', '1px solid red').focus();
                        $(this).closest('.form-group').addClass('has-error');
                        isValid = false;
                        return false;
                    }
                }

                $(this).css('border', '');
                $(this).closest('.form-group').removeClass('has-error');
            });

            if($('#pro_dprice').val() != '') {
                if($('#daterange').val() == '') {
                    $('#daterange').attr('placeholder', "{{ trans('localize.fieldrequired')}} ");
                    $('#daterange').css('border', '1px solid red');
                    isValid = false;
                    return false;
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
				cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: false
            }, function(isConfirm){
                if(isConfirm)
                    $("#add_new_pricing").submit();
            });

        });

        $('button').on('click', function(){
            var mer_id = $(this).attr('data-merid');
            var this_id = $(this).attr('data-id');
            var this_action = $(this).attr('data-action');
            var pro_id = "{{ $product['details']->pro_id }}";

            if (this_action == 'edit_product_pricing') {
                edit_product_pricing(mer_id,this_id);
            } else if (this_action == 'set_to_inactive') {
                swal({
                    title: "{{trans('localize.sure')}}",
                    text: "{{trans('localize.product_pricing_set_inactive_desc')}}",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#5cb85c",
                    confirmButtonText: "{{trans('localize.yes')}}",
					cancelButtonText: "{{trans('localize.cancel')}}",
                    closeOnConfirm: false
                }, function(){
                    var url = '/admin/product/toggle_pricing_status/' + this_id + '/' + mer_id;
                    window.location.href = url;
                });
            } else if (this_action == 'edit_pricing_attribute') {
                edit_pricing_attribute(mer_id, pro_id, this_id);
            } else if (this_action == 'delete_product_pricing') {
                swal({
                    title: "{{trans('localize.sure')}}",
                    text: "{{trans('localize.Are_you_sure_want_to_delete_this_pricing')}}",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#5cb85c",
                    confirmButtonText: "{{trans('localize.yes')}}",
					cancelButtonText: "{{trans('localize.cancel')}}",
                    closeOnConfirm: false
                }, function(){
                    var url = '/admin/product/delete_product_pricing/' + this_id;
                    window.location.href = url;
                });
            }
        });

        $('#update_status').click(function(event) {
            event.preventDefault();

            var status = $("#status option:selected").val();
            if(status == '') {
                swal("{{trans('localize.error')}}", "{{trans('localize.Please_select_status_to_update')}}", "error");
                return false;
            } else if(status == 0) {

                swal({
                    title: "{{trans('localize.sure')}}",
                    text: "{{trans('localize.product_pricing_set_inactive_desc')}}",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#5cb85c",
                    confirmButtonText: "{{trans('localize.yes')}}",
					cancelButtonText: "{{trans('localize.cancel')}}",
                    closeOnConfirm: true
                }, function(isConfirm){
                    if(isConfirm) {
                        $('#spinner').show();
                        $('#batch_status').submit();
                    }
                });
                return false;
            }

            $('#batch_status').submit();
        });

    });

    function calculate_price(rate, price, dprice)
    {
        var calculate_price = 0;
        if(isNaN(dprice)) {
            dprice = 0;
            calculate_price = price;
            $('#discount_percentage').val('');
            $('#pro_dprice').val('');
        } else {
            calculate_price = dprice;
            var discounted_percentage = 100 - ((dprice/price) * 100);
            $('#discount_percentage').val(discounted_percentage.toFixed(2) + ' %');
            $('#discounted_rate').val(discounted_percentage.toFixed(4));
        }

        if(dprice >= price) {
            swal("{{trans('localize.error')}}", "{{trans('localize.discounted_error_desc')}}", "error");
            $('#pro_dprice').css('border', '1px solid red');
            $('#pro_dprice').val('');
            $('#discount_percentage').val('');
            $('#pro_dprice').focus();
            return false;
        } else {
            $('#pro_dprice').css('border', '');
            $('#pro_dprice').css('border', '');
        }

        credit = calculate_price / rate;

        $('#credit').val(credit.toFixed(4));
    }
</script>
@endsection
