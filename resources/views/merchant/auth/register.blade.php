@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.backend')
@endsection

@section('content')
<div class="page-title">
    <a href="javascript:history.go(-1)" class="back transition">
        <i class="fa fa-angle-left"></i>
    </a>
    @lang('localize.mer_signup')
</div>
<div class="ContentWrapper">

    @include('merchant.common.errors')

    <form id="merc_register" method="POST" action="{{ url('merchant/register') }}" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel-general panel-merchant active">
        <h4 class="panel-title">
            <a id="show-merchant">
                <span class="notextdeco">@lang('localize.mer_information')</span>
            </a>
        </h4>
        <div class="form-general">

            <div class="col-sm-12">
                <div class="form-group">
                    <input type="text" class="form-control compulsary username" placeholder="@lang('localize.username')" name="username"
                        value="{{ old('username') }}">
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <input type="text" class="form-control compulsary email merchant-email" placeholder="@lang('localize.email')" name="email" value="{{ old('email') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="password" class="form-control compulsary password" placeholder="@lang('localize.password')" id="password" name="password">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="password" class="form-control compulsary password" placeholder="@lang('localize.confirmPassword')" id="cpassword"
                        name="password_confirmation">
                </div>
            </div>

            <div class="col-sm-12 form-group">
                <div id="password_hint" class="hint" style="color: #a94442; display: none;"></div>
            </div><br>

            <div class="col-sm-12">
                <div class="form-group">
                    <label for="stor_type">@lang('localize.mer_type')</label>
                    <select class="form-control" id="stor_type" name="stor_type">
                        <option value="0" {{ old('stor_type') == 0? 'selected' : ''}}>@lang('localize.online_store')</option>
                        <option value="1" {{ old('stor_type') == 1? 'selected' : ''}}>@lang('localize.offline_store')</option>
                    </select>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" placeholder="@lang('localize.firstname')" class="form-control compulsary" name="fname" value="{{ old('fname') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" placeholder="@lang('localize.lastname')" class="form-control compulsary" name="lname" value="{{ old('lname') }}">
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <input type="text" class="form-control compulsary" name="address1" placeholder="@lang('localize.address1')"
                        value="{{ old('address1') }}">
                    <br>
                    <input type="text" class="form-control" name="address2" placeholder="@lang('localize.address2')" value="{{ old('address2') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" placeholder="@lang('localize.city')" class="form-control compulsary" name="mer_city_name" value="{{ old('mer_city_name') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" placeholder="@lang('localize.zipcode')" class="form-control compulsary numeric" id="zipcode" name="zipcode" value="{{ old('zipcode') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <select class="form-control compulsary" id="country" name="country" onchange="get_states('#state', this.value)"></select>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <select class="form-control compulsary" id="state" name="state">
                        <option value="">@lang('localize.selectCountry_first')</option>
                    </select>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" placeholder="@lang('localize.phone')" class="form-control compulsary numeric" name="tel" value="{{ old('tel') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" placeholder="@lang('localize.office_number')" class="form-control compulsary numeric" name="office_number" value="{{ old('office_number') }}">
                </div>
            </div>

            <a id="next-store" class="btn btn-primary ">@lang('localize.next')</a>
        </div>
    </div>

    <div class="panel-general panel-store">
        <h4 class="panel-title">
            <a id="show-merchant">
                <span class="notextdeco">@lang('localize.mer_store_information')</span>
            </a>
        </h4>
        <div class="form-general">

            <div class="col-sm-12">
                <div class="form-group">
                    <input type="text" placeholder="@lang('localize.mer_store_name')" class="form-control compulsary" name="stor_name" value="{{ old('stor_name') }}">
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <label>@lang('localize.mer_website')</label>
                    <div class="input-group">
                        <span class="input-group-btn">
                            <select id="http" class="btn btn-default" style="height:40px;outline:none;margin-right:0;">
                                <option value="http://" {{ (old('stor_website') && parse_url(old('stor_website'))['scheme'] == 'http')? 'selected' : '' }} >http://</option>
                                <option value="https://" {{ (old('stor_website') && parse_url(old('stor_website'))['scheme'] == 'https')? 'selected' : '' }}>https://</option>
                            </select>
                        </span>
                        <input type="text" class="form-control website" id="website" placeholder="@lang('localize.mer_website_desc')" value="{{ old('stor_website') && isset(parse_url(old('stor_website'))['host'])? parse_url(old('stor_website'))['host'] : '' }}">
                        <input type="hidden" name="stor_website" id="stor_website">
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <input type="text" class="form-control compulsary" name="stor_address1" placeholder="@lang('localize.address1')" value="{{ old('stor_address1') }}">
                    <br>
                    <input type="text" class="form-control" name="stor_address2" placeholder="@lang('localize.address2')" value="{{ old('stor_address2') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" placeholder="@lang('localize.zipcode')" class="form-control compulsary numeric" name="stor_zipcode" value="{{ old('stor_zipcode') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" placeholder="@lang('localize.city')" class="form-control compulsary" name="stor_city_name" value="{{ old('stor_city_name') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <select class="form-control compulsary" id="stor_country" name="stor_country" onchange="get_states('#stor_state', this.value)"></select>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <select class="form-control compulsary" id="stor_state" name="stor_state">
                        <option value="">@lang('localize.selectCountry_first')</option>
                    </select>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" placeholder="@lang('localize.phone')" class="form-control compulsary numeric" name="stor_phone" value="{{ old('stor_phone') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" placeholder="@lang('localize.office_number')" class="form-control compulsary numeric" name="stor_office_number" value="{{ old('stor_office_number') }}">
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <input type="text" placeholder="@lang('localize.mer_meta_keyword')" class="form-control" name="stor_metakeywords" value="{{ old('stor_metakeywords') }}">
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <input type="text" placeholder="@lang('localize.mer_meta_description')" class="form-control" name="stor_metadesc" value="{{ old('stor_metadesc') }}">
                </div>
            </div>

            <div class="col-sm-12">
                {{-- MAP --}}
                <input type="hidden" id="map_type" name="map_type" value="{{ old('old_map_type') }}">
                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longtitude" name="longtitude">

                <div class="form-group">
                    <div class="panel with-nav-tabs panel-default">
                        <div class="panel-heading">
                            <ul class="nav nav-tabs">
                                <li><a id="tab_baidu_map" href="#pane_baidu_map" data-toggle="tab">@lang('localize.map_baidu')</a></li>
                                <li class="active"><a id="tab_google_map" href="#pane_google_map" data-toggle="tab">@lang('localize.map_google')</a></li>
                            </ul>
                        </div>
                        <div class="panel-body">
                            <div class="tab-content">
                                <div class="tab-pane fade" id="pane_baidu_map">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>@lang('localize.mer_search_store')</label>
                                            <input type="text" class="form-control" id="location_baidu" name="location_baidu">
                                            <div id="searchResultPanel" style="border:1px solid #C0C0C0;width:150px;height:auto; display:none;"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label><small>@lang('localize.mapguide')</small></label>
                                            <div id="l_map" ></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{trans('localize.latitude')}} </label>
                                            <input type="text" class="form-control" id="latitude_baidu" value="{{ old('latitude') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{trans('localize.longtitude')}} </label>
                                            <input type="text" class="form-control" id="longitude_baidu" value="{{ old('longtitude') }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade active in" id="pane_google_map">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>@lang('localize.mer_search_store')</label>
                                            <div class="form-group">
                                                <input type="text" class="form-control gllpSearchField" id="location_google" name="location_google">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label><small>@lang('localize.mapguide')</small></label>
                                            <div class="gllpMap" id="map_canvas">{{trans('localize.googleMaps')}}</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{trans('localize.latitude')}} </label>
                                            <input type="text" class="form-control gllpLatitude compulsary" id="latitude_google" value="{{ old('latitude') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{trans('localize.longtitude')}} </label>
                                            <input type="text" class="form-control gllpLongitude compulsary" id="longitude_google" value="{{ old('longtitude') }}" readonly>
                                        </div>
                                    </div>

                                    <input type="text" class="gllpZoom" style="visibility:hidden">
                                    <input class="gllpUpdateButton" style="visibility:hidden">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- END MAP --}}
            </div>

            {{--  <div class="col sm-12">
                <div class="form-group col-sm-6">
                    <label>@lang('localize.latitude')</label>
                    <span class="required"> *</span>
                    <input type="text" class="form-control gllpLatitude" id="latitude" name="latitude" value="{{ old('latitude') }}" readonly>
                </div>
                <div class="form-group col-sm-6">
                    <label>@lang('localize.longtitude')</label>
                    <span class="required"> *</span>
                    <input type="text" class="form-control gllpLongitude" id="longtitude" name="longtitude" value="{{ old('longtitude') }}" readonly>
                </div>
            </div>  --}}

            <div class="col-sm-12">
                <div class="form-group">
                    <label>@lang('localize.storeImg')</label>
                    <input type="file" class="form-control compulsary image" name="stor_img">
                    <label id="imgError" style="color:red;"></label>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <input type="text" placeholder="@lang('localize.short_description')" class="form-control" name="short_description" value="{{ old('short_description') }}">
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <textarea class="form-control" placeholder="@lang('localize.long_description')" rows="5" style="resize: none;" name="long_description">{{ old('long_description') }}</textarea>
                </div>
            </div>

            <div class="col-sm-12" id="stor_category_div" style="display: none;">
                <div class="form-group">
                    <label>{{trans('localize.select_category')}}</label>
                    <span class="required"> *</span>
                    <div class="panel panel-default" style="padding:20px;">
                        <table id="showCats" class="table table-condensed"></table>
                        <br>
                        <input type="hidden" id="stor_category" name="stor_category" value="[]">
                        <div id="cats" class="demo"></div>
                    </div>
                </div>
            </div>

            <a id="next-referrer" class="btn btn-primary ">@lang('localize.next')</a>
        </div>
    </div>

    <div class="panel-general panel-referrer">
        <h4 class="panel-title">
            <a id="show-merchant">
                <span class="notextdeco">@lang('localize.referrer_info')</span>
            </a>
        </h4>
        <div class="form-general">

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="@lang('localize.guarantor_name')" name="referrer_name" value="{{ old('referrer_name') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="@lang('localize.guarantor_username')" name="referrer_username" value="{{ old('referrer_username') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="@lang('localize.guarantor_nationality')" name="referrer_nationality" value="{{ old('referrer_nationality') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control numeric" placeholder="@lang('localize.guarantor_phone')" name="referrer_phone" value="{{ old('referrer_phone') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control email" placeholder="@lang('localize.guarantor_email')" name="referrer_email" value="{{ old('referrer_email') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="@lang('localize.guarantor_bank_name')" name="referrer_bank_name" value="{{ old('referrer_bank_name') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="@lang('localize.guarantor_acc_name')" name="referrer_acc_name" value="{{ old('referrer_acc_name') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="@lang('localize.guarantor_bank_acc')" name="referrer_bank_acc" value="{{ old('referrer_bank_acc') }}">
                </div>
            </div>

            <a id="next-guarantor" class="btn btn-primary ">@lang('localize.next')</a>
        </div>
    </div>

    <div class="panel-general panel-guarantor">
        <h4 class="panel-title">
            <a id="show-merchant">
                <span class="notextdeco">@lang('localize.guarantor_info')</span>
            </a>
        </h4>
        <div class="form-general">

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="@lang('localize.guarantor_name')" name="guarantor_name" value="{{ old('guarantor_name') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="@lang('localize.guarantor_username')" name="guarantor_username" value="{{ old('guarantor_username') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="@lang('localize.guarantor_nationality')" name="guarantor_nationality" value="{{ old('guarantor_nationality') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control numeric" placeholder="@lang('localize.guarantor_phone')" name="guarantor_phone" value="{{ old('guarantor_phone') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control email" placeholder="@lang('localize.guarantor_email')" name="guarantor_email" value="{{ old('guarantor_email') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="@lang('localize.guarantor_bank_name')" name="guarantor_bank_name" value="{{ old('guarantor_bank_name') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="@lang('localize.guarantor_acc_name')" name="guarantor_acc_name" value="{{ old('guarantor_acc_name') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="@lang('localize.guarantor_bank_acc')" name="guarantor_bank_acc" value="{{ old('guarantor_bank_acc') }}">
                </div>
            </div>

            <a id="next-bank" class="btn btn-primary ">@lang('localize.next')</a>
        </div>
    </div>

    <div class="panel-general panel-bank">
        <h4 class="panel-title">
            <a id="show-merchant">
                <span class="notextdeco">@lang('localize.mer_bank_information')</span>
            </a>
        </h4>
        <div class="form-general">

            <div class="col-sm-12">
                <div class="form-group">
                    <input type="text" class="form-control compulsary" placeholder="@lang('localize.mer_acc_holder')" name="bank_acc_name" value="{{ old('bank_acc_name') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control compulsary" placeholder="@lang('localize.mer_acc_number')" name="bank_acc_no" value="{{ old('bank_acc_no') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control compulsary" placeholder="@lang('localize.gst_no')" name="bank_gst" value="{{ old('bank_gst') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" placeholder="@lang('localize.mer_bank_name')" class="form-control compulsary" name="bank_name" value="{{ old('bank_name') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <select class="form-control compulsary" id="bank_country" name="bank_country"></select>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <input type="text" placeholder="@lang('localize.mer_bank_address')" class="form-control compulsary" name="bank_address" value="{{ old('bank_address') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" placeholder="@lang('localize.bank_swift')" class="form-control" name="bank_swift" value="{{ old('bank_swift') }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" placeholder="@lang('localize.if_europe')" class="form-control" name="bank_europe" value="{{ old('bank_europe') }}">
                </div>
            </div>

            <a id="next-review" class="btn btn-primary ">@lang('localize.next')</a>
        </div>
    </div>

    </form>

    <div class="panel-general panel-review">
        <h4 class="panel-title">
            <a id="show-merchant">
                <span class="notextdeco">@lang('localize.reg_review')</span>
            </a>
        </h4>
        <div class="form-general">

            {{--  Start merchant info  --}}
            <div class="col-sm-12 seperator"></div>
            <h4>@lang('localize.mer_loginAuth')</h4>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.email')</label>
                    <label class="form-control" id="preview-merchant-email" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.username')</label>
                    <label class="form-control" id="preview-merchant-username" readonly></label>
                </div>
            </div>

            <div class="col-sm-12 seperator"></div>
            <h4>@lang('localize.mer_information')</h4>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.firstname')</label>
                    <label class="form-control" id="preview-merchant-fname" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.lastname')</label>
                    <label class="form-control" id="preview-merchant-lname" readonly></label>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>@lang('localize.address')</label>
                    <label type="text" class="form-control" id="preview-merchant-address1" readonly></label>
                    <br>
                    <label type="text" class="form-control" id="preview-merchant-address2" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.country')</label>
                    <label class="form-control" id="preview-merchant-country" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.zipcode')</label>
                    <label class="form-control" id="preview-merchant-zipcode" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.state')</label>
                    <label class="form-control" id="preview-merchant-state" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.mobile')</label>
                    <label class="form-control" id="preview-merchant-tel" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.office_number')</label>
                    <label class="form-control" id="preview-merchant-office_number" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.city')</label>
                    <label class="form-control" id="preview-merchant-mer_city_name" readonly></label>
                </div>
            </div>

            <div class="form-action text-center">
                <button id="prev-merchant" class="btn btn-primary">@lang('localize.mer_edit_merchant_information')</button>
            </div>
            <div class="col-sm-12 seperator"></div>
            {{--  End merchant info  --}}

            {{--  Start store info  --}}
            <h4>@lang('localize.mer_store_information')</h4>
            {{--
            <div class="col-sm-12">--}} {{--
                <div class="form-group">--}} {{--
                    <label>@lang('localize.mer_store_type')</label>--}} {{--
                    <label class="form-control" id="rs_type" readonly></label>--}} {{--
                </div>--}} {{--
            </div>--}}
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.mer_store_name')</label>
                    <label class="form-control" id="preview-store-stor_name" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.mer_website')</label>
                    <label class="form-control" id="preview-store-stor_website" readonly></label>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>@lang('localize.mer_address')</label>
                    <label type="text" class="form-control" id="preview-store-stor_address1" readonly></label>
                    <br>
                    <label type="text" class="form-control" id="preview-store-stor_address2" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.country')</label>
                    <label class="form-control" id="preview-store-stor_country" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.zipcode')</label>
                    <label class="form-control" id="preview-store-stor_zipcode" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.state')</label>
                    <label class="form-control" id="preview-store-stor_state" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.mobile')</label>
                    <label class="form-control" id="preview-store-stor_phone" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.office_number')</label>
                    <label class="form-control" id="preview-store-stor_office_number" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.city')</label>
                    <label class="form-control" id="preview-store-stor_city_name" readonly></label>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>@lang('localize.mer_meta_keyword')</label>
                    <label class="form-control" id="preview-store-stor_metakeywords" readonly></label>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>@lang('localize.mer_meta_description')</label>
                    <label class="form-control" id="preview-store-stor_metadesc" readonly></label>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <label>@lang('localize.short_description')</label>
                    <label class="form-control" id="preview-store-short_description" readonly></label>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <label>@lang('localize.long_description')</label>
                    <label class="form-control" id="preview-store-long_description" readonly></label>
                </div>
            </div>

            {{--  <div class="col-sm-12" id="stor_category_div" style="display:none;">
                <div class="form-group">
                    <label>{{trans('localize.select_category')}}</label>
                    <div class="panel panel-default" style="padding:20px;">
                        <table id="showCats" class="table table-condensed"></table>
                        <br>
                        <input type="hidden" id="stor_category" name="stor_category" value="[]">
                        <div id="cats" class="demo"></div>
                    </div>
                </div>
            </div>  --}}

            <div class="form-action text-center">
                <button id="prev-store" class="btn btn-primary">@lang('localize.mer_edit_store_information')</button>
            </div>
            <div class="col-sm-12 seperator"></div>
            {{--  End store info  --}}

            {{--  Start referrer info  --}}
            <h4>@lang('localize.referrer_info')</h4>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>@lang('localize.guarantor_name')</label>
                    <label class="form-control" id="preview-referrer-referrer_name" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.guarantor_username')</label>
                    <label class="form-control" id="preview-referrer-referrer_username" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.guarantor_nationality')</label>
                    <label class="form-control" id="preview-referrer-referrer_nationality" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.guarantor_phone')</label>
                    <label class="form-control" id="preview-referrer-referrer_phone" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.guarantor_email')</label>
                    <label class="form-control" id="preview-referrer-referrer_email" readonly></label>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>@lang('localize.guarantor_bank_name')</label>
                    <label class="form-control" id="preview-referrer-referrer_bank_name" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.guarantor_acc_name')</label>
                    <label class="form-control" id="preview-referrer-referrer_acc_name" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.guarantor_bank_acc')</label>
                    <label class="form-control" id="preview-referrer-referrer_bank_acc" readonly></label>
                </div>
            </div>

            <div class="form-action text-center">
                <button id="prev-referrer" class="btn btn-primary">@lang('localize.edit_referrer_info')</button>
            </div>
            <div class="col-sm-12 seperator"></div>
            {{--  End Referrer info --}}

            {{--  Start Guarantor info  --}}
            <h4>@lang('localize.guarantor_info')</h4>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>@lang('localize.guarantor_name')</label>
                    <label class="form-control" id="preview-guarantor-guarantor_name" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.guarantor_username')</label>
                    <label class="form-control" id="preview-guarantor-guarantor_username" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.guarantor_nationality')</label>
                    <label class="form-control" id="preview-guarantor-guarantor_nationality" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.guarantor_phone')</label>
                    <label class="form-control" id="preview-guarantor-guarantor_phone" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.guarantor_email')</label>
                    <label class="form-control" id="preview-guarantor-guarantor_email" readonly></label>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>@lang('localize.guarantor_bank_name')</label>
                    <label class="form-control" id="preview-guarantor-guarantor_bank_name" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.guarantor_acc_name')</label>
                    <label class="form-control" id="preview-guarantor-guarantor_acc_name" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.guarantor_bank_acc')</label>
                    <label class="form-control" id="preview-guarantor-guarantor_bank_acc" readonly></label>
                </div>
            </div>

            <div class="form-action text-center">
                <button id="prev-guarantor" class="btn btn-primary">@lang('localize.mer_edit_guarantor_information')</button>
            </div>
            <div class="col-sm-12 seperator"></div>
            {{--  End Guarantor info  --}}

            {{--  Start bank info  --}}
            <h4>@lang('localize.mer_bank_information')</h4>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>@lang('localize.mer_acc_holder')</label>
                    <label class="form-control" id="preview-bank-bank_acc_name" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.mer_acc_number')</label>
                    <label class="form-control" id="preview-bank-bank_acc_no" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.gst_no')</label>
                    <label class="form-control" id="preview-bank-bank_gst" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.mer_bank_name')</label>
                    <label class="form-control" id="preview-bank-bank_name" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.mer_bank_country')</label>
                    <label class="form-control" id="preview-bank-bank_country" readonly></label>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>@lang('localize.mer_bank_address')</label>
                    <label class="form-control" id="preview-bank-bank_address" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.bank_swift')</label>
                    <label class="form-control" id="preview-bank-bank_swift" readonly></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>@lang('localize.if_europe')</label>
                    <label class="form-control" id="preview-bank-bank_europe" readonly></label>
                </div>
            </div>

            <div class="cart_navigation text-center">
                <button id="prev-bank" class="btn btn-primary">@lang('localize.mer_edit_bank_information')</button>
            </div>
            <div class="col-sm-12 seperator"></div>
            {{--  End bank info  --}}

            <hr>
            <div class="form-action">
                <button id="submit" type="button" class="btn btn-next btn-primary">@lang('localize.submit')</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
    <link href="/backend/css/plugins/jsTree/style.min.css" rel="stylesheet">
    <style>
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .notextdeco{
            text-decoration: none !important;
            color: inherit;
        }

        #l_map {
            height:500px;
            width:100%;
        }

        .gllpMap {
            height: 500px;
        }

        .panel.with-nav-tabs .panel-heading{
            padding: 5px 5px 0 5px;
        }
        .panel.with-nav-tabs .nav-tabs{
            border-bottom: none;
        }
        .panel.with-nav-tabs .nav-justified{
            margin-bottom: -1px;
        }
    </style>
@endsection

@section('scripts')
<script src="http://maps.google.cn/maps/api/js?key={{ config('app.map.google') }}&libraries=geometry,places"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak={{ config('app.map.baidu') }}"></script>
<script type="text/javascript" src="{{ asset('backend/js/plugins/jsTree/jstree.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('backend/js/custom_baidu_map.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/js/custom_gmaps.js') }} "></script>
<script type="text/javascript" src="{{ asset('assets/js/sweetalert.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/js/custom.js') }}"></script>

<script type="text/javascript">
$(function() {

    get_countries('#country', "{{ old('country', '0') }}", '#state', "{{ old('state', '0') }}");
    get_countries('#stor_country', "{{ old('stor_country', '0') }}", '#stor_state', "{{ old('stor_state', '0') }}");
    get_countries('#bank_country', "{{ old('bank_country', '0') }}");

    @if (old('stor_type') && old('stor_type') == 1)
        $('#stor_category_div').show();
        $('#stor_category').addClass('compulsary');
    @endif

    $('.numeric').keydown(function (e) {-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});

    $('.username').on({
        keydown: function(e) {
            if (e.which === 32)
                return false;
        },
        change: function(e) {
            this.value = this.value.replace(/\s/g, "");
            check_merchant_username($(this), $(this).val(), '', e);
        }
    });

    $('.email').on({
        keydown: function(e) {
            if (e.which === 32)
                return false;
        },
        change: function(e) {

            if(validate_email_regex($(this).val()) == false) {
                $(this).css('border', '1px solid red').focus();
                return false;
            }
            $(this).css('border', '');

            if($(this).hasClass('merchant-email'))
                check_merchant_email($(this), $(this).val(), '', e);
        }
    });

    $(".password").on({
        change: function(e) {
            check_password();
        }
    });

    $('#country').change(function() {
        var country_id = $(this).val();
        get_states('#stor_state', country_id);
        $('#bank_country').val(country_id);
    });

    $('#stor_type').change(function() {
        if($('#stor_type').val() == 1) {
            $('#stor_category_div').show();
            $('#stor_category').addClass('compulsary');

            if($('#map_type').val() === '1') {
                $('#latitude_google').addClass('compulsary');
                $('#longitude_google').addClass('compulsary');
            } else {
                $('#latitude_baidu').addClass('compulsary');
                $('#longitude_baidu').addClass('compulsary');
            }
        }
        else {
            $('#stor_category_div').hide();
            $('#stor_category').removeClass('compulsary');
            removeMapCompulsary();
        }
    });

    $('#tab_google_map').click(function() {
        if($('#stor_type').val() == 1) {
            $('#latitude_google').addClass('compulsary');
            $('#longitude_google').addClass('compulsary');
            $('#latitude_baidu').removeClass('compulsary');
            $('#longitude_baidu').removeClass('compulsary');
        } else {
            removeMapCompulsary();
        }

        $('#map_type').val(1);
        setTimeout(initialize, 1000);
    });

    $('#tab_baidu_map').click(function() {
        if($('#stor_type').val() == 1) {
            $('#latitude_google').removeClass('compulsary');
            $('#longitude_google').removeClass('compulsary');
            $('#latitude_baidu').addClass('compulsary');
            $('#longitude_baidu').addClass('compulsary');
        } else {
            removeMapCompulsary();
        }

        $('#map_type').val(2);
    });

    $('#cats').jstree({
        'core' : {
            'data' : {
                'url' : function (node) {
                    return node.id === '#' ? '/get_offline_category' : '/get_offline_category/' + node.id;
                },
                'dataType' : 'json',
            },
            'themes' : {
                'icons' : false
            },
        },
        'plugins' : [ 'wholerow', 'themes' ],
    });

    $('#cats').on("changed.jstree", function (e, data) {
        if(data.selected.length) {
            var parent = false;
            var child =  false;
            var catParents = data.node.parents;
            var catChilds = data.node.children_d;

            var catSelected = JSON.parse($('#stor_category').val());
            var catExist = jQuery.inArray( data.node.id, catSelected );

            jQuery.each(catParents, function(key, val) {
                if (jQuery.inArray( val, catSelected ) != -1) {
                    parent = true;
                    return false;
                }
            });

            if (parent) {
                swal("Opsss!", "{{trans('localize.Parent_category_already_selected')}}", "error");
                return false;
            }

            jQuery.each(catChilds, function(key, val) {
                if (jQuery.inArray( val, catSelected ) != -1) {
                    child = true;
                    return false;
                }
            });

            if (child) {
                swal("Opsss!", "{{trans('localize.Child_category_already_selected')}}", "error");
                return false;
            }

            if (catSelected.length < 1) {
                var cat_id = data.node.id;

                swal({
                    title: "{{ trans('localize.sure') }}",
                    text: "Confirm to select " + data.instance.get_node(data.selected[0]).text + " ?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#5cb85c",
                    confirmButtonText: "Confirm",
                    closeOnConfirm: true,
                }, function(){
                    catSelected.push(data.node.id);
                    $('#stor_category').val(JSON.stringify(catSelected));
                    $('#showCats').append("<tr id='category_" + data.node.id + "'><td>" + data.node.text + "</td><td><a onClick='removeCat(\"" + data.node.id + "\"); return false;' class='btn btn-xs btn-danger pull-right'>Remove</a></td></tr>");
                });

            } else {
                swal("Sorry!", "{{trans('localize.Maximum_1_category_only_can_be_selected')}}.", "error");
            }
        }
    });
});
</script>
<script type="text/javascript">

$(function() {
    function validateMerchant(event) {

        var valid = true;
        $('.panel-merchant :input').each(function() {

            var preview = $(this).attr('name');
            if($(this).val()) {
                if ($(this).is('input:text') || $(this).is("textarea")) {
                    $('#preview-merchant-'+preview).text($(this).val());
                } else {
                    $('#preview-merchant-'+preview).text($(this).find('option:selected').text());
                }
            }

            if ($(this).hasClass('compulsary')) {

                if (!$(this).val() ) {
                    $(this).css('border', '1px solid red');
                    valid = false;
                } else {
                    $(this).css('border', '');
                }
            }

            if($(this).hasClass('username') && check_merchant_username($(this), $(this).val(), '', event) == false)
                valid = false;

            if($(this).hasClass('email') && check_merchant_email($(this), $(this).val(), '', event) == false)
                valid = false;

            if($(this).hasClass('password') && check_password() == false)
                valid = false;
        });

        return valid;
    };

    function validateStore() {

        var valid = true;
        $('.panel-store :input').each(function() {

            var preview = $(this).attr('name');
            if($(this).val() && !$(this).hasClass('website')) {
                if ($(this).is('input:text') || $(this).is("textarea")) {
                    $('#preview-store-'+preview).text($(this).val());
                } else {
                    $('#preview-store-'+preview).text($(this).find('option:selected').text());
                }
            }

            if ($(this).hasClass('compulsary')) {

                if (!$(this).val()) {
                    $(this).css('border', '1px solid red');
                    valid = false;
                } else {
                    $(this).css('border', '');
                }
            }

            if (valid && $(this).hasClass('website')) {
                if($(this).val()) {
                    var value = $('#http').val() + $(this).val();
                    var urlregex = /^(https?|ftp):\/\/([a-zA-Z0-9.-]+(:[a-zA-Z0-9.&%$-]+)*@)*((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]?)(\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}|([a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(:[0-9]+)*(\/($|[a-zA-Z0-9.,?'\\+&%$#=~_-]+))*$/;

                    if (!urlregex.test(value)) {
                        swal("@lang('localize.error')", "@lang('localize.website_url_error')", "error");
                        $(this).css('border', '1px solid red').focus();
                        $('#stor_website').val('');
                        $('#preview-store-stor_website').text('');
                        valid = false;
                    } else {
                        $(this).css('border', '');
                        $('#stor_website').val(value);
                    }
                } else {
                    $(this).css('border', '');
                }
            }

            if (valid && $(this).hasClass('image')) {
                var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];

                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    $('#show-store').trigger('click');
                    swal("@lang('localize.error')", "@lang('localize.imgError')", "error");
                    $(this).css('border', '1px solid red').focus();
                    valid = false;
                } else if (($(this)[0].files[0].size) > 1000000){
                    swal("@lang('localize.error')", "@lang('localize.imgSizeError')", "error");
                    $(this).css('border', '1px solid red').focus();
                    valid = false;
                } else {
                    $(this).css('border', '');
                }
           }

        });

        if (valid && $('#stor_type').val() == '1') {
            if (JSON.parse($('#stor_category').val()).length == 0) {
                swal("{{trans('localize.error')}}", "{{trans('localize.select_category_to_proceed')}}", "error");
                valid = false;
            }
        }

        if(valid) {
            $('#preview-store-stor_website').text($('#stor_website').val());

            let latitude = '';
            let longitude = '';

            if ($('#map_type').val() === '1') {
                latitude = $('#latitude_google').val();
                longitude = $('#longitude_google').val();
            } else if ($('#map_type').val() === '2') {
                latitude = $('#latitude_baidu').val();
                longitude = $('#longitude_baidu').val();
            }

            $('#latitude').val(latitude);
            $('#longtitude').val(longitude);
        }

        return valid;
    }

    function validateBank() {

        var valid = true;
        $('.panel-bank :input').each(function() {

            var preview = $(this).attr('name');
            if($(this).val()) {
                if ($(this).is('input:text') || $(this).is("textarea")) {
                    $('#preview-bank-'+preview).text($(this).val());
                } else {
                    $('#preview-bank-'+preview).text($(this).find('option:selected').text());
                }
            }

            if ($(this).hasClass('compulsary')) {

                if (!$(this).val()) {
                    $(this).css('border', '1px solid red');
                    valid = false;
                } else {
                    $(this).css('border', '');
                }
            }
        });

        return valid;
    }

    function validateGuarantor() {

        var valid = true;
        $('.panel-guarantor :input').each(function() {

            var preview = $(this).attr('name');
            if($(this).val()) {
                if ($(this).is('input:text') || $(this).is("textarea")) {
                    $('#preview-guarantor-'+preview).text($(this).val());
                } else {
                    $('#preview-guarantor-'+preview).text($(this).find('option:selected').text());
                }
            }

            if ($(this).hasClass('compulsary')) {

                if (!$(this).val()) {
                    $(this).css('border', '1px solid red');
                    valid = false;
                } else {
                    $(this).css('border', '');
                }
            }

            if(valid && $(this).hasClass('email')) {
                if(validate_email_regex($(this).val()) == false) {
                    $(this).css('border', '1px solid red');
                    valid = false;
                } else {
                     $(this).css('border', '');
                }
            }
        });

        return valid;
    }

    function validateReferrer() {

        var valid = true;
        $('.panel-referrer :input').each(function() {

            var preview = $(this).attr('name');
            if($(this).val()) {
                if ($(this).is('input:text') || $(this).is("textarea")) {
                    $('#preview-referrer-'+preview).text($(this).val());
                } else {
                    $('#preview-referrer-'+preview).text($(this).find('option:selected').text());
                }
            }

            if ($(this).hasClass('compulsary')) {

                if (!$(this).val()) {
                    $(this).css('border', '1px solid red');
                    valid = false;
                } else {
                    $(this).css('border', '');
                }
            }

            if(valid && $(this).hasClass('email') && $(this).val()) {
                if(validate_email_regex($(this).val()) == false) {
                    $(this).css('border', '1px solid red');
                    valid = false;
                } else {
                     $(this).css('border', '');
                }
            }
        });

        return valid;
    }

    function showMerchant() {
        $(".panel-merchant").addClass('active');
        $(".panel-store").removeClass('active');
        $(".panel-referrer").removeClass('active');
        $(".panel-guarantor").removeClass('active');
        $(".panel-bank").removeClass('active');
        $(".panel-guarantor").removeClass('active');
        $(".panel-review").removeClass('active');

        $(".panel-merchant").removeClass('panel-success');
        $(".panel-store").removeClass('panel-success');
        $(".panel-referrer").removeClass('panel-success');
        $(".panel-guarantor").removeClass('panel-success');
        $(".panel-bank").removeClass('panel-success');
        $(".panel-review").removeClass('panel-success');

        $(window).scrollTop(200);
        initialize();
    };

    function showStore() {
        $(".panel-merchant").removeClass('active');
        $(".panel-store").addClass('active');
        $(".panel-referrer").removeClass('active');
        $(".panel-guarantor").removeClass('active');
        $(".panel-bank").removeClass('active');
        $(".panel-guarantor").removeClass('active');
        $(".panel-review").removeClass('active');

        $(".panel-merchant").addClass('panel-success');
        $(".panel-store").removeClass('panel-success');
        $(".panel-referrer").removeClass('panel-success');
        $(".panel-guarantor").removeClass('panel-success');
        $(".panel-bank").removeClass('panel-success');

        $(window).scrollTop(200);
        initialize();
    };

    function showReferrer() {
        $(".panel-merchant").removeClass('active');
        $(".panel-store").removeClass('active');
        $(".panel-referrer").addClass('active');
        $(".panel-guarantor").removeClass('active');
        $(".panel-bank").removeClass('active');
        $(".panel-review").removeClass('active');

        $(".panel-store").addClass('panel-success');
        $(".panel-referrer").removeClass('panel-success');
        $(".panel-guarantor").removeClass('panel-success');
        $(".panel-bank").removeClass('panel-success');

        $(window).scrollTop(200);
    };

    function showGuarantor() {
        $(".panel-merchant").removeClass('active');
        $(".panel-store").removeClass('active');
        $(".panel-referrer").removeClass('active');
        $(".panel-guarantor").addClass('active');
        $(".panel-bank").removeClass('active');
        $(".panel-guarantor").addClass('active');
        $(".panel-review").removeClass('active');

        $(".panel-referrer").addClass('panel-success');
        $(".panel-guarantor").removeClass('panel-success');
        $(".panel-bank").removeClass('panel-success');

        $(window).scrollTop(200);
    };

    function showBank() {
        $(".panel-merchant").removeClass('active');
        $(".panel-store").removeClass('active');
        $(".panel-referrer").removeClass('active');
        $(".panel-guarantor").removeClass('active');
        $(".panel-bank").addClass('active');
        $(".panel-review").removeClass('active');

        $(".panel-guarantor").addClass('panel-success');
        $(".panel-bank").removeClass('panel-success');

        $(window).scrollTop(200);
    };

    function showReview() {
        $(".panel-merchant").removeClass('active');
        $(".panel-store").removeClass('active');
        $(".panel-referrer").removeClass('active');
        $(".panel-guarantor").removeClass('active');
        $(".panel-bank").removeClass('active');
        $(".panel-review").addClass('active');

        $(".panel-bank").addClass('panel-success');

        $(window).scrollTop(200);
    };

    $('#prev-merchant').click(function(event) {
        showMerchant();
    });

    $('#next-store, #prev-store').click(function(event) {
        var status = validateMerchant(event);
        if (status === false) return;
        showStore();
    });

    $('#next-referrer, #prev-referrer').click(function(){
        var status = validateStore();
        if (status === false) return;
        showReferrer();
    });

    $('#next-guarantor, #prev-guarantor').click(function() {
        var status = validateReferrer();
        if (status === false) return;
        showGuarantor();
    });

    $('#next-bank, #prev-bank').click(function(){
        var status = validateGuarantor();
        if (status === false) return;
        showBank();
    });

    $('#next-review').click(function() {
        var status = validateBank();
        if (status === false) return;
        showGuarantor();
    });

    $('#next-preview').click(function() {
        var status = validateGuarantor();
        if (status === false) return;
        showReview();
    });

    $('#submit').click(function(event) {
        var status = true;

        status = validateMerchant(event);
        if(status === false) {
            showMerchant();
            event.preventDefault();
            return false;
        }

        status = validateStore();
        if(status === false) {
            showStore();
            event.preventDefault();
            return false;
        }

        status = validateReferrer();
        if(status === false) {
            showReferrer();
            event.preventDefault();
            return false;
        }

        status = validateGuarantor();
        if(status === false) {
            showGuarantor();
            event.preventDefault();
            return false;
        }

        status = validateBank();
        if(status === false) {
            showBank();
            event.preventDefault();
            return false;
        }

        swal({
            title: "@lang('localize.sure')",
            text: "@lang('localize.signup_term')",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#5cb85c",
            confirmButtonText: "@lang('localize.register')",
            closeOnConfirm: true
        }, function() {
            $("#merc_register").submit();
        });
    });
});

function removeCat(id) {
    var catSelected = JSON.parse($('#stor_category').val());
    jQuery('#category_' + id).remove();
    var index = jQuery.inArray( id, catSelected );
    if (index > -1) {
        catSelected.splice(index, 1);
    }
    $('#stor_category').val(JSON.stringify(catSelected));
}

function check_password() {
    var password = $("#password").val();
    var password_confirmation = $("#cpassword").val();

    var passwordRegex = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])([a-zA-Z0-9]{6,})$/;

    if(password.length < 6) {
        $("#password_hint").html("<i class='fa fa-info-circle'></i> @lang('localize.password_min')").show();
        $("#password").css('border', '1px solid red').focus();
        return false;
    } else if(!passwordRegex.test(password)) {
        $("#password_hint").html("<i class='fa fa-info-circle'></i> @lang('localize.passwordHint')").show();
        $("#password").css('border', '1px solid red').focus();
        return false;
    } else if(password_confirmation != '' && password != password_confirmation) {
        $("#password_hint").html("<i class='fa fa-info-circle'></i> @lang('localize.password_not_match')").show();
        $("#cpassword").css('border', '1px solid red').focus();
        return false;
    }

    $("#password_hint").hide();
    $("#password, #cpassword").css('border', '');
    return true;
}

function validate_email_regex(email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

    if(!emailReg.test(email)) {
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
        return false;
    }

    return true;
}

function switchMap() {
    let storeCountryCode = $('#stor_country').val();
    if (storeCountryCode == '6') {
        $('#tab_baidu_map').click();
    } else {
        $('#tab_google_map').click();
    }
}

function removeMapCompulsary() {
    $('#latitude_google').removeClass('compulsary');
    $('#longitude_google').removeClass('compulsary');
    $('#latitude_baidu').removeClass('compulsary');
    $('#longitude_baidu').removeClass('compulsary');
}
</script>
@endsection
