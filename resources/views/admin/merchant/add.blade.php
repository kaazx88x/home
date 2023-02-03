@extends('admin.layouts.master')

@section('title', 'Add Profile')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.add')}} {{trans('localize.Merchants')}} </h2>
        <ol class="breadcrumb">
            <li class="active">
                <strong>{{trans('localize.add')}} {{trans('localize.Merchants')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInUp">

    @include('admin.common.notifications')

    <form id="add_merchant" action="{{url('admin/merchant/add')}}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}

        <div class="row">
            <div class="col-lg-12" style="padding-bottom:20px;">
                <div class="col-lg-2 pull-right">
                    <button class="btn btn-block btn-primary submit" type="submit" id="submit" style="margin: 0 20px 0"><a style="color:#fff" >{{trans('localize.add')}} {{trans('localize.Merchants')}}</a></button>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{trans('localize.Merchants')}} {{trans('localize.profile')}} </h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form">
                                    <div class="form-group">
                                        <label class="control-label">{{trans('localize.Username')}} <span class='text-danger'>*</span></label>
                                        <input type="text" class="form-control compulsary username" name="username" id="username" value="{{old('username')}}">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">{{trans('localize.First_Name')}} <span class='text-danger'>*</span></label>
                                        <input type="text" class="form-control compulsary" name="fname" id="fname" value="{{old('fname')}}">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{trans('localize.Last_Name')}} <span class='text-danger'>*</span></label>
                                        <input type="text" class="form-control compulsary" name="lname" id="lname" value="{{old('lname')}}">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{trans('localize.email')}} <span class='text-danger'>*</span></label>
                                        <input type="text" class="form-control compulsary email" name="email" id="email" value="{{old('email')}}">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{trans('localize.Phone')}} <span class='text-danger'>*</span></label>
                                        <input type="text" class="form-control compulsary phonefax" name="tel" id="tel" value="{{old('tel')}}">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{trans('localize.office_number')}} <span class='text-danger'>*</span></label>
                                        <input type="text" class="form-control compulsary phonefax" name="office_number" id="office_number" value="{{ old('office_number') }}">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{trans('localize.address')}} <span class='text-danger'>*</span></label>
                                        <input type="text" class="form-control compulsary" name="address1" id="address1" value="{{old('address1')}}">
                                        <br>
                                        <input type="text" class="form-control" name="address2" value="{{old('address2')}}">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{trans('localize.country')}} <span class='text-danger'>*</span></label>
                                        <select class="form-control compulsary" id="mer_country" name="country" onchange="get_states('#mer_state', this.value)"></select>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label compulsary">{{trans('localize.State')}} <span class='text-danger'>*</span></label>
                                        <select class="form-control compulsary" id="mer_state" name="state">
                                            <option value="">{{trans('localize.selectCountry_first')}}</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label compulsary">{{trans('localize.city')}} <span class='text-danger'>*</span></label>
                                        <input type="text" class="form-control compulsary" name="mer_city_name" id="mer_city_name" value="{{old('mer_city_name')}}">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{trans('localize.zipcode')}} <span class='text-danger'>*</span></label>
                                        <input type="text" placeholder="{{trans('localize.zipcode')}}" class="form-control compulsary" name='zipcode' id='zipcode' value='{{old("zipcode")}}'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{trans('localize.Referrer')}} {{trans('localize.information')}}</h5>
                                <div class="ibox-tools">
                                </div>
                            </div>
                            <div class="ibox-content">
                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_name')</label>
                                    <input type="text" class="form-control" name="referrer_name" value="{{old('referrer_name')}}">
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_username')</label>
                                    <input type="text" class="form-control" name="referrer_username" value="{{old('referrer_username')}}">
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_nationality')</label>
                                    <input type="text" class="form-control" name="referrer_nationality" value="{{old('referrer_nationality')}}">
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_phone')</label>
                                    <input type="text" class="form-control phonefax" name="referrer_phone" value="{{old('referrer_phone')}}">
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_email')</label>
                                    <input type="text" class="form-control gemail" name="referrer_email" value="{{old('greferrer_email')}}">
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_bank_name')</label>
                                    <input type="text" class="form-control" name="referrer_bank_name" value="{{old('referrer_bank_name')}}">
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_acc_name')</label>
                                    <input type="text" class="form-control" name="referrer_acc_name" value="{{old('referrer_acc_name')}}">
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_bank_acc')</label>
                                    <input type="text" class="form-control" name="referrer_bank_acc" value="{{old('referrer_bank_acc')}}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>@lang('localize.guarantor_info')</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_name')</label>
                                    <input type="text" class="form-control" name="guarantor_name" value="{{old('guarantor_name')}}">
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_username')</label>
                                    <input type="text" class="form-control" name="guarantor_username" value="{{old('guarantor_username')}}">
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_nationality')</label>
                                    <input type="text" class="form-control" name="guarantor_nationality" value="{{old('guarantor_nationality')}}">
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_phone')</label>
                                    <input type="text" class="form-control phonefax" name="guarantor_phone" value="{{old('guarantor_phone')}}">
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_email')</label>
                                    <input type="text" class="form-control gemail" name="guarantor_email" value="{{old('guarantor_email')}}">
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_bank_name')</label>
                                    <input type="text" class="form-control" name="guarantor_bank_name" value="{{old('guarantor_bank_name')}}">
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_acc_name')</label>
                                    <input type="text" class="form-control" name="guarantor_acc_name" value="{{old('guarantor_acc_name')}}">
                                </div>

                                <div class="form-group">
                                    <label class="control-label">@lang('localize.guarantor_bank_acc')</label>
                                    <input type="text" class="form-control" name="guarantor_bank_acc" value="{{old('guarantor_bank_acc')}}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{trans('localize.Bank_Account')}}</h5>
                                <div class="ibox-tools">
                                </div>
                            </div>
                            <div class="ibox-content">
                                <div class="form">
                                    <div class="form-group">
                                        <label class="control-label">{{trans('localize.mer_acc_holder')}} <span class='text-danger'>*</span></label>
                                        <input type="text" class="form-control compulsary" name="bank_acc_name" id="bank_holder" value="{{old('bank_acc_name')}}">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{trans('localize.mer_acc_number')}} <span class='text-danger'>*</span></label>
                                        <input type="text" class="form-control compulsary" name="bank_acc_no" id="bank_acc" value="{{old('bank_acc_no')}}">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{trans('localize.mer_bank_name')}} <span class='text-danger'>*</span></label>
                                        <input type="text" class="form-control compulsary" name="bank_name" id="bank_name" value="{{old('bank_name')}}">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{trans('localize.mer_bank_country')}} <span class='text-danger'>*</span></label>
                                        <select class="form-control compulsary" id="bank_country" name="bank_country"></select>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{trans('localize.mer_bank_address')}} <span class='text-danger'>*</span></label>
                                        <input type="text" class="form-control compulsary" name="bank_address" value="{{old('bank_address')}}">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{trans('localize.bank_swift')}}</label>
                                        <input type="text" class="form-control" name="bank_swift" value="{{old('bank_swift')}}">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{trans('localize.if_europe')}}</label>
                                        <input type="text" class="form-control" name="bank_europe" value="{{old('bank_europe')}}">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{trans('localize.gst_no')}}</label>
                                        <input type="text" class="form-control" name="bank_gst" value="{{old('bank_gst')}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-6">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{trans('localize.store')}} {{trans('localize.information')}} </h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.store_type')}} <span class='text-danger'>*</span></label>
                            <select class="form-control compulsary" name='stor_type' id='stor_type'>
                                <option value="0" {{(old("stor_type") == 0) ? 'selected' : ''}}>{{trans('localize.Online')}}</option>
                                <option value="1" {{(old("stor_type") == 1) ? 'selected' : ''}}>{{trans('localize.Offline')}}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.store_name')}} <span class='text-danger'>*</span></label>
                            <input type="text" placeholder="{{trans('localize.store_name')}}" class="form-control compulsary" name="stor_name" id="stor_name" value="{{old('stor_name')}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.Phone')}} <span class='text-danger'>*</span></label>
                            <input type="text" placeholder="{{trans('localize.Phone')}}" class="form-control compulsary phonefax" name="stor_phone" id="stor_phone" value="{{old('stor_phone')}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.office_number')}} <span class='text-danger'>*</span></label>
                            <input type="text" class="form-control compulsary phonefax" placeholder="{{trans('localize.office_number')}}" name="stor_office_number" id="stor_office_number" value="{{ old('stor_office_number') }}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.address1')}} <span class='text-danger'>*</span></label>
                            <input type="text" placeholder="{{trans('localize.address1')}}" class="form-control compulsary" name="stor_address1" id="stor_address1" value="{{old('stor_address1')}}">
                        </div>
                        <div class="form-group">
                            {{-- <label class="control-label">{{trans('localize.address2')}} <span class='text-danger'>*</span></label> --}}
                            <input type="text" placeholder="{{trans('localize.address2')}}" class="form-control" name="stor_address2" id="stor_address2" value="{{old('stor_address2')}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.zipcode')}} <span class='text-danger'>*</span></label>
                            <input type="text" placeholder="{{trans('localize.zipcode')}}" class="form-control compulsary" name='stor_zipcode' id='stor_zipcode' value='{{old("stor_zipcode")}}'>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.country')}} <span class='text-danger'>*</span></label>
                            <select class="form-control compulsary" name='stor_country' id='stor_country' onchange="get_states('#stor_state', this.value)"></select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.State')}} <span class='text-danger'>*</span></label>
                            <select class="form-control compulsary" id="stor_state" name="stor_state">
                                <option value="">{{trans('localize.selectCountry_first')}}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.city')}} <span class='text-danger'>*</span></label>
                            <input type="text" class="form-control compulsary" name="stor_city_name" id="stor_city_name" value="{{old('stor_city_name')}}">
                        </div>

                        {{-- MAP --}}
                        <input type="hidden" id="map_type" name="map_type">
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
                                                    <label>{{trans('localize.latitude')}} <span class='text-danger map_asterisk'></span></label>
                                                    <input type="text" class="form-control location" id="latitude_baidu" readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('localize.longtitude')}} <span class='text-danger map_asterisk'></span></label>
                                                    <input type="text" class="form-control location" id="longitude_baidu" readonly>
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
                                                    <label>{{trans('localize.latitude')}} <span class='text-danger map_asterisk'></span></label>
                                                    <input type="text" class="form-control gllpLatitude location" id="latitude_google" readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('localize.longtitude')}} <span class='text-danger map_asterisk'></span></label>
                                                    <input type="text" class="form-control gllpLongitude location" id="longitude_google" readonly>
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
                        {{-- <div class="gllpLatlonPicker form-group">
                            <label class="control-label">Store Location <br/><small>Drag marker to get latitude & longitude</small></label>
                            <input type="text" class="form-control gllpSearchField" placeholder="{{trans('localize.enter_location')}}" id="location">
                            <div class="gllpMap" id="map_canvas"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Longitude <span class='text-danger'>*</span></label>
                            <input type="text" class="form-control gllpLongitude" id="longtitude" name="longtitude" value="{{old('longtitude')}}" readonly>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Latitude <span class='text-danger'>*</span></label>
                            <input type="text" class="form-control gllpLatitude" id="latitude" name="latitude" value="{{old('latitude')}}" readonly>
                        </div>
                        <input type="text" class="gllpZoom" style="visibility:hidden">
                        <input class="gllpUpdateButton" style="visibility:hidden"> --}}
                        <!-- end map -->

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.Meta_Keywords')}}</label>
                            <input type="text" class="form-control" placeholder="{{trans('localize.Meta_Keywords')}}" name="stor_metakeywords" id="stor_metakeywords" value="{{old('stor_metakeywords')}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.mer_meta_description')}}</label>
                            <input type="text" class="form-control" placeholder="{{trans('localize.mer_meta_description')}}" name="stor_metadesc" id="stor_metadesc" value="{{old('stor_metadesc')}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.website')}}</label>
                            <input type="text" class="form-control" placeholder="{{trans('localize.website')}}" name="stor_website" id="stor_website" value="{{old('stor_website')}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.storeImg')}} <span class="text-danger">*</span></label>
                            <div id="img_upload">
                                <span class="btn btn-default"><input type="file" name="stor_img" id="stor_img" class="compulsary files"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.short_description')}}</label>
                            <input type="text" class="form-control" placeholder="{{trans('localize.short_description')}}" name="short_description" id="short_description" value="{{old('short_description')}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.short_description')}}</label>
                            <textarea placeholder="{{trans('localize.short_description')}}" class="form-control" name="long_description" id="long_description">{{old('long_description')}}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.Commision')}}</label>
                            <p class="form-control-static">10 (%) </p>
                            {{-- <input type="number" class="form-control" name="commission" value="10" readonly> --}}
                        </div>

                        <div id="stor_category_div" style="display:{{ ( old('stor_type', 0) == 0)? 'none' : '' }};">
                            <div class="form-group">
                                <label>{{trans('localize.select_category')}} <span class="text-danger">*</span></label>
                                <div class="panel panel-default" style="padding:20px;">
                                    <table id="showCats" class="table table-condensed"></table>
                                    <br>
                                    <input type="hidden" id="stor_category" name="stor_category" value="[]">
                                    <div id="cats" class="demo"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-lg-offset-2" style="padding-bottom:20px;">
                <div class="col-lg-6 pull-right">
                    <button class="btn btn-block btn-primary submit" type="submit" id="submit" style="margin: 0 20px 0"><a style="color:#fff" >{{trans('localize.add')}} {{trans('localize.Merchants')}}</a></button>
                </div>
                <div class="col-lg-6 pull-right">
                    <button class="btn btn-block btn-default" type="reset">{{trans('localize.reset_form')}}</button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@section('style')
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
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

