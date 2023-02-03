@extends('admin.layouts.master')
@section('title', 'Edit CMS Page')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Edit')}} CMS {{trans('localize.Page')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin">{{trans('localize.dashboard')}}</a>
            </li>
            <li>
                <a href="/admin/setting/cms">CMS</a>
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
            @include('admin.common.success')
            @include('admin.common.errors')
            <form id='form' class="form-horizontal" action='/admin/setting/cms/edit/{{$cms->cp_id}}' method="POST">
                {{ csrf_field() }}
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#tab-1"> @lang('localize.English') </a></li>
                        <li class=""><a data-toggle="tab" href="#tab-2"> @lang('localize.Chinese') @lang('localize.Simplified') </a></li>
                        <li class=""><a data-toggle="tab" href="#tab-3"> @lang('localize.Chinese') @lang('localize.Traditional') </a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane active">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">{{trans('localize.Title')}}</label>
                                    <div class="col-lg-10">
                                        <input type="text" placeholder="{{trans('localize.Title')}}" class="form-control compulsary" name='title_en'  value='{{empty(old('title_en'))?$cms->cp_title_en:old('title_en')}}'>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">{{trans('localize.Description')}}</label>
                                    <div class="col-sm-10">
                                        <textarea class="summernote" name="desc_en">{{empty(old('desc_en'))?$cms->cp_description_en:old('desc_en')}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="tab-2" class="tab-pane">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">{{trans('localize.Title')}}</label>
                                    <div class="col-lg-10">
                                        <input type="text" placeholder="{{trans('localize.Title')}}" class="form-control" name='title_cn' value='{{empty(old('title_cn'))?$cms->cp_title_cn:old('title_cn')}}'>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">{{trans('localize.Description')}}</label>
                                    <div class="col-sm-10">
                                        <textarea class="summernote" name="desc_cn">{{empty(old('desc_cn'))?$cms->cp_description_cn:old('desc_cn')}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="tab-3" class="tab-pane">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">{{trans('localize.Title')}}</label>
                                    <div class="col-lg-10">
                                        <input type="text" placeholder="{{trans('localize.Title')}}" class="form-control" name='title_cnt' value='{{empty(old('title_cnt'))?$cms->cp_title_cnt:old('title_cnt')}}'>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">{{trans('localize.Description')}}</label>
                                    <div class="col-sm-10">
                                        <textarea class="summernote" name="desc_cnt">{{empty(old('desc_cnt'))?$cms->cp_description_cnt:old('desc_cnt')}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>{{trans('localize.General_Info')}}</h5>
                        </div>

                        <div class="ibox-content">
                            @if ($cms->type->type == 'web')
                            <div class="form-group">
                                <label class="col-lg-2 control-label">{{trans('localize.Url_Slug')}} </label>
                                <div class="col-lg-5">
                                    <div class="input-group m-b">
                                        <span class="input-group-addon">http://www.meihome.asia/info/</span>
                                        <input type="text" placeholder="Eg: contact_us" class="form-control url compulsary" name="url"  value="{{empty(old('url'))?$cms->cp_url:old('url')}}">
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="form-group">
                                <label class="col-lg-2 control-label">{{trans('localize.Status')}} </label>
                                <div class="col-lg-10">
                                    <div class="i-checks">
                                        <label>
                                            <input type="radio" value="1" name="status" {{(empty(old('status')) && $cms->cp_status) || old('status')=='1'?'checked':''}} > <i></i> {{trans('localize.Active')}}
                                        </label>
                                    </div>
                                    <div class="i-checks">
                                        <label> <input type="radio" value="0" name="status" {{(empty(old('status')) && !$cms->cp_status) || old('status')=='0'?'checked':''}}> <i></i> {{trans('localize.Inactive')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @if ($cms->type->type == 'web')
                            <div class="form-group">
                                <label class="col-lg-2 control-label">{{trans('localize.Display_at_footer')}} </label>
                                <div class="col-lg-10">
                                    <div class="i-checks">
                                        <label>
                                            <input type="radio" value="1" name="footer" {{(empty(old('footer')) && $cms->cp_footer) || old('footer')=='1'?'checked':''}} > <i></i> {{trans('localize.Yes')}}
                                        </label>
                                    </div>
                                    <div class="i-checks">
                                        <label> <input type="radio" value="0" name="footer" {{(empty(old('footer')) && !$cms->cp_footer) || old('footer')=='0'?'checked':''}}> <i></i> {{trans('localize.No')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                    @if($edit_permission)
                                    <button class="btn btn-sm btn-primary form_submit" type="button">{{trans('localize.Update')}}</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@section('style')
<link href="/backend/css/plugins/summernote/summernote.css" rel="stylesheet">
<link href="/backend/css/plugins/summernote/summernote-bs3.css" rel="stylesheet">
<link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/summernote/summernote.min.js"></script>
<script src="/backend/js/plugins/iCheck/icheck.min.js"></script>

<script>
    $(document).ready(function() {

        $('.summernote').summernote();

        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $('.url').bind('keyup keydown change', function() {
            $(this).val($(this).val().toLowerCase().replace(/ /g,"_"));
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