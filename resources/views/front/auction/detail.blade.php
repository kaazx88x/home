@extends('layouts.master')

@section('content')
<?php
    $cusid = (Auth::user()) ? Auth::user()->cus_id : '';

    $auctionStarted = false;
    if (strtotime(date('Y-m-d H:i:s')) >= strtotime($auction->auc_start_date)) {
        $auctionStarted = true;
    }
?>
<div class="columns-container">
    <div class="container" id="columns">
        <!-- breadcrumb -->
        <div class="breadcrumb clearfix">
            <a class="home" href="/" title="Return to Home">{{trans('localize.home')}}</a>
            <span class="navigation-pipe">&nbsp;</span>
            <a href="/products" title="">{{trans('front.mall')}}</a>
        </div>
        <!-- ./breadcrumb -->
        <!-- row -->
        <div class="row">

            <!-- Center colunm-->
            <div class="center_column col-xs-12 col-sm-12" id="center_column">
                <!-- Product -->
                <div id="product">
                    <div class="primary-box row">
                        <div class="pb-left-column col-xs-12 col-sm-5">
                            <!-- product-imge-->
                            <?php $auc_img = explode('/**/', $auction->auc_image);?>
                            <div class="product-image">
                                <div class="product-full">
                                    <img id="product-zoom" src="{{'/images/auction/'.$auc_img[0]}}" data-zoom-image="{{'/images/auction/'.$auc_img[0]}}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';"/>
                                </div>
                                <div class="product-img-thumb" id="gallery_01">
                                    <ul class="owl-carousel" data-items="3" data-nav="true" data-dots="false"
                                        data-margin="21" data-loop="false">
                                        @foreach($auc_img as $key => $img)
                                            @if($img)
                                            <li>
                                                <a href="#" data-image="{{'/images/auction/'.$img}}"
                                                   data-zoom-image="{{'/images/auction/'.$img}}">
                                                    <img id="product-zoom" src="/web/images/loading.gif" data-src="{{'/images/auction/'.$img}}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';"/>
                                                </a>
                                            </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <!-- product-imge-->
                        </div>
                        <div class="pb-right-column col-xs-12 col-sm-7">
                            <input type="hidden"  id="serverdate" value="">
                            <input type="hidden"  id="auccusid" value="{{$cusid}}">
                            <input type="hidden"  id="auction_type" value="{{$auction->auc_product_type}}">
                            <input type="hidden"  id="page_type" value="view">
                            <input type='hidden' name="auctionid" id='{{$auction->auc_id}}' value='{{$auction->auc_id}}' />
                            <input type='hidden' name="servertime"   value='{{strtotime(date('Y-m-d H:i:s')) >= strtotime($auction->auc_start_date)}}' />
                            <input type='hidden' name="auctionstarted" id='auctionstarted_{{$auction->auc_id}}' value='{{!$auctionStarted?'0':'1'}}' />
                            <input type='hidden' name="lastbiddate" id='lastbiddate_{{$auction->auc_id}}' value='{{$auction->lastbiddate}}' />
                            <input type='hidden' name="auctionstartdate" id='auctionstartdate_{{$auction->auc_id}}' value='{{$auction->auc_start_date}}' />
                            <h1 class="product-name">{{ucwords(strtolower($auction->title))}}</h1>
                            <div class="content_price bid">
                                <table>
                                    <tr>
                                        <td>{{trans('localize.retail')}}</td>
                                        <td> : </td>
                                        <td>&nbsp;<b>USD {{number_format((round($auction->auc_original_price)), 0, '.', ',')}}</b></td>
                                    </tr>
                                    <tr>
                                        <td>{{trans('localize.startDate')}}</td>
                                        <td> : </td>
                                        <td>&nbsp;<b id="auctionstartdate">{{date('d F Y H:i:s', strtotime($auction->auc_start_date))}}</b></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="info-orther clearfix"></div>
                            <div class="timer_section Required-bid row">
                                <div class="col-xs-12">
                                    @if($auction->auc_status == 1)
                                        <span class="time-icon"><i class="fa fa-clock-o" aria-hidden="true"></i> {{(!$auctionStarted) ? (trans('localize.to_start')) : (trans('localize.time_left'))}}</span>
                                        <span class="timer timing" id="timerCounter_{{$auction->auc_id}}">--</span>
                                    @else
                                        <span class="message_box timing" ><b>{{trans('localize.aucEnd')}}</b></span>
                                    @endif
                                </div>
                                <div class="col-xs-12">
                                    <div class="bid-price">
                                        <p>{{ trans('localize.bidRequired') }}:</p>
                                        <div class="total-points"><img src="/web/images/loading.gif" data-src="{{url('/web/images/game-icon.png')}}" alt=""/>
                                            <span class="timingmultiply">X
                                                <span id='bidsAmount'>
                                                    @if(isset($auction->auc_game_point) && $auction->auc_game_point != 0)
                                                    {{$auction->auc_game_point}}
                                                    @else
                                                    {{'2'}}
                                                    @endif
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="divaction">
                                        @if ($auction->auc_status == 1)
                                            <div class="bids_buttons buttons" id="biddingbutton_{{$auction->auc_id}}" style="display:{{$auctionStarted?'block':'none'}}">
                                                @if (Auth::check())
                                                    <?php
                                                        $winner = '';
                                                        if($auction->auc_product_type == 'rookie_product') {
                                                            $winner = Home::get_auction_winner_byid($cusid, 'rookie_product');
                                                        }
                                                    ?>
                                                    @if (empty($winner))
                                                        <a class='bidNowBulk' data-id='{{$auction->auc_id}}' data-gp='{{$auction->auc_game_point}}'>
                                                            <button class="button">{{ trans('localize.bidnow') }}</button>
                                                        </a>
                                                        <a class='autobidModal' data-id='{{$auction->auc_id}}' data-gp='{{$auction->auc_game_point}}'>
                                                            <button class="button">{{ trans('localize.autobid') }}</button>
                                                        </a>
                                                        <br/>
                                                        <label class='autobidlabel'>
                                                            {{ trans('localize.autobidLeft') }}: <span id='autoBidsLeft'>{{(!empty($cust_autobid)) ? $cust_autobid->ab_bids_place : '0'}}</span>
                                                        </label>
                                                    @else
                                                        <strong>{{ trans('localize.wonRookies') }}</strong>
                                                    @endif
                                                @else
                                                    <button class="button request-login">{{ trans('localize.bidnow') }}</button>
                                                    <button class="button request-login">{{ trans('localize.autobid') }}</button>
                                                @endif
                                            </div>
                                            <span id="bidWin" style="display:none;">
                                                <h4 class="prev_text" style="margin: 0 0 20px !important;">
                                                    <i class="fa fa-trophy" aria-hidden="true" style="color: #c79810;"></i>
                                                    <br/>
                                                    <span id="winnerName">...</span>
                                                </h4>
                                                <span id="bidCheckout"></span>
                                            </span>
                                            <div class="auction_status startsoon " id="auctionstatus_{{$auction->auc_id}}" style="display:{{$auctionStarted?'none':'block'}}; text-align: center;"> {{ trans('localize.starting_soon') }} </div>

                                            <span id="biddingbuttonHide" style="display:none;background-color: red;padding: 10px;color: #fff;"></span>
                                        @elseif($auction->auc_status == 2)
                                            <div class="bids_buttons buttons">
                                                @if (Auth::check() && $auction->auc_winner_cus_id == $cusid && $auction->auc_product_type != 'demo_product')
                                                    <a href="/auctions/checkout/{{$auction->auc_id}}"><button class="button">{{ trans('localize.checkoutAuction') }}</button></a>
                                                @else
                                                    {{ trans('localize.wonRookies') }}
                                                    <strong>{{ trans('localize.aucEnd') }}</strong>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="top_latest_bids">
                                <div class="total_bids_info first">
                                    <div class="left-total">
                                        <span><img alt="" src="/web/images/loading.gif" data-src="{{url('/web/images/game-icon.png')}}" width="20">{{ trans('localize.bidTotal') }}</span>
                                        <span class="bid-total" id='{{"bidsTotal_" . $auction->auc_id}}'>{{$auction->auc_total_gp}}</span>
                                    </div>
                                </div>

                                <!-- Latest bidder list starts-->
                                <div class="total_bids_info">
                                    <p class="hbclass5 bidder-title">{{ trans('localize.top10') }}</p>

                                    <ul class="bidder-history">
                                        @if ($bidders)
                                        <input type="hidden" id="lastbidtime" value="{{$bidders[0]->oa_bid_date}}">
                                            @foreach ($bidders as $key => $bidder)
                                            <?php
                                            // $biddatetime = date("Y-m-d H:i:s", strtotime($bidder->oa_bid_date));
                                            // $biddatetime = CommonHelper::ConvertUTCToLocalDateTime($biddatetime)->format('d M Y, h:i:s A');
                                            ?>
                                            <li>
                                                {{-- <span class="bid-time">{{$biddatetime}}</span> --}}
                                                <span class="bid-time">{{$bidder->oa_bid_date}}</span>
                                                <span class="bidder pull-right">{{$bidder->oa_cus_name}}</span>
                                            </li>
                                            @endforeach
                                        @endif
                                    </ul>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- tab product -->
                    <div class="product-tab">
                        <ul class="nav-tab">
                            <li class="active">
                                <a aria-expanded="false" data-toggle="tab" href="#product-detail">{{ trans('localize.pro_details') }}</a>
                            </li>
                            <li>
                                <a aria-expanded="true" data-toggle="tab" href="#information">{{ trans('localize.information') }}</a>
                            </li>
                        </ul>
                        <div class="tab-container">
                            <div id="product-detail" class="tab-panel active">
                                {!!$auction->auc_description_en!!}
                            </div>
                            <div id="information" class="tab-panel">
                                <table class="table table-bordered">
                                    <tr>
                                        <td width="200">{{ trans('localize.product') }}</td>
                                        <td>{{$auction->title}}</td>
                                    </tr>
                                    <tr>
                                        <td width="200">{{ trans('localize.auction_category') }}</td>
                                        <td>{{$auction->mc_name_en}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- ./box product -->
                </div>
                <!-- Product -->
            </div>
            <!-- ./ Center colunm -->
        </div>
        <!-- ./row-->
    </div>
</div>

<!-- Autobid popup starts-->
<div class="modal fade" tabindex="-1" role="dialog" id="autobidNum">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>{{ trans('localize.autobid') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <form>
                            <div class="form-group">
                                <input type="number" min="1" id="autobidVal" class="form-control" placeholder="Enter Number of Times Bids" >
                                <span class="bar"></span>
                                <div id="error_div"  style="color:#F00;font-weight:300;font-size: 90%;"></div>
                            </div>
                            <div class="action right">
                                <a class='bidAutoBulk' data-id='' data-gp=''>
                                    <input type="button" id="bidAuto" class="main-btn" value="{{ trans('localize.submit') }}" />
                                </a>
                                <button id="bidClose" class="secondary-btn" data-dismiss="modal" aria-hidden="true">{{ trans('localize.close_') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script type="text/javascript" src="/web/lib/jquery.countdown.min.js" ></script>
    <script type="text/javascript" src="/web/lib/date.format.js" ></script>
    <script type="text/javascript" src="/web/lib/moment.min.js"></script>
    <script type="text/javascript" src="/web/lib/moment-timezone-with-data.min.js"></script>
    <script type="text/javascript" src="/web/lib/timer.jquery.js"></script>
    <script type="text/javascript" src="/web/lib/sweetalert/sweetalert.min.js"></script>

    <!--<script src="http://localhost:3030/socket.io/socket.io.js"></script>-->
    <script src="http://socket.{{str_replace("www.", "", $_SERVER['HTTP_HOST'])}}/socket.io/socket.io.js"></script>
    <script type="text/javascript" src="/web/lib/auctionhelper.js"></script>
@endsection
