@extends('admin.layouts.master')

@section('title', 'Add Auction')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Add Auction Product</h2>
        <ol class="breadcrumb">
            <li>
                Auction
            </li>
            <li class="active">
                <strong>Add Auction</strong>
            </li>
        </ol>
    </div>
</div>
@if (isset($error))
    <div class="alert alert-danger">
        {{ $error }}
    </div>
@endif
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Auction Info</h5>
                </div>

                <div class="ibox-content">
                    @include('admin.common.errors')
                    <form class="form-horizontal" id="add_auction" action='/admin/auction/add' method="POST" enctype="multipart/form-data">
                         {{ csrf_field() }}
                         <div class="form-group">
                             <label class="col-lg-2 control-label">Merchant <span style="color:red;">*</span></label>
                             <div class="col-lg-10">
                                 <select class="form-control" id="mer_id" name="mer_id" >
                                     	<option value="0">-- Select Merchant --</option>
                                         @foreach ($merchants as $merchant)
                                             <option value="{{$merchant->mer_id}}">{{$merchant->mer_fname}}</option>
                                         @endforeach
                                </select>
                             </div>
                         </div>
                         <div class="form-group">
                             <label class="col-lg-2 control-label">Store <span style="color:red;">*</span></label>
                             <div class="col-lg-10">
                                 <select class="form-control" id="stor_id" name="stor_id" >
                                     	<option value="0">-- Please Select Merchant First --</option>
                                </select>
                             </div>
                         </div>
                         <div class="form-group">
                            <label class="col-lg-2 control-label">Product Type<span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <select class="form-control" name="pro_type">
                                    <option value="normal_product">Normal Product</option>
                                    <option value="rookie_product">Rookie Product</option>
                                    <option value="demo_product">Demo Product</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Product Status <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <select class="form-control" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Title (English) <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="Product Title (English)" class="form-control" id="pro_title_en" name='pro_title_en' >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Title (Chinese)</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="Product Title (Chinese)" class="form-control" name='pro_title_cn'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Title (Bahasa)</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="Product Title (Bahasa)" class="form-control" name='pro_title_my'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Category <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <select class="form-control" id="pro_category" name="pro_category">
                                    <option value="0">-- Select Category --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{$category->mc_id}}">{{$category->mc_name_en}}</option>
                                    @endforeach
                               </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Main Category <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <select class="form-control" id="pro_maincategory" name="pro_maincategory">
                                    <option value="0">-- Select Category To Proceed --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Sub Category <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <select class="form-control" id="pro_subcategory" name="pro_subcategory">
                                    <option value="0">-- Select Main Category To Proceed --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Second Sub Category <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <select class="form-control" id="pro_secsubcategory" name="pro_secsubcategory">
                                    <option value="0">-- Select Second Sub Category To Proceed --</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Original Price <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <input type="number"  placeholder="Numbers Only" class="form-control" id='original_price' name='original_price'>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Auction Price <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <input type="number"  placeholder="Numbers Only" class="form-control" id='auction_price' name='auction_price'>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Game Point Per Bid <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <input type="number"  placeholder="Numbers Only" class="form-control" id='game_point' name='game_point'>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Client Game Point Target <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <input type="number"  placeholder="Numbers Only" class="form-control" id='maxbid' name='maxbid'>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Bot Game Point Target <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <input type="number"  placeholder="Numbers Only" class="form-control" id='bot_maxbid' name='bot_maxbid'>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Auction Start-End <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <input type="text" id="daterange" name="daterange" class="form-control"/>
                                <input type="hidden" name="start" id="start">
                                <input type="hidden" name="end" id="end">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label text-nowrap">Shipping Information</label>
                            <div class="col-lg-10">
                                <textarea class="form-control" rows="5" style="resize: none;" name="shipping_info"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Description (English) <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <textarea id="desc_en" placeholder="Description in English" class="form-control"  id='pro_desc_en' name='pro_desc_en'></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Description (Chinese)</label>
                            <div class="col-lg-10">
                                <textarea id="desc_cn" placeholder="Description in Chinese" class="form-control" name='pro_desc_cn'></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Description (Bahasa)</label>
                            <div class="col-lg-10">
                                <textarea id="desc_my" placeholder="Description in Bahasa" class="form-control" name='pro_desc_my'></textarea>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-lg-2 control-label">Meta Keywords</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" placeholder="Enter meta keyword" name='metakeyword'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Meta Description</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" placeholder="Enter meta description" name='metadescription'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Product Image <span style="color:red;">*</span></label>
                            <div class="col-lg-10" id="img_upload">
                                <span class="btn btn-default"><input type='file' id='file' name='file[]'/></span>
                                <span class="label label-info label-xs" >This will be the default image.</span>
                                <br/><br/>
                                <div id="divTxt"></div>
                                <a class="btn btn-sm btn-primary btn-grad" id="addImg">Add More</a>
                                <input type="hidden" id="aid" value="1">
                                <input type="hidden" id="count" name="count" value="0">
                            </div>
                        </div>


                        <input type="hidden" name="min_bids" value="0">
                        <input type="hidden" name="shippingfee" value="0">
                        <input type="hidden" name="bid_increment" value="0">

                        <div class="form-group">
                            <div class="col-lg-2 pull-right">
                                <button class="btn btn-block btn-primary" id="submit">Add Auction</button>
                            </div>
                            <div class="col-lg-2 pull-right">
                                <button class="btn btn-block btn-default" id="reset">Reset Form</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@stop

