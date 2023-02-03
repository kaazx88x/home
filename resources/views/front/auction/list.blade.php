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

    if($auction_type == 'normal_product') {
        $auctiontypetitle = trans('localize.live');
    } elseif($auction_type == 'demo_product') {
        $auctiontypetitle = trans('localize.trial');
    } elseif($auction_type == 'rookie_product') {
        $auctiontypetitle = trans('localize.rookies');
    }
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
        <div class="general row">
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
                            $auction_img = explode('/**/', $fetch_auction->auc_image);
                            $auctionStarted = false;
                            if (strtotime(date('Y-m-d H:i:s')) >= strtotime($fetch_auction->auc_start_date)) {
                                $auctionStarted = true;
                            }
                        ?>
                        <input type='hidden' name="auctionid" id='{{$fetch_auction->auc_id}}' value='{{$fetch_auction->auc_id}}' />
                        <input type='hidden' name="servertime"   value='{{strtotime(date('Y-m-d H:i:s')) >= strtotime($fetch_auction->auc_start_date)?'1':'0'}}' />
                        <input type='hidden' name="auctionstarted" id='auctionstarted_{{$fetch_auction->auc_id}}' value='{{strtotime(date('Y-m-d H:i:s')) >= strtotime($fetch_auction->auc_start_date)?'1':'0'}}' />
                        <input type='hidden' name="lastbiddate" id='lastbiddate_{{$fetch_auction->auc_id}}' value='{{$fetch_auction->lastbiddate}}' />
                        <input type='hidden' name="auctionstartdate" id='auctionstartdate_{{$fetch_auction->auc_id}}' value='{{$fetch_auction->auc_start_date}}' />
                        <li class="col-sx-12 col-sm-3">
                            <div class="product-container bid">
                                <div class="left-block">
                                    <a href="{{ url('auctions/detail/').'/'.$fetch_auction->auc_id}}">
                                        <div class="discounted-percentage product-list-discount-percentage" id="bidsTotal_{{$fetch_auction->auc_id}}">{{$fetch_auction->auc_total_gp}}</div>
                                        <img class="img-responsive" alt="product" src="/web/images/loading.gif" data-src="{{'/images/auction/'.$auction_img[0]}}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';"/>
                                    </a>
                                </div>
                                <div class="right-block">
                                    <h5 class="product-name"><a href="{{ url('auctions/detail/').'/'.$fetch_auction->auc_id}}">{{ ucwords(strtolower($fetch_auction->auc_title)) }}</a></h5>
                                    <div class="content_price">
                                        <p class="price product-price">{{ trans('localize.retail') }}</p>
                                        <p>USD {{ number_format((round($fetch_auction->auc_original_price)), 0, '.', ',') }}</p>
                                    </div>
                                    <div class="vcoin-wrapper vcoin-discounted-wrapper">
                                        <span class="product-coin discounted-vcoin"> x {{ $fetch_auction->auc_game_point }}</span>
                                        <span class="bidder" id="lastbidder_{{$fetch_auction->auc_id}}"> {{$fetch_auction->lastbidder}}
                                        </span>
                                    </div>
                                    <div class="timer" id="timerCounter_{{$fetch_auction->auc_id}}">--</div>
                                    <div class="divaction">
                                        <div class="auction_status startsoon " id="auctionstatus_{{$fetch_auction->auc_id}}" style="display:{{$auctionStarted?'none':'block'}}"> {{ trans('localize.starting_soon') }} </div>
                                        <div class="bids_buttons" id="biddingbutton_{{$fetch_auction->auc_id}}" style="display:{{$auctionStarted?'block':'none'}}">
                                            @if(Auth::check())
                                            <a class='bidNowBulk' data-id='{{$fetch_auction->auc_id}}' data-gp='{{$fetch_auction->auc_game_point}}'>
                                                <button class="button">{{ trans('localize.bidnow') }}</button>
                                            </a>
                                            <a class='autobidModal' data-id='{{$fetch_auction->auc_id}}' data-gp='{{$fetch_auction->auc_game_point}}'>
                                                <button class="button">{{ trans('localize.autobid') }}</button>
                                            </a>
                                            @else
                                            <button class="button request-login">{{ trans('localize.bidnow') }}</button>
                                            <button class="button request-login">{{ trans('localize.autobid') }}</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
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
                                <label>{{ trans('localize.autobidInput') }}</label>
                                <input type="number" min="1" id="autobidVal" class="form-control" placeholder="" >
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

@section('style')
<link rel="stylesheet" href="/web/lib/sweetalert/sweetalert.css">
@endsection

@section('script')
<script type="text/javascript" src="/web/lib/jquery.countdown.min.js" ></script>
<script type="text/javascript" src="/web/lib/date.format.js" ></script>
<script type="text/javascript" src="/web/lib/moment.min.js"></script>
<script type="text/javascript" src="/web/lib/moment-timezone-with-data.min.js"></script>
<script type="text/javascript" src="/web/lib/timer.jquery.js"></script>
<script type="text/javascript" src="/web/lib/sweetalert/sweetalert.min.js"></script>

