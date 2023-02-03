<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="text-center">@lang('localize.edit_image')</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12 text-center">
            <?php
                $path = $image->image;
                if (!str_contains($image->image, 'http://'))
                    $path = env('IMAGE_DIR').'/product/'.$mer_id.'/'.$image->image;
            ?>
            <img alt="image" class="img-circle" src="{{$path}}" width="140px" height="130px" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';"><br><br>
        </div>
    </div>
    <form class="form-horizontal" action='/product_image/edit' method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
        <div class="form-group">
            <label class="col-sm-3 control-label">@lang('localize.image_title')</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" name="title" value="{{$image->title}}">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">@lang('localize.image_file')</label>
            <div class="col-sm-9">
                <span class='btn btn-default btn-block'><input type='file' name='file'></span>
                <small>@lang('localize.or_choose_new_image_to_replace')</small>
            </div>
        </div>

        @if($image->main == 0)
        <div class="form-group">
            <label class="col-sm-3 control-label">@lang('localize.status')</label>
            <div class="col-sm-9">
                <select name='status' class='form-control'>
                    <option value='1' {{($image->status == 1)? 'selected': ''}}>@lang('localize.active')</option>
                    <option value='0' {{($image->status == 0)? 'selected': ''}}>@lang('localize.inactive')</option>
                </select>
            </div>
        </div>
        @else
        <input type="hidden" name="status" value="1">
        @endif
        <div class="form-group">
            <div class="col-sm-9 col-sm-offset-3">
                <input type="hidden" name="id" value="{{$image->id}}">
                <input type="hidden" name="mer_id" value="{{$mer_id}}">
                <input type="hidden" name="old_image" value="{{$image->image}}">
                <button type="submit" class="btn btn-outline btn-primary pull-right">@lang('localize.edit_image')</button>
            </div>
        </div>
    </form>
</div>