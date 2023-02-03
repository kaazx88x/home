@extends('merchant.layouts.master')

@section('title', 'Manage Products')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>{{trans('localize.manage_products')}}</h2>
        <ol class="breadcrumb">
            <li>
                {{trans('localize.product')}}
            </li>
            <li class="active">
                <strong>{{trans('localize.manage_products')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <form id="filter" action="{{ url($route . '/product/manage') }}" method="GET">
                <div class="col-sm-1">
                    <div class="form-group">
                        <input type="text" value="{{$input['id']}}" placeholder="{{trans('localize.#id')}}" class="form-control" id="id" name="id">
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="col-sm-5 nopadding">
                        <span class="input-group-btn">
                            <select class="form-control" id="search_type" name="search_type"  onchange="set_value()">
                                <option value="load_name" {{ ($input['search_type'] == "load_name") ? 'selected' : '' }}>{{trans('localize.productName')}}</option>
                                <option value="load_merchant_name" {{ ($input['search_type'] == "load_merchant_name") ? 'selected' : '' }} >{{trans('localize.merchant_user')}}{{trans('localize.name')}}</option>
                            </select>
                        </span>
                    </div>
                    <div class="col-sm-7 nopadding">
                        <input type="text" value="{{$input['name']}}" placeholder="{{trans('localize.Search_By_Product_Store_Merchant_Name')}}" class="form-control" id="name" name="name">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <select class="form-control" id="status" name="status">
                            <option value="" {{ ($input['status'] == "") ? 'selected' : '' }}>{{trans('localize.status')}}</option>
                            <option value="1" {{ ($input['status'] == "1") ? 'selected' : '' }}>{{trans('localize.active')}}</option>
                            <option value="0" {{ ($input['status'] == "0") ? 'selected' : '' }}>{{trans('localize.inactive')}}</option>
                            <option value="2" {{ ($input['status'] == "2") ? 'selected' : '' }}>{{trans('localize.incomplete')}}</option>
                            <option value="3" {{ ($input['status'] == "3") ? 'selected' : '' }}>{{trans('localize.pending_review')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <select class="form-control" id="sort" name="sort" style="font-family:'FontAwesome', sans-serif;">
                            <option value="id_asc" {{ ($input['sort'] == "id_desc") ? 'selected' : '' }}>{{trans('localize.#id')}} : &#xf162;</option>
                            <option value="id_desc" {{ ($input['sort'] == "" || $input['sort'] == "id_desc") ? 'selected' : '' }}>{{trans('localize.#id')}} : &#xf163;</option>
                            <option value="name_asc" {{ ($input['sort'] == "name_asc") ? 'selected' : '' }}>{{trans('localize.productName')}} : &#xf15d;</option>
                            <option value="name_desc" {{ ($input['sort'] == "name_desc") ? 'selected' : '' }}>{{trans('localize.productName')}} : &#xf15e;</option>
                            <option value="new" {{($input['sort'] == 'new') ? 'selected' : ''}}>{{trans('localize.newest')}}</option>
                            <option value="old" {{($input['sort'] == 'old') ? 'selected' : ''}}>{{trans('localize.oldest')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-block btn-outline btn-primary" id="filter">{{trans('localize.search')}}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            @include('merchant.common.success')
            @include('merchant.common.error')
            <div class="ibox">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <div class="col-lg-12 nopadding" style="margin-bottom:10px;">
                            <div class="col-sm-2 pull-right">
                              @if($logintype == 'merchants')
                                <a href="/{{ $route }}/product/add" class="btn btn-primary btn-block btn-md">@lang('localize.add_products')</a>
                              @endif
                            </div>
                        </div>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center text-nowrap">{{trans('localize.#id')}}</th>
                                    <th class="text-nowrap" width="70%">{{trans('localize.productName')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.store')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.details')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.category')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.product_image')}}</th>
                                    <th class="text-center text-nowrap" width="15%">{{trans('localize.action')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.status')}}</th>
                                    <!--<th class="text-center text-nowrap">{{trans('localize.preview_live')}}</th>-->
                                </tr>
                            </thead>
                            @if ($products->total())
                                <tbody>
                                    @foreach ($products as $key => $product)
                                        <tr class="text-center">
                                            <td>{{ $product->pro_id}}</td>
                                            <td class="text-left">{{ $product->pro_title_en}}</td>
                                            <td class="text-nowrap">{{($product->stor_name) ? $product->stor_name : trans('localize.store_unavailable')}}</td>
                                            {{--  <td>{{ $product->pro_no_of_purchase}}</td>  --}}
                                            <td class="text-left">
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
                                                <p>@lang('localize.quantity') : {{ $product->pro_qty }}</p>
                                                <p><a class="btn btn-outline btn-link btn-xs" href="/merchant/transaction/product?pid={{$product->pro_id}}&status=4"><span><i class="fa fa-line-chart"></i> View Sold Products</a></p>
                                            </td>
                                            <td class="text-nowrap">{{$product->category_name}}</td>
                                            {{-- <td class="text-left">
                                                @foreach ($pricing[$key] as $price)
                                                <small><div class="well well-sm text-nowrap">
                                                    <p class="text-center text-navy">{{ $price->co_name }}</p>
                                                    <p>@lang('localize.price') : {{ $price->co_cursymbol }} {{$price->price}}</p>
                                                    @if($price->discounted_price != 0.00)
                                                        <p>@lang('localize.discounted') : {{ $price->co_cursymbol }} {{$price->discounted_price}}</p>
                                                        <p>@lang('localize.from') : {{ date('d/m/Y', strtotime($price->discounted_from)) }}</p>
                                                        <p>@lang('localize.to') : {{ date('d/m/Y', strtotime($price->discounted_to)) }}</p>
                                                    @endif
                                                </small></div>
                                                @endforeach
                                            </td> --}}
                                            <?php
                                                $image = $product->image;
                                                if (!str_contains($product->image, 'http://'))
                                                    $image = env('IMAGE_DIR').'/product/'.$mer_id.'/'.$product->image;
                                            ?>
                                            <td width="10%">
                                                {{--  <img alt="image" src="{{ $image }}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';" class="img-responsive img-thumbnail">  --}}

                                                <a href="{{ $image }}" title="{{ $product->pro_id . ' - ' . $product->pro_title_en }}" data-gallery=""><img src="{{ $image }}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';" class="img-responsive img-thumbnail"></a>
                                            </td>
                                            <td class="text-nowrap">
                                                <p>
                                                    <a class="btn btn-white btn-sm btn-block" href="{{ url($route . '/product/edit', [$product->pro_id]) }}"><span><i class="fa fa-edit"></i> {{trans('localize.edit')}}</span></a>
                                                </p>
                                                <p>
                                                    <a href="{{ url($route . '/product/view', [$product->pro_id]) }}" class="btn btn-white btn-sm btn-block"><span><i class="fa fa-file-text-o"></i> {{trans('localize.view')}}</span></a>
                                                </p>

                                                @if($product->pro_type == 4)
                                                <p>
                                                    <a style="width:100%" class="btn btn-white btn-sm" href="{{ url($route.'/product/code/listing', [$product->pro_id]) }}"><span><i class="fa fa-barcode"></i> @lang('localize.e-card.view')</span></a>
                                                </p>
                                                @endif

                                            </td>
                                            <td>
                                                @if (($product->pro_status) == 1)
                                                    <a target="_blank" class="btn btn-outline btn-link btn-sm btn-block" href="/products/detail/{{$product->pro_id}}"><span><i class='fa fa-search'></i> {{trans('localize.preview')}}</span></a>
                                                    <span class="text-nowrap text-navy"><i class='fa fa-check'></i> {{trans('localize.active')}}</span>
                                                @elseif (($product->pro_status) == 0)
                                                    <span class="text-nowrap text-warning"><i class='fa fa-ban'></i> {{trans('localize.inactive')}}</span>
                                                @elseif (($product->pro_status) == 2)
                                                    <span class="text-nowrap text-danger"><i class='fa fa-wrench'></i> @lang('localize.incomplete')</span>
                                                @elseif (($product->pro_status) == 3)
                                                    <span class="text-nowrap text-danger"><i class='fa fa-warning'></i> @lang('localize.pending_review')</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10">
                                            <div class=" col-xs-6">
                                                <span class="pagination">
                                                    Showing {{$products->firstItem()}} to {{$products->lastItem()}} of {{$products->Total()}} Records
                                                </span>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <div class="col-xs-7 text-right">
                                                    {{$products->appends(Request::except('page'))->links()}}
                                                </div>
                                                <div class="col-xs-5 text-right pagination">
                                                    Go To Page
                                                    <input type='number' id='pageno' name='pageno' size="3" min="1" max="{{$products->lastPage()}}">
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="gotopage('page')">
                                                        <i class="fa fa-share-square-o"></i> Go
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            @else
                                <tr>
                                    <td colspan="10" class="text-center">@lang('localize.nodata')</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<link href="/backend/css/plugins/blueimp/css/blueimp-gallery.min.css" rel="stylesheet">
@endsection

@section('script')
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

        $('#search_type a').on('click', function(e) {
            e.preventDefault();
            $("#search_type li").removeClass("select");
            $(this).closest('li').addClass('select');
            $('#search_type').val($(this).data('value'));

            var link = $(this).data('value');
            $("#name").typeahead('destroy');
            $.get('/'+link, function(data){
                $("#name").typeahead({
                    source: data
                });
            },'json');
        });

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
