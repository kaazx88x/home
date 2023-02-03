@extends('merchant.layouts.master')

@section('title', 'View Profile')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.profile')}}</h2>
        <ol class="breadcrumb">
            <li>
                {{trans('localize.profile')}}
            </li>
            <li class="active">
                <strong>{{trans('localize.view')}}</strong>
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


<div class="wrapper wrapper-content animated fadeInUp">

    <div class="row">
        <div class="col-lg-12">
            <div class="alert alert-info">
                <h4>@lang('localize.self_edit_profile_disclaimer')</h4>
            </div>
        </div>
        <div class="col-lg-6">

            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{trans('localize.general_info')}}</h5>
                    <div class="ibox-tools">
                    </div>
                </div>
                <div class="ibox-content">

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.username')}}</label>
                        <p class="form-control-static">{{$merchant->username}}</p>
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.firstname')}}</label>
                        <p class="form-control-static">{{$merchant->mer_fname}}</p>
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.lastname')}}</label>
                        <p class="form-control-static">{{$merchant->mer_lname}}</p>
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.email')}}</label>
                        <p class="form-control-static">{{$merchant->email}}</p>
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.phone')}}</label>
                        <p class="form-control-static">{{$merchant->mer_phone}}</p>
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.office_number')}}</label>
                        <p class="form-control-static">{{$merchant->mer_office_number}}</p>
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.address')}}</label>
                        <p class="form-control-static">{{$merchant->mer_address1}}</p>
                        <p class="form-control-static">{{$merchant->mer_address2}}</p>
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.country')}}</label>
                        @foreach($country as $co)
                            @if($co->co_id == $merchant->mer_co_id)
                                <p class="form-control-static">{{$co->co_name}}</p>
                            @endif
                        @endforeach
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.state')}}</label>
                        @foreach($state as $st)
                            @if($st->id == $merchant->mer_state)
                                <p class="form-control-static">{{$st->name}}</p>
                            @endif
                        @endforeach
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.city')}}</label>
                        <p class="form-control-static">{{$merchant->mer_city_name}}</p>
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
                        @foreach ($country as $co)
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
</div>

@endsection

@section('style')
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script>
    $(document).ready(function() {
        load_state('')
        $('.footable').footable();
    });
</script>
@endsection
