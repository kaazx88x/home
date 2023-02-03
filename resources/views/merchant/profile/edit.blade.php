@extends('merchant.layouts.master')

@section('title', 'Edit Profile')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>@lang('localize.profile')</h2>
        <ol class="breadcrumb">
            <li>
                @lang('localize.profile')
            </li>
            <li class="active">
                <strong>@lang('localize.edit')</strong>
            </li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
            @include('merchant.common.errors')
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            @include('merchant.common.error')
            <form action="{{ url('merchant/profile/edit') }}" method="post">
            {{ csrf_field() }}
                <div class="row m-b">
                    <div class="form-group">
                        <div class="col-lg-12">
                            <div class="col-md-2 pull-right">
                                <button class="btn btn-primary btn-block" type="submit" id="submit" style="margin: 0 20px 0"><a style="color:#fff" >{{trans('localize.update')}}</a></button>
                            </div>
                            {{--<a href="{{ url('merchant/profile') }}" class="btn btn-default btn-md pull-right" style="color:#000">{{trans('localize.reset')}}</a>--}}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>@lang('localize.profile')</h5>
                                <div class="ibox-tools"></div>
                            </div>
                            <div class="ibox-content">

                                <div class="form-group">
                                    <label>{{trans('localize.username')}}</label>
                                    <p class="form-control-static">{{ $merchant->username }}</p>
                                </div>

                                <div class="form-group">
                                    <label>{{trans('localize.email')}}</label>
                                    <p class="form-control-static">{{ $merchant->email }}</p>
                                </div>

                                <div class="form-group">
                                    <label>{{trans('localize.phone')}}</label>
                                    <p class="form-control-static">{{ $merchant->mer_phone }}</p>
                                </div>

                                <div class="form-group">
                                    <label>{{trans('localize.firstname')}}</label> <span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="fname" value="{{ old('fname', $merchant->mer_fname) }}">
                                </div>

                                <div class="form-group">
                                    <label>{{trans('localize.lastname')}}</label> <span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="lname" value="{{ old('lname', $merchant->mer_lname) }}">
                                </div>

                                <div class="form-group">
                                    <label>{{trans('localize.office_number')}}</label> <span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="office_number" value="{{ old('office_number', $merchant->mer_office_number) }}">
                                </div>

                                <div class="form-group">
                                    <label>{{trans('localize.address')}}</label> <span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="address1" value="{{ old('address1', $merchant->mer_address1) }}">
                                    <br>
                                    <input type="text" class="form-control" name="address2" value="{{ old('address2', $merchant->mer_address2) }}">
                                </div>

                                {{-- <div class="form-group">
                                    <label class="col-lg-2 control-label">{{trans('localize.email')}}</label>
                                    <div class="col-lg-10">
                                        <input type="text" class="form-control" name="paymentEmail" value="{{$merchant->mer_payment}}">
                                    </div>
                                </div> --}}

                                <div class="form-group">
                                    <label>{{trans('localize.country')}}</label> <span class="text-danger">*</span>
                                    <select class="form-control" id="country" name="country">
                                        <option value="">@lang('localize.selectCountry')</option>
                                        @foreach ($country_details as $country)
                                            <option {{ (old('country', $merchant->mer_co_id) == $country->co_id) ? 'selected' : '' }} value="{{$country->co_id}}">{{$country->co_name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>{{trans('localize.state')}}</label> <span class="text-danger">*</span>
                                    <select class="form-control" id="state" name="state">
                                        <option value="">@lang('localize.selectCountry_first')</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>{{trans('localize.city')}}</label> <span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="mer_city_name" value="{{ old('mer_city_name', $merchant->mer_city_name) }}">
                                </div>

                            </div>
                        </div>
                    {{--  </div>

                    <div class="col-lg-6">  --}}
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{trans('localize.mer_bank_information')}}</h5>
                                <div class="ibox-tools">
                                </div>
                            </div>
                            <div class="ibox-content">

                                <div class="alert alert-info" role="alert">
                                    {{trans('localize.bank_info_disclaimer')}}
                                </div>

                                <div class="form-group">
                                    <label class="control-label">{{trans('localize.mer_acc_holder')}}</label>
                                    <p class="form-control-static">{{$merchant->bank_acc_name}}</p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">{{trans('localize.mer_acc_number')}}</label>
                                    <p class="form-control-static">{{$merchant->bank_acc_no}}</p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">{{trans('localize.mer_bank_name')}}</label>
                                    <p class="form-control-static">{{$merchant->bank_name}}</p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">{{trans('localize.mer_bank_country')}}</label>
                                    @foreach ($country_details as $co)
                                        @if($co->co_id == $merchant->bank_country)
                                            <p class="form-control-static">{{$co->co_name}}</p>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="form-group">
                                    <label class="control-label">{{trans('localize.mer_bank_address')}}</label>
                                    <p class="form-control-static">{{$merchant->bank_address}}</p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">{{trans('localize.bank_swift')}}</label>
                                    <p class="form-control-static">{{$merchant->bank_swift}}</p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">{{trans('localize.gst_no')}}</label>
                                    <p class="form-control-static">{{$merchant->bank_gst}}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{trans('localize.referrer_info')}}</h5>
                                <div class="ibox-tools">
                                </div>
                            </div>
                            <div class="ibox-content">

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_name')</label>
                                    <p class="form-control-static">
                                        {{ ($merchant->referrer) ? $merchant->referrer->name : ''}}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_username')</label>
                                    <p class="form-control-static">
                                        {{ ($merchant->referrer) ? $merchant->referrer->username : ''}}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_nationality')</label>
                                    <p class="form-control-static">
                                        {{ ($merchant->referrer) ? $merchant->referrer->nationality : ''}}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_phone')</label>
                                    <p class="form-control-static">
                                        {{ ($merchant->referrer) ? $merchant->referrer->phone : ''}}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_email')</label>
                                    <p class="form-control-static">
                                        {{ ($merchant->referrer) ? $merchant->referrer->email : ''}}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_bank_name')</label>
                                    <p class="form-control-static">
                                        {{ ($merchant->referrer) ? $merchant->referrer->bank_name : ''}}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{trans('localize.guarantor_info')}}</h5>
                                <div class="ibox-tools">
                                </div>
                            </div>
                            <div class="ibox-content">

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_acc_name')</label>
                                    <p class="form-control-static">
                                        {{ ($merchant->referrer) ? $merchant->referrer->bank_acc_name : ''}}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_bank_acc')</label>
                                    <p class="form-control-static">
                                        {{ ($merchant->referrer) ? $merchant->referrer->bank_acc_no : ''}}
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{trans('localize.guarantor_info')}}</h5>
                                <div class="ibox-tools">
                                </div>
                            </div>
                            <div class="ibox-content">

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_name')</label>
                                    <p class="form-control-static">
                                        {{ ($merchant->guarantor) ? $merchant->guarantor->name : ''}}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_username')</label>
                                    <p class="form-control-static">
                                        {{ ($merchant->guarantor) ? $merchant->guarantor->username : ''}}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_nationality')</label>
                                    <p class="form-control-static">
                                        {{ ($merchant->guarantor) ? $merchant->guarantor->nationality : ''}}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_phone')</label>
                                    <p class="form-control-static">
                                        {{ ($merchant->guarantor) ? $merchant->guarantor->phone : ''}}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_email')</label>
                                    <p class="form-control-static">
                                        {{ ($merchant->guarantor) ? $merchant->guarantor->email : ''}}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_bank_name')</label>
                                    <p class="form-control-static">
                                        {{ ($merchant->guarantor) ? $merchant->guarantor->bank_name : ''}}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_acc_name')</label>
                                    <p class="form-control-static">
                                        {{ ($merchant->guarantor) ? $merchant->guarantor->bank_acc_name : ''}}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_bank_acc')</label>
                                    <p class="form-control-static">
                                        {{ ($merchant->guarantor) ? $merchant->guarantor->bank_acc_no : ''}}
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
                <div class="row m-b">
                    <div class="form-group">
                        <div class="col-lg-12">
                            <div class="col-md-2 pull-right">
                                <button class="btn btn-primary btn-block" type="submit" id="submit" style="margin: 0 20px 0"><a style="color:#fff" >{{trans('localize.update')}}</a></button>
                            </div>
                            {{--<a href="{{ url('merchant/profile') }}" class="btn btn-default btn-md pull-right" style="color:#000">{{trans('localize.reset')}}</a>--}}
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('style')
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script src="/backend/js/custom.js"></script>

<script>
load_state('#state',"{{ old('country',$merchant->mer_co_id) }}", "{{ old('state', $merchant->mer_state) }}");

$(document).ready(function() {

    $('#country').change(function() {
        var update_input = '#state';
        var country_id = $(this).val();

        load_state(update_input, country_id);
    });

    $('.footable').footable();
});
</script>
@endsection
