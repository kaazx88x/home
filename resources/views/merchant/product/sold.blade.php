@extends('merchant.layouts.master')

@section('title', 'Sold Product')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>{{trans('localize.sold_product')}}</h2>
        <ol class="breadcrumb">
            <li>
               <a href="{{ url( $route . '/product/manage') }}">{{trans('localize.manage_products')}}</a>
            </li>
            <li class="active">
                <strong>{{trans('localize.sold_product')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <form id="filter" action="{{ url('merchant/product/sold') }}" method="GET">
                <div class="col-sm-1">
                    <div class="form-group">
                        <input type="text" value="{{$input['id']}}" placeholder="{{trans('localize.#id')}}" class="form-control" id="id" name="id">
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="form-group">
                        <input type="text" value="{{$input['name']}}" placeholder="{{trans('localize.productName')}}" class="form-control" id="name" name="name">
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
            <div class="ibox">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="footable table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th class="text-center">{{trans('localize.#id')}}</th>
                                <th>{{trans('localize.productName')}}</th>
                                <th class="text-center">{{trans('localize.store_name')}}</th>
                                <th class="text-center">{{trans('localize.product_image')}}</th>
                                <th class="text-center">{{trans('localize.action')}}</th>
                            </tr>
                            </thead>
                            @if ($products->total())
                                <tbody>
                                    @foreach ($products as $product)
                                    <tr class="text-center">
                                        <?php
                                            // $images = (!empty($product->pro_Img)) ? explode("/**/", $product->pro_Img) : array();
                                            // $path = env('IMAGE_DIR').'/product/'.$product->pro_mr_id.'/';
                                            $image = env('IMAGE_DIR').'/product/'.$product->pro_mr_id.'/'.$product->image;
                                        ?>
                                        <td>{{$product->pro_id}}</td>
                                        <td class="text-left">{{$product->pro_title_en}}</td>
                                        <td>{{$product->stor_name}}</td>
                                        <td width="13%"><img alt="image" src="{{$image}}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';" class="img-responsive img-thumbnail"></td>
                                        <td><a href="{{ url('merchant/product/view', [$product->pro_id]) }}" class="btn btn-white btn-block btn-sm"><span><i class="fa fa-pencil"></i> {{trans('localize.view')}}</span></a></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7">
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
                                    <td colspan="7" class="text-center">@lang('localize.nodata')</td>
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
@section('script')
<script>
    $(document).ready(function() {

        $("#pageno").change(function(){
            var input = $(this).val();
            var max = {{$products->lastPage()}};
            if(input > max){
                $(this).val(max);
            }
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

</script>
@endsection