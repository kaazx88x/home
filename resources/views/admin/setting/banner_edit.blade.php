@extends('admin.layouts.master')
@section('title', 'Edit Banner')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Edit')}} {{trans('localize.Banner')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin/setting/banner">{{trans('localize.Banner')}}</a>
            </li>
            <li>
                <a href="/admin/setting/banner/manage/{{$banner->type}}">{{trans('localize.manage')}}</a>
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
                    <form id="form" class="form-horizontal" action='/admin/setting/banner/edit/{{$banner->bn_id}}' method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                       <div class="form-group">
                           <label class="col-lg-2 control-label">{{trans('localize.Title')}}</label>
                           <div class="col-lg-10">
                               <input type="text" placeholder="{{trans('localize.Title')}}" class="form-control compulsary" name='title'  value='{{empty(old('title'))?$banner->bn_title:old('title')}}'>
                           </div>
                       </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.image')}}</label>
                            <div class="col-lg-10">
                                <div class="img_old">
                                    @if($edit_permission)
                                    <button type="button" class="btn btn-primary btn-sm img_replace"><i class="fa fa-recycle"></i> {{trans('localize.Replace_Banner_Image')}}</button>
                                    @endif
                                    {{-- <img src="{{ url('images/banner/'.$banner->bn_img) }}" class="img-responsive"> --}}
                                    <img src="{{ env('IMAGE_DIR') . '/banner/' . $banner->bn_img }}" class="img-responsive">
                                </div>
                                <div class="img_new" style="display:none">
                                    <input type="file" placeholder="{{trans('lcoalize.Banner_Image')}}" class="form-control" name='file'>
                                    <label style="margin-top: 3px;"><span style="color:#337ab7;">{{trans('localize.Best_resolution_for_mobile_banner_ratio')}} - (2.5 : 1) Eg: 650 x 260, 1300 x 520</span></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Media_Type')}}</label>
                            <div class="col-lg-10">
                                <select class="form-control" id="media_type" name="media_type">
                                    <option value="1" {{ (old('media_type', $banner->bn_type) == '1') ? 'selected' : '' }}>{{trans('localize.image')}}</option>
                                    <option value="2" {{ (old('media_type', $banner->bn_type) == '2') ? 'selected' : '' }}>{{trans('localize.Video')}}</option>
                               </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.URL')}}</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{trans('localize.URL')}}" class="form-control compulsary" id="url" name='url'  value='{{empty(old('url'))?$banner->bn_redirecturl:old('url')}}'>
                                <label id="url_example" style="margin-top:3px; color:#337ab7; {{ (old('media_type', $banner->bn_type) == '1') ? 'display:none;' : 'display:block;' }}">{{trans('localize.Please_use_embeded_url')}} Eg: https://www.youtube.com/embed/DBE6v3tQjHY</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Open_in_New_Page')}} </label>
                            <div class="col-lg-10">
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="1" name="open" {{(($banner->bn_open && empty(old('open')) || old('open')) =='1')? 'checked' : '' }}> <i></i> {{trans('localize.Yes')}}
                                    </label>
                                </div>
                                <div class="i-checks">
                                    <label> <input type="radio" value="0" name="open" {{(($banner->bn_open && empty(old('open')) || old('open')) =='0')? 'checked' : '' }}> <i></i> {{trans('localize.No')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                         <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Banner_Type')}}</label>
                            <div class="col-lg-10">
                                <select class="form-control" name="type">
                                     @foreach ($bannertypes as $type)
                                        <option value="{{ $type->id }}" {{($type->id == $banner->type)? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                               </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Status')}} </label>
                            <div class="col-lg-10">
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="1" name="status" {{(empty(old('status')) && $banner->bn_status) || old('status')=='1'?'checked':''}} > <i></i> {{trans('localize.Active')}}
                                    </label>
                                </div>
                                <div class="i-checks">
                                    <label> <input type="radio" value="0" name="status" {{(empty(old('status')) && !$banner->bn_status) || old('status')=='0'?'checked':''}}> <i></i> {{trans('localize.Inactive')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.country')}} </label>
                            <div class="col-lg-10">
                                <div class="i-checks">
                                @foreach($countries as $country)
                                    <input type="checkbox" class="i-checks" name="banner_country[]" value="{{ $country->co_id }}" {{ ($banner->countries->contains('co_id', $country->co_id)? 'checked' : '') }} ><label style="padding-left:5px;"> {{ $country->co_name }}</label><br>
                                @endforeach
                                </div>
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
<script src="/assets/js/iCheck/icheck.min.js"></script>
<script>
    $(document).ready(function() {
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $('.img_replace').on('click', function(){
            $('.img_old').hide();
            $('.img_new').show();
        });

        $('#media_type').on('change', function(){
            if ($(this).val() == '1') {
                $('#url').attr('placeholder', '{{trans('localize.Redirect_URL')}}');
                $('#url_example').hide();
            } else {
                $('#url').attr('placeholder', '{{trans('localize.Video_URL')}}');
                $('#url_example').show();
            }
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
    });
</script>
@endsection