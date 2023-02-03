@extends('admin.layouts.master')

@section('title', 'Edit Customer')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Edit')}} {{trans('localize.customer')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="">{{trans('localize.customer')}}</a>
            </li>
            <li class="active">
                <strong>{{trans('localize.Edit')}} {{trans('localize.customer')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="ibox float-e-margins border-bottom">
        <div class="ibox-title ibox-title-filter">
            <div class="row">
                @if($online_order_report)
                <div class="col-sm-2">
                    <a target="_blank" href="/admin/transaction/product/orders?cid={{$customer->cus_id}}" class="btn btn-white btn-sm btn-block">{{trans('localize.Online_Order_Report')}}</a>
                </div>
                @endif

                @if($offline_order_report)
                <div class="col-sm-2">
                    <a target="_blank" href="/admin/transaction/offline?cid={{$customer->cus_id}}" class="btn btn-white btn-sm btn-block">{{trans('localize.Offline_Order_Report')}}</a>
                </div>
                @endif

                <div class="col-sm-2">
                    <a target="_blank" href="/admin/transaction/product/ecards?cid={{$customer->cus_id}}" class="btn btn-white btn-sm btn-block">{{ trans('localize.e-card.report') }}</a>
                </div>

                @if($micredit_report_permission)
                <div class="col-sm-2">
                    <a target="_blank" href="/admin/customer/credit/{{$customer->cus_id}}" class="btn btn-white btn-sm btn-block">@lang("common.credit_name") {{trans('localize.report')}}</a>
                </div>
                @endif

                @if($manage_micredit_permission)
                <div class="col-sm-2">
                    <button type="button" class="btn btn-white btn-sm btn-block" data-toggle="modal" data-target="#manage_credit">{{trans('localize.manage')}} @lang("common.credit_name")</button>
                </div>
                @endif

                @if($hard_reset_password)
                <div class="col-sm-2">
                    <button type="button" class="btn btn-white btn-sm btn-block" data-toggle="modal" data-target="#password_reset_modal">{{trans('localize.reset_password')}}</button>
                </div>
                @elseif($softreset_password)
                <div class="col-sm-2">
                    <button type="button" class="btn btn-white btn-sm btn-block" id="soft_reset_password" data-id="{{ $customer->cus_id }}" >{{trans('localize.reset_password')}}</button>
                </div>
                @endif

                @if($hard_reset_secure_code)
                <div class="col-sm-2">
                    <button type="button" class="btn btn-white btn-sm btn-block" data-toggle="modal" data-target="#code_reset_modal">{{trans('localize.Reset_Secure_Code')}}</button>
                </div>
                @elseif($soft_reset_secure_code)
                <div class="col-sm-2">
                    <button type="button" class="btn btn-white btn-sm btn-block" id="soft_reset_payment_code" data-id="{{ $customer->cus_id }}">{{trans('localize.Reset_Secure_Code')}}</button>
                </div>
                @endif

                @if (!$customer->email_verified)
                <div class="col-sm-2">
                    <button type="button" class="btn btn-white btn-sm btn-block" id="resend_activation_email" data-id="{{ $customer->cus_id }}">@lang("localize.resend_verification_email")</button>
                </div>
                @endif
            </div>
        </div>
    </div>

    @include('admin.common.error')
    @include('admin.common.errors')
    @include('admin.common.success')

    <form id="customer-edit" class="form" action='/admin/customer/edit' method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-lg-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>{{trans('localize.customer')}} {{trans('localize.information')}}</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.Status')}}</label>
                            <select class="form-control"name="cus_status">
                                {{-- <option value="1" {{($customer->cus_status == 1)? 'selected':''}}>Active</option>
                                <option value="0"{{($customer->cus_status == 0)? 'selected':''}}>Inactive</option> --}}
                                <option value="1" {{(old('cus_status', $customer->cus_status) == 1)? 'selected':''}}>{{trans('localize.Active')}}</option>
                                <option value="0"{{(old('cus_status', $customer->cus_status) == 0)? 'selected':''}}>{{trans('localize.Inactive')}}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.Name')}} <span style="color:red;">*</span></label>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <select class="form-control" name="cus_title" style="width:100px;">
                                        <option value="">--</option>
                                        @foreach($select['person_titles'] as $id => $title)
                                        {{-- <option value="{{ $id }}" {{ (($customer->info) && $customer->info->cus_title == $id)? ' selected' : '' }}>{{ $title }}</option> --}}
                                        <option value="{{ $id }}" {{ (($customer->info) && $customer->info->cus_title == $id)? ' selected' : '' }}>{{ $title }}</option>
                                        @endforeach
                                    </select>
                                </span>
                                <input type="text" class="form-control" name='name' value="{{ old('name', $customer->cus_name) }}">
                            </div>
                        </div>

                        {{--  <div class="form-group">
                            <label class="control-label">Username <span style="color:red;">*</span></label>
                            <input type="text" class="form-control compulsary" id='username' name='username' value="{{ old('username', $customer->username) }}">
                        </div>  --}}

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.ic_number')}}</label>
                            <input type="text" class="form-control" id='identity_card' name='identity_card' value="{{ old('identity_card', $customer->identity_card) }}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.email')}}</label>
                            @if($edit_customer_email_permission)
                            <input type="text" class="form-control" id='email' name='email' value="{{ old('email', $customer->email) }}">
                            @else
                            <p class="form-control-static">{{ $customer->email }}</p>
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.Phone')}}</label>
                            @if($edit_customer_phone_permission)
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select class="form-control compulsary" name="areacode" id="areacode"></select>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <input class="form-control phone number" id="phone" name="cus_phone" value="{{ old('cus_phone', $customer->cus_phone) }}">
                                        <span class="mobilehint hint"><i class="fa fa-info-circle"></i> @lang('localize.mobileHint')</span>
                                    </div>
                                </div>
                            </div>
                            @else
                            <p class="form-control-static">{{ $customer->phone_area_code.$customer->cus_phone }}</p>
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.address')}}</label>
                            <input type="text" class="form-control" id="cus_address1" name='cus_address1' value="{{ old('cus_address1', $customer->cus_address1) }}">
                            <br>
                            <input type="text" class="form-control" id="cus_address2" name='cus_address2' value="{{ old('cus_address2', $customer->cus_address2) }}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.zipcode')}}</label>
                            <input type="text" class="form-control" name='cus_postalcode' value="{{ old('cus_postalcode', $customer->cus_postalcode) }}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.country')}}</label>
                            <select class="form-control" id="cus_country" name="cus_country" onchange="get_states('#cus_state', this.value)"></select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.State')}}</label>
                            <select class="form-control" id="cus_state" name="cus_state">
                                <option value="">{{trans('localize.selectCountry_first')}}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.city')}}</label>
                            <input type="text" class="form-control" name='cus_city' value="{{ old('cus_city',$customer->cus_city_name ) }}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang("common.credit_name") {{trans('localize.balance')}}</label>
                            <div class="row">
                                @foreach ($customer_wallets as $cw)
                                <div class="col-sm-6"><b>{{ ucfirst($cw->wallet->name) }} :</b> {{$cw->credit}}</div>
                                @endforeach
                                @if ($customer->special_wallet > 0)
                                <div class="col-sm-6"><b>Hemma :</b> {{$customer->special_wallet}}</div>
                                @endif
                            </div>
                            {{-- <button type="button" class="btn btn-white btn-sm"><a class="nolinkcolor" href="/admin/customer/credit/{{$customer->cus_id}}">{{ ($customer->v_token)? $customer->v_token : '0.00' }}</a></button> --}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>{{trans('localize.Other_Information')}}</h5>
                        <div class="ibox-tools">
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <label class="control-label">@lang('localize.job')</label>
                            <select class="form-control" name="cus_job">
                                <option value="">@lang('localize.selectOption')</option>
                                @foreach($select['jobs'] as $id => $title)
                                {{-- <option value="{{ $id }}" {{ (($customer->info) && $customer->info->cus_job == $id)? ' selected' : '' }}>{{ $title }}</option> --}}
                                <option value="{{ $id }}" {{($id == old('cus_job', empty($customer->info)?null:$customer->info->cus_job )) ? 'selected' : ''}}>{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.monthly_incomes')</label>
                            <select class="form-control" name="cus_incomes">
                                <option value="">@lang('localize.selectOption')</option>
                                @foreach($select['incomes'] as $id => $title)
                                {{-- <option value="{{ $id }}" {{ (($customer->info) && $customer->info->cus_incomes == $id)? ' selected' : '' }}>{{ $title }}</option> --}}
                                <option value="{{ $id }}" {{($id == old('cus_incomes', empty($customer->info)?null:$customer->info->cus_incomes )) ? 'selected' : ''}}>{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.education')</label>
                            <select class="form-control" name="cus_education">
                                <option value="">@lang('localize.selectOption')</option>
                                @foreach($select['educations'] as $id => $title)
                                {{-- <option value="{{ $id }}" {{ (($customer->info) && $customer->info->cus_education == $id)? ' selected' : '' }}>{{ $title }}</option> --}}
                                <option value="{{ $id }}" {{($id == old('cus_education', empty($customer->info)?null:$customer->info->cus_education )) ? 'selected' : ''}}>{{ $title }}</option>

                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.gender')</label>
                            <div class="i-checks">
                            {{-- <label class="control-label"><input type="radio" name="cus_gender" value="1" {{ (($customer->info) && $customer->info->cus_gender == 1)? ' checked' : 'checked' }}> Male</label>
                            <label class="control-label"><input type="radio" name="cus_gender" value="2" {{ (($customer->info) && $customer->info->cus_gender == 2)? ' checked' : '' }}> Female</label> --}}
                            <label class="control-label"><input type="radio" name="cus_gender" value="1" {{(old('cus_gender', empty($customer->info)?null:$customer->info->cus_gender) == 1)? 'checked' : '' }}> {{trans('localize.male')}}</label>
                            <label class="control-label"><input type="radio" name="cus_gender" value="2" {{(old('cus_gender', empty($customer->info)?null:$customer->info->cus_gender) == 2)? 'checked' : '' }}> {{trans('localize.female')}}</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.dob')</label>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group date">
                                        {{-- <input type="text" class="form-control" name="cus_dob" id="dob" placeholder="DOB" style="width:100%;" readonly value="{{ ($customer->info) ? $customer->info->cus_dob : '' }}"/> --}}
                                        <input type="text" class="form-control" name="cus_dob" id="dob" placeholder="{{trans('localize.dob')}}" style="width:100%;" readonly value="{{ old('cus_dob', empty($customer->info)?null:$customer->info->cus_dob) ? old('cus_dob', date('d-m-Y', strtotime($customer->info->cus_dob))) : '' }}"/>
                                        <span class="input-group-addon open-calendar"><i class="fa fa-calendar open-calendar"></i></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.nationality')</label>
                            <select class="form-control" name="cus_nationality">
                                <option value="">@lang('localize.selectOption')</option>
                                @foreach($select['nationality'] as $id => $title)
                                {{-- <option value="{{ $id }}" {{ (($customer->info) && $customer->info->cus_nationality == $id)? ' selected' : '' }}>{{ $title }}</option> --}}
                                <option value="{{ $id }}" {{($id == old('cus_nationality', empty($customer->info)?null:$customer->info->cus_nationality )) ? 'selected' : ''}}>{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.race')</label>
                            <select class="form-control" name="cus_race">
                                <option value="">@lang('localize.selectOption')</option>
                                @foreach($select['races'] as $id => $title)
                                {{-- <option value="{{ $id }}" {{ (($customer->info) && $customer->info->cus_race == $id)? ' selected' : '' }}>{{ $title }}</option> --}}
                                <option value="{{ $id }}" {{($id == old('cus_race', empty($customer->info)?null:$customer->info->cus_race )) ? 'selected' : ''}}>{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.religion')</label>
                            <select class="form-control" name="cus_religion">
                                <option value="">@lang('localize.selectOption')</option>
                                @foreach($select['religions'] as $id => $title)
                                {{-- <option value="{{ $id }}" {{ (($customer->info) && $customer->info->cus_religion == $id)? ' selected' : '' }}>{{ $title }}</option> --}}
                                <option value="{{ $id }}" {{($id == old('cus_religion', empty($customer->info)?null:$customer->info->cus_religion )) ? 'selected' : ''}}>{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.marital_status')</label>
                            <select class="form-control" name="cus_marital">
                                <option value="">@lang('localize.selectOption')</option>
                                @foreach($select['marital'] as $id => $title)
                                {{-- <option value="{{ $id }}" {{ (($customer->info) && $customer->info->cus_marital == $id)? ' selected' : '' }}>{{ $title }}</option> --}}
                                <option value="{{ $id }}" {{($id == old('cus_marital', empty($customer->info)?null:$customer->info->cus_marital )) ? 'selected' : ''}}>{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.no_of_children')</label>
                            {{-- <input type="number" class="form-control" name="cus_children" placeholder="" value="{{ ($customer->info) ? $customer->info->cus_children : '0' }}"> --}}
                            <input type="number" class="form-control" name="cus_children" placeholder="" value="{{  old('cus_children', empty($customer->info)?null:$customer->info->cus_children) ? old('cus_children', $customer->info->cus_children) : '0' }}">
                            @if ($errors->has('children'))
                                <span class="help-block">
                                    {{ $errors->first('children') }}
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="control-label">@lang('localize.hobby')</label>
                            {{-- <input type="text" class="form-control" name="cus_hobby" placeholder="" value="{{ ($customer->info) ? $customer->info->cus_hobby : '' }}" > --}}
                            <input type="text" class="form-control" name="cus_hobby" placeholder="" value="{{ old('cus_hobby', empty($customer->info)?null:$customer->info->cus_hobby) }}" >
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{--  <div class="col-lg-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>{{trans('localize.Shipping')}} {{trans('localize.information')}}</h5>
                        <div class="ibox-tools">
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.address')}}</label>
                            <input type="text" class="form-control" id="address1" name='address1' value="{{ old('address1', $customer->cus_address1) }}">
                            <br>
                            <input type="text" class="form-control" id="address2" name='address2' value="{{ old('address2', $customer->cus_address2) }}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.zipcode')}}</label>
                            <input type="text" class="form-control" name='zipcode' value="{{ old('zipcode', $customer->cus_postalcode) }}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.country')}}</label>
                            <select class="form-control" id="country" name="country">
                                <option value="">{{trans('localize.selectCountry')}}</option>
                                @foreach($countries as $key => $co)
                                    <option value="{{$co->co_id}}" {{($co->co_id == old('country', $customer->cus_country )) ? 'selected' : ''}}>{{$co->co_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.State')}}</label>
                            <select class="form-control" id="state" name="state">
                                <option value="">{{trans('localize.selectCountry_first')}}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.city')}}</label>
                            <input type="text" class="form-control" name='city_name' value="{{ old('city_name', $customer->cus_city_name) }}">
                        </div>
                    </div>
                </div>
            </div>  --}}

            <div class="col-lg-12">
                <input type="hidden" class="form-control" name='cus_id' value="{{$customer->cus_id}}">
                <div class="form-group">
                    <div class="col-sm-3 pull-right">
                        {{-- <button class="btn btn-block btn-primary" id="submit">{{trans('localize.Edit')}} {{trans('localize.customer')}}</button><br> --}}
                        <input id="update-info" type="button" class="btn btn-block btn-primary" value="{{trans('localize.Edit')}} {{trans('localize.customer')}}"><br/>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal inmodal" id="password_reset_modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content animated flipInY">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">{{trans('localize.close_')}}</span></button>
                <h4 class="modal-title">{{trans('localize.reset_password')}}</h4>
                <small class="font-bold">{{trans('localize.This_form_will')}} <span class="text-danger">{{trans('localize.override_exsisting_customer_password')}}</span> {{trans('localize.and_email_the_new_password_to_customer_based_on_email_from_customer_details_below')}}</small>
                <br/>
                <button type="button" id="soft_reset_password" data-id="{{ $customer->cus_id }}" class="btn btn-xs btn-default pull-right"><i class="fa fa-magic"></i> {{trans('localize.Soft_Reset_Password')}}</button>
            </div>
            <div class="modal-body">
                <form id="resetpassword" action="/admin/customer/reset_password" class="form-horizontal" method="POST">
                {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.customer')}} {{trans('localize.details')}}</label>
                        <p class="form-control-static">
                            <span>ID : {{$customer->cus_id}}</span><br>
                            <span>{{trans('localize.Name')}} : {{$customer->cus_name}}</span><br>
                            <span>{{trans('localize.email')}} : {{$customer->email}}</span><br>
                            <span>{{trans('localize.Phone')}} : {{$customer->cus_phone}}</span><br>
                        </p>
                        <input type="hidden" name="cus_id" value="{{$customer->cus_id}}">
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.new_password')}}</label>
                        <input type="password" class="form-control" id="password" name="password" onchange="check_length(this.value);">
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.confirm_password')}}</label>
                        <input type="password" class="form-control" id="confirmpassword" name="password_confirmation" onchange="check_password(this.value);">
                    </div>
                </form>
            </div>
            <div class="modal-footer"><button type="button" id="reset_password" class="btn btn-primary"><i class="fa fa-envelope-o"></i> {{trans('localize.reset_password')}}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal inmodal" id="code_reset_modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content animated flipInY">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">{{trans('localize.close_')}}</span></button>
                <h4 class="modal-title">{{trans('localize.Reset_Payment_Secure_Code')}}</h4>
                <small class="font-bold">{{trans('localize.This_form_will')}} <span class="text-danger">{{trans('localize.override_exsisting_customer_payment_secure_code')}}</span> {{trans('localize.and_email_the_new_payment_secure_code_to_customer_based_on_email_from_customer_details_below')}}</small>
                <br/>
                <button type="button" id="soft_reset_payment_code" data-id="{{ $customer->cus_id }}" class="btn btn-xs btn-default pull-right"><i class="fa fa-magic"></i> {{trans('localize.Soft_Reset_Secure_Code')}}</button>
            </div>
            <div class="modal-body">
                <form id="update_code" action="/admin/customer/reset_secure_code" class="form-horizontal" method="POST">
                {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.customer')}} {{trans('localize.details')}}</label>
                        <p class="form-control-static">
                            <span>ID : {{$customer->cus_id}}</span><br>
                            <span>{{trans('localize.Name')}} : {{$customer->cus_name}}</span><br>
                            <span>{{trans('localize.email')}} : {{$customer->email}}</span><br>
                            <span>{{trans('localize.Phone')}} : {{$customer->cus_phone}}</span><br>
                        </p>
                        <input type="hidden" name="cus_id" value="{{$customer->cus_id}}">
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 text-center">{{trans('localize.New_Payment_Secure_Code')}}</label>
                        <div class="col-xs-12">
                            <div class="col-xs-2">
                                <input id="new1" type="password" class="form-control text-center nopadding" data-key="1" name="new" maxlength="1">
                            </div>
                            <div class="col-xs-2">
                                <input id="new2" type="password" class="form-control text-center nopadding" data-key="2" name="new" maxlength="1">
                            </div>
                            <div class="col-xs-2">
                                <input id="new3" type="password" class="form-control text-center nopadding" data-key="3" name="new" maxlength="1">
                            </div>
                            <div class="col-xs-2">
                                <input id="new4" type="password" class="form-control text-center nopadding" data-key="4" name="new" maxlength="1">
                            </div>
                            <div class="col-xs-2">
                                <input id="new5" type="password" class="form-control text-center nopadding" data-key="5" name="new" maxlength="1">
                            </div>
                            <div class="col-xs-2">
                                <input id="new6" type="password" class="form-control text-center nopadding" data-key="6" name="new" maxlength="1">
                            </div>
                            <input id="newcode" type="hidden" name="securecode">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 text-center">{{trans('localize.Confirm_Payment_Secure_Code')}}</label>
                        <div class="col-xs-12">
                            <div class="col-xs-2">
                                <input id="confirm1" type="password" class="form-control text-center nopadding" data-key="1" name="confirm" maxlength="1">
                            </div>
                            <div class="col-xs-2">
                                <input id="confirm2" type="password" class="form-control text-center nopadding" data-key="2" name="confirm" maxlength="1">
                            </div>
                            <div class="col-xs-2">
                                <input id="confirm3" type="password" class="form-control text-center nopadding" data-key="3" name="confirm" maxlength="1">
                            </div>
                            <div class="col-xs-2">
                                <input id="confirm4" type="password" class="form-control text-center nopadding" data-key="4" name="confirm" maxlength="1">
                            </div>
                            <div class="col-xs-2">
                                <input id="confirm5" type="password" class="form-control text-center nopadding" data-key="5" name="confirm" maxlength="1">
                            </div>
                            <div class="col-xs-2">
                                <input id="confirm6" type="password" class="form-control text-center nopadding" data-key="6" name="confirm" maxlength="1">
                            </div>
                            <input id="confirmcode" type="hidden" name="securecode_confirmation">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="reset_payment_code" class="btn btn-primary"><i class="fa fa-envelope-o"></i> {{trans('localize.Reset_Secure_Code')}}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal inmodal" id="manage_credit" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content animated flipInY">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">{{trans('localize.close')}}</span></button>
                <h4 class="modal-title">{{trans('localize.manage')}} @lang("common.credit_name")</h4>
                <small class="font-bold">{{trans('localize.This_form_is_use_for_debit_or_credit_customer')}} @lang("common.credit_name") {{trans('localize.amount')}}</small>
            </div>
            <div class="modal-body">
                <form id="managecredit" action="/admin/customer/manage_credit/{{$customer->cus_id}}" class="form-horizontal" method="POST">
                {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.customer')}} {{trans('localize.details')}}</label>
                        <p class="form-control-static">
                            <div class="row>">
                                <div class="col-sm-12">ID : {{$customer->cus_id}}</div>
                                <div class="col-sm-12">{{trans('localize.Name')}} : {{$customer->cus_name}}</div>
                                <div class="col-sm-12">{{trans('localize.email')}} : {{$customer->email}}</div>
                                <div class="col-sm-12">{{trans('localize.Phone')}} : {{$customer->cus_phone}}</div>
                                <div class="col-sm-12">{{trans('localize.Current')}} @lang("common.credit_name") : {{$customer->v_token}}</div>
                                @foreach ($customer_wallets as $cw)
                                <div class="col-sm-6">{{ ucfirst($cw->wallet->name) }} : {{$cw->credit}}</div>
                                @endforeach
                                @if ($customer->special_wallet > 0)
                                <div class="col-sm-6">Hemma : {{$customer->special_wallet}}</div>
                                @endif
                            </div>
                        </p>
                        <input type="hidden" name="cus_id" value="{{$customer->cus_id}}">
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 text-left" style="height:3em; vertical-align: middle;">
                            <label class="control-label">{{trans('localize.type')}}</label>
                        </div>
                        <div class="col-sm-3" style="height:3em; vertical-align: middle;">
                            <div class="i-checks"><label class="control-label"> <input type="radio" id="credit" value="credit" name="type" checked> <i></i> {{trans('localize.credit')}} </label></div>
                        </div>
                        <div class="col-sm-3" style="height:3em; vertical-align: middle;">
                            <div class="i-checks"><label class="control-label"> <input type="radio" id="debit" value="debit" name="type"> <i></i> {{trans('localize.debit')}} </label></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">@lang("localize.svi_wallet")</label>
                        <select class="form-control" id="wallet" name="wallet">
                            @foreach($wallets as $key => $wallet)
                                <option value="{{ $wallet->id }}">{{ $wallet->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">@lang("common.credit_name") {{trans('localize.amount')}}</label>
                        <input type="text" step="any" class="form-control number" id="amount" name="amount">
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.Remark')}}</label>
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
<link href="/backend/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
<link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
<link href="/backend/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
<style>
.col-xs-2{
    padding:0px;
}
</style>
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script src="/backend/js/plugins/sweetalert/sweetalert.min.js"></script>
<script src="/backend/js/plugins/iCheck/icheck.min.js"></script>
<script src="/backend/js/plugins/datapicker/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="/backend/js/custom.js"></script>

<script>
    $(document).ready(function() {
        $('.number').keydown(function (e) {-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});

        get_countries('#cus_country', "{{ old('cus_country', $customer->cus_country) }}", '#cus_state', "{{ old('cus_state', $customer->cus_state) }}");
        get_phoneAreacode('#areacode', "{{ old('areacode', $customer->phone_area_code) }}");

        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $("#email").on({
            keydown: function(e) {
                if (e.which === 32)
                return false;
            },
            change: function(e) {
                this.value = this.value.replace(/\s/g, "");
                if(this.value.length != 0)
                    check_member_email($(this), $(this).val(), '{{ $customer->cus_id }}', e);
            }
        });

        {{--  $("#phone").on({
            keydown: function(e) {
                if (e.which === 32)
                return false;
            },
            change: function(e) {
                this.value = this.value.replace(/\s/g, "");
                check_member_phone($(this), $(this).val(), '{{ $customer->cus_id }}', e);
            }
        });  --}}

        $("#password").on({
            change: function(e) {
                var passwordRegex = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])([a-zA-Z0-9]{6,})$/;

                if(!passwordRegex.test($(this).val())) {
                    swal("@lang('localize.swal_error')", "Hint! Password must contain at least 6 alphanumerical and 1 Uppercase", "error");
                    $(this).css('border', '1px solid red').focus();
                } else {
                    $(this).css('border', '');
                }
            }
        });

        $('#dob').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            format: 'dd-mm-yyyy',
            autoclose: true,
            forceParse: false,
            Default: true,
            todayHighlight: true,
        });

        $('.open-calendar').click(function(event){
            event.preventDefault();
            $('#dob').focus();
        });

        $('#update-info').click(function() {

            var isValid = true

            $(':input, select').each(function(e) {
                if ($(this).hasClass('compulsary')) {
                    console.log($(this).attr('name'));
                    if (!$(this).val()) {
                        $(this).attr('placeholder', '{{trans('localize.fieldrequired')}}').css('border', '1px solid red').focus();
                        isValid = false;
                        return false;
                    }
                }

                $(this).css('border', '');
            });

            if (isValid) {
                $('#customer-edit').submit();
            }
        });

        $('#reset_password').click(function() {

            var passwordRegex = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])([a-zA-Z0-9]{6,})$/;

            if($('#password').val() == '') {
                $('#password').attr('placeholder', '{{trans('localize.fieldrequired')}}');
                $('#password').css('border', '1px solid red');
                return false;
            } else {
                $('#password').css('border', '');
            }

            if(!passwordRegex.test($('#password').val())) {
                swal("@lang('localize.swal_error')", "Hint! Password must contain at least 6 alphanumerical and 1 Uppercase", "error");
                $('#password').css('border', '1px solid red').focus();
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

            if($('#password').val() != $('#confirmpassword').val()) {
                swal("@lang('localize.swal_error')", "@lang('localize.password_not_match')", "error");
                $('#password').css('border', '1px solid red');
                $('#confirmpassword').css('border', '');
                return false;
            } else {
                $('#password').css('border', '');
            }

            swal({
                title: "{{trans('localize.sure')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d9534f",
                confirmButtonText: "{{trans('localize.reset_password')}}",
				cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function(){
                    $("#resetpassword").submit();
                }
            );

        });

        $('#soft_reset_password').click(function() {
            var id = $(this).attr('data-id');
            swal({
                title: "{{trans('localize.sure')}}",
                text: "{{trans('localize.This_will_auto_generate_password_for_customer_and_email_the_password')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d9534f",
                confirmButtonText: "{{trans('localize.reset_password')}}",
				cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function(){
                    window.location.href = '/admin/customer/soft_reset_password/' + id;
                }
            );
        });

        $('#reset_payment_code').click(function(e) {
            e.preventDefault();
            var form = $(this).parents('#update_code');

            var new1 = $('#new1').val();
            var new2 = $('#new2').val();
            var new3 = $('#new3').val();
            var new4 = $('#new4').val();
            var new5 = $('#new5').val();
            var new6 = $('#new6').val();

            var confirm1 = $('#confirm1').val();
            var confirm2 = $('#confirm2').val();
            var confirm3 = $('#confirm3').val();
            var confirm4 = $('#confirm4').val();
            var confirm5 = $('#confirm5').val();
            var confirm6 = $('#confirm6').val();

            var newcode = new1+new2+new3+new4+new5+new6;
            var confirmcode = confirm1+confirm2+confirm3+confirm4+confirm5+confirm6;


            if(newcode != confirmcode){
                swal("{{trans('localize.error')}}", "{{trans('localize.Payment_secure_code_does_not_match')}}", "error");
                return false;
            }
            else {
                $('#newcode').val(newcode);
                $('#confirmcode').val(confirmcode);

                swal({
                    title: "{{trans('localize.sure')}}",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "{{trans('localize.Reset_Secure_Code')}}",
					cancelButtonText: "{{trans('localize.cancel')}}",
                    closeOnConfirm: false
                }, function(isConfirm){
                        if (isConfirm) $("#update_code").submit();
                    }
                );
            }

        });

        $('#soft_reset_payment_code').click(function() {
            var id = $(this).attr('data-id');
            swal({
                title: "{{trans('localize.sure')}}",
                text: "{{trans('localize.This_will_auto_generate_secure_code_for_customer_and_email_the_secure_code')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d9534f",
                confirmButtonText: "{{trans('localize.Reset_Secure_Code')}}",
				cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function(){
                    window.location.href = '/admin/customer/soft_reset_secure_code/' + id;
                }
            );
        });

        $('#resend_activation_email').click(function() {
            var id = $(this).attr('data-id');
            swal({
                title: "{{trans('localize.sure')}}",
                text: "{{trans('localize.resend_verification_email')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d9534f",
                confirmButtonText: "{{trans('localize.swal_ok')}}",
				cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function(){
                    window.location.href = '/admin/customer/resend_activation/' + id;
                }
            );
        });

        $('#submit_credit').click(function() {
            var cus_wallets = {!! json_encode($customer_wallets) !!};
            var amount = parseFloat($('#amount').val());

            if($('#amount').val() == '') {
                $('#amount').attr('placeholder', '{{trans("localize.Please_insert")}} @lang("common.credit_name") {{trans("localize.amount")}}');
                $('#amount').css('border', '1px solid red');
                return false;
            } else {
                $('#amount').css('border', '');
            }

            if($('#remark').val() == '') {
                $('#remark').attr('placeholder', '{{trans("localize.Please_fill_remarks_field")}}');
                $('#remark').css('border', '1px solid red');
                return false;
            } else {
                $('#remark').css('border', '');
            }

            if($('#debit').is(':checked')) {
                var limit = 0;
                $.each(cus_wallets, function(idx, value) {
                    if(value['wallet']['id'] == $('#wallet :selected').val())
                        limit = value['credit'];
                });

                if(amount > limit) {
                    swal("Error!", "Insufficient customer credit to deduct", "error");
                    $('#amount').css('border', '1px solid red');
                    return false;
                } else if(amount < 0.0001) {
                    swal("Error!", "Minimum credit to deduct is 0.0001", "error");
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
                closeOnConfirm: true,
            }, function(isConfirm){
                    if (isConfirm) $("#managecredit").submit();
                }
            );
        });

        $("input[name='new']").keyup(function () {
            var next = parseInt($(this).attr('data-key')) + 1;
            if (next < 7) {
            var $next = $("#new"+next);
            $next.val('');
            $next.focus();
            $(this).blur();
            }
        });

        $("input[name='confirm']").keyup(function () {
            var next = parseInt($(this).attr('data-key')) + 1;
            if (next < 7) {
            var $next = $("#confirm"+next);
            $next.val('');
            $next.focus();
            $(this).blur();
            }
        });

        $('#confirm1, #confirm2, #confirm3, #confirm4, #confirm5, #confirm6, #new1, #new2, #new3, #new4, #new5, #new6').keydown(function (e) {
            if (e.shiftKey || e.ctrlKey || e.altKey) {
                e.preventDefault();
            } else {
                var key = e.keyCode;
                if (!((key == 8) || (key == 46) || (key >= 35 && key <= 40) || (key >= 48 && key <= 57) || (key >= 96 && key <= 105))) {
                    e.preventDefault();
                }
            }
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
</script>
@endsection
