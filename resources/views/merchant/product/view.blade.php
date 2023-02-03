@extends('merchant.layouts.master') @section('title', 'View Product') @section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.view_product')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url( $route . '/product/manage') }}">{{trans('localize.manage_products')}}</a>
            </li>
            <li class="active">
                <strong>{{trans('localize.view_product')}}</strong>
            </li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Product Info</h5>
                    <div class="ibox-tools">
                        <a href="{{ url( $route. '/product/edit', [$product['details']->pro_id]) }}" class="btn btn-primary btn-sm">@lang('localize.edit_product')</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.product_store_name')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{{($product['store']) ? $product['store']->stor_name : 'Store Unavailable'}}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.product_tittle_english')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{{$product['details']->pro_title_en}}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.product_tittle_chinese')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{{$product['details']->pro_title_cn}}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.product_tittle_bahasa')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{{$product['details']->pro_title_my}}</p></div>
                        </div>
                        {{-- <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.category')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{{$product['category']->mc_name_en}}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.main_category')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{{($product['maincategory']) ? $product['maincategory']->smc_name_en : ''}}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.sub_category')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{{($product['subcategory']) ? $product['subcategory']->sb_name_en : ''}}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.second_sub_category')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{{($product['secsubcategory']) ? $product['secsubcategory']->ssb_name_en : ''}}</p></div>
                        </div> --}}
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.product_quantity')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{{$product['details']->pro_qty}}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.price')}}</label>
                            <div class="col-lg-10">
                                <div class="row">
                                @foreach ($pricing as $ref => $item)
                                    <?php $item_attributes = ($ref) ? json_decode($ref, true) : null; ?>
                                    <div class="col-lg-12">
                                        @if($item_attributes)
                                            <p class="form-control-static">@lang('localize.product_attributes') :</p>
                                            @foreach($item_attributes as $attribute => $attribute_item)
                                                <b>{{$attribute}} : </b>{{$attribute_item}}</br>
                                            @endforeach
                                        @endif
                                        <br/>
                                    </div>
                                    @foreach ($item as $key => $price)
                                        <div class="col-lg-3">
                                            <div class="well well-sm text-justify">
                                                <h4><span class="text-center text-navy">{{ $price->co_name }}</span></h4>
                                                <span>@lang('localize.price') : {{ $price->co_cursymbol }} {{$price->price}}</span><br>
                                                @if($price->discounted_price > 0.00)
                                                    <span>@lang('localize.discounted') : {{ $price->co_cursymbol }} {{$price->discounted_price}}</span><br>
                                                    <span>@lang('localize.from') : {{ date('d/m/y h:i:s A', strtotime($price->discounted_from)) }}</span><br>
                                                    <span>@lang('localize.to') : {{ date('d/m/Y h:i:s A', strtotime($price->discounted_to)) }}</span><br>
                                                @endif
                                                @if($price->delivery_days)
                                                <span>@lang('localize.delivery_days') : {{$price->delivery_days}}</span><br>
                                                @endif
                                                <span>
                                                    @lang('localize.shipping_fees') :
                                                    @if ($price->shipping_fees_type == 1)
                                                        {{$price->co_cursymbol}} {{ number_format($price->shipping_fees , 2) }} @lang('localize.shipping_fees_product')
                                                    @elseif ($price->shipping_fees_type == 2)
                                                        {{$price->co_cursymbol}} {{ number_format($price->shipping_fees , 2) }} @lang('localize.shipping_fees_transaction')
                                                    @else
                                                        @lang('localize.shipping_fees_free')
                                                    @endif
                                                </span><br>
                                            </div>
                                        </div>
                                    @endforeach
                                    </div><hr/><div class="row">
                                @endforeach
                                </div>
                            </div>
                        </div>
                        {{-- <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.product_credit_value')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{{$product['details']->pro_vcoin_value}}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.original_price')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{{$product['details']->pro_price}}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.discounted_price')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{{$product['details']->pro_disprice}}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.shipping_amount')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{{$product['details']->pro_shippamt}}</p></div>
                        </div> --}}
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.description_english')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{!! $product['details']->pro_desc_en !!}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.description_chinese')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{!! $product['details']->pro_desc_cn !!}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.description_bahasa')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{!! $product['details']->pro_desc_my !!}</p></div>
                        </div>
                        {{-- <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.delivery_days')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{{$product['details']->pro_delivery}}</p></div>
                        </div> --}}
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.mer_meta_keyword')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{{$product['details']->pro_mkeywords}}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.mer_meta_description')}}</label>
                            <div class="col-lg-10"><p class="form-control-static">{{$product['details']->pro_mdesc}}</p></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">{{trans('localize.product_image')}}</label>
                            <div class="col-lg-10">
                                <div class="row">
                                    @foreach ($images as $key => $image)
                                        <?php
                                            // $images = (!empty($product['details']->pro_Img)) ? explode("/**/", $product['details']->pro_Img) : array();
                                            $path = $image->image;
                                            if (!str_contains($image->image, 'http://'))
                                                $path = env('IMAGE_DIR').'/product/'.$product['details']->pro_mr_id.'/'.$image->image;
                                        ?>
                                        <div class="col-md-2">
                                            <img class="img-responsive img-thumbnail" src="{{ $path }}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';" class="img-responsive img-thumbnail">
                                        </div>
                                    @endforeach
                               </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
