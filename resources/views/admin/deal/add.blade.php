@extends('admin.layouts.master')

@section('title', 'Add Product')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Add New Deal</h2>
        <ol class="breadcrumb">
            <li>
                Deal
            </li>
            <li class="active">
                <strong>Add Deal</strong>
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
                    <h5>Deal Info</h5>
                </div>

                <div class="ibox-content">
                    @include('admin.common.errors')
                    <form class="form-horizontal" id="add_product" action='/admin/deal/add' method="POST" enctype="multipart/form-data">
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
                             <label class="col-lg-2 control-label">Title <span style="color:red;">*</span></label>
                             <div class="col-lg-10">
                                 <input type="text" placeholder="Deal Title" class="form-control" id="title" name='title' >
                             </div>
                         </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Category <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <select class="form-control" id="pro_category" name="deal_category">
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
                                <select class="form-control" id="pro_maincategory" name="deal_maincategory">
                                    <option value="0">-- Select Category To Proceed --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Sub Category <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <select class="form-control" id="pro_subcategory" name="deal_subcategory">
                                    <option value="0">-- Select Main Category To Proceed --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Second Sub Category <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <select class="form-control" id="pro_secsubcategory" name="deal_secsubcategory">
                                    <option value="0">-- Select Second Sub Category To Proceed --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Original Price <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <input type="number"  placeholder="Original Price" class="form-control" id='pro_price' name='pro_price'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Discounted Price</label>
                            <div class="col-lg-10">
                                <input type="number" placeholder="Discounted Price" class="form-control" id='pro_dprice' name='pro_dprice'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Start Date</label>
                            <div class="col-lg-10">
                                <div class="form-group">
                                    <label class="font-noraml">Range select</label>
                                    <div class="input-daterange input-group" id="datepicker">
                                        <input type="text" class="input-sm form-control" name="start" value="05/14/2014">
                                        <span class="input-group-addon">to</span>
                                        <input type="text" class="input-sm form-control" name="end" value="05/22/2014">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">End Date</label>
                            <div class="col-lg-10">
                                {{-- <input type="number"  placeholder="Shipping Amount" class="form-control" id='pro_shipping' name='pro_shipping'> --}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Deal Expiry Date</label>
                            <div class="col-lg-10">
                                {{-- <input type="number"  placeholder="Shipping Amount" class="form-control" id='pro_shipping' name='pro_shipping'> --}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Description <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <textarea id="desc_en" placeholder="Deal Description" class="form-control"  id='pro_desc_en' name='pro_desc_en'></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Minimum Deal Limit</label>
                            <div class="col-lg-10">
                                <input type="number" placeholder="Numbers Only" class="form-control" id='pro_dprice' name='pro_dprice'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Maximum Deal Limit</label>
                            <div class="col-lg-10">
                                <input type="number" placeholder="Numbers Only" class="form-control" id='pro_dprice' name='pro_dprice'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Meta Keywords <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" placeholder="Enter meta keyword" name='metakeyword' id='metakeyword'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Meta Description <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" placeholder="Enter meta description" name='metadescription' id='metadescription'>
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
                        <div class="form-group">
                            <div class="col-lg-2 pull-right">
                                <button class="btn btn-block btn-primary" id="submit">Add Product</button>
                            </div>
                            <div class="col-lg-2 pull-right">
                                <button class="btn btn-block btn-default">Reset Form</button>
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
<link href="/backend/css/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet">
@endsection

@section('script')
<!-- plugins -->
<script src="/backend/lib/wysiwyg/wysihtml5x-toolbar.min.js"></script>
<script src="/backend/lib/wysiwyg/handlebars.runtime.min.js"></script>
<script src="/backend/lib/wysiwyg/wysihtml5.min.js"></script>
<script src="/backend/js/custom.js"></script>
<script src="/backend/js/plugins/daterangepicker/daterangepicker.js"></script>

<script type="text/javascript">
    $(document).ready(function(){
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

        $('#pro_size').change(function() {
            var sdata = $('#si').val();
            var val = $(this).val();

            load_size(val, sdata);
        });

        $('#pro_color').change(function() {
            var cdata = $('#co').val();
            var val = $(this).val();

            load_color(val, cdata);
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

        $('#Delivery_Days').keyup(function() {
            if (this.value.match(/[^0-9-()\s]/g))
            {
                this.value = this.value.replace(/[^0-9-()\s]/g, '');
            }
        });

        $('#submit').click(function() {
            // check validation field
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

            if($('#pro_quantity').val() == '') {
                $('#pro_quantity').attr('placeholder', 'Please enter product quantity!');
                $('#pro_quantity').css('border', '1px solid red');
                $('#pro_quantity').focus();
                return false;
            } else {
                $('#pro_quantity').css('border', '');
            }

            if($('#pro_credit').val() == '') {
                $('#pro_credit').attr('placeholder', 'Please enter product Mei Point value!');
                $('#pro_credit').css('border', '1px solid red');
                $('#pro_credit').focus();
                return false;
            } else {
                $('#pro_credit').css('border', '');
            }

            if($('#pro_price').val() == '') {
                $('#pro_price').attr('placeholder', 'Please enter product price!');
                $('#pro_price').css('border', '1px solid red');
                $('#pro_price').focus();
                return false;
            } else {
                $('#pro_price').css('border', '');
            }

            if($('#desc_en').val() == '') {
                swal("Product Description is required!", "Please fill in the description", "error");
                $('#desc_en').focus();
                return false;
            }

            if($('#metakeyword').val() == '') {
                $('#metakeyword').attr('placeholder', 'This field is required!');
                $('#metakeyword').css('border', '1px solid red');
                $('#metakeyword').focus();
                return false;
            } else {
                $('#metakeyword').css('border', '');
            }

            if($('#metadescription').val() == '') {
                $('#metadescription').attr('placeholder', 'This field is required!');
                $('#metadescription').css('border', '1px solid red');
                $('#metadescription').focus();
                return false;
            } else {
                $('#metadescription').css('border', '');
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

            $("#add_product").submit();
        });

        $('.input-daterange').datepicker({
        });
    });
</script>
@endsection