@section('script')
<script src="http://maps.google.cn/maps/api/js?key={{ config('app.map.google') }}&libraries=geometry,places"></script>
<script src="/web/lib/googlemaps/js/jquery-gmaps-latlon-picker-old.js"></script>
<script src="/backend/js/custom_gmaps.js"></script>
<script src="/backend/js/custom.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak={{ config('app.map.baidu') }}"></script>
<script type="text/javascript" src="/backend/js/custom_baidu_map.js"></script>

<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script src="/backend/js/plugins/jsTree/jstree.min.js"></script>

<script>
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
                    text: "{{trans('localize.confirm_to_select')}} " + data.instance.get_node(data.selected[0]).text + " ?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#5cb85c",
                    confirmButtonText: "{{trans('localize.confirm')}}",
					cancelButtonText: "{{trans('localize.cancel')}}",
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

    $(document).on('change', 'input[type=file]', function() {
        var fileSize = this.files[0].size;
        var fileExtension = ['jpeg', 'jpg', 'png'];

        if (fileSize > 1000000) {
            swal("Sorry!", "{{trans('localize.File_selected_exceed_maximum_size_1Mb')}}", "error");
            $('#' + this.id).val('');
        } else if ($.inArray($('#' + this.id).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            swal("Image format!", "{{trans('localize.imgError')}}", "error");
            $('#' + this.id).val('');
        }
    });

    function switchMap() {
        let storeCountryCode = $('#stor_country').val();
        if (storeCountryCode == '6') {
            $('#tab_baidu_map').click();
        } else {
            $('#tab_google_map').click();
        }
    }

    $(document).ready(function() {

        get_countries('#mer_country', "{{ old('country', '0') }}", '#mer_state', "{{ old('state', '0') }}");
        get_countries('#stor_country', "{{ old('stor_country', '0') }}", '#stor_state', "{{ old('stor_state', '0') }}");
        get_countries('#bank_country', "{{ old('bank_country', '0') }}");

        $('#stor_country').change(function() {
            if ($(this).val() == '6') {
                $('#tab_baidu_map').click();
            } else {
                $('#tab_google_map').click();
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

                $('.map_asterisk').text('*');
            }
            else {
                $('#stor_category_div').hide();
                $('#stor_category').removeClass('compulsary');

                removeMapCompulsary();
            }
        });

        $('.phonefax').keydown(function (e) {
            if (e.shiftKey || e.ctrlKey || e.altKey) {
                e.preventDefault();
            } else {
                var key = e.keyCode;
                if (!((key == 8) || (key == 46) || (key >= 35 && key <= 40) || (key >= 48 && key <= 57) || (key >= 96 && key <= 105))) {
                    e.preventDefault();
                }
            }
        });

        $(".gemail").on({
            keydown: function(e) {
                if (e.which === 32)
                return false;
            },
            change: function(e) {
                validate_email(this);
            }
        });

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
                check_merchant_email($(this), $(this).val(), '', e);
            }
        });

        $('#add_merchant').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });

        $('#stor_phone,#tel,#zipcode,#stor_zipcode').keydown(function (e) {
            if (e.shiftKey || e.ctrlKey || e.altKey) {
                e.preventDefault();
            } else {
                var key = e.keyCode;
                if (!((key == 8) || (key == 46) || (key >= 35 && key <= 40) || (key >= 48 && key <= 57) || (key >= 96 && key <= 105))) {
                    e.preventDefault();
                }
            }
        });

        $('.submit').click(function(form) {
            if($('stor_type').val() === '0') {
                removeMapCompulsary();
            }

            $('#add_merchant :input').each(function() {
                if ($(this).hasClass('compulsary')) {
                    if (!$(this).val()) {
                        if($(this).hasClass('location')) {
                            swal("{{trans('localize.error')}}", "{!! trans('localize.store_location_info') !!}", "error");
                            form.preventDefault();
                            return false;
                        } else {
                            $(this).attr('placeholder', '{{trans('localize.fieldrequired')}}').css('border', '1px solid red').focus();
                            form.preventDefault();
                            return false;
                        }
                    }
                }

                if ($(this).hasClass('username')) {
                    check_merchant_username($(this), $(this).val(), '', form);
                }

                if ($(this).hasClass('email')) {
                    check_merchant_email($(this), $(this).val(), '', form);
                }

                if($(this).hasClass('gemail') && !validate_email(this)) {
                    form.preventDefault();
                    return false;
                }

                if ($(this).hasClass('files')) {
                    var fileExtension = ['jpeg', 'jpg', 'png'];

                    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                        swal("{{trans('localize.error')}}", "{{trans('localize.imgError')}}", "error");
                        $(this).css('border', '1px solid red').focus();
                        isValid = false;
                        return false;
                    } else if (($(this)[0].files[0].size) > 1000000){
                        swal("{{trans('localize.error')}}", "{{trans('localize.imgSizeError')}}", "error");
                        $(this).css('border', '1px solid red').focus();
                        isValid = false;
                        return false;
                    }
                }

                if($(this).attr('id') === 'stor_category' && $('stor_type').val() === '1') {
                    if(JSON.parse($(this).val()).length == 0) {
                        swal("{{trans('localize.error')}}", "{{trans('localize.select_category_to_proceed')}}", "error");
                        event.preventDefault();
                        isValid = false;
                        return false;
                    }
                }

                $(this).css('border', '');
            });

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

            $("#add_merchant").submit();
        });

    });

    function removeCat(id) {
        var catSelected = JSON.parse($('#stor_category').val());
        jQuery('#category_' + id).remove();
        var index = jQuery.inArray( id, catSelected );
        console.log(index);
        if (index > -1) {
            catSelected.splice(index, 1);
        }
        $('#stor_category').val(JSON.stringify(catSelected));
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

    function removeMapCompulsary() {
        $('#latitude_google').removeClass('compulsary');
        $('#longitude_google').removeClass('compulsary');
        $('#latitude_baidu').removeClass('compulsary');
        $('#longitude_baidu').removeClass('compulsary');
        $('.map_asterisk').text('');
    }
</script>
@endsection
