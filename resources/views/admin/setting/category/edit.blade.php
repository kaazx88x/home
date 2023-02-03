@extends('admin.layouts.master')
@section('title', 'Edit Category')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Edit')}} {{trans('localize.Category')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin">{{trans('localize.dashboard')}}</a>
            </li>
            <li>
                <a href="/admin/setting/category/listing/{{$category->parent_id}}">{{trans('localize.Category')}}</a>
            </li>
            <li class="active">
                <strong>{{trans('localize.Edit')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{trans('localize.General_Info')}}</h5>
                </div>

                <div class="ibox-content">
                    @include('admin.common.success')
                    @include('admin.common.errors')
                    <form id='form' class="form-horizontal" action='/admin/setting/category/edit/{{$category->id}}' method="POST" enctype="multipart/form-data">
                         {{ csrf_field() }}
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Name')}} ({{trans('localize.English')}})</label>
                            <div class="col-lg-10">
                                <input type="text" id="name-en" placeholder="{{trans('localize.Name')}} ({{trans('localize.English')}})" class="form-control compulsary" name='name_en'  value="{{empty(old('name_en'))?$category->name_en:old('name_en')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Name')}} ({{trans('localize.Chinese')}} - {{trans('localize.Simplified')}})</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{trans('localize.Name')}} ({{trans('localize.Chinese')}} - {{trans('localize.Simplified')}})" class="form-control" name='name_cn' value='{{empty(old('name_cn'))?$category->name_cn:old('name_cn')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Name')}} ({{trans('localize.Chinese')}} - {{trans('localize.Traditional')}})</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{trans('localize.Name')}} ({{trans('localize.Chinese')}} - {{trans('localize.Traditional')}})" class="form-control" name='name_cnt' value='{{empty(old('name_cnt'))?$category->name_cnt:old('name_cnt')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Name')}} ({{trans('localize.Malay')}})</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{trans('localize.Name')}} ({{trans('localize.Malay')}})" class="form-control" name='name_my' value='{{empty(old('name_my'))?$category->name_my:old('name_my')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Url_Slug')}}</label>
                            <div class="col-lg-10">
                                <input type="text" id="url-slug" placeholder="{{ trans('localize.Url_Slug') }}" class="form-control" name='url_slug' value="{{ empty(old('url_slug'))?$category->url_slug:old('url_slug')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Status')}} </label>
                            <div class="col-lg-10">
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="1" name="status" {{(empty(old('status')) && $category->status) || old('status')=='1'?'checked':''}} > <i></i> {{trans('localize.Active')}}
                                    </label>
                                </div>
                                <div class="i-checks">
                                    <label> <input type="radio" value="0" name="status" {{(empty(old('status')) && !$category->status) || old('status')=='0'?'checked':''}}> <i></i> {{trans('localize.Inactive')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Featured')}} </label>
                            <div class="col-lg-10">
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="1" name="featured" {{(empty(old('featured')) && $category->featured) || old('featured')=='1'?'checked':''}} > <i></i> {{trans('localize.Yes')}}
                                    </label>
                                </div>
                                <div class="i-checks">
                                    <label> <input type="radio" value="0" name="featured" {{(empty(old('featured')) && !$category->featured) || old('featured')=='0'?'checked':''}}> <i></i> {{trans('localize.No')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        @if($category->parent_id == 0 && !$category->default_ticket)
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.default_ticket')}} </label>
                            <div class="col-lg-10">
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="1" name="default_ticket" {{ old('default_ticket', $category->default_ticket) =='1'?'checked':''}} > <i></i> {{trans('localize.Yes')}}
                                    </label>
                                </div>
                                <div class="i-checks">
                                    <label> <input type="radio" value="0" name="default_ticket" {{ empty(old('default_ticket', $category->default_ticket)) ?'checked':''}}> <i></i> {{trans('localize.No')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="if_featured">
                            <div class="form-group">
                                <label class="col-lg-2 control-label">{{trans('localize.image')}} </label>
                                <div class="col-lg-10">
                                    <div class="col-xs-5 col-md-3">
                                        <img src="{{ env('IMAGE_DIR').'/category/image/'.$category->image }}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';" class="img-responsive">
                                    </div>
                                    <div class="col-xs-7 col-md-9">
                                        <input type="file" class="form-control" name="image">
                                        <label style="margin-top: 3px;"><span style="color:#337ab7;">{{trans('localize.Best_size_for_image')}} : 160 x 90</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">{{trans('localize.Banner')}} </label>
                                <div class="col-lg-10">
                                    <div class="col-xs-5 col-md-3">
                                        <img src="{{ env('IMAGE_DIR').'/category/banner/'.$category->banner }}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';" class="img-responsive">
                                    </div>
                                    <div class="col-xs-7 col-md-9">
                                        <input type="file" class="form-control" name="banner">
                                        <label style="margin-top: 3px;"><span style="color:#337ab7;">{{trans('localize.Best_size_for_banner')}} : 1022 x 244</span></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Wallet Type <span style="color:red;">*</span></label>
                            <div class="col-lg-10">
                                <select class="form-control" name="wallet_id">
                                    {{--  <option value="">-- Select Wallet Type --</option>  --}}
                                    @foreach($wallets as $wallet)
                                    <option value="{{ $wallet->id }}" {{ ($category->wallet_id == $wallet->id)? 'selected' : '' }} >{{ $wallet->name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="all_child" value="0">
                                <input type="checkbox" class="i-checks" name="all_child" value="1"> Set This Wallet Type For All Sub Categories
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                @if($edit_permission)
                                <button class="btn btn-sm btn-primary form_submit" type="button">{{trans('localize.Update')}}</button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('style')
<link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/iCheck/icheck.min.js"></script>

<script>
$(document).ready(function() {
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    $('.form_submit').click(function() {
        var isValid = true;

        $(':input').each(function(e) {
            if ($(this).hasClass('compulsary')) {
                if (!$(this).val()) {
                    $(this).attr('placeholder', '{{trans('localize.fieldrequired')}}').css('border', '1px solid red').focus();
                    isValid = false;
                    return false;
                }
            }

            $(this).css('border', '');
        });

        if (isValid) {
            $("#form").submit();
        }
    });

    $('#name-en').blur(function(){
        if($('#url-slug').val() == ''){
            var slug = $('#name-en').val().toLowerCase().trim().replace(/&/g, 'and').replace(/\//g, '-').replace(/ /g, '-');
            $('#url-slug').val(slug);
        }
    });

});
</script>
@endsection