@extends('merchant.layouts.master')

@section('title', 'Add Product')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.add_products')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('merchant/product/manage') }}">{{trans('localize.product')}}</a>
            </li>
            <li class="active">
                <strong>{{trans('localize.add_products')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    @include('merchant.common.notifications')

    <div class="row">
        <div class="col-lg-12">
            <div class="tabs-container">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#tab-1"> @lang('localize.new_products')</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-2"> @lang('localize.description')</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-2"> @lang('localize.manage_image')</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-2"> @lang('localize.manage_filter')</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-2"> @lang('localize.manage_attributes')</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-2"> @lang('localize.manage_pricing')</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-2"> @lang('localize.quantity_log')</a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane active">
                        <div class="panel-body">

                            <form class="form" id="mer_add_product" action="{{ url('merchant/product/add') }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="row">
                                <div class="col-lg-3 col-lg-offset-9">
                                    <button class="btn btn-block btn-primary" id="submit_top">{{trans('localize.submit')}}</button>
                                    <br>
                                </div>
                                <div class="col-lg-6">

                                    <div class="col-lg-12 no-spacing">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                               <h5>@lang('localize.product_info')</h5>
                                            </div>
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label class="control-label">@lang('localize.store') <span style="color:red;">*</span></label>
                                                    @if (count($stores) != 0)
                                                    <select class="form-control compulsary" id="stor_id" name="stor_id" >
                                                        <option value="">-- @lang('localize.select_store') --</option>
                                                            @foreach ($stores as $key => $store)
                                                                <option value="{{$store->stor_id}}" {{ ($key == 0 && $stores->count() == 1)? 'selected' : '' }}>{{$store->stor_name}}</option>
                                                            @endforeach
                                                    </select>
                                                    @else
                                                        <span class="pull-left text-center" style="color:red; font-size:16px;">@lang('localize.u_dont_have_any_store_to_proceed')</span>
                                                    @endif
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label">@lang('localize.product_types')</label>
                                                    <select class="form-control" name="pro_type" id="pro_type">
                                                        <option value="1" {{ (old('pro_type') == 1)? 'selected' : '' }}>@lang('localize.normal_product')</option>
                                                        {{--  <option value="2" {{ (old('pro_type') == 2)? 'selected' : '' }}>@lang('localize.coupon')</option>
                                                        <option value="3" {{ (old('pro_type') == 3)? 'selected' : '' }}>@lang('localize.ticket')</option>  --}}
                                                        <option value="4" {{ (old('pro_type') == 4)? 'selected' : '' }}>@lang('localize.e-card.name')</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label">@lang('localize.product_tittle_english')<span style="color:red;">*</span></label>
                                                    <input type="text" placeholder="@lang('localize.product_tittle_english')" class="form-control compulsary" id="pro_title_en" name='pro_title_en' value="{{ old('pro_title_en') }}">
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label">@lang('localize.product_tittle_chinese')</label>
                                                    <input type="text" placeholder="@lang('localize.product_tittle_chinese')" class="form-control" name='pro_title_cn' value="{{ old('pro_title_cn') }}">
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label">@lang('localize.product_tittle_chinese_traditional')</label>
                                                    <input type="text" placeholder="@lang('localize.product_tittle_chinese_traditional')" class="form-control" name='pro_title_cnt' value="{{ old('pro_title_cnt') }}">
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label">@lang('localize.product_tittle_bahasa')</label>
                                                    <input type="text" placeholder="@lang('localize.product_tittle_bahasa')" class="form-control" name='pro_title_my' value="{{ old('pro_title_my') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="col-lg-12 no-spacing">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h5>@lang('localize.limit')</h5>
                                            </div>
                                            <div class="panel-body">

                                                <div class="form-group">
                                                    <label class="control-label">@lang('localize.enabled')</label>
                                                    <p class="form-control-static">
                                                        <label>
                                                            <input class="i-checks limit-enabled" type="radio" value="1" name="limit_enabled" {{ old('limit_enabled', 0)? 'checked' : '' }}> @lang('localize.yes')
                                                        </label> <br>
                                                        <label>
                                                            <input class="i-checks limit-enabled" type="radio" value="0" name="limit_enabled" {{ !old('limit_enabled', 0)? 'checked' : '' }}> @lang('localize.no')
                                                        </label>
                                                    </p>
                                                </div>

                                                <div class="limit-enable-div {{ old('limit_enabled', 0)? 'show' : 'hide' }}">
                                                    <div class="form-group">
                                                        <label class="control-label">@lang('localize.quantity')</label>
                                                        <input type="number" class="form-control" placeholder="@lang('localize.quantity')" name="limit_quantity" value="{{ old('limit_quantity', 0) }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">@lang('localize.type')</label>
                                                        <p class="form-control-static">
                                                            @foreach ($limit_types as $value => $type)
                                                            <label><input class="i-checks" type="radio" value="{{ $value }}" name="limit_type" {{ old('limit_type', 0) == $value ? 'checked' : '' }}> {{ $type }}</label><br>
                                                            @endforeach
                                                        </p>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div> --}}

                                </div>

                                <div class="col-lg-6">

                                    <div class="col-lg-12 no-spacing">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h5>@lang('localize.main_image')</h5>
                                            </div>
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label class="control-label">@lang('localize.image_title')</label>
                                                    <input type="text" class="form-control" name="title" value="{{ old('title') }}">
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label">@lang('localize.image_file')<span style="color:red;">*</span></label>
                                                    <span class='btn btn-default btn-block'><input type='file' id='file' name='file' class="compulsary files"></span>
                                                    <span>@lang('localize.you_may_add_more_image_later')</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 no-spacing">
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label class="control-label">{{trans('localize.mer_meta_keyword')}}</label>
                                                    <input type="text" class="form-control" placeholder="{{trans('localize.mer_meta_keyword')}}" name='metakeyword' id='metakeyword' value="{{ old('metakeyword') }}">
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label">{{trans('localize.mer_meta_description')}}</label>
                                                    <textarea type="text" class="form-control text-noresize" rows="8" placeholder="{{trans('localize.mer_meta_description')}}" name='metadescription' id='metadescription'>{{ old('metadescription') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 date-picker no-spacing" style="display:none;">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h5 class="validity-text">@lang('localize.ticket_validity')</h5>
                                            </div>
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <input type="text" id="daterange" name="daterange" value="" placeholder="@lang('localize.date_range')" class="form-control"/>
                                                    <input type="hidden" name="start_date" id="start">
                                                    <input type="hidden" name="end_date" id="end">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 no-spacing">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h5>@lang('localize.category')
                                                    <small class="cat-desc">(@lang('localize.max3category'))</small>
                                                    <span style="color:red;">*</span>
                                                </h5>
                                            </div>
                                            <div class="panel-body">
                                                <table id="showCats" class="table table-condensed table-striped"></table>
                                                <input type="hidden" id="selected_cats" name="selected_cats" value="[]" class="compulsary">
                                                <div id="cats" class="demo"></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-lg-3 pull-right">
                                        <button class="btn btn-block btn-primary" id="submit">{{trans('localize.submit')}}</button>
                                    </div>
                                    <div class="col-lg-3 pull-right">
                                        <button type="button" onclick="reset()" class="btn btn-block btn-default">{{trans('localize.reset')}}</button>
                                    </div>
                                </div>

                            </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('style')
<link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
<link href="/backend/lib/wysiwyg/wysihtml5.min.css" rel="stylesheet">
<link href="/backend/css/plugins/jsTree/style.min.css" rel="stylesheet">
<link href="/backend/css/plugins/daterangepicker/custom-daterangepicker.css" rel="stylesheet">
<style>
    .jstree-open > .jstree-anchor > .fa-folder:before {
        content: "\f07c";
    }

    .jstree-default .jstree-icon.none {
        width: 0;
    }
</style>
@endsection

@section('script')
<script src="/backend/js/plugins/iCheck/icheck.min.js"></script>
<script src="/backend/lib/wysiwyg/wysihtml5x-toolbar.min.js"></script>
<script src="/backend/lib/wysiwyg/handlebars.runtime.min.js"></script>
<script src="/backend/lib/wysiwyg/wysihtml5.min.js"></script>
<script src="/backend/js/custom.js"></script>
<script src="/backend/js/plugins/jsTree/jstree.min.js"></script>
<script src="/backend/js/plugins/daterangepicker/moment.min.js"></script>
<script src="/backend/js/plugins/daterangepicker/custom-daterangepicker.js"></script>

<script type="text/javascript">
    function grab_categories(type) {

        var response = '';
        if(type == 'ticket')
            response = '?ticket=required';
        else
            response = '?ticket=disable';

        $('#selected_cats').val('[]');
        $('#showCats').empty();
        $('#cats').jstree("destroy").jstree({
            'core' : {
                'data' : {
                    'url' : function (node) {
                        return node.id === '#' ? '/get_product_category' + response : '/get_product_category/' + node.id;
                    },
                    'dataType' : 'json',
                },
                'themes' : {
                    'icons' : false
                },
            },
            'plugins' : [ 'wholerow', 'themes' ],
        });

        $('#cats').on("changed.jstree", function (e, data) {
            if(data.selected.length) {
                var parent = false;
                var child =  false;
                var catParents = data.node.parents;
                var catChilds = data.node.children_d;

                var catSelected = JSON.parse($('#selected_cats').val());
                var catExist = jQuery.inArray( data.node.id, catSelected );

                jQuery.each(catParents, function(key, val) {
                    if (jQuery.inArray( val, catSelected ) != -1) {
                        parent = true;
                        return false;
                    }
                });

                if (parent) {
                    swal("Opsss!", "Parent category already selected", "error");
                    return false;
                }

                jQuery.each(catChilds, function(key, val) {
                    if (jQuery.inArray( val, catSelected ) != -1) {
                        child = true;
                        return false;
                    }
                });

                if (child) {
                    swal("Opsss!", "Child category already selected", "error");
                    return false;
                }

                if (catExist == -1) {
                    if (catSelected.length < 3) {
                        var cat_id = data.node.id;

                        swal({
                            title: window.translations.sure,
                            text: "Confirm to select " + data.instance.get_node(data.selected[0]).text + " ?",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#5cb85c",
                            confirmButtonText: "Confirm",
                            closeOnConfirm: true,
                        }, function(){
                            catSelected.push(data.node.id);
                            $('#selected_cats').val(JSON.stringify(catSelected));
                            $('#showCats').append("<tr id='" + data.node.id + "'><td>" + data.node.text + "</td><td><a onClick='removeCat(\"" + data.node.id + "\"); return false;' class='btn btn-xs btn-danger pull-right'>Remove</a></td></tr>");
                        });
                    } else {
                        swal("Sorry!", "Maximum 3 category only can be selected.", "error");
                    }
                } else {
                    swal("Opsss!", data.node.text + " already selected", "error");
                }
            }
        });
    }

    function removeCat(id) {
        var catSelected = JSON.parse($('#selected_cats').val());
        jQuery('#' + id).remove();
        var index = jQuery.inArray( id, catSelected );

        if (index > -1) {
            catSelected.splice(index, 1);
        }
        $('#selected_cats').val(JSON.stringify(catSelected));
    }

    $(document).ready(function(){

        grab_categories('product');

        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $('.limit-enabled').on('ifClicked', function(event) {

            $('.limit-enable-div').removeClass('show').addClass('hide');
            if(parseInt(this.value) === 1)
            {
                $('.limit-enable-div').removeClass('hide').addClass('show');
            }
        });

        var dateToday = new Date();
        $('#daterange').daterangepicker({
            timePicker: true,
            timePickerIncrement: 1,
            autoUpdateInput : false,
            minDate: dateToday,
            locale: {
                format: 'DD-MM-YYYY h:mm A'
            }
        });

        $('#daterange').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY h:mm A') + ' - ' + picker.endDate.format('DD-MM-YYYY h:mm A'));
            $('#start').val($("#daterange").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss'));
            $('#end').val($("#daterange").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss'));
        });

        $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#start').val('');
            $('#end').val('');
        });

        $('#pro_type').change(function() {
            if(this.value == '3') {
                $('.cat-desc').text('');
                $('.date-picker').show();
                $('.validity-text').text("@lang('localize.ticket_validity')");
                $('#daterange').addClass('compulsary');
                grab_categories('ticket');
            } else if(this.value == '4') {
                $('.cat-desc').text('');
                $('.date-picker').show();
                $('.validity-text').text("@lang('localize.e-card.validity')");
                $('#daterange').removeClass('compulsary');
                grab_categories('product');
            } else {
                $('.cat-desc').text("@lang('localize.max3category')");
                $('.date-picker').hide();
                $('#daterange').removeClass('compulsary');
                grab_categories('product');
            }
        });

        $('#desc_en, #desc_cn, #desc_my').wysihtml5({
            toolbar: {
                fa: true
            }
        });
        $('.wysihtml5-sandbox').css("resize", "vertical");

        $('.files').change(function() {
            var fileExtension = ['jpeg', 'jpg', 'png'];

            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                swal("{{trans('localize.error')}}", "{{trans('localize.imgError')}}", "error");
                $(this).css('border', '1px solid red').focus().val('');
            } else if (($(this)[0].files[0].size) > 1000000){
                swal("{{trans('localize.error')}}", "{{trans('localize.imgSizeError')}}", "error");
                $(this).css('border', '1px solid red').focus().val('');
            }
        });

        $('#submit,#submit_top').click(function() {

            $(':input').each(function(e) {
                if ($(this).hasClass('compulsary')) {
                    if (!$(this).val()) {
                        if($(this).is('textarea')) {
                            swal("{{trans('localize.error')}}", "{{trans('localize.fieldrequired')}}\n{{trans('localize.description_english')}}", "error");
                        } else {
                            $(this).attr('placeholder', "{{trans('localize.fieldrequired')}}").css('border', '1px solid red').focus();
                        }
                        $(this).closest('.form-group').addClass('has-error');
                        event.preventDefault();
                        return false;
                    }

                    if($(this).attr('id') === 'selected_cats') {
                        if(JSON.parse($(this).val()).length == 0) {
                            swal("{{trans('localize.error')}}", "{{trans('localize.select_category_to_proceed')}}", "error");
                            event.preventDefault();
                            return false;
                        }
                    }
                }

                if ($(this).hasClass('files')) {
                    var fileExtension = ['jpeg', 'jpg', 'png'];
                    if($(this).val()) {
                        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                            swal("{{trans('localize.error')}}", "{{trans('localize.imgError')}}", "error");
                            $(this).css('border', '1px solid red').focus();
                            event.preventDefault();
                            return false;
                        } else if (($(this)[0].files[0].size) > 1000000){
                            swal("{{trans('localize.error')}}", "{{trans('localize.imgSizeError')}}", "error");
                            $(this).css('border', '1px solid red').focus();
                            event.preventDefault();
                            return false;
                        }
                    }
                }

                $(this).css('border', '');
                $(this).closest('.form-group').removeClass('has-error');
            });

            $("#mer_add_product").submit();
        });
    });

    function reset() {
        document.getElementById("mer_add_product").reset();
    }
</script>
@endsection