@section('style')
<link href="/backend/lib/wysiwyg/wysihtml5.min.css" rel="stylesheet">
<link href="/backend/css/plugins/daterangepicker/custom-daterangepicker.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/daterangepicker/moment.min.js"></script>
<script src="/backend/js/plugins/daterangepicker/custom-daterangepicker.js"></script>
<script src="/backend/lib/wysiwyg/wysihtml5x-toolbar.min.js"></script>
<script src="/backend/lib/wysiwyg/handlebars.runtime.min.js"></script>
<script src="/backend/lib/wysiwyg/wysihtml5.min.js"></script>
<script src="/backend/js/custom.js"></script>

<script type="text/javascript">

    $(document).ready(function(){

        $('input[name="daterange"]').daterangepicker({
            timePicker: true,
            timePickerIncrement: 1,
            locale: {
                format: 'DD-MM-YYYY HH:mm:ss'
            }
        });

        $('#daterange').change(function() {
            $('#start').val($("#daterange").data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss'));
            $('#end').val($("#daterange").data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss'));
        });

        // Load Selection
        $('#pro_category').change(function() {
            var update_input = '#pro_maincategory';
            var val = $(this).val();

            load_maincategory(update_input, val);
            $('#pro_subcategory').html('<option>-- Select Main Category To Proceed --</option>');
            $('#pro_secsubcategory').html('<option>-- Select Sub Category To Proceed --</option>');
        });

        $('#pro_maincategory').change(function() {
            var update_input = '#pro_subcategory';
            var val = $(this).val();

            load_subcategory(update_input, val);
            $('#pro_secsubcategory').html('<option>-- Select Sub Category To Proceed --</option>');
        });

        $('#pro_subcategory').change(function() {
            var update_input = '#pro_secsubcategory';
            var val = $(this).val();

            load_secsubcategory(update_input, val);
        });

        $('#mer_id').change(function() {
            var update_input = '#stor_id';
            var val = $(this).val();

            load_merchant_store(update_input, val);
        });

        $('#desc_en, #desc_cn, #desc_my').wysihtml5({
            toolbar: {
                fa: true
            }
        });
        $('.wysihtml5-sandbox').css("resize", "vertical");

        $('#addImg').click(function() {
            var id = $('#aid').val();
            var count_id = $('#count').val();
            if (count_id < 4){
                $('#count').val(parseInt(count_id) + 1);
                $('#divTxt').append("<div id='row" + count_id + "'><span class='btn btn-default'><input type='file' id='file" + count_id + "' name='file[]'/></span>&nbsp;<a onClick='removeDiv(\"#row" + count_id + "\"); return false;' class='btn btn-md btn-default'>Remove</a><br/><br/></div>");
                id = (id - 1) + 2;
                $('#aid').val(id);
            }
        });

        $(document).on('change', 'input[type=file]', function() {
            var fileSize = this.files[0].size;
            if (fileSize > 1000000) {
                swal("Sorry!", "File selected exceed maximum size 1Mb!", "error");
                $('#' + this.id).val('');
            }
        });

        $('#submit').click(function() {
            // check validation field
            if($('#mer_id').val() == 0) {
                $('#mer_id').attr('placeholder', 'Please select Merchant!');
                $('#mer_id').css('border', '1px solid red');
                $('#mer_id').focus();
                return false;
            } else {
                $('#mer_id').css('border', '');
            }

            if($('#stor_id').val() == 0) {
                $('#stor_id').attr('placeholder', 'Please select stor!');
                $('#stor_id').css('border', '1px solid red');
                $('#stor_id').focus();
                return false;
            } else {
                $('#stor_id').css('border', '');
            }

            if($('#pro_title_en').val() == '') {
                $('#pro_title_en').attr('placeholder', 'This field is required!');
                $('#pro_title_en').css('border', '1px solid red');
                $('#pro_title_en').focus();
                return false;
            } else {
                $('#pro_title_en').css('border', '');
            }

            if($('#pro_category').val() == 0) {
                $('#pro_category').attr('placeholder', 'Please select product category!');
                $('#pro_category').css('border', '1px solid red');
                $('#pro_category').focus();
                return false;
            } else {
                $('#pro_category').css('border', '');
            }

            if($('#pro_maincategory').val() == 0) {
                $('#pro_maincategory').attr('placeholder', 'Please select product main category!');
                $('#pro_maincategory').css('border', '1px solid red');
                $('#pro_maincategory').focus();
                return false;
            } else {
                $('#pro_maincategory').css('border', '');
            }

            if($('#pro_subcategory').val() == 0) {
                $('#pro_subcategory').attr('placeholder', 'Please select product sub category!');
                $('#pro_subcategory').css('border', '1px solid red');
                $('#pro_subcategory').focus();
                return false;
            } else {
                $('#pro_subcategory').css('border', '');
            }

            if($('#pro_secsubcategory').val() == 0) {
                $('#pro_secsubcategory').attr('placeholder', 'Please select product seecond sub category!');
                $('#pro_secsubcategory').css('border', '1px solid red');
                $('#pro_secsubcategory').focus();
                return false;
            } else {
                $('#pro_secsubcategory').css('border', '');
            }

            if($('#original_price').val() == '') {
                $('#original_price').attr('placeholder', 'Please enter Original price!');
                $('#original_price').css('border', '1px solid red');
                $('#original_price').focus();
                return false;
            } else {
                $('#original_price').css('border', '');
            }

            if($('#auction_price').val() == '') {
                $('#auction_price').attr('placeholder', 'Please enter Auction price!');
                $('#auction_price').css('border', '1px solid red');
                $('#auction_price').focus();
                return false;
            } else {
                $('#auction_price').css('border', '');
            }

            if($('#game_point').val() == '') {
                $('#game_point').attr('placeholder', 'Please enter Game Point per Bid!');
                $('#game_point').css('border', '1px solid red');
                $('#game_point').focus();
                return false;
            } else {
                $('#game_point').css('border', '');
            }

            if($('#maxbid').val() == '') {
                $('#maxbid').attr('placeholder', 'Please enter Client Game Point Target!');
                $('#maxbid').css('border', '1px solid red');
                $('#maxbid').focus();
                return false;
            } else {
                $('#maxbid').css('border', '');
            }

            if($('#bot_maxbid').val() == '') {
                $('#bot_maxbid').attr('placeholder', 'Please enter Bot Game Point Target!');
                $('#bot_maxbid').css('border', '1px solid red');
                $('#bot_maxbid').focus();
                return false;
            } else {
                $('#bot_maxbid').css('border', '');
            }

            if($('#desc_en').val() == '') {
                swal("Product Description is required!", "Please fill Description English", "error");
                $('#desc_en').focus();
                return false;
            }

            if($('#start').val() == '') {
                $('#start').attr('placeholder', 'Please select Auction Start!');
                $('#start').css('border', '1px solid red');
                $('#start').focus();
                return false;
            } else {
                $('#start').css('border', '');
            }

            if($('#end').val() == '') {
                $('#end').attr('placeholder', 'Please select Auction End!');
                $('#end').css('border', '1px solid red');
                $('#end').focus();
                return false;
            } else {
                $('#end').css('border', '');
            }

            var file = $('#file');
            var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
            if(file.val() == "") {
                swal("Product Image Required!", "Please select at least one product image", "error");
                file.focus();
                file.css('border', '1px solid red');
                return false;
            } else if ($.inArray(file.val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                swal("Image format!", "Invalid image format \nOnly jpeg, jpg, png, gif and bmp image accepted ", "error");
                file.focus();
                file.css('border', '1px solid red');
                return false;
            } else if (($('#file')[0].files[0].size) >= 1000000){
                swal("Image size!", "Image size must not greater than 1 Mb", "error");
                file.focus();
                file.css('border', '1px solid red');
                return false;
            } else {
                file.css('border', '');
            }

            $("#add_auction").submit();
        });
    });
</script>
@endsection
