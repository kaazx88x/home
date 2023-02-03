@extends('admin.layouts.master')
@section('title', 'Create Banner')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Create_New_Banner')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin/setting/banner">{{trans('localize.Banner')}}</a>
            </li>
            <li>
                <a href="/admin/setting/banner/manage/{{$type->id}}">{{trans('localize.manage')}}</a>
            </li>
            <li class="active">
                <strong>{{trans('localize.Create')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{trans('localize.general_info')}}</h5>
                </div>

                <div class="ibox-content">
                    @include('admin.common.errors')
                    <form id="form" class="form-horizontal" action='/admin/setting/banner/add/{{$type->id}}' method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.title')}}</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{trans('localize.title')}}" class="form-control compulsary" name='title'  value='{{old('title')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.image')}}</label>
                            <div class="col-lg-10">
                                <input type="file" placeholder="{{trans('localize.image')}}" class="form-control compulsary" name='file'  value='{{old('file')}}'>
                                <label style="margin-top: 3px;"><span style="color:#337ab7;">{{trans('localize.Best_resolution_for_mobile_banner_ratio')}} - (2.5 : 1) Eg: 650 x 260, 1300 x 520</span></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Media_Type')}}</label>
                            <div class="col-lg-10">
                                <select class="form-control" id="media_type" name="media_type">
                                    <option value="1">{{trans('localize.image')}}</option>
                                    <option value="2">{{trans('localize.Video')}}</option>
                               </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.URL')}}</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{trans('localize.URL')}}" class="form-control compulsary" id="url" name='url'  value='{{old('url')}}'>
                                <label id="url_example" style="margin-top:3px; color:#337ab7; display:none;">Please use embeded url. Eg: https://www.youtube.com/embed/DBE6v3tQjHY</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Open_in_New_Page')}}</label>
                            <div class="col-lg-10">
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="1" name="open" {{old('open')=='1'? 'checked':''}} > <i></i> {{trans('localize.yes')}}
                                    </label>
                                </div>
                                <div class="i-checks">
                                    <label> <input type="radio" value="0" name="open" {{old('open')=='0' || empty(old('open'))?'checked':''}}> <i></i> {{trans('localize.no')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Banner_Type')}}</label>
                            <div class="col-lg-10">
                                <select class="form-control" name="type">
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                               </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.status')}} </label>
                            <div class="col-lg-10">
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="1" name="status" {{old('status')=='1' || empty(old('status'))?'checked':''}} > <i></i> {{trans('localize.active')}}
                                    </label>
                                </div>
                                <div class="i-checks">
                                    <label> <input type="radio" value="0" name="status" {{old('status')=='0'?'checked':''}}> <i></i> {{trans('localize.inactive')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        @if($type->id == 3)
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.popup_ads')}} </label>
                            <div class="col-lg-10">
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="1" name="popup"> <i></i> {{trans('localize.yes')}}
                                    </label>
                                </div>
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="0" name="popup" checked> <i></i> {{trans('localize.no')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.country')}} </label>
                            <div class="col-lg-10">
                                <div class="i-checks">
                                @foreach($countries as $country)
                                    <input type="checkbox" class="i-checks" name="banner_country[]" value="{{ $country->co_id }}"><label style="padding-left:5px;"> {{ $country->co_name }}</label><br>
                                @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" col-lg-2 col-lg-offset-10">
                                <button class="btn btn-md btn-primary btn-block form_submit" type="button">{{trans('localize.Create')}}</button>
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