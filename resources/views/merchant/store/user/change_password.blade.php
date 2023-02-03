@extends('merchant.layouts.master')

@section('title', 'Change Password')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>@lang('localize.setting')</h2>
        <ol class="breadcrumb">
            <li>
                @lang('localize.profile')
            </li>
            <li class="active">
                <strong>@lang('localize.change_password')</strong>
            </li>
        </ol>
    </div>
</div>
@if (session('status'))
    <br>
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
@if($errors->any())
    <br>
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
@endif
<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
             @include('merchant.common.success')
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{trans('localize.change_password')}}</h5>
                    <div class="ibox-tools">
                    </div>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" role="form" action="{{url('store/password/edit')}}" method="POST">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group">
                                <label class="col-lg-2 control-label">{{trans('localize.old_password')}}</label>
                                <div class="col-lg-10">
                                    <input type="password" class="form-control" name="old_password">
                                    @if ($errors->has('old_password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('old_password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-2 control-label">{{trans('localize.new_password')}}</label>
                                <div class="col-lg-10">
                                    <input type="password" class="form-control" name="password">
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-2 control-label">{{trans('localize.confirm_password')}}</label>
                                <div class="col-lg-10">
                                    <input type="password" class="form-control" name="password_confirmation">
                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-12">
                                    <button class="btn btn-primary btn-md pull-right" type="submit"><a style="color:#fff" >{{trans('localize.update')}}</a></button>
                                </div>
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
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>

<script>
    $(document).ready(function() {
        $('.footable').footable();
    });
</script>
@endsection
