@extends('admin.layouts.master')
@section('title', 'Edit Country')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Edit')}} {{trans('localize.country')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin">{{trans('localize.dashboard')}}</a>
            </li>
            <li>
                <a href="/admin/setting/country">{{trans('localize.country')}}</a>
            </li>
            <li class="active">
                <strong>{{trans('localize.Edit')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        @include('admin.common.errors')
        <form id='form' class="form-horizontal" action='/admin/setting/country/edit/{{$country->co_id}}' method="POST">
            {{ csrf_field() }}
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>{{trans('localize.General_Info')}}</h5>
                    </div>

                    <div class="ibox-content">
                        <div class="form-group">
                           <label class="col-lg-3 control-label">{{trans('localize.Code')}}</label>
                           <div class="col-lg-9">
                               <input type="text" placeholder="{{trans('localize.Code')}}" class="form-control compulsary" name='code'  value='{{empty(old('code'))?$country->co_code:old('code')}}'>
                           </div>
                       </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{trans('localize.Name')}}</label>
                            <div class="col-lg-9">
                                <input type="text" placeholder="{{trans('localize.Name')}}" class="form-control compulsary" name='name' value='{{empty(old('name'))?$country->co_name:old('name')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{trans('localize.Currency_Symbol')}}</label>
                            <div class="col-lg-9">
                                <input type="text" placeholder="{{trans('localize.Currency_Symbol')}}" class="form-control compulsary" name='cursymbol'  value='{{empty(old('cursymbol'))?$country->co_cursymbol:old('cursymbol')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{trans('localize.Currency_Code')}}</label>
                            <div class="col-lg-9">
                                <input type="text" placeholder="{{trans('localize.Currency_Code')}}" class="form-control compulsary" name='curcode'  value='{{empty(old('curcode'))?$country->co_curcode:old('curcode')}}'>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>{{trans('localize.Online')}}</h5>
                    </div>

                    <div class="ibox-content">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{trans('localize.Exchange_Rate')}}</label>
                            <div class="col-lg-9">
                                <input id="rate" type="text" data-mask="9.99" placeholder="Online Exchange Rate" class="form-control currency compulsary" name='rate' value='{{empty(old('rate'))? number_format($country->co_rate, 2) :old('rate')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{trans('localize.Status')}} </label>
                            <div class="col-lg-9">
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="1" name="status" {{(empty(old('status')) && $country->co_status) || old('status')=='1'?'checked':''}} > <i></i> {{trans('localize.Active')}}
                                    </label>
                                </div>
                                <div class="i-checks">
                                    <label> <input type="radio" value="0" name="status" {{(empty(old('status')) && !$country->co_status) || old('status')=='0'?'checked':''}}> <i></i> {{trans('localize.Inactive')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><div class="col-lg-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>{{trans('localize.Offline')}}</h5>
                    </div>

                    <div class="ibox-content">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{trans('localize.Exchange_Rate')}}</label>
                            <div class="col-lg-9">
                                <input id="offline_rate" type="text" data-mask="9.99" placeholder="{{trans('localize.Exchange_Rate')}}" class="form-control currency compulsary" name='offline_rate'  value='{{empty(old('offline_rate'))? number_format($country->co_offline_rate, 2) : old('offline_rate')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{trans('localize.Status')}} </label>
                            <div class="col-lg-9">
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="1" name="offline_status" {{(empty(old('offline_status')) && $country->co_offline_status) || old('offline_status')=='1'?'checked':''}} > <i></i> {{trans('localize.Active')}}
                                    </label>
                                </div>
                                <div class="i-checks">
                                    <label> <input type="radio" value="0" name="offline_status" {{(empty(old('offline_status')) && !$country->co_offline_status) || old('offline_status')=='0'?'checked':''}}> <i></i> {{trans('localize.Inactive')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-group">
                    <div class="col-lg-offset-3 col-lg-6">
                        @if($edit_permission)
                        <button class="btn btn-sm btn-block btn-primary form_submit" type="button" >{{trans('localize.update')}}</button>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script src="/backend/js/plugins/inputmask/inputmask.js"></script>
<script>
    $(document).ready(function() {
        $('#rate').inputmask({'mask':"9{0,5}.9{0,2}", greedy: false});
        $('#offline_rate').inputmask({'mask':"9{0,5}.9{0,2}", greedy: false});

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