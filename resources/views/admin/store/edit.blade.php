@extends('admin.layouts.master')

@section('title', 'Edit Store')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Edit')}} {{trans('localize.store')}} </h2>
        <ol class="breadcrumb">
            <li>
                {{trans('localize.Merchants')}}
            </li>
            <li>
                {{trans('localize.store')}}
            </li>
            <li class="active">
                <strong>{{trans('localize.Edit')}} {{trans('localize.store')}}</strong>
            </li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
    <form class="form" id="mer_update_store" action='/admin/store/edit/{{$store->stor_merchant_id}}/{{$store->stor_id}}' method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-lg-7">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>{{trans('localize.mer_store_information')}}</h5>
                    </div>
                    <div class="ibox-content">
                        @include('admin.common.errors')
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.mer_store_type')}}</label>
                            <p class="form-control-static">
                                <span class="label" style="font-size:13px">{{ ($store->stor_type == 0) ? trans('localize.online_store') : trans('localize.offline_store') }}</span>
                            </p>
                            {{-- <select class="form-control" name='stor_type' id='stor_type'>
                                <option value="0" {{($store->stor_type == 0) ? 'selected' : ''}}>Online</option>
                                <option value="1" {{($store->stor_type == 1) ? 'selected' : ''}}>Offline</option>
                            </select> --}}
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.mer_store_name')}} <span class='text-danger'>*</span></label>
                            <input type="text" placeholder="{{trans('localize.mer_store_name')}}" class="form-control compulsary" name="stor_name" id="stor_name"  value="{{ old('stor_name', $store->stor_name) }}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.phone')}} <span class='text-danger'>*</span></label>
                            <input type="number" placeholder="{{trans('localize.phone')}}" class="form-control compulsary" name="stor_phone" id="stor_phone" value="{{ old('stor_phone', $store->stor_phone) }}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.office_number')}} <span class='text-danger'>*</span></label>
                            <input type="number" placeholder="{{trans('localize.office_number')}}" class="form-control compulsary" name="stor_office_number" id="stor_office_number" value="{{old('stor_office_number', $store->stor_office_number)}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.address')}} <span class='text-danger'>*</span></label>
                            <input type="text" placeholder="{{trans('localize.address1')}}" class="form-control compulsary" name="stor_address1" id="stor_address1" value="{{ old('stor_address1', $store->stor_address1) }}">
                        </div>
                        <div class="form-group">
                            <input type="text" placeholder="{{trans('localize.address2')}}" class="form-control" name="stor_address2" id="stor_address2" value="{{ old('stor_address2', $store->stor_address2) }}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.zipcode')}}</label>
                            <input type="text" placeholder="{{trans('localize.zipcode')}}" class="form-control compulsary" name='stor_zipcode' id='stor_zipcode' value="{{ old('stor_zipcode', $store->stor_zipcode) }}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.country')}} <span class='text-danger'>*</span></label>
                            <select class="form-control compulsary" name='stor_country' id='stor_country' onchange="get_states('#stor_state', this.value)"></select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.state')}} <span class='text-danger'>*</span></label>
                            <select class="form-control compulsary" name='stor_state' id='stor_state'>
                                <option value="">{{trans('localize.selectCountry_first')}}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.city')}}</label>
                            <input type="text" class="form-control compulsary" name='stor_city_name' id='stor_city_name' value="{{ old('stor_city_name', $store->stor_city_name) }}">
                        </div>

                        {{-- MAP --}}
                        <input type="hidden" id="map_type" name="map_type" value="{{ $store->map_type }}">
                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longtitude" name="longtitude">

                        <div class="form-group">
                            <div class="panel with-nav-tabs panel-default">
                                <div class="panel-heading">
                                    <ul class="nav nav-tabs">
                                        <li {{ ($store->map_type == 2) ? 'class=active' : '' }}><a id="tab_baidu_map" href="#pane_baidu_map" data-toggle="tab">@lang('localize.map_baidu')</a></li>
                                        <li {{ ($store->map_type != 2) ? 'class=active' : '' }}><a id="tab_google_map" href="#pane_google_map" data-toggle="tab">@lang('localize.map_google')</a></li>
                                    </ul>
                                </div>
                                <div class="panel-body">
                                    <div class="tab-content">
                                        <div class="tab-pane fade {{ ($store->map_type == 2) ? 'active in' : '' }}" id="pane_baidu_map">
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
                                                    <label>{{trans('localize.latitude')}} <span class='text-danger'>{{ $store->stor_type == 1? '*' : '' }}</span></label>
                                                    <input type="text" class="form-control location" id="latitude_baidu" value="{{$store->stor_latitude}}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('localize.longtitude')}} <span class='text-danger'>{{ $store->stor_type == 1? '*' : '' }}</span></label>
                                                    <input type="text" class="form-control location" id="longitude_baidu" value="{{$store->stor_longitude}}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade {{ ($store->map_type != 2) ? 'active in' : '' }}" id="pane_google_map">
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
                                                    <label>{{trans('localize.latitude')}} <span class='text-danger'>{{ $store->stor_type == 1? '*' : '' }}</span></label>
                                                    <input type="text" class="form-control gllpLatitude location" id="latitude_google" value="{{$store->stor_latitude}}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('localize.longtitude')}} <span class='text-danger'>{{ $store->stor_type == 1? '*' : '' }}</span></label>
                                                    <input type="text" class="form-control gllpLongitude location" id="longitude_google" value="{{$store->stor_longitude}}" readonly>
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
                        <!-- Map auto lat lang -->
                        <!--<div class="form-group">
                            <label class="control-label">Search<span class=''>*</span></label>
                            <div class="col-lg-10">
                                <div class="input-group">
                                    <input type="text" class="form-control gllpSearchField" id="location" name="location">
                                    <span class="input-group-btn"><button type="button" class="btn btn-primary">Search</button></span>
                                </div>
                            </div>
                        </div>-->
                        {{-- <div class="gllpLatlonPicker form-group">
                            <label class="control-label">{{trans('localize.store_location')}} <br/><small>{{trans('localize.store_location_info')}}</small></label>
                            <input type="text" class="form-control gllpSearchField" placeholder="{{trans('localize.enter_location')}}" id="location">
                            <div class="gllpMap" id="map_canvas"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.longtitude')}} <span class='text-danger'>*</span></label>
                            <input type="text" class="form-control gllpLongitude" id="longtitude" name="longtitude" value="{{ old('longtitude', $store->stor_longitude) }}" readonly>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.latitude')}} <span class='text-danger'>*</span></label>
                            <input type="text" class="form-control gllpLatitude" id="latitude" name="latitude" value="{{ old('latitude', $store->stor_latitude) }}" readonly>
                        </div>
                        <input type="text" class="gllpZoom" style="visibility:hidden">
                        <input class="gllpUpdateButton" style="visibility:hidden"> --}}
                        <!-- end map -->

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.mer_meta_keyword')}}</label>
                            <input type="text" class="form-control" placeholder="{{trans('localize.mer_meta_keyword')}}" name="stor_metakeywords" id="stor_metakeywords" value="{{ old('stor_metakeywords', $store->stor_metakeywords) }}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.mer_meta_description')}}</label>
                            <input type="text" class="form-control" placeholder="{{trans('localize.mer_meta_description')}}" name="stor_metadesc" id="stor_metadesc" value="{{ old('stor_metadesc', $store->stor_metadesc) }}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.mer_website')}}</label>
                            <input type="text" class="form-control" placeholder="{{trans('localize.mer_website_desc')}}" name="stor_website" id="stor_website" value="{{ old('stor_website', $store->stor_website) }}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.short_description')}}</label>
                            <input type="text" class="form-control" placeholder="{{trans('localize.short_description')}}" name="short_description" id="short_description" value="{{ old('short_description', $store->short_description) }}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.long_description')}}</label>
                            <textarea placeholder="{{trans('localize.long_description')}}" rows="5" class="form-control" name="long_description" id="long_description">{{ old('long_description', $store->long_description) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.office_hour')}}</label>
                            <textarea placeholder="{{trans('localize.office_hour')}}" rows="5" class="form-control" name="office_hour">{{ old('office_hour', $store->office_hour) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>{{trans('localize.image')}}</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.storeImg')}} <span class="text-danger">*</span></label>
                            <div id="img_upload">
                                <?php $path = env('IMAGE_DIR').'/store/'.$store->stor_merchant_id.'/'.$store->stor_img; ?>
                                <input type="hidden" name="stor_img_old" value="{{$store->stor_img}}">
                                <div class="row">
                                    <div class="col-xs-5 col-md-3">
                                        <img src="{{$path}}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';" class="img-responsive">
                                    </div>
                                    @if($edit_permission)
                                    <div class="col-xs-7 col-md-9">
                                        <span class="btn btn-default"><input type="file" name="stor_img" id="stor_img" class="files"></span>
                                        <br/>
                                        <small>{{trans('localize.gallery_desc')}}</small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($store->stor_type == 1)
            <div class="col-lg-5">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>{{trans('localize.gallery')}}</h5>
                    </div>
                    <div class="ibox-content">

                        <div class="form-group">
                            <div id="img_upload">
                                @if (count($images) == 0)
                                    <span class="btn btn-default"><input type="file" name="file[]" id="file" class="files"></span><br/><br/>
                                @else
                                    @foreach ($images as $key => $image)
                                        <?php $path = env('IMAGE_DIR').'/store/'.$store->stor_merchant_id.'/'.$image->image_name; ?>
                                        <div class="row widget" id="row{{ $key }}">
                                            <input type="hidden" name="file_old[]" value="{{$image->image_name}}">
                                            <input type="hidden" name="file_old_id[]" value="{{$image->id}}">
                                            <div class="col-xs-3 col-md-2">
                                                <img src="{{$path}}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';" class="img-responsive">
                                            </div>
                                            @if($edit_permission)
                                            <div class="col-xs-9 col-md-10">
                                                <span class="btn btn-default"><input type='file' id='file{{ $key }}' name='file[]' class="files"/></span>
                                                @if ($key > 0)
                                                    <a class="btn btn-md btn-danger" id="delImg" onclick="removeDiv('#row{{ $key }}'); return false;">{{trans('localize.remove')}}</a>
                                                @endif
                                                <br/>
                                                <small>{{trans('localize.gallery_desc')}}</small>
                                            </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif

                                <div id="divTxt"></div>
                                @if($edit_permission)
                                <a class="btn btn-sm btn-primary btn-grad pull-right" id="addImg">{{trans('localize.add_more')}}</a>
                                @endif
                                <input type="hidden" id="aid" value="1">
                                <input type="hidden" id="count" name="count" value="{{count($images)}}">
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="col-lg-5">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>@lang('localize.user_permission')</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            @if($users->count() > 0)
                                @foreach($users as $user)
                                <div class="col-sm-12">
                                    <div class="col-sm-1">
                                        {{-- <input type="checkbox" class="i-checks" name="store_users[]" value="{{ $user->id }}" {{ ($user->assigned == 1)? 'checked' : '' }} > --}}
                                        <input type="checkbox" class="i-checks" name="store_users[]" value="{{ $user->id }}" {{ (old('store_users[]', $user->assigned) == 1)? 'checked' : '' }} >
                                    </div>
                                    <div class="col-sm-11">
                                        <label>{{ $user->name }}</label>
                                    </div>
                                    <br><br>
                                </div>
                                @endforeach
                            @else
                                <div class="col-sm-12">
                                    <label class="text-danger"> @lang('localize.user_not_found')!, <a href="/admin/store/user/{{ $store->stor_merchant_id }}"> @lang('localize.click_here_to_add_store_user') </a></label>
                                </div>
                            @endif

                            <input type="hidden" name="exist" value="{{ ($users->count() > 0)? 1 : 0 }}">
                        </div>
                    </div>
                </div>
            </div>

            @if ($store->stor_type > 0)
            <div class="col-lg-5">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Categories</h5>
                    </div>
                    <div class="ibox-content">
                        <table id="showCats" class="table table-condensed table-striped">
                            <tbody>
                                @foreach ($categories as $key => $category)
                                    <tr id="{{$category['details']->offline_category_id}}">
                                        <td>{{($category['parents']->names) ? $category['parents']->names . ' > ' . $category['details']->name_en : '' . $category['details']->name_en}}</td>
                                        <td><a onclick="removeCat('{{$category['details']->offline_category_id}}'); return false;" class="btn btn-xs btn-danger pull-right">Remove</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <input type="hidden" id="selected_cats" name="selected_cats" class="compulsary" value="{{json_encode($store_category_list)}}">
                        <div id="cats" class="demo"></div>
                    </div>
                </div>
            </div>
            @endif

            <div class="col-lg-5">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>{{trans('localize.features')}} </h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.Featured')}}</label>
                            <div class="i-checks">
                                <label><input type="radio" value="1" name="featured" {{(old('featured', $store->featured) == 1)? 'checked' : '' }}> <i></i> {{trans('localize.yes')}} </label>
                            </div>
                            <div class="i-checks">
                                {{-- <label><input type="radio" value="0" name="featured" {{($store->featured == 0)? 'checked' : '' }}> <i></i> No </label> --}}
                                <label><input type="radio" value="0" name="featured" {{(old('featured', $store->featured) == 0)? 'checked' : '' }}> <i></i> {{trans('localize.no')}} </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label">{{trans('localize.Accept_Payment')}}</label>
                            <div class="i-checks">
                                <label><input type="radio" value="1" name="accept_payment" {{(old('accept_payment', $store->accept_payment) == 1)? 'checked' : '' }}> <i></i> {{trans('localize.yes')}} </label>
                                {{-- <label><input type="radio" value="1" name="accept_payment" {{($store->accept_payment == 1)? 'checked' : '' }}> <i></i> Yes </label> --}}
                            </div>
                            <div class="i-checks">
                                <label><input type="radio" value="0" name="accept_payment" {{(old('accept_payment', $store->accept_payment) == 0)? 'checked' : '' }}> <i></i> {{trans('localize.no')}} </label>
                            </div>
                        </div>

                        @if ($store->stor_type > 0)
                            <div class="form-group">
                                <label class="control-label">{{trans('localize.Listed')}} {{trans('localize.Offline')}} ?</label>
                                <div class="i-checks">
                                    <label><input type="radio" value="1" name="listed" {{($store->listed == 1)? 'checked' : '' }}> <i></i> {{trans('localize.yes')}} </label>
                                </div>
                                <div class="i-checks">
                                    <label><input type="radio" value="0" name="listed" {{($store->listed == 0)? 'checked' : '' }}> <i></i> {{trans('localize.no')}} </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">@lang('localize.default_price')</label>
                                <input type="text" class="form-control number" placeholder="{{trans('localize.default_price')}}" name="default_price" id="default_price" value="{{ old('default_price', $store->default_price) }}">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                {{-- <button class="btn btn-block btn-primary btn-block" id="submit">{{trans('localize.update_store')}}</button> --}}
                @if($edit_permission)
                <input id="update-store" type="button" class="btn btn-block btn-primary" value="{{trans('localize.update_store')}}">
                @endif
            </div>
        </div><br/>
    </form>
</div>
@endsection

@section('style')
<link href="/backend/css/plugins/jsTree/style.min.css" rel="stylesheet">
<link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
<style>
    .jstree-open > .jstree-anchor > .fa-folder:before {
        content: "\f07c";
    }

    .jstree-default .jstree-icon.none {
        width: 0;
    }

    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
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

<script src="/backend/js/plugins/jsTree/jstree.min.js"></script>
<script src="/backend/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript">
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

            var catSelected = JSON.parse($('#selected_cats').val());
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

            if (catExist == -1) {
                if (catSelected.length < 3) {
                    var cat_id = data.node.id;

                    $('input[name="selectedCats[]"]').filter(function() {
                        console.log($(this).val());
                    });

                    swal({
                        title: window.translations.sure,
                        text: "{{trans('localize.confirm_to_select')}} " + data.instance.get_node(data.selected[0]).text + " ?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#5cb85c",
                        confirmButtonText: "{{trans('localize.confirm')}}",
						cancelButtonText: "{{trans('localize.cancel')}}",
                        closeOnConfirm: true,
                    }, function(){
                        catSelected.push(data.node.id);
                        $('#selected_cats').val(JSON.stringify(catSelected));
                        $('#showCats').append("<tr id='" + data.node.id + "'><td>" + data.node.text + "</td><td><a onClick='removeCat(\"" + data.node.id + "\"); return false;' class='btn btn-xs btn-danger pull-right'>Remove</a></td></tr>");
                    });
                } else {
                    swal("Sorry!", "{{trans('localize.Maximum_3_category_only_can_be_selected')}}", "error");
                }
            } else {
                swal("Opsss!", data.node.text + " {{trans('localize.already_selected')}}", "error");
            }
        }
    });

    function removeCat(id) {
        var catSelected = JSON.parse($('#selected_cats').val());
        jQuery('#' + id).remove();
        var index = jQuery.inArray( id, catSelected );
        console.log(index);
        if (index > -1) {
            catSelected.splice(index, 1);
        }
        $('#selected_cats').val(JSON.stringify(catSelected));
    }

    function switchMap() {
        let storeCountryCode = $('#stor_country').val();
        let mapType = $('#map_type').val();

        if (storeCountryCode == '6' && mapType != 1) {
            $('#tab_baidu_map').click();
        } else {
            $('#tab_google_map').click();
        }
    }

    $(document).on('change', 'input[type=file]', function() {
        var fileSize = this.files[0].size;
        var fileExtension = ['jpeg', 'jpg', 'png'];

        if (fileSize > 1000000) {
            swal("{{trans('localize.error')}}", "{{trans('localize.imgSizeError')}}", "error");
            $('#' + this.id).val('');
        } else if ($.inArray($('#' + this.id).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            swal("{{trans('localize.error')}}", "{{trans('localize.imgError')}}", "error");
            $('#' + this.id).val('');
        }
    });

    $('#location').bind('keypress', function(e)
    {
        if(e.keyCode == 13)
        {
            return false;
        }
    });


    $(document).ready(function(){
        $('.number').keydown(function (e) {-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});

        var store_type = parseInt("{{ $store->stor_type }}");
        if(store_type == 1) {
            if($('#map_type').val() == 1) {
                $('#latitude_google').addClass('compulsary');
                $('#longitude_google').addClass('compulsary');
            } else {
                $('#latitude_baidu').addClass('compulsary');
                $('#longitude_baidu').addClass('compulsary');
            }
        }

        get_countries('#stor_country', "{{ old('stor_country', $store->stor_country? $store->stor_country : '0') }}", '#stor_state', "{{ old('stor_state', $store->stor_state? $store->stor_state : '0') }}");

        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $('#stor_country').change(function() {

            var country_id = $(this).val();

            if (country_id == '6') {
                $('#tab_baidu_map').click();
            } else {
                $('#tab_google_map').click();
            }
        });

        $('#tab_google_map').click(function() {
            if ($(this).parent().hasClass('active')) {
                return;
            }

            if(store_type == 1) {
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

            if(store_type == 1) {
                $('#latitude_google').removeClass('compulsary');
                $('#longitude_google').removeClass('compulsary');
                $('#latitude_baidu').addClass('compulsary');
                $('#longitude_baidu').addClass('compulsary');
            } else {
                removeMapCompulsary();
            }

            $('#map_type').val(2);
        });

        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $('#addImg').click(function() {
            var id = $('#aid').val();
            var count_id = $('#count').val();
            if (count_id < 9){
                $('#count').val(parseInt(count_id) + 1);
                $('#divTxt').append("<div id='row" + count_id + "'><span class='btn btn-default'><input type='file' id='file" + count_id + "' name='file[]'/></span>&nbsp;<a onClick='removeDiv(\"#row" + count_id + "\"); return false;' class='btn btn-md btn-danger'>{{trans('localize.remove')}}</a><br/><br/></div>");
                id = (id - 1) + 2;
                $('#aid').val(id);
            }
        });

        $('.files').change(function() {
            var fileExtension = ['jpeg', 'jpg', 'png'];

            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                swal("{{trans('localize.error')}}", "{{trans('localize.imgError')}}", "error");
                $(this).css('border', '1px solid red').focus().val('');
            } else if (($(this)[0].files[0].size) > 1000000){
                swal("{{trans('localize.error')}}", "{{trans('localize.imgSizeError')}}", "error");
                $(this).css('border', '1px solid red').focus().val('');
            }
        });

        $('#update-store').click(function(event) {
            var isValid = true

            $(':input').each(function(e) {
                if ($(this).hasClass('compulsary')) {
                    if (!$(this).val()) {
                        if($(this).hasClass('location')) {
                            swal("{{trans('localize.error')}}", "{!! trans('localize.store_location_info') !!}", "error");
                            event.preventDefault();
                            isValid = false;
                            return false;
                        } else if($(this).val() <= 0) {
                            $(this).attr('placeholder', '{{trans('localize.fieldrequired')}}').css('border', '1px solid red').focus();
                            event.preventDefault();
                            isValid = false;
                            return false;
                        }
                    }

                    if($(this).attr('id') === 'selected_cats') {
                        if(JSON.parse($(this).val()).length == 0) {
                            swal("{{trans('localize.error')}}", "{{trans('localize.select_category_to_proceed')}}", "error");
                            event.preventDefault();
                            isValid = false;
                            return false;
                        }
                    }
                }

                if ($(this).hasClass('files')) {
                    var fileExtension = ['jpeg', 'jpg', 'png'];

                    if($(this).val()) {
                        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                            swal("{{trans('localize.error')}}", "{{trans('localize.imgError')}}", "error");
                            $(this).css('border', '1px solid red').focus();
                            event.preventDefault();
                            isValid = false;
                            return false;
                        } else if (($(this)[0].files[0].size) > 1000000){
                            swal("{{trans('localize.error')}}", "{{trans('localize.imgSizeError')}}", "error");
                            $(this).css('border', '1px solid red').focus();
                            event.preventDefault();
                            isValid = false;
                            return false;
                        }
                    }
                }

                $(this).css('border', '');
            });
            if (isValid) {
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

                $("#mer_update_store").submit();
            }
        });
    });

    function removeMapCompulsary() {
        $('#latitude_google').removeClass('compulsary');
        $('#longitude_google').removeClass('compulsary');
        $('#latitude_baidu').removeClass('compulsary');
        $('#longitude_baidu').removeClass('compulsary');
    }
</script>
@endsection
