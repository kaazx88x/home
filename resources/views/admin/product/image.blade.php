@extends('admin.layouts.master')

@section('title', 'Product Image')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.product')}} {{trans('localize.image')}}</h2>
        <ol class="breadcrumb">
            <li>
                {{trans('localize.product')}}
            </li>
            <li>
                {{trans('localize.image')}}
            </li>
            <li class="active">
                <strong>{{$product['details']->pro_id}} - {{$product['details']->pro_title_en}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    @include('admin.common.error')
    @include('admin.common.errors')
    @include('admin.common.success')

    <div class="row">
        <div class="tabs-container">

            @include('admin.product.nav-tabs', ['link' => 'image'])

            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="panel-body">

                        @if($product['details']->pro_image_count < 5)
                        <div class="ibox float-e-margins">
                            <div class="ibox-tools">
                                <a class="collapse-link nolinkcolor">
                                    <div class="btn btn-primary btn-outline">@lang('localize.add_new_image_maximum')</div>
                                </a>
                            </div>

                            <div class="ibox-content" style="display:none; border-style:none;">
                                <div class="row">
                                    <form class="form-horizontal" action='/admin/product/image/add' method="POST" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">{{trans('localize.image')}} {{trans('localize.Title')}}</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="title">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">{{trans('localize.image')}} {{trans('localize.File')}}</label>
                                            <div class="col-sm-9">
                                                <span class='btn btn-default btn-block'><input type='file' name='file' id="file"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">{{trans('localize.Status')}}</label>
                                            <div class="col-sm-9">
                                                <select name='status' class='form-control'>
                                                    <option value='1'>{{trans('localize.Active')}}</option>
                                                    <option value='0'>{{trans('localize.Inactive')}}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-9 col-sm-offset-2">
                                                <input type="hidden" name="mer_id" value="{{$mer_id}}">
                                                <input type="hidden" name="pro_id" value="{{$pro_id}}">
                                                <input type="hidden" name="pro_status" value="{{$product['details']->pro_status}}">
                                                <button type="submit" class="btn btn-outline btn-primary pull-right">{{trans('localize.add_image')}}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="col-lg-12">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <h5>{{trans('localize.product_image_drag_drop')}} </h5>
                                    @if($product['details']->pro_image_count >= 5)
                                    <div class="ibox-tools">
                                        <span class="label label-warning-light pull-right">{{trans('localize.maximum_file_limit')}}</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="ibox-content">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <tbody class="sort">
                                                @foreach ($images as $image)
                                                <?php
                                                    //$images = (!empty($product['details']->pro_Img)) ? explode("/**/", $product['details']->pro_Img) : array();
                                                    $path = $image->image;
                                                    if (!str_contains($image->image, 'http://'))
                                                        $path = env('IMAGE_DIR').'/product/'.$product['details']->pro_mr_id.'/'.$image->image;
                                                ?>
                                                <tr class="text-left" id="{{$image->id}}">
                                                    {{-- <!--<td><input type="text" class="form-control" value="{{ $image->title }}"></td>
                                                    <td>{{$image->title}}</td>
                                                    <td><img src="{{$path.$image->image }}" height="50" width="50"></td>
                                                    <td>
                                                        <span class="{{ ($image->status == 1)? 'text-navy' : 'text-warning' }}">{{ ($image->status == 1)? 'Active' : 'Inactive' }}</span>
                                                    </td>
                                                    <td><i class="{{ ($image->main == 1)? 'fa fa-check' : '' }} text-navy"></i></td>
                                                    <td width="10%">
                                                        <p>
                                                            @if($image->main == 0 && $image->status == 1)
                                                            <a class="btn btn-white btn-block btn-sm text-navy" href="/admin/product/image/set_main_image/{{$image->id}}" ><span><i class="fa fa-toggle-on"></i> Main Image</span></a>
                                                            @endif
                                                        </p>
                                                        <p>
                                                            @if ($image->status == 0)
                                                                <a class="btn btn-white btn-block btn-sm text-navy" href="/admin/product/image/toggle_image_status/{{$image->id}}"><span><i class="fa fa-refresh"></i>  Set To Active</span></a>
                                                            @elseif ($image->status == 1 && $image->main == 0)
                                                                <a class="btn btn-white btn-block btn-sm text-warning" href="/admin/product/image/toggle_image_status/{{$image->id}}"><span><i class="fa fa-refresh"></i> Set to Inactive</span></a>
                                                            @endif
                                                        </p>
                                                        <p>
                                                            @if ($image->main == 0)
                                                                <a class="btn btn-white btn-block btn-sm text-danger" href="/admin/product/image/delete/{{$image->id}}/{{$product['details']->pro_mr_id}}"><span><i class="fa fa-trash"></i>  Delete Image</span></a>
                                                            @endif
                                                        </p>
                                                    </td>--> --}}

                                                    <td>
                                                        <div class="feed-element">
                                                            <p class="pull-left">
                                                                {{--  <img alt="image" class="img-thumbnail" src="{{$path }}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';" style="height:115px; width:auto;">  --}}

                                                                <a href="{{ $path }}" data-gallery=""><img src="{{ $path }}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';" class="img-responsive img-thumbnail" style="height:115px; width:auto;"></a>
                                                            </p>
                                                            <div class="media-body">
                                                                @if($image->main)<h4 class="pull-right text-navy">{{trans('localize.Main')}} {{trans('localize.image')}}</h4>@endif
                                                                <h3>{{$image->title}}</h3>
                                                                <h4><span class="{{ ($image->status == 1)? 'text-navy' : 'text-warning' }}">{{ ($image->status == 1)? trans('localize.Active') : trans('localize.Inactive') }}</span></h4>
                                                                <small class="text-muted">{{trans('localize.created_at')}} : {{ Carbon\Carbon::createFromTimestamp(strtotime($image->created_at))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A') }}</small>

                                                                <div class="actions">
                                                                    @if($edit_permission)
                                                                        @if($image->main == 0 && $image->status == 1)
                                                                            <a class="btn btn-white btn-sm text-navy" href="/admin/product/image/set_main_image/{{$image->id}}/{{$mer_id}}" ><span><i class="fa fa-toggle-on"></i> {{trans('localize.Main')}} {{trans('localize.image')}}</span></a>
                                                                        @endif

                                                                        @if ($image->status == 0)
                                                                            <a class="btn btn-white btn-sm text-navy" href="/admin/product/image/toggle_image_status/{{$image->id}}"><span><i class="fa fa-refresh"></i>  {{trans('localize.set_to_active')}}</span></a>
                                                                        @elseif ($image->status == 1 && $image->main == 0)
                                                                            <a class="btn btn-white btn-sm text-warning" href="/admin/product/image/toggle_image_status/{{$image->id}}"><span><i class="fa fa-refresh"></i> {{trans('localize.set_to_inactive')}}</span></a>
                                                                        @endif

                                                                        @if ($image->main == 0 && count($images) > 1)
                                                                            <button type="button" class="btn btn-danger btn-circle btn-lg btn-outline pull-right" data-id="{{$image->id}}" mer-id="{{$product['details']->pro_mr_id}}" data-action="delete_image"><i class="fa fa-trash"></i></button>
                                                                        @endif

                                                                        <button type="button" class="btn btn-white btn-sm text-info" data-toggle="modal" data-id="{{ $image->id  }}" mer-id ="{{$product['details']->pro_mr_id}}" data-post="data-php" data-action="edit_image"><span><i class="fa fa-edit"></i> {{trans('localize.Edit')}} {{trans('localize.image')}}</span></button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><br>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('style')
<link href="/backend/lib/wysiwyg/wysihtml5.min.css" rel="stylesheet">
<link href="/backend/css/plugins/sortable/sortable.css" rel="stylesheet">
<link href="/backend/css/plugins/blueimp/css/blueimp-gallery.min.css" rel="stylesheet">
@endsection

@section('script')
<!-- plugins -->
<script src="/backend/lib/wysiwyg/wysihtml5x-toolbar.min.js"></script>
<script src="/backend/lib/wysiwyg/handlebars.runtime.min.js"></script>
<script src="/backend/lib/wysiwyg/wysihtml5.min.js"></script>
<script src="/backend/js/plugins/sortable/sortable.js"></script>
<script src="/backend/js/plugins/blueimp/jquery.blueimp-gallery.min.js"></script>
<script src="/backend/js/custom.js"></script>

<script type="text/javascript">
    $(document).ready(function(){

        $('button').on('click', function() {
            var this_id = $(this).attr('data-id');
            var mer_id = $(this).attr('mer-id');
            var this_action = $(this).attr('data-action');

            if (this_action == 'edit_image') {
                edit_product_image(mer_id,this_id);
            } else if (this_action == 'delete_image') {
                swal({
                title: "{{trans('localize.sure')}}",
                text: "{{trans('localize.confirm_delete_image')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d9534f",
                confirmButtonText: "{{trans('localize.yes_delete_it')}}",
				cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: false
            }, function(){
                var url = '/admin/product/image/delete/' + this_id + '/' + mer_id;
                window.location.href = url;
            });
            }
        });

        $( ".sort" ).sortable({
            update : function () {
                var order = $(this).sortable('toArray').toString();
                var pass = 'newOrder='+order;

                 $.ajax({
                    type: 'get',
                    data: pass,
                    url: '/admin/product/image/save_order',
                    beforeSend : function() {
                        $('#spinner').show();
                    },
                    success: function () {
                        $('#spinner').hide();
                    }
                });
            }
        });

        // $('#addImg').click(function() {
        //     var id = $('#aid').val();
        //     var count_id = $('#count').val();
        //     if (count_id < 5){
        //         $('#count').val(parseInt(count_id) + 1);
        //         $('#divTxt').append("<div id='row" + count_id + "'><span class='btn btn-default'><input type='file' id='file" + count_id + "' name='file[]'/></span>&nbsp;<a onClick='removeDiv(\"#row" + count_id + "\"); return false;' class='btn btn-md btn-default'>Remove</a><br/><br/></div>");
        //         id = (id - 1) + 2;
        //         $('#aid').val(id);
        //     }
        // });

        $(document).on('change', 'input[type=file]', function() {
            var fileSize = $('#file')[0].files[0].size;
            var fileExtension = ['jpeg', 'jpg', 'png'];
            var file = $('#file');
            if (fileSize > 1000000) {
                swal("{{trans('localize.error')}}", "{{trans('localize.imgSizeError')}}", "error");
                $('#' + this.id).val('');
            } else if ($.inArray(file.val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                swal("{{trans('localize.error')}}", "{{trans('localize.imgError')}}", "error");
                file.val('');
                file.focus();
                file.css('border', '1px solid red');
                return false;
            }
        });

    });
</script>
@endsection
