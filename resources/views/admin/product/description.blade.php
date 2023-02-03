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

            @include('admin.product.nav-tabs', ['link' => 'description'])

            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="panel-body">
                        <div class="col-lg-12 nopadding">
                            <div class="col-sm-3 pull-right">
                                <button class="btn btn-block btn-primary" id="submit_top">{{trans('localize.update')}}</button>
                            </div><br><br><br>
                        </div>
                        <form class="form" id="edit_product" action="/admin/product/description" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="col-lg-12">
                            <div class="ibox float-e-margins well">
                                <div class="ibox-title">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#en">English</a></li>
                                        <li class=""><a href="#cn">Chinese - Simplified</a></li>
                                        <li class=""><a href="#cnt">Chinese - Traditional</a></li>
                                        <li><a href="#my">Bahasa</a></li>
                                    </ul>
                                </div>
                                <div class="ibox-content">
                                <div class="tab-content">
                                    <input type="hidden" name="mer_id" value="{{$product['details']->pro_mr_id}}" id="mer_id">
                                    <input type="hidden" name="pro_id" value="{{$product['details']->pro_id}}" id="pro_id">
                                    <div class="form-group tab-pane fade in active" id="en">
                                        <label class="control-label">{{trans('localize.description_english')}}<span style="color:red;">*</span></label>
                                        <textarea id="desc_en" placeholder="{{trans('localize.description_english')}}" class="form-control compulsary desc"  name='pro_desc_en' rows="100">{!! old('pro_desc_en', $product['details']->pro_desc_en) !!}</textarea>
                                    </div>
                                    <div class="form-group tab-pane fade" id="cn">
                                        <label class="control-label">{{trans('localize.description_chinese')}}</label>
                                        <textarea id="desc_cn" placeholder="{{trans('localize.description_chinese')}}" class="form-control desc" name='pro_desc_cn' rows="100">{!! old('pro_desc_cn', $product['details']->pro_desc_cn) !!}</textarea>

                                    </div>

                                    <div class="form-group tab-pane fade" id="cnt">
                                        <label class="control-label">{{trans('localize.description_chinese_traditional')}}</label>
                                        <textarea id="desc_cnt" placeholder="{{trans('localize.description_chinese_traditional')}}" class="form-control desc" name='pro_desc_cnt' rows="100">{!! old('pro_desc_cnt', $product['details']->pro_desc_cnt) !!}</textarea>
                                    </div>

                                    <div class="form-group tab-pane fade" id="my">
                                        <label class="control-label">{{trans('localize.description_bahasa')}}</label>
                                        <textarea id="desc_my" placeholder="{{trans('localize.description_bahasa')}}" class="form-control desc" name='pro_desc_my' rows="100">{!! old('pro_desc_my', $product['details']->pro_desc_my) !!}</textarea>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                        </form>
                        <div class="col-lg-5">
                            <div class="col-sm-6">
                                <button type="button" class="btn btn-block btn-default" onclick="reset()">{{trans('localize.reset_form')}}</button>
                            </div>
                            <div class="col-sm-6">
                                <button class="btn btn-block btn-primary" id="submit">{{trans('localize.update')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('style')
<link href="/backend/lib/wysiwyg/wysihtml5.min.css" rel="stylesheet">
<link href="/backend/css/plugins/sortable/sortable.css" rel="stylesheet">
<link href="/backend/css/plugins/jsTree/style.min.css" rel="stylesheet">
<link href="/backend/css/plugins/daterangepicker/custom-daterangepicker.css" rel="stylesheet">
<link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.8/summernote.css" rel="stylesheet">
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
<!-- plugins -->
<script src="/backend/lib/wysiwyg/wysihtml5x-toolbar.min.js"></script>
<script src="/backend/lib/wysiwyg/handlebars.runtime.min.js"></script>
<script src="/backend/lib/wysiwyg/wysihtml5.min.js"></script>

<script src="/backend/js/plugins/sortable/sortable.js"></script>
<script src="/backend/js/plugins/jsTree/jstree.min.js"></script>
<script src="/backend/js/plugins/daterangepicker/moment.min.js"></script>
<script src="/backend/js/plugins/daterangepicker/custom-daterangepicker.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.8/summernote.js"></script>
<script src="/backend/js/custom.js"></script>


<script type="text/javascript">

$(document).ready(function() {

    $(".nav-tabs a").click(function(){
        $(this).tab('show');
    });

    $('#desc_en,#desc_cn,#desc_cnt,#desc_my').summernote({

        height: 1000,
            callbacks: {
                onImageUpload: function(files) {
                    var mer_id =  parseInt($("#mer_id").val());
                    console.log(mer_id);
                    file_name = files[0].name;
                    var fileExtension = ['jpeg', 'jpg', 'png'];

                    if ($.inArray(file_name.split('.').pop().toLowerCase(), fileExtension) == -1) {
                        swal("{{trans('localize.error')}}", "{{trans('localize.imgError')}}", "error");
                        $(this).css('border', '1px solid red').focus().val('');
                    }else{
                        var textarea_id = $(this).attr('id');
                        sendFile(files[0], textarea_id, mer_id);
                    }
                }
            }
    });

    $('.wysihtml5-sandbox').css("resize", "vertical");

    $('#submit,#submit_top').click(function(event) {

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
            }
            $(this).css('border', '');
            $(this).closest('.form-group').removeClass('has-error');
        });

        $("#edit_product").submit();
    });
});

function reset() {
    document.getElementById("mer_edit_product").reset();
}

</script>
@endsection
