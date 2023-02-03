@extends('admin.layouts.master')

@section('title', 'Edit Product')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Edit')}} {{trans('localize.product')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin/product/manage">{{trans('localize.product')}}</a>
            </li>
            <li>
                {{trans('localize.Edit')}}
            </li>
            <li class="active">
                <strong>{{$product['details']->pro_id}} - {{$product['details']->pro_title_en}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    @include('admin.common.notifications')

    <div class="row">
        <div class="tabs-container">

            @include('admin.product.nav-tabs', ['link' => 'edit'])

            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="panel-body">

                        @if($edit_permission)
                        <div class="col-lg-12 nopadding">
                            <div class="col-sm-3 pull-right nopadding">
                                <button class="btn btn-block btn-primary btn-form-submit">{{trans('localize.update')}}</button>
                            </div><br><br><br>
                        </div>
                        @endif

                        <form class="form" id="edit_product" action="{{ url('admin/product/edit', [$product['details']->pro_mr_id, $product['details']->pro_id]) }}" method="POST" enctype="multipart/form-data">

                        {{ csrf_field() }}

                        <div class="row">


                            <div class="col-lg-6">

                                <div class="col-lg-12 no-spacing">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h5>@lang('localize.product_info')</h5>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('localize.status')}}</label>
                                                <p class="form-control-static">
                                                    @if ($product['details']->pro_status == 1)
                                                        <label class="text-nowrap text-navy">@lang('localize.active')</label>
                                                    @elseif ($product['details']->pro_status == 0)
                                                        <label class="text-nowrap text-warning">@lang('localize.inactive')</label>
                                                    @elseif ($product['details']->pro_status == 2)
                                                        <label class="text-nowrap text-danger">@lang('localize.incomplete')</label>
                                                    @elseif ($product['details']->pro_status == 3)
                                                        <label class="text-nowrap text-danger">@lang('localize.pending_review')</label>
                                                    @endif
                                                </p>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label">@lang('localize.product_types')</label>
                                                <p class="form-control-static">
                                                    @if ($product['details']->pro_type == 1)
                                                        @lang('localize.normal_product')
                                                    @elseif ($product['details']->pro_type == 2)
                                                        @lang('localize.coupon')
                                                    @elseif ($product['details']->pro_type == 3)
                                                        @lang('localize.ticket')
                                                    @elseif ($product['details']->pro_type == 4)
                                                        @lang('localize.e-card.name')
                                                    @endif
                                                </p>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label">{{trans('localize.store')}}<span style="color:red;">*</span></label>
                                                @if (count($stores) != 0)
                                                <select class="form-control compulsary" id="stor_id" name="stor_id" >
                                                    <option value="">-- {{trans('localize.select_store')}} --</option>
                                                        @foreach ($stores as $store)
                                                            <option value="{{$store->stor_id}}" {{($store->stor_id == old('stor_id', $product['details']->pro_sh_id)) ? 'selected' : ''}}>{{$store->stor_name}}</option>
                                                        @endforeach
                                                </select>
                                                @else
                                                <span class="pull-left text-center" style="color:red; font-size:16px;">{{trans('localize.You_dont_have_any_online_store_to_proceed')}}</span>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label">{{trans('localize.product_tittle_english')}}<span style="color:red;">*</span></label>
                                                <input type="text" placeholder="{{trans('localize.product_tittle_english')}}" class="form-control compulsary" id="pro_title_en" name='pro_title_en' value="{{ old('pro_title_en', $product['details']->pro_title_en) }}">
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label">{{trans('localize.product_tittle_chinese')}}</label>
                                                <input type="text" placeholder="{{trans('localize.product_tittle_chinese')}}" class="form-control" name='pro_title_cn' value="{{ old('pro_title_cn', $product['details']->pro_title_cn) }}">
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label">{{trans('localize.product_tittle_chinese_traditional')}}</label>
                                                <input type="text" placeholder="{{trans('localize.product_tittle_chinese_traditional')}}" class="form-control" name='pro_title_cnt' value="{{ old('pro_title_cnt', $product['details']->pro_title_cnt) }}">
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label">{{trans('localize.product_tittle_bahasa')}}</label>
                                                <input type="text" placeholder="{{trans('localize.product_tittle_bahasa')}}" class="form-control" name='pro_title_my' value="{{ old('pro_title_my', $product['details']->pro_title_my) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12 no-spacing">
                                    <div class="panel panel-default">
                                        <div class="panel-body">

                                            <div class="form-group">
                                                <label class="control-label">@lang('localize.Sold_Quantity')</label>
                                                <input type="number" class="form-control" value="{{ $product['details']->pro_no_of_purchase }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">@lang('localize.mer_meta_keyword')</label>
                                                <input type="text" class="form-control" placeholder="{{trans('localize.mer_meta_keyword')}}" name='metakeyword' id='metakeyword' value="{{ old('metakeyword', $product['details']->pro_mkeywords) }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">@lang('localize.mer_meta_description')</label>
                                                <textarea type="text" class="form-control text-noresize" rows="8" placeholder="{{trans('localize.mer_meta_description')}}" name='metadescription' id='metadescription'>{!! old('metadescription', $product['details']->pro_mdesc) !!}</textarea>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-lg-6">

                                <div class="col-lg-12 no-spacing">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h5>@lang('localize.limit')</h5>
                                        </div>
                                        <div class="panel-body">

                                            <div class="form-group">
                                                <label class="control-label">@lang('localize.enabled')</label>
                                                <p class="form-control-static">
                                                    <label>
                                                        <input class="i-checks limit-enabled" type="radio" value="1" name="limit_enabled" {{ old('limit_enabled', $product['details']->limit_enabled)? 'checked' : '' }}> @lang('localize.yes')
                                                    </label> <br>
                                                    <label>
                                                        <input class="i-checks limit-enabled" type="radio" value="0" name="limit_enabled" {{ !old('limit_enabled', $product['details']->limit_enabled)? 'checked' : '' }}> @lang('localize.no')
                                                    </label>
                                                </p>
                                            </div>

                                            <div class="limit-enable-div {{ old('limit_enabled', $product['details']->limit_enabled)? 'show' : 'hide' }}">
                                                <div class="form-group">
                                                    <label class="control-label">@lang('localize.quantity')</label>
                                                    <input type="number" class="form-control" placeholder="@lang('localize.quantity')" name="limit_quantity" value="{{ old('limit_quantity', $product['details']->limit_quantity) }}">
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">@lang('localize.type')</label>
                                                    <p class="form-control-static">
                                                        @foreach ($limit_types as $value => $type)
                                                        <label><input class="i-checks" type="radio" value="{{ $value }}" name="limit_type" {{ old('limit_type', $product['details']->limit_type) == $value ? 'checked' : '' }}> {{ $type }}</label><br>
                                                        @endforeach
                                                    </p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                @if($product['details']->pro_type == 3 || $product['details']->pro_type == 4)
                                <div class="col-lg-12 no-spacing">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h5>{{ $product['details']->pro_type == 3? trans('localize.ticket_validity') : trans('localize.e-card.validity') }}</h5>
                                        </div>`
                                        <div class="panel-body">

                                            <div class="form-group">
                                                <input type="text" id="daterange" value="{{ ($product['details']->start_date && $product['details']->end_date)? \Helper::UTCtoTZ($product['details']->start_date, 'd-m-Y h:i:s A').' - '.\Helper::UTCtoTZ($product['details']->end_date, 'd-m-Y h:i:s A') : '' }}" placeholder="@lang('localize.date_range')" class="form-control {{ $product['details']->pro_type == 3? 'compulsary' : '' }}"/>
                                                <input type="hidden" name="start_date" id="start" value="{{ $product['details']->start_date? \Helper::UTCtoTZ($product['details']->start_date, 'd-m-Y H:i:s') : '' }}">
                                                <input type="hidden" name="end_date" id="end" value="{{ $product['details']->end_date? \Helper::UTCtoTZ($product['details']->end_date, 'd-m-Y H:i:s') : '' }}">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="col-lg-12 no-spacing">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h5>@lang('localize.category')
                                                <small class="cat-desc">(@lang('localize.max3category'))</small>
                                                <span style="color:red;">*</span>
                                            </h5>
                                        </div>
                                        <div class="panel-body">
                                            <table id="showCats" class="table table-condensed table-striped">
                                                <tbody>
                                                    @foreach ($product['category'] as $key => $category)
                                                        <tr id="{{$category['details']->category_id}}">
                                                            <td><b>{{ ($category['details']->id == $main_category)? '[ '. trans('localize.Main').' ]' : '' }}</b> {{$category['details']->name_en}}</td>
                                                            <td><a onclick="removeCat('{{$category['details']->category_id}}'); return false;" class="btn btn-xs btn-danger pull-right">{{trans('localize.remove')}}</a></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <input type="hidden" id="selected_cats" name="selected_cats" value="{{json_encode($product_category_list)}}" class="compulsary">
                                            <div id="cats" class="demo"></div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                        </form>

                        @if($edit_permission)
                        <div class="row">
                            <div class="col-sm-12 nopadding">
                                <div class="col-sm-3 col-sm-offset-6">
                                    <button type="button" onclick="reset()" class="btn btn-block btn-default">{{trans('localize.reset')}}</button>
                                </div>

                                <div class="col-sm-3">
                                    <button class="btn btn-block btn-primary btn-form-submit">{{trans('localize.update')}}</button>
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
<link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
<link href="/backend/lib/wysiwyg/wysihtml5.min.css" rel="stylesheet">
<link href="/backend/css/plugins/sortable/sortable.css" rel="stylesheet">
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
<script src="/backend/js/plugins/sortable/sortable.js"></script>
<script src="/backend/js/plugins/jsTree/jstree.min.js"></script>
<script src="/backend/js/plugins/daterangepicker/moment.min.js"></script>
<script src="/backend/js/plugins/daterangepicker/custom-daterangepicker.js"></script>

<script type="text/javascript">

$(document).ready(function() {

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

    var pro_type = "{{ $product['details']->pro_type == 3? 'ticket' : 'product' }}";
    grab_categories(pro_type);

    $('#desc_en, #desc_cn, #desc_my').wysihtml5({
        toolbar: {
            fa: true
        }
    });

    @if($product['details']->pro_type == 3 || $product['details']->pro_type == 4)
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
    @endif

    $('.wysihtml5-sandbox').css("resize", "vertical");

    $('.btn-form-submit').click(function(event) {

        $(':input').each(function(e) {
            if ($(this).hasClass('compulsary')) {
                if (!$(this).val()) {
                    if($(this).is('textarea')) {
                        swal("{{trans('localize.error')}}", "{{trans('localize.fieldrequired')}}\n{{trans('localize.description_english')}}", "error");
                    } else {
                        $(this).attr('placeholder', '{{trans('localize.fieldrequired')}}').css('border', '1px solid red').focus();
                    }
                    $(this).closest('.form-group').addClass('has-error');
                    e.preventDefault();
                    return false;
                }
                if($(this).attr('id') === 'selected_cats') {
                    if(JSON.parse($(this).val()).length == 0) {
                        swal("{{trans('localize.error')}}", "{{trans('localize.select_category_to_proceed')}}", "error");
                        e.preventDefault();
                        return false;
                    }
                }
            }
            $(this).css('border', '');
            $(this).closest('.form-group').removeClass('has-error');
        });

        {{-- var update = true;
        @if($main_category && $product['details']->pro_status == 1)
        if(JSON.parse($('#selected_cats').val())[0] != parseInt("{{ $main_category }}")) {
            update = false;
        }
        @endif

        if(!update) {
            swal({
                title: "{{trans('localize.sure')}}",
                text: "{!! trans('localize.product_category_change_disclaimer') !!}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#5cb85c",
                confirmButtonText: "{{trans('localize.proceed')}}",
                cancelButtonText : "{{trans('localize.cancel')}}",
                closeOnConfirm: true,
                html: true
                }, function(isConfirm){
                if (isConfirm) {
                    $('#spinner').show();
                    $("#edit_product").submit();
                }
            });
            event.preventDefault();
            return false;
        } --}}

        $("#edit_product").submit();
    });
});

function reset() {
    document.getElementById("edit_product").reset();
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

function grab_categories(type) {

    var response = '';
    if(type == 'ticket')
        response = '?ticket=required';
    else
        response = '?ticket=disable';

    $('.cat-desc').text('');
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
                swal("@lang('localize.swal_error')", "@lang('localize.Parent_category_already_selected')", "error");
                return false;
            }

            jQuery.each(catChilds, function(key, val) {
                if (jQuery.inArray( val, catSelected ) != -1) {
                    child = true;
                    return false;
                }
            });

            if (child) {
                swal("@lang('localize.swal_error')", "@lang('localize.Child_category_already_selected')", "error");
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
                        $('#showCats').append("<tr id='" + data.node.id + "'><td>" + data.node.text + "</td><td><a onClick='removeCat(\"" + data.node.id + "\"); return false;' class='btn btn-xs btn-danger pull-right'>@lang('localize.remove')</a></td></tr>");
                    });
                } else {
                    swal("@lang('localize.swal_error')", "@lang('localize.Maximum_3_category_only_can_be_selected')", "error");
                }
            } else {
                swal("@lang('localize.swal_error')", data.node.text + " @lang('localize.already_selected')", "error");
            }
        }
    });
}
</script>
@endsection
