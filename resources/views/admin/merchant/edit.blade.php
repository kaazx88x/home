@extends('admin.layouts.master')

@section('title', 'Edit Profile')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Edit')}} {{trans('localize.Merchants')}} </h2>
        <ol class="breadcrumb">
            <li>
                @if ($merchant->mer_type == 0)
                    <a href="/admin/merchant/manage/online">{{trans('localize.Merchants')}} {{trans('localize.Online')}}</a>
                @elseif ($merchant->mer_type == 1)
                    <a href="/admin/merchant/manage/offline">{{trans('localize.Merchants')}} {{trans('localize.Offline')}}</a>
                @endif
            </li>
            <li class="active">
                <strong>{{trans('localize.Edit')}} {{trans('localize.Merchants')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInUp">
    <div class="ibox float-e-margins border-bottom">
        <div class="ibox-title ibox-title-filter">
            <div class="row">
                <div class="col-sm-12">
                    @if($online_order_report)
                    <div class="col-sm-2">
                        <a target="_blank" href="/admin/transaction/product/orders?mid={{$merchant->mer_id}}" class="btn btn-white btn-sm btn-block">{{trans('localize.Online_Order_Report')}}</a>
                    </div>
                    @endif

                    @if($offline_order_report)
                    <div class="col-sm-2">
                        <a target="_blank" href="/admin/transaction/offline?mid={{$merchant->mer_id}}" class="btn btn-white btn-sm btn-block">{{trans('localize.Offline_Order_Report')}}</a>
                    </div>
                    @endif

                    <div class="col-sm-2">
                        <a target="_blank" href="/admin/transaction/fund-request?id={{$merchant->mer_id}}" class="btn btn-white btn-sm btn-block">{{trans('localize.fund_request_report')}}</a>
                    </div>

                    @if($manage_store)
                    <div class="col-sm-2">
                        <a target="_blank" href="/admin/store/manage/{{$merchant->mer_id}}" class="btn btn-white btn-sm btn-block">{{trans('localize.manage_store')}}</a>
                    </div>
                    @endif

                    @if($merchant->mer_staus == 0)
                        @if($resend_activation_email_permission)
                        <div class="col-md-2">
                            <button type="button" class="btn btn-white btn-sm btn-block" data-href="{{ url('/merchant/resend/activation', [$merchant->mer_id, $merchant->email]) }}" id="resend_activation_email">{{trans('localize.resend_activation_email')}}</button>
                        </div>
                        @endif
                    @endif

                    @if($hard_reset_password)
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-white btn-sm btn-block" data-toggle="modal" data-target="#password_reset_modal">{{trans('localize.Reset_Password')}}</button>
                        </div>
                    @elseif($soft_reset_password)
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-white btn-sm btn-block" id="soft_reset_password" data-id="{{ $merchant->mer_id }}">{{trans('localize.Soft_Reset_Password')}}</button>
                        </div>
                    @endif

                </div>

                <div class="col-sm-12">
                    @if($mi_credit_report_permission)
                    <div class="col-sm-2">
                        <a target="_blank" href="/admin/merchant/credit/{{$merchant->mer_id}}" class="btn btn-white btn-sm btn-block">@lang('common.credit_name') {{trans('localize.report')}}</a>
                    </div>
                    @endif

                    @if($mi_credit_manage_permission)
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-white btn-sm btn-block" data-toggle="modal" data-target="#manage_credit">{{trans('localize.manage')}} @lang('common.credit_name')</button>
                    </div>
                    @endif

                    <div class="col-sm-2">
                        <a target="_blank" href="/admin/merchant/tax/{{$merchant->mer_id}}" class="btn btn-white btn-sm btn-block">@lang('localize.tax_invoice')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.common.notifications')

    <div class="row">
        <form id="edit_merchant" action="{{url('admin/merchant/edit')}}" method="POST" class="form">
        {{ csrf_field() }}

        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{trans('localize.Merchants')}} {{trans('localize.profile')}}</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label class="control-label">Username <span class='text-danger'>*</span></label>
                        @if($edit_merchant_username_permission)
                        <input type="text" class="form-control compulsary" name="username" id="username" value="{{ old('username', empty($merchant->username)?null:$merchant->username) }}">
                        @else
                        <p class="form-control-static">{{ $merchant->username }}</p>
                        @endif
                    </div>

                    @if($edit_merchant_type_permission)
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.mer_type')}}</label>
                        <select class="form-control" name="type">
                            <option value="0" {{($merchant->mer_type == 0)? 'selected' : ''}}>{{trans('localize.Online')}} {{trans('localize.Merchants')}}</option>
                            <option value="1" {{($merchant->mer_type == 1)? 'selected' : ''}}>{{trans('localize.Offline')}} {{trans('localize.Merchants')}}</option>
                        </select>
                    </div>
                    @endif

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.First_Name')}} <span class='text-danger'>*</span></label>
                        <input type="text" class="form-control compulsary" name="fname" id="fname" value="{{ old('fname', $merchant->mer_fname) }}">
                        <input type="hidden" name="mer_id" value="{{$merchant->mer_id}}">
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.Last_Name')}} <span class='text-danger'>*</span></label>
                        <input type="text" class="form-control compulsary" name="lname" id="lname" value="{{ old('lname', $merchant->mer_lname) }}">
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.email')}} <span class='text-danger'>*</span></label>
                        @if($edit_merchant_email_permission)
                        <input type="text" class="form-control compulsary" name="email" id="email" value="{{ old('email', empty($merchant->email)?null:$merchant->email) }}" >
                        @else
                        <p class="form-control-static">{{$merchant->email}}</p>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.Phone')}} <span class='text-danger'>*</span></label>
                        @if($edit_merchant_phone_permission)
                        <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone', $merchant->mer_phone) }}">
                        @else
                        <p class="form-control-static">{{$merchant->mer_phone}}</p>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.office_number')}} <span class='text-danger'>*</span></label>
                        <input type="text" class="form-control compulsary" name="office_number" id="office_number" value="{{ old('office_number', $merchant->mer_office_number) }}">
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.address')}} <span class='text-danger'>*</span></label>
                        <input type="text" class="form-control compulsary" name="address1" id="address1" value="{{ old('address1', $merchant->mer_address1) }}">
                        <br>
                        <input type="text" class="form-control" name="address2" value="{{ old('address2', $merchant->mer_address2) }}">
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.country')}} <span class='text-danger'>*</span></label>
                        <select class="form-control compulsary" id="mer_country" name="country" onchange="get_states('#mer_state', this.value)"></select>
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.State')}} <span class='text-danger'>*</span></label>
                        <select class="form-control compulsary" id="mer_state" name="state">
                            <option value="">{{trans('localize.selectCountry_first')}}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.city')}} <span class='text-danger'>*</span></label>
                        <input type="text" class="form-control compulsary" name="mer_city_name" id="mer_city_name" value="{{ old('mer_city_name', $merchant->mer_city_name) }}">
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.zipcode')}} <span class='text-danger'>*</span></label>
                        <input type="text" class="form-control compulsary" name="zipcode" id="zipcode" value="{{ old('zipcode', $merchant->zipcode) }}">
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.Commission')}}</label>
                        @if($edit_merchant_commission)
                        <input type="text" class="form-control" name="commission" id="commission" value="{{ old('commission', $merchant->mer_commission) }}">
                        @else
                        <p class="form-control-static">{{$merchant->mer_commission}} %</p>
                        {{-- <input type="hidden" name="commission" value="{{$merchant->mer_commission}}"> --}}
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">@lang('common.partner_name') @lang('localize.platform_charges') {{trans('localize.Rate')}}</label>
                        @if($edit_merchant_platform_charge)
                        <input type="text" class="form-control" name="mer_platform_charge" id="mer_platform_charge" value="{{ old('mer_platform_charge', $merchant->mer_platform_charge) }}">
                        @else
                        <p class="form-control-static">{{$merchant->mer_platform_charge}} %</p>
                        {{-- <input type="hidden" name="mer_platform_charge" value="{{$merchant->mer_platform_charge}}"> --}}
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">@lang('common.mall_name') @lang('localize.gst') {{trans('localize.Rate')}}</label>
                        @if($edit_merchant_service_charge)
                        <input type="text" class="form-control" name="mer_service_charge" id="mer_service_charge" value="{{ old('mer_service_charge', $merchant->mer_service_charge) }}">
                        @else
                        <p class="form-control-static">{{$merchant->mer_service_charge}} %</p>
                        {{-- <input type="hidden" name="mer_service_charge" value="{{$merchant->mer_service_charge}}"> --}}
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">@lang('common.credit_name') {{trans('localize.balance')}}</label>
                        <!--<button type="button" class="btn btn-white btn-sm"><a class="nolinkcolor" href="/admin/merchant/credit/{{$merchant->mer_id}}">{{ ($merchant->mer_vtoken)? $merchant->mer_vtoken : '0.00' }}</a></button>-->
                        <p class="form-control-static">{{ ($merchant->mer_vtoken)? $merchant->mer_vtoken : '0.00' }}</p>
                    </div>
                </div>
            </div>
        {{--  </div>

        <div class="col-lg-6">  --}}
            <div class="ibox">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{trans('localize.guarantor_bank_acc')}}</h5>
                        <div class="ibox-tools">
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.mer_acc_holder')}} <span class='text-danger'>*</span></label>
                            <input type="text" class="form-control compulsary" name="bank_acc_name" id="bank_holder" value="{{old('bank_acc_name', $merchant->bank_acc_name)}}" {{ (!$edit_merchant_bank_info_permission) ? 'readonly disabled' : '' }}>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.mer_acc_number')}} <span class='text-danger'>*</span></label>
                            <input type="text" class="form-control compulsary" name="bank_acc_no" id="bank_acc" value="{{old('bank_acc_no', $merchant->bank_acc_no)}}" {{ (!$edit_merchant_bank_info_permission) ? 'readonly disabled' : '' }}>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.mer_bank_name')}} <span class='text-danger'>*</span></label>
                            <input type="text" class="form-control compulsary" name="bank_name" id="bank_name" value="{{old('bank_name', $merchant->bank_name)}}" {{ (!$edit_merchant_bank_info_permission) ? 'readonly disabled' : '' }}>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.mer_bank_country')}} <span class='text-danger'>*</span></label>
                            <select class="form-control compulsary" id="bank_country" name="bank_country" {{ (!$edit_merchant_bank_info_permission) ? 'readonly disabled' : '' }}></select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.mer_bank_address')}} <span class='text-danger'>*</span></label>
                            <input type="text" class="form-control compulsary" name="bank_address" value="{{old('bank_address', $merchant->bank_address)}}" {{ (!$edit_merchant_bank_info_permission) ? 'readonly disabled' : '' }}>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.bank_swift')}}</label>
                            <input type="text" class="form-control" name="bank_swift" value="{{old('bank_swift', $merchant->bank_swift)}}" {{ (!$edit_merchant_bank_info_permission) ? 'readonly disabled' : '' }}>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.if_europe')}}</label>
                            <input type="text" class="form-control" name="bank_europe" value="{{old('bank_europe', $merchant->bank_europe)}}" {{ (!$edit_merchant_bank_info_permission) ? 'readonly disabled' : '' }}>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.gst_no')}}</label>
                            <input type="text" class="form-control" name="bank_gst" value="{{old('bank_gst', $merchant->bank_gst)}}" {{ (!$edit_merchant_bank_info_permission) ? 'readonly disabled' : '' }}>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{trans('localize.Referrer')}} {{trans('localize.information')}}</h5>
                    <div class="ibox-tools">
                    </div>
                </div>
                <div class="ibox-content">


                        <div class="form-group">
                            <label class="control-label">@lang('localize.guarantor_name')</label>
                            <input type="text" class="form-control" name="referrer_name" value="{{ ($merchant->referrer) ? $merchant->referrer->name : ''}}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.guarantor_username')</label>
                            <input type="text" class="form-control" name="referrer_username" value="{{ ($merchant->referrer) ? $merchant->referrer->username : ''}}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.guarantor_nationality')</label>
                            <input type="text" class="form-control" name="referrer_nationality" value="{{ ($merchant->referrer) ? $merchant->referrer->nationality : ''}}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.guarantor_phone')</label>
                            <input type="text" class="form-control phone" name="referrer_phone" value="{{ ($merchant->referrer) ? $merchant->referrer->phone : ''}}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.guarantor_email')</label>
                            <input type="text" class="form-control email" name="referrer_email" value="{{ ($merchant->referrer) ? $merchant->referrer->email : ''}}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.guarantor_bank_name')</label>
                            <input type="text" class="form-control" name="referrer_bank_name" value="{{ ($merchant->referrer) ? $merchant->referrer->bank_name : ''}}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.guarantor_acc_name')</label>
                            <input type="text" class="form-control" name="referrer_acc_name" value="{{ ($merchant->referrer) ? $merchant->referrer->bank_acc_name : ''}}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.guarantor_bank_acc')</label>
                            <input type="text" class="form-control" name="referrer_bank_acc" value="{{ ($merchant->referrer) ? $merchant->referrer->bank_acc_no : ''}}">
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

                    {{--  @if($admin_role == 'superuser')  --}}
                        <div class="form-group">
                            <label class="control-label">@lang('localize.guarantor_name')</label>
                            <input type="text" class="form-control" name="guarantor_name" value="{{ ($merchant->guarantor) ? $merchant->guarantor->name : ''}}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.guarantor_username')</label>
                            <input type="text" class="form-control" name="guarantor_username" value="{{ ($merchant->guarantor) ? $merchant->guarantor->username : ''}}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.guarantor_nationality')</label>
                            <input type="text" class="form-control" name="guarantor_nationality" value="{{ ($merchant->guarantor) ? $merchant->guarantor->nationality : ''}}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.guarantor_phone')</label>
                            <input type="text" class="form-control phone" name="guarantor_phone" value="{{ ($merchant->guarantor) ? $merchant->guarantor->phone : ''}}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.guarantor_email')</label>
                            <input type="text" class="form-control email" name="guarantor_email" value="{{ ($merchant->guarantor) ? $merchant->guarantor->email : ''}}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.guarantor_bank_name')</label>
                            <input type="text" class="form-control" name="guarantor_bank_name" value="{{ ($merchant->guarantor) ? $merchant->guarantor->bank_name : ''}}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.guarantor_acc_name')</label>
                            <input type="text" class="form-control" name="guarantor_acc_name" value="{{ ($merchant->guarantor) ? $merchant->guarantor->bank_acc_name : ''}}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.guarantor_bank_acc')</label>
                            <input type="text" class="form-control" name="guarantor_bank_acc" value="{{ ($merchant->guarantor) ? $merchant->guarantor->bank_acc_no : ''}}">
                        </div>
                    {{--  @else
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
                    @endif  --}}

                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <input type="hidden" name="username" value="{{$merchant->username}}">
            <div class="col-md-2 pull-right">
                <button class="btn btn-block btn-primary" type="submit" id="submit"><a style="color:#fff" >{{trans('localize.Edit')}} {{trans('localize.Merchants')}} </a></button>
            </div>
            <div class="col-md-2 pull-right">
                <button class="btn btn-block btn-default" type="reset">{{trans('localize.reset_form')}}</button>
            </div>
        </div>
    </div>
    <br>
    </form>
</div>

<div class="modal inmodal" id="password_reset_modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content animated flipInY">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">{{trans('localize.close_')}}</span></button>
                <h4 class="modal-title">{{trans('localize.reset_password')}}</h4>
                <small class="font-bold">{{trans('localize.This_form_will')}} <span class="text-danger">{{trans('localize.override_exsisting_merchant_password')}}</span> {{trans('localize.and_email_the_new_password_to_merchant_based_on_email_from_merchant_details_below')}}</small>
                <br/>
                <button type="button" id="soft_reset_password" data-id="{{ $merchant->mer_id }}" class="btn btn-xs btn-default pull-right"><i class="fa fa-magic"></i> {{trans('localize.Soft_Reset_Password')}}</button>
            </div>
            <div class="modal-body">
                <form id="resetpassword" action="/admin/merchant/reset_password" class="form-horizontal" method="POST">
                {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.Merchants')}} {{trans('localize.details')}} </label>
                        <p class="form-control-static">
                            <span>ID : {{$merchant->mer_id}}</span><br>
                            <span>{{trans('localize.First_Name')}} : {{$merchant->mer_fname}}</span><br>
                            <span>{{trans('localize.Last_Name')}} : {{$merchant->mer_lname}}</span><br>
                            <span>{{trans('localize.email')}} : {{$merchant->email}}</span><br>
                            <span>{{trans('localize.Phone')}} : {{$merchant->mer_phone}}</span><br>
                        </p>
                        <input type="hidden" name="mer_id" value="{{$merchant->mer_id}}">
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.newpassword')}}</label>
                        <input type="password" class="form-control" id="password" name="password" onchange="check_length(this.value);">
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.confirm_password')}}</label>
                        <input type="password" class="form-control" id="confirmpassword" name="password_confirmation" onchange="check_password(this.value);">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="reset_password" class="btn btn-primary"><i class="fa fa-envelope-o"></i> {{trans('localize.reset_password')}}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal inmodal" id="manage_credit" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content animated flipInY">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">{{trans('localize.close_')}}</span></button>
                <h4 class="modal-title">{{trans('localize.manage')}} @lang('common.credit_name')</h4>
                <small class="font-bold">{{trans('localize.This_form_is_use_for_debit_or_credit_merchant')}} @lang('common.credit_name') {{trans('localize.amount')}}</small>
            </div>
            <div class="modal-body">
                <form id="managecredit" action="/admin/merchant/manage_credit/{{$merchant->mer_id}}" class="form-horizontal" method="POST">
                {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.Merchants')}} {{trans('localize.details')}} </label>
                        <p class="form-control-static">
                            <span>ID : {{$merchant->mer_id}}</span><br>
                            <span>{{trans('localize.First_Name')}} : {{$merchant->mer_fname}}</span><br>
                            <span>{{trans('localize.Last_Name')}} : {{$merchant->mer_lname}}</span><br>
                            <span>{{trans('localize.email')}} : {{(!empty($merchant->email))? $merchant->email : $merchant->email}}</span><br>
                            <span>{{trans('localize.Phone')}} : {{$merchant->mer_phone}}</span><br>
                            <span>{{trans('localize.Current')}} @lang('common.credit_name') : {{$merchant->mer_vtoken}}</span><br>
                        </p>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 text-left" style="height:3em; vertical-align: middle;">
                            <label class="control-label">{{trans('localize.type')}}</label>
                        </div>
                        <div class="col-sm-3" style="height:3em; vertical-align: middle;">
                            <div class="i-checks"><label> <input type="radio" id="credit" value="credit" name="type" checked> <i></i> {{trans('localize.credit')}} </label></div>
                        </div>
                        <div class="col-sm-3" style="height:3em; vertical-align: middle;">
                            <div class="i-checks"><label> <input type="radio" id="debit" value="debit" name="type"> <i></i> {{trans('localize.debit')}} </label></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">@lang('common.credit_name') {{trans('localize.amount')}}</label>
                        <input type="number" step="any" class="form-control" id="amount" name="amount">
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.remarks')}}</label>
                        <textarea class="form-control" name="remark" id="remark" rows="3" style="resize: none;"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="submit_credit" class="btn btn-primary"><i class="fa fa-send-o"></i> {{trans('localize.submit')}}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
<link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script src="/backend/js/plugins/iCheck/icheck.min.js"></script>
<script src="/backend/js/custom.js"></script>
<script>

    $(document).ready(function() {

        get_countries('#mer_country', "{{ old('country', $merchant->mer_co_id? $merchant->mer_co_id : '0') }}", '#mer_state', "{{ old('state', $merchant->mer_state? $merchant->mer_state : '0') }}");
        get_countries('#bank_country', "{{ old('bank_country', $merchant->bank_country? $merchant->bank_country : '0') }}");

        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $(".phone").on({
            keydown: function(e) {
                if (e.shiftKey || e.ctrlKey || e.altKey) {
                    e.preventDefault();
                } else {
                    var key = e.keyCode;
                    if (!((key == 8) || (key == 46) || (key >= 35 && key <= 40) || (key >= 48 && key <= 57) || (key >= 96 && key <= 105))) {
                        e.preventDefault();
                    }
                }
            }
        });

        $(".email").on({
            keydown: function(e) {
                if (e.which === 32)
                return false;
            },
            change: function(e) {
                validate_email(this);
            }
        });

        $('#reset_password').click(function() {
            if($('#password').val() == '') {
                $('#password').attr('placeholder', '{{trans('localize.fieldrequired')}}');
                $('#password').css('border', '1px solid red');
                return false;
            } else {
                $('#password').css('border', '');
            }

             if($('#confirmpassword').val() == '') {
                $('#confirmpassword').attr('placeholder', '{{trans('localize.fieldrequired')}}');
                $('#confirmpassword').css('border', '1px solid red');
                return false;
            } else {
                $('#confirmpassword').css('border', '');
            }

            swal({
                title: "{{trans('localize.sure')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d9534f",
                confirmButtonText: "{{trans('localize.reset_password')}}",
				cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: false
            }, function(){
                    $("#resetpassword").submit();
                }
            );

        });

        $('#soft_reset_password').click(function() {
            var id = $(this).attr('data-id');
            swal({
                title: "{{trans('localize.sure')}}",
                text: "{{trans('localize.This_will_auto_generate_password_for_merchant_and_email_the_password')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d9534f",
                confirmButtonText: "{{trans('localize.reset_password')}}",
				cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function(){
                    window.location.href = '/admin/merchant/soft_reset_password/' + id;
                }
            );
        });

        $('#submit').click(function(event) {
            $('form#edit_merchant :input').each(function() {

                if ($(this).hasClass('compulsary')) {
                    if (!$(this).val()) {
                        $(this).attr("placeholder", "{{trans('localize.fieldrequired')}}").css('border', '1px solid red').focus();
                        event.preventDefault();
                        return false;
                    }
                }

                if($(this).hasClass('email') && !validate_email(this)) {
                    event.preventDefault();
                    return false;
                }

                $(this).css('border', '');
            });

            $('#edit_merchant').submit();
        });

        $('#submit_credit').click(function() {
            var mer_vc = parseFloat("{{ $merchant->mer_vtoken }}");
            var amount = parseFloat($('#amount').val());

            if($('#amount').val() == '') {
                $('#amount').attr('placeholder', '{{trans('localize.Please_insert')}} @lang("common.credit_name") {{trans('localize.amount')}}');
                $('#amount').css('border', '1px solid red');
                return false;
            } else {
                $('#amount').css('border', '');
            }

            if($('#remark').val() == '') {
                $('#remark').attr('placeholder', '{{trans('localize.Please_fill_remarks_field')}}');
                $('#remark').css('border', '1px solid red');
                return false;
            } else {
                $('#remark').css('border', '');
            }

            if($('#debit').is(':checked')) {
                if(amount > mer_vc) {
                    swal("Error!", "{{trans('localize.Insufficient_merchant')}} @lang('common.credit_name') {{trans('localize.to_deduct')}}", "error");
                    $('#amount').css('border', '1px solid red');
                    return false;
                } else {
                    $('#amount').css('border', '');
                }
            }

            swal({
                title: "{{trans('localize.sure')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#5cb85c",
                confirmButtonText: "{{trans('localize.yes')}}",
				cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: true
            }, function(isConfirm){
                    if (isConfirm) $("#managecredit").submit();
                }
            );
        });

         $('#resend_activation_email').click(function() {
            var url = $(this).attr('data-href');
            swal({
                title: "{{trans('localize.sure')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#5cb85c",
                confirmButtonText: "{{trans('localize.yes')}}",
				cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: true
            }, function(isConfirm){
                    if (isConfirm) {
                        $('#spinner').show();
                        window.location.href = url;
                    }
                }
            );
        });

    });

    function check_length(password) {
        var password = $('#password').val();
            if (password.length <  6)
            {
                $('#password').css('border', '1px solid red');
                swal("{{trans('localize.error')}}", "{{trans('localize.minpassword')}}", "error");
            }
            else {
                $('#password').css('border', '');
            }
        }

    function check_password(confirmpassword) {
        var password = $('#password').val();
        if (confirmpassword !=  password)
        {
            swal("{{trans('localize.error')}}", "{{trans('localize.matchpassword')}}", "error");
            $('#confirmpassword').val('');
            $('#confirmpassword').css('border', '1px solid red');
            return false;
        } else {
            $('#confirmpassword').css('border', '');
            return true;
        }
    }

    function validate_email(e) {
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

        if(!emailReg.test(e.value)) {
            swal({
                title: window.translations.error,
                text: window.translations.email_validation_error,
                type: "error",
                showCancelButton: false,
                confirmButtonColor: "#d9534f",
                confirmButtonText: window.translations.ok,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            });
            $(e).css('border', '1px solid red').focus();
            return false;
        }

        return true;
    }

</script>
@endsection
