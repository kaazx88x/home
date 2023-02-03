@extends('admin.layouts.master')

@section('title', 'Manage Product')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.manage_product')}}</h2>
        <ol class="breadcrumb">
            <li>
			{{trans('localize.product')}}
            </li>
            <li class="active">
                <strong>{{trans('localize.manage')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="ibox float-e-margins border-bottom">
        <a class="collapse-link nolinkcolor">
            <div class="ibox-title ibox-title-filter">
                <h5>@lang('localize.Search_Filter')</h5>
                <div class="ibox-tools">
                    <i class="fa fa-chevron-down"></i>
                </div>
            </div>
        </a>
        <div class="ibox-content ibox-content-filter" style="display:none;">
            <div class="row">
                <form class="form-horizontal" id="filter" action='/admin/product/manage' method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.Search_by_ID')}}</label>
                        <div class="col-sm-3">
                            <input type="text" value="{{$input['id']}}" placeholder="{{trans('localize.Search_By_Product_ID')}}" class="form-control" id="id" name="id">
                        </div>
                        <div class="col-sm-3">
                            <input type="text" value="{{$input['mid']}}" placeholder="{{trans('localize.Search_By_Merchant_ID')}}" class="form-control" id="mid" name="mid">
                        </div>
                        <div class="col-sm-3">
                            <input type="text" value="{{$input['sid']}}" placeholder="{{trans('localize.Search_By_Store_ID')}}" class="form-control" id="sid" name="sid">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.search')}}</label>
                        <div class="col-sm-9">
                            <div class="col-sm-3 nopadding">
                                <span class="input-group-btn">
                                    <select class="form-control" id="search_type" name="search_type"  onchange="set_value()">
                                        <option value="load_name" {{ ($input['search_type'] == "load_name") ? 'selected' : '' }}>{{trans('localize.productName')}}</option>
                                        <option value="load_store_name" {{ ($input['search_type'] == "load_store_name") ? 'selected' : '' }}>{{trans('localize.mer_store_name')}}</option>
                                        <option value="load_merchant_name" {{ ($input['search_type'] == "load_merchant_name") ? 'selected' : '' }} >{{trans('localize.merchant_user')}}{{trans('localize.name')}}</option>
                                    </select>
                                </span>
                            </div>
                            <div class="col-sm-9 nopadding">
                                <input type="text" value="{{$input['name']}}" placeholder="{{trans('localize.Search_By_Product_Store_Merchant_Name')}}" class="form-control" id="name" name="name">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.Status')}}</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="status" name="status">
                                <option value="" {{ ($input['status'] == "") ? 'selected' : '' }}>{{trans('localize.All')}}</option>
                                <option value="1" {{ ($input['status'] == "1") ? 'selected' : '' }}>{{trans('localize.Active')}}</option>
                                <option value="0" {{ ($input['status'] == "0") ? 'selected' : '' }}>{{trans('localize.Inactive')}}</option>
                                <option value="2" {{ ($input['status'] == "2") ? 'selected' : '' }}>{{trans('localize.incomplete')}}</option>
                                <option value="3" {{ ($input['status'] == "3") ? 'selected' : '' }}>{{trans('localize.Pending_Review')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.sort')}}</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="sort" name="sort" style="font-family:'FontAwesome', sans-serif;">
                                <option value="id_asc" {{ ($input['sort'] == "id_desc") ? 'selected' : '' }}>#ID : &#xf162;</option>
                                <option value="id_desc" {{ ($input['sort'] == "id_desc") ? 'selected' : '' }}> #ID : &#xf163;</option>
                                <option value="name_asc" {{ ($input['sort'] == "name_asc") ? 'selected' : '' }}>{{trans('localize.product')}} {{trans('localize.name')}} : &#xf15d;</option>
                                <option value="name_desc" {{ ($input['sort'] == "name_desc") ? 'selected' : '' }}>{{trans('localize.product')}} {{trans('localize.name')}} : &#xf15e;</option>c
                                <option value="new" {{($input['sort'] == "" || $input['sort'] == 'new') ? 'selected' : ''}}>{{trans('localize.Newest')}}</option>
                                <option value="old" {{($input['sort'] == 'old') ? 'selected' : ''}}>{{trans('localize.Oldest')}}</option>
                                <option value="merchant_asc" {{ ($input['sort'] == "merchant_asc") ? 'selected' : '' }}>{{trans('localize.Merchants')}} {{trans('localize.Name')}} : &#xf15d;</option>
                                <option value="merchant_desc" {{ ($input['sort'] == "merchant_desc") ? 'selected' : '' }}>{{trans('localize.Merchants')}} {{trans('localize.Name')}} : &#xf15e;</option>
                                <option value="store_asc" {{ ($input['sort'] == "store_asc") ? 'selected' : '' }}>{{trans('localize.store')}} {{trans('localize.Name')}} : &#xf15d;</option>
                                <option value="store_desc" {{ ($input['sort'] == "store_desc") ? 'selected' : '' }}>{{trans('localize.store')}} {{trans('localize.Name')}} : &#xf15e;</option>
                            </select>
                        </div>
                    </div>
                     <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('localize.country')</label>
                        <div class="col-sm-9">
                            <label style="cursor: pointer;"><input type="checkbox" class="i-checks input_checkbox" name="countries[]" value="0" {{ (isset($input['countries']) && (in_array("0", $input['countries']))? 'checked' : '' ) }}>&nbsp; No Country</label>&nbsp;
                            @foreach($countries as $country)
                                <label style="cursor: pointer;"><input type="checkbox" class="i-checks input_checkbox" name="countries[]" value="{{ $country->co_id }}" {{ (isset($input['countries']) && (in_array($country->co_id, $input['countries']))? 'checked' : '' ) }}>&nbsp; {{ $country->co_name }}</label>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-9 col-sm-offset-2">
                            <button type="submit" class="btn btn-block btn-outline btn-primary" id="filter">{{trans('localize.search')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            @include('admin.common.success')
            @include('admin.common.status')
            @include('admin.common.error')
            <div class="ibox">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center text-nowrap">#ID</th>
                                    <th class="text-nowrap" witdh="30%">{{trans('localize.product')}} {{trans('localize.Name')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Merchants')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.store')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Details')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.quantity_sold')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.category')}}</th>
                                    {{-- <th class="text-center text-nowrap">{{trans('localize.Quantity')}}</th>
                                    <th class="text-center text-nowrap">Mei Point</th>
                                    <th class="text-center text-nowrap">Sold Products</th> --}}
                                    <th class="text-center text-nowrap">{{trans('localize.image')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Action')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Status')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $key => $product)
                                    <tr class="text-center">
                                        <td>{{ $product->pro_id }}</td>
                                        <td class="text-left">{{ $product->pro_title_en}}</td>
                                        <td class="text-left">
                                            <a class="nolinkcolor" href="/admin/merchant/view/{{ $product->pro_mr_id }}" data-toggle="tooltip" title="View this merchant">{{ $product->pro_mr_id }} - {{ $product->mer_fname }}</a>
                                        </td>
                                        <td class="text-left">
                                            @if(!empty($product->stor_id))
                                                <a class="nolinkcolor" href="/admin/store/edit/{{$product->mer_id}}/{{$product->stor_id}}" data-toggle="tooltip" title="Edit this store">{{$product->stor_id}} - {{$product->stor_name}}</a>
                                            @else
                                                <span class="text-danger">{{trans('localize.store_unavailable')}}</span>
                                            @endif
                                        </td>
                                        <td nowrap class="text-left">
                                            <p>
                                                @lang('localize.type') :
                                                @if($product->pro_type == 1)
                                                    @lang('localize.normal_product')
                                                @elseif($product->pro_type == 2)
                                                    @lang('localize.coupon')
                                                @elseif($product->pro_type == 3)
                                                    @lang('localize.ticket')
                                                @elseif($product->pro_type == 4)
                                                    @lang('localize.e-card.name')
                                                @endif
                                            </p>
                                            <p>{{trans('localize.quantity')}} : {{ $product->pro_qty }}</p>
                                            <p><a class="btn btn-outline btn-link btn-xs" href="/admin/transaction/product/orders?pid={{$product->pro_id}}"><span><i class="fa fa-line-chart"></i> {{trans('localize.View_Sold_Products')}}</a></p>
                                            {{-- <p>Mei Point : {{ $product->pro_vtoken_value}}</p> --}}
                                            {{-- @foreach ($pricing[$key] as $price)
                                            <small><div class="well well-sm text-nowrap">
                                                <p class="text-center text-navy">{{ $price->co_name }}</p>
                                                <p>@lang('localize.price') : {{ $price->co_cursymbol }} {{$price->price}}</p>
                                                @if($price->discounted_price != 0.00)
                                                    <p>@lang('localize.discounted') : {{ $price->co_cursymbol }} {{$price->discounted_price}}</p>
                                                    <p>@lang('localize.from') : {{ \Helper::UTCtoTZ($price->discounted_from) }}</p>
                                                    <p>@lang('localize.to') : {{ \Helper::UTCtoTZ($price->discounted_to) }}</p>
                                                @endif
                                            </small></div>
                                            @endforeach --}}
                                        </td>
                                        <td class="text-nowrap">{{$product->pro_no_of_purchase}}</td>
                                        <td class="text-nowrap">{{$product->category}}</td>
                                        {{-- <td>{{ $product->pro_qty }}</td>
                                        <td>{{ $product->pro_vtoken_value}}</td> --}}
                                        {{-- <td>
                                            <p>
                                                <a style="width:100%" class="btn btn-white btn-sm" href="/admin/transaction/product/orders?id={{$product->pro_id}}&status=4"><span><i class="fa fa-line-chart"></i> View Transaction</a>
                                            </p>
                                        </td> --}}
                                        <?php
                                            $mer_id = $product->mer_id;
                                            $image = $product->image;
                                            if (!str_contains($product->image, 'http://'))
                                                $image = env('IMAGE_DIR').'/product/'.$mer_id.'/'.$product->image;
                                        ?>
                                        <td width="10%">
                                            {{--  <img alt="image" src="{{$image}}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';" class="img-responsive img-thumbnail">  --}}
                                            <a href="{{ $image }}" title="{{ $product->pro_id . ' - ' . $product->pro_title_en }}" data-gallery=""><img src="{{ $image }}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';" class="img-responsive img-thumbnail"></a>
                                        </td>
                                        <td class="text-nowrap" width="5%">
                                            <p>
                                                <a class="btn btn-white btn-sm" href="/admin/product/edit/{{$mer_id}}/{{$product->pro_id}}"><span><i class="fa fa-edit"></i> {{trans('localize.Edit')}}</span></a>
                                                <a class="btn btn-white btn-sm" href="/admin/product/view/{{$mer_id}}/{{$product->pro_id}}"><span><i class="fa fa-file-text-o"></i> {{trans('localize.view')}}</span></a>
                                            </p>
                                            <!--<p>
                                                <a href="/admin/product/image/view/{{$mer_id}}/{{$product->pro_id}}" class="btn btn-white btn-sm btn-block"><span><i class="fa fa-image"></i> Manage Image</span></a>
                                            </p>-->

                                            @if($product->pro_type == 4)
                                            <p>
                                                <a style="width:100%" class="btn btn-white btn-sm" href="{{ url('admin/product/code/listing', [$product->pro_mr_id, $product->pro_id]) }}"><span><i class="fa fa-barcode"></i> @lang('localize.e-card.view')</span></a>
                                            </p>
                                            @endif

                                            <p>
                                                @if($product->pro_status == 1)
                                                    <a style="width:100%" class="btn btn-white btn-sm text-warning" href="/update_product_status/{{$product->pro_id}}/0"><span><i class="fa fa-refresh"></i>  {{trans('localize.set_to_inactive')}}</span></a>
                                                @elseif ($product->pro_status == 0)
                                                    <a style="width:100%" class="btn btn-white btn-sm text-navy" href="/update_product_status/{{$product->pro_id}}/1"><span><i class="fa fa-refresh"></i>  {{trans('localize.set_to_active')}}</span></a>
                                                @elseif ($product->pro_status == 3)
                                                    <p><a style="width:100%" class="btn btn-white btn-sm text-warning" href="/update_product_status/{{$product->pro_id}}/0"><span><i class="fa fa-refresh"></i>  {{trans('localize.set_to_inactive')}}</span></a></p>
                                                    <p><a style="width:100%" class="btn btn-white btn-sm text-navy" href="/update_product_status/{{$product->pro_id}}/1"><span><i class="fa fa-refresh"></i>  {{trans('localize.set_to_active')}}</span></a></p>
                                                @endif
                                            </p>
                                        </td>
                                        <td>
                                            @if ($product->pro_status == 1)
                                                <a target="_blank" class="btn btn-outline btn-link btn-sm btn-block" href="/products/detail/{{$product->pro_id}}"><span><i class='fa fa-search'></i> {{trans('localize.preview_live')}}</span></a>
                                                <span class="text-nowrap text-navy"><i class='fa fa-check'></i> {{trans('localize.Active')}}</span>
                                            @elseif ($product->pro_status == 0)
                                                <span class="text-nowrap text-warning"><i class='fa fa-ban'></i> {{trans('localize.Inactive')}}</span>
                                            @elseif ($product->pro_status == 2)
                                                <span class="text-nowrap text-danger"><i class='fa fa-wrench'></i> {{trans('localize.incomplete')}}</span>
                                            @elseif ($product->pro_status == 3)
                                                <span class="text-nowrap text-danger"><i class='fa fa-warning'></i> {{trans('localize.pending_review')}}</span>
                                            @endif

                                        </td>
                                        {{-- @if (($product->pro_status) == 1)
                                            <td class="text-nowrap text-navy"><i class='fa fa-check'></i> Active</td>
                                        @elseif (($product->pro_status) == 0)
                                            <td class="text-nowrap text-warning"><i class='fa fa-ban'></i> Inactive</td>
                                        @endif --}}
                                        {{-- <td class="text-nowrap">
                                            @if (($product->pro_status) == 1)
                                                <a target="_blank" class="btn btn-white btn-block btn-sm" href="/products/detail/{{$product->pro_id}}"><span><i class='fa fa-search'></i> Preview</span></a>
                                            @else
                                                <span style="color:red;">Product Inactive</span>
                                            @endif
                                        </td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                            {{-- <tfoot>
                                <tr>
                                    <td colspan="10">
                                        <ul class="pagination pull-right">{{$products->appends(Request::except('page'))->links()}}</ul>
                                    </td>
                                </tr>
                            </tfoot> --}}
                             <tfoot>
                                <tr>
                                    <td colspan="10">
                                        <div class="row">
                                            <div class=" col-xs-6">
                                                <span class="pagination">
												{{trans('localize.Showing')}} {{$products->firstItem()}} {{trans('localize.to')}} {{$products->lastItem()}} {{trans('localize.of')}} {{$products->Total()}} {{trans('localize.Records')}}
                                                </span>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <div class="col-xs-7 text-right">
                                                    {{$products->appends(Request::except('page'))->links()}}
                                                </div>
                                                <div class="col-xs-5 text-right pagination">
													{{trans('localize.Go_To_Page')}}
                                                    <input type='number' id='pageno' name='pageno' size="3" min="1" max="{{$products->lastPage()}}">
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="gotopage('page')">
                                                        <i class="fa fa-share-square-o"></i> {{trans('localize.Go')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
<link href="/backend/css/plugins/blueimp/css/blueimp-gallery.min.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script src="/backend/js/plugins/blueimp/jquery.blueimp-gallery.min.js"></script>
<script src="/backend/js/plugins/typehead/bootstrap3-typeahead.js"></script>
<script src="/backend/js/plugins/typehead/bootstrap3-typeahead.min.js"></script>

<script>
    $(document).ready(function() {

        $("#pageno").change(function(){
            var input = $(this).val();
            var max = {{$products->lastPage()}};
            if(input > max){
                $(this).val(max);
            }
        });

        var link = $('#search_type').val();
        $.get('/'+link, function(data){
            $("#name").typeahead({
                source: data
            });
        },'json');
    });

    function gotopage($page) {
        $val =  $("#pageno").val();

        var href = window.location.href.substring(0, window.location.href.indexOf('?'));
        var qs = window.location.href.substring(window.location.href.indexOf('?') + 1, window.location.href.length);
        var newParam = $page + '=' + $val;

        if (qs.indexOf($page + '=') == -1) {
            if (qs == '') {
                qs = '?'
            }
            else {
                qs = qs + '&'
            }
            qs = newParam;

        }
        else {
            var start = qs.indexOf($page + "=");
            var end = qs.indexOf("&", start);
            if (end == -1) {
                end = qs.length;
            }
            var curParam = qs.substring(start, end);
            qs = qs.replace(curParam, newParam);
        }
        window.location.replace(href + '?' + qs);
    }



    function set_value() {
        var link = $('#search_type').val();
        $("#name").typeahead('destroy');

        $.get('/'+link, function(data){
            $("#name").typeahead({
                source: data
            });
        },'json');
    }
</script>
@endsection