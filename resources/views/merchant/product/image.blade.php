@extends('merchant.layouts.master')

@section('title', 'Product Image')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>@lang('localize.product_image')</h2>
        <ol class="breadcrumb">
            <li>
                @lang('localize.product')
            </li>
            <li>
                @lang('localize.image')
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

            @include('merchant.product.nav-tabs', ['link' => 'image'])

            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="panel-body">

                        @if($product['details']->pro_image_count < 5)
                        <div class="ibox float-e-margins">
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <div class="btn btn-primary btn-outline">@lang('localize.add_new_image_maximum')</div>
                                </a>
                            </div>

                            <div class="ibox-content" style="display:none; border-style:none;">
                                <div class="row">
                                    <form class="form-horizontal" action="{{ url( $route . '/product/image/add') }}" method="POST" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">@lang('localize.image_title')</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="title">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">@lang('localize.image_file')</label>
                                            <div class="col-sm-9">
                                                <span class='btn btn-default btn-block'><input type='file' name='file' id="file"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">@lang('localize.status')</label>
                                            <div class="col-sm-9">
                                                <select name='status' class='form-control'>
                                                    <option value='1'>@lang('localize.active')</option>
                                                    <option value='0'>@lang('localize.inactive')</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-9 col-sm-offset-2">
                                                <input type="hidden" name="pro_id" value="{{$pro_id}}">
                                                <input type="hidden" name="pro_status" value="{{$product['details']->pro_status}}">
                                                <button type="submit" class="btn btn-outline btn-primary pull-right">@lang('localize.add_image')</button>
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
                                    <h5>@lang('localize.product_image_drag_drop')</h5>
                                    @if($product['details']->pro_image_count >= 5)
                                    <div class="ibox-tools">
                                        <span class="label label-warning-light pull-right">@lang('localize.maximum_file_limit')</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="ibox-content">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <tbody class="sort">
                                                @foreach ($images as $image)
                                                <?php
                                                    $path = $image->image;
                                                    if (!str_contains($image->image, 'http://'))
                                                        $path = env('IMAGE_DIR').'/product/'.$product['details']->pro_mr_id.'/'.$image->image;
                                                ?>
                                                <tr class="text-left" id="{{$image->id}}">
                                                    <td>
                                                        <div class="feed-element">
                                                            <p class="pull-left">
                                                                {{--  <img alt="image" class="img-thumbnail" src="{{$path }}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';" style="height:115px; width:auto;">  --}}

                                                                <a href="{{ $path }}" data-gallery=""><img src="{{ $path }}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';" class="img-responsive img-thumbnail" style="height:115px; width:auto;"></a>
                                                            </p>
                                                            <div class="media-body">
                                                                @if($image->main)
                                                                    <h4 class="pull-right text-navy"> @lang('localize.main_image')</h4>
                                                                @endif
                                                                <h3>{{$image->title}}</h3>
                                                                <h4><span class="{{ ($image->status == 1)? 'text-navy' : 'text-warning' }}">{{ ($image->status == 1)? trans('localize.active') : trans('localize.inactive') }}</span></h4>
                                                                <small class="text-muted">@lang('localize.created_at') :
                                                                {{ Carbon\Carbon::createFromTimestamp(strtotime($image->created_at))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A') }}
                                                                </small>
                                                                <div class="actions">
                                                                    @if($image->main == 0 && $image->status == 1)
                                                                        <a class="btn btn-white btn-sm text-navy" href="{{ url( $route . '/product/image/set_main_image', [$image->id]) }}" ><span><i class="fa fa-toggle-on"></i> @lang('localize.main_image')</span></a>
                                                                    @endif

                                                                    @if ($image->status == 0)
                                                                        <a class="btn btn-white btn-sm text-navy" href="{{ url( $route . '/product/image/toggle_image_status', [$image->id]) }}">
                                                                            <span><i class="fa fa-refresh"></i>  @lang('localize.set_to_active')</span>
                                                                        </a>
                                                                    @elseif ($image->status == 1 && $image->main == 0)
                                                                        <a class="btn btn-white btn-sm text-warning" href="{{ url( $route . '/product/image/toggle_image_status', [$image->id]) }}">
                                                                            <span><i class="fa fa-refresh"></i> @lang('localize.set_to_inactive')</span>
                                                                        </a>
                                                                    @endif

                                                                    @if ($image->main == 0 && count($images) > 1)
                                                                        <button type="button" class="btn btn-danger btn-circle btn-lg btn-outline pull-right" data-id="{{ $image->id }}" mer-id="{{$product['details']->pro_mr_id}}" style="margin-right:20px;" data-action="delete_image"><i class="fa fa-trash"></i></button>
                                                                    @endif

                                                                    <button type="button" class="btn btn-white btn-sm text-info" data-toggle="modal" data-id="{{ $image->id  }}" mer-id ="{{$product['details']->pro_mr_id}}" data-post="data-php" data-action="edit_image"><span><i class="fa fa-edit"></i> @lang('localize.edit_image')</span></button>

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
                confirmButtonText: "{{trans('localize.yes')}}",
                cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function(){
                var url = "{{ url( $route . '/product/image/delete') }}/"  + this_id;
                window.location.href = url;
            });
            }
        });

        $( ".sort" ).sortable({
            update : function () {
                var order = $(this).sortable('toArray').toString();
                var pass = 'newOrder='+order;
                console.log(pass);

                 $.ajax({
                    type: 'get',
                    data: pass,
                    url: "{{ url( $route . '/product/image/save_order') }}",
                    beforeSend : function() {
                        $('#spinner').show();
                    },
                    success: function () {
                        $('#spinner').hide();
                    }
                });
            }
        });

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
