@extends('layouts.master')

@section('content')
<?php
$date1 = date('Y-m-d H:i:s');
$date2 = '';
$deal_end_year = date('Y', strtotime($date2));
$deal_end_month = date('m', strtotime($date2));
$deal_end_date = date('d', strtotime($date2));
$deal_end_hours = date('H', strtotime($date2));
$deal_end_minutes = date('i', strtotime($date2));
$deal_end_seconds = date('s', strtotime($date2));

if ($auction_type == 'normal_product')
{
    $auctiontypetitle = trans('localize.live');
}
elseif ($auction_type == 'demo_product')
{
    $auctiontypetitle = trans('localize.trial');
}
elseif ($auction_type == 'rookie_product')
{
    $auctiontypetitle = trans('localize.rookies');
}
elseif ($auction_type == 'closed_bidding')
{
    $auctiontypetitle = 'Closed Bidding';
}


$randomtext = array
    ( 32=>"vitasstar",
    33=>"DJO",
    36=>"YYY",
    40=>"ItsGame",
    53=>"remora",
    43=>"KiMei",
    44=>"brian01684",
    88=>"kenneth09",
    91=>"qkfree168",
    66=>"shining",
    63=>"LouisLu",
    74=>"abedan21",
    70=>"dudlen1",
    69=>"kevsam29",
    72=>"evejim48",
    55=>"donmax16",
    62=>"everob8",
    85=>"teddan38",
    76=>"patted90",
    77=>"amyeve13",
    93=>"vicdot55",
    101=>"robnat56",
    94=>"ronflo2",
    97=>"desuna54",
    131=>"flosid8",
    128=>"budhal80",
    137=>"budtom37"
);
?>
<div class="columns-container">
    <div class="container" id="columns">
        <!-- breadcrumb -->
        <div class="breadcrumb clearfix">
            <a class="home" href="/" title="Return to Home">{{trans('front.home')}}</a>
            <span class="navigation-pipe">&nbsp;</span>
            <span class="navigation_page">{{ ucwords(strtolower($auctiontypetitle)) }}</span>
        </div>
        <!-- breadcrumb -->
        <!-- row -->
        <div class="row">
            <!-- Center colunm-->
            <div class="col-xs-12 col-sm-12">
                <!-- page heading-->
                <h2 class="page-heading">
                    <span class="page-heading-title2">{{  $auctiontypetitle }} </span>
                </h2>
                @if($auction_type == 'demo_product')
                <div class="products-header">
                    <!-- Hourly Countdown -->
                    <div class="row">
                        <div class="countdown_section">
                            <span class="time-icon"><i class='fa fa-clock-o'></i></span>
                            <span id="clock" class="message_box timing auction_countdown" >--</span>
                            <span id="labelStart" class="left_time">{{ trans('localize.time_left') }}</span>
                        </div>
                    </div>
                </div>
                @endif
                <input type="hidden"  id="serverdate" value="">
                <input type="hidden"  id="auccusid" value="{{Auth::check()?Auth::user()->cus_id:''}}">
                <input type="hidden"  id="auction_type" value="{{$auction_type}}">
                <input type="hidden"  id="page_type" value="grid">
                <div id="view-product-list" class="view-product-list auction">
                    <ul class="row product-list grid">
                        @foreach($auctions as $fetch_auction)
                        <?php
                        $auc_id = $fetch_auction['details']->auc_id;
                        $auction_img = explode('/**/', $fetch_auction['details']->auc_image);
                        $auctionStarted = false;
                        $startdate = $fetch_auction['details']->auc_start_date;
                        if (strtotime(date('Y-m-d H:i:s')) >= strtotime($startdate))
                        {
                            $auctionStarted = true;
                        }
                        ?>
                        <input type='hidden' name="auctionid" id='{{$auc_id}}' value='{{$auc_id}}' />
<?php $servertime = (strtotime(date('Y-m-d H:i:s')) >= strtotime($startdate)) ? 1 : 0; ?>
                        <input type='hidden' name="servertime"   value='{{$servertime}}' />
                        <input type='hidden' name="auctionstarted" id='auctionstarted_{{$auc_id}}' value='{{strtotime(date('Y-m-d H:i:s')) >= strtotime($startdate)?'1':'0'}}' />

                        <input type='hidden' name="auctionstartdate" id='auctionstartdate_{{$auc_id}}' value='{{$startdate}}' />
                        <li class="col-sx-12 col-sm-3">
                            <div class="product-container">
                                <div class="left-block">
                                    <a href="{{ url('auctions/detail/').'/'.$auc_id}}">
                                        <div class="discounted-percentage product-list-discount-percentage" id="bidsTotal_{{$auc_id}}">{{$fetch_auction['details']->auc_total_gp}}</div>
                                        <img class="img-responsive" alt="product" src="/web/images/loading.gif" data-src="{{'images/auction/'.$auction_img[0]}}" onerror="if (this.src != 'error.jpg') this.src = '/web/data/p40.jpg';"/>
                                    </a>
                                </div>
                                <div class="right-block">
                                    <div class="text-center">
                                        <h5 class="title"><b>{{ (strlen($fetch_auction['details']->title)  >= 40 )? ucwords(strtolower(substr($fetch_auction['details']->title, 0, 59).' ...')) : ucwords(strtolower($fetch_auction['details']->title)) }}</b></h5>
                                        <br>
                                        <div class="text-center">
                                            <center><p class="price product-price">{{ trans('localize.retail') }}</p></center>
                                            <center><p class="price product-price">USD {{ number_format((round($fetch_auction['details']->auc_original_price)), 0, '.', ',') }}</p></center>
                                        </div>
                                        <br>
                                        <div class="vcoin-wrapper vcoin-discounted-wrapper text-center">
                                            <span class="product-coin discounted-vcoin"> x {{ $fetch_auction['details']->auc_game_point }}</span>
                                        </div>
                                        <br>
                                        <span>Total bids</span>
                                        <p class="price product-price">
                                            <span>{{ $fetch_auction['details']->auc_total_gp }}</span>
                                        </p>
                                        <br>
                                        <p>
                                            <i class="fa fa-trophy" style="color: #FFD700;"></i>
                                            <br>
                                            @if($fetch_auction['customer'])
                                                {{ strtoupper($fetch_auction['customer']->username)}}
                                            @elseif(array_key_exists($auc_id,$randomtext))
                                                {{$randomtext[$auc_id]}}
                                            @else
                                            @endif


                                        </p>
                                    </div>
<?php $cusid = (Auth::user()) ? Auth::user()->cus_id : ''; ?>
                                    <br>
                                    <div class="">
                                        @if($fetch_auction['details']->auc_winner_cus_id)
                                        @if($fetch_auction['details']->auc_winner_cus_id == $cusid)
                                        @if(!empty($fetch_auction['orderAuction']->oa_cus_id))
                                        @if($fetch_auction['orderAuction']->oa_bid_item_status == 0 && $fetch_auction['orderAuction']->oa_cus_id == $cusid)
                                        <a href="/auctions/checkout/{{$auc_id}}"><button type="button" class="button btn-block" name="button">Check out item</button></a>
                                        @endif
                                        @endif
                                        @endif
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <!-- pagination for auction-->

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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <h4>{{ trans('localize.autobid') }}</h4>
                        <div id="error_div"  style="color:#F00;font-weight:300"></div>
                        <form>
                            <div class="input-group">
                                <input type="number" min="1" id="autobidVal" class="form-control" placeholder="{{ trans('localize.autobidInput') }}" >
                                <span class="input-group-btn">
                                    <a class='bidAutoBulk' data-id='' data-gp=''>
                                        <input type="button" id="bidAuto" class="btn  btn-warning " value="{{ trans('localize.submit') }}" />
                                    </a>
                                    <button id="bidClose" class="btn" data-dismiss="modal" aria-hidden="true">{{ trans('localize.close_') }}</button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('style')
<link rel="stylesheet" href="/web/lib/sweetalert/sweetalert.css">
@endsection
