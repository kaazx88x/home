@extends('admin.layouts.master')

@section('title', 'View Auction')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>View Auction Product</h2>
        <ol class="breadcrumb">
            <li>
                Auction
            </li>
            <li class="active">
                <strong>View Auction</strong>
            </li>
        </ol>
    </div>
</div>
@if (isset($error))
    <div class="alert alert-danger">
        {{ $error }}
    </div>
@endif
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Auction Info</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-horizontal">
                         <div class="form-group">
                            <label class="col-lg-2 control-label">Merchant</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{{ $auction['merchant']->mer_fname }}</p>
                             </div>
                         </div>
                         <div class="form-group">
                             <label class="col-lg-2 control-label">Store</label>
                             <div class="col-lg-10">
                                <p class="form-control-static">{{$auction['store']->stor_name}}</p>

                             </div>
                         </div>
                         <div class="form-group">
                            <label class="col-lg-2 control-label">Product Type</label>
                            <div class="col-lg-10">
                                @if($auction['details']->auc_product_type == "demo_product")
                                    <p class="form-control-static">Demo Product</p>
                                @elseif($auction['details']->auc_product_type == "normal_product")
                                    <p class="form-control-static">Normal Product</p>
                                @else
                                    <p class="form-control-static">Rookie Product</p>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Product Status</label>
                            <div class="col-lg-10">
                                <p class="form-control-static {{ ($auction['details']->auc_status == 1)? 'text-navy' : 'text-warning' }}">{{ ($auction['details']->auc_status == 1)? 'Active' : 'Inactive' }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Title (English)</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{{ $auction['details']->auc_title_en }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Title (Chinese)</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{{ $auction['details']->auc_title_cn }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Title (Bahasa)</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{{ $auction['details']->auc_title_my }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Category</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{{ $auction['category']->mc_name_en }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Main Category</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{{ ($auction['maincategory']) ? $auction['maincategory']->smc_name_en : '' }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Sub Category</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{{ ($auction['subcategory']) ? $auction['subcategory']->sb_name_en : '' }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Second Sub Category</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{{ ($auction['secsubcategory']) ? $auction['secsubcategory']->ssb_name_en : '' }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Original Price</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{{ $auction['details']->auc_original_price }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Auction Price</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{{ $auction['details']->auc_auction_price }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Game Point Per Bid</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{{ $auction['details']->auc_game_point }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Client Game Point Target</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{{ $auction['details']->auc_total_bids }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Bot Game Point Target</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{{ $auction['details']->auc_bot_target }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Auction Start-End</label>
                            <div class="col-lg-8">
                                <div class="input-group">
                                    <p class="form-control-static">
                                        {{ $auction['details']->auc_start_date }}
                                     <b>-</b>
                                        {{ $auction['details']->auc_end_date }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label text-nowrap">Shipping Information</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{!! $auction['details']->auc_shippinginfo !!}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Description (English)</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{!! $auction['details']->auc_description_en !!}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Description (Chinese)</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{!! $auction['details']->auc_description_cn !!}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Description (Bahasa)</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{!! $auction['details']->auc_description_my !!}</p>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-lg-2 control-label">Meta Keywords</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{{ $auction['details']->auc_meta_keyword }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Meta Description</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">{{ $auction['details']->auc_meta_description }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">{{trans('localize.product_image')}}</label>
                            <?php
                                $images = (!empty($auction['details']->auc_image)) ? explode("/**/", $auction['details']->auc_image) : array();
                                $path = env('IMAGE_DIR').'/auction/'.$auction['merchant']->mer_id.'/';
                            ?>
                            <div class="col-lg-10">
                                <div class="row">
                                    @foreach ($images as $key => $image)
                                        <div class="col-md-3">
                                            <img class="img-responsive img-thumbnail" src="{{ $path.$image }}">
                                        </div>
                                    @endforeach
                               </div>
                            </div>
                        </div>

                        {{-- <div class="form-group">
                            <div class="col-lg-2 pull-right">
                                <button class="btn btn-block btn-primary" id="submit">Add Auction</button>
                            </div>
                            <div class="col-lg-2 pull-right">
                                <button class="btn btn-block btn-default" id="reset">Reset Form</button>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@stop