@if ($breadcumb_title == 'Free Trial')
<!-- Hour Countdown -->
<script type="text/javascript">
    $(document).ready(function () {
        var now = "<?php echo date('Y-m-d H:i:s'); ?>";
        $.ajax({
            async: false,
            url: "getDate",
            dataType: 'json',
            type: 'get',
            success: function (res) {
                now = res.dateData;
            }
        });
        var now_tz = moment.tz(now, "UTC");
        var nextHour = getNextHour(now_tz.toDate());
        $('#clock').countdown(nextHour)
            .on('update.countdown', function (event) {
                var format = '%H:%M:%S';
                if (event.offset.days > 0) {
                    format = '%-d day%!d ' + format;
                }
                if (event.offset.weeks > 0) {
                    format = '%-w week%!w ' + format;
                }
                $(this).html(event.strftime(format));
            })
            .on('finish.countdown', function (event) {
                location.reload();
            });
    });

    function getNextHour(date) {
        date.setHours(date.getHours() + 1);
        date.setMinutes(0);
        date.setSeconds(0);
        date.setMilliseconds(0);
        return date;
    }
</script>
@endif

<!-- Count Down Coding -->
<script type="text/javascript">

    year = <?php echo $deal_end_year; ?>;
    month = <?php echo $deal_end_month; ?>;
    day = <?php echo $deal_end_date; ?>;
    hour = <?php echo $deal_end_hours; ?>;
    min = <?php echo $deal_end_minutes; ?>;
    sec = <?php echo $deal_end_seconds; ?>;
    $(function () {
        countProcess();
    });
    var timezone = new Date()
    month = --month;
    dateFuture = new Date(year, month, day, hour, min, sec);
    function countProcess() {

        dateNow = new Date();
        amount = dateFuture.getTime() - dateNow.getTime() + 5;
        delete dateNow;
        /* time is already past */
        if (amount < 0) {
            output = "<span class='countDays'><span class='position'><span class='digit static' style='top: 0px; opacity: 1;'>0</span></span><span class='position'><span class='digit static' style='top: 0px; opacity: 1;'>0</span></span><span class='countDiv countDiv0'></span><span class='countHours'><span class='position'><span class='digit static' style='top: 0px; opacity: 1;'>0</span></span><span class='position'><span class='digit static' style='top: 0px; opacity: 1;'>0</span></span></span><span class='countDiv countDiv1'></span><span class='countMinutes'><span class='position'><span class='digit static' style='top: 0px; opacity: 1;'>0</span></span><span class='position'><span class='digit static' style='top: 0px; opacity: 1;'>0</span></span></span><span class='countDiv countDiv2'></span><span class='countSeconds'><span class='position'><span class='digit static' style='top: 0px; opacity: 1;'>0</span></span><span class='position'><span class='digit static' style='top: 0px; opacity: 1;'>0</span></span></span>";
            //window.location='<?php echo url("auction"); ?>';
            $('#countdown').html(output);
        }
        /* date is still good */
        else {
            days = 0;
            hours = 0;
            mins = 0;
            secs = 0;
            output = "";
            amount = Math.floor(amount / 1000); /* kill the milliseconds */

            days = Math.floor(amount / 86400); /* days */
            amount = amount % 86400;
            hours = Math.floor(amount / 3600); /* hours */
            amount = amount % 3600;
            mins = Math.floor(amount / 60); /* minutes */
            amount = amount % 60;
            secs = Math.floor(amount); /* seconds */

            fdays = parseInt(days / 10);
            sdays = days % 10;
            fhours = parseInt(hours / 10);
            shours = hours % 10;
            fmins = parseInt(mins / 10);
            smins = mins % 10;
            fsecs = parseInt(secs / 10);
            ssecs = secs % 10;
            output = "<span class='countDays'><span class='position'><span class='digit static' style='top: 0px; opacity: 1;'>" + fdays + "</span></span><span class='position'><span class='digit static' style='top: 0px; opacity: 1;'>" + sdays + "</span></span><span class='countDiv countDiv0'></span><span class='countHours'><span class='position'><span class='digit static' style='top: 0px; opacity: 1;'>" + fhours + "</span></span><span class='position'><span class='digit static' style='top: 0px; opacity: 1;'>" + shours + "</span></span></span><span class='countDiv countDiv1'></span><span class='countMinutes'><span class='position'><span class='digit static' style='top: 0px; opacity: 1;'>" + fmins + "</span></span><span class='position'><span class='digit static' style='top: 0px; opacity: 1;'>" + smins + "</span></span></span><span class='countDiv countDiv2'></span><span class='countSeconds'><span class='position'><span class='digit static' style='top: 0px; opacity: 1;'>" + fsecs + "</span></span><span class='position'><span class='digit static' style='top: 0px; opacity: 1;'>" + ssecs + "</span></span></span>";
            $('#countdown').html(output);
            setTimeout("countProcess()", 1000);
        }

    }
</script>
<!--<script src="http://localhost:3030/socket.io/socket.io.js"></script>-->
<script src="http://socket.{{str_replace("www.", "", $_SERVER['HTTP_HOST'])}}/socket.io/socket.io.js"></script>
<script type="text/javascript" src="/web/lib/auctionhelper.js"></script>
@endsection
