$(function () {
    if (typeof io == "undefined")
    {
        //disable timer
        //disable buttons and show error message
        $("input[name='auctionid']").each(function () {
            var aucid = $(this).attr('id');
            $("#biddingbutton_" + aucid).hide();
            $('#timerCounter_' + aucid).timer('pause');
            if ($('#auctionstarted_' + aucid).val() === '1')
            {
                $("#auctionstatus_" + aucid).show();
                $("#auctionstatus_" + aucid).html(window.translations.connError);
            }
        });
    }
    else
    {
        //var socket = io('http://' + window.location.hostname + ':3030', {reconnect: true});
        var socket = io('http://socket.'+window.location.hostname.replace("www.", ""), {reconnect: true});
        GetServerDate();
        var cus_id = $('#auccusid').val();
        var bid_type = $('#auction_type').val();
        var serverDate = $('#serverdate').val();
        socket.on('connect', function ()
        {
            socket.emit('checkrunning');
            if ($("#page_type").val() === 'grid') {
                var auction_type = $("#auction_type").val();
                socket.emit('join', 'gridview-' + auction_type);
            } else if ($("#page_type").val() === 'view') {
                var auc_id = $("input[name='auctionid']").val();
                socket.emit('join', 'auctionview-' + auc_id);
            }
        });

        socket.on('canbid', function (data) {
            if (data)
            {
                $("input[name='auctionid']").each(function () {
                    var aucid = $(this).attr('id');

                    if ($('#auctionstarted_' + aucid).val() === '1')
                    {
                        $("#auctionstatus_" + aucid).hide();
                        $("#auctionstatus_" + aucid).html("");
                        $("#biddingbutton_" + aucid).show();
                        console.log(serverDate);
                        startTimerCountdown(aucid, serverDate);
                    }
                });
            }
            else
            {
                $("input[name='auctionid']").each(function () {
                    var aucid = $(this).attr('id');
                    $("#biddingbutton_" + aucid).hide();
                    $('#timerCounter_' + aucid).timer('pause');
                    $('#timerCounter_' + aucid).html("--");
                });
            }
        });

        socket.on('disconnect', function () {
            console.log('WTF');
            $("input[name='auctionid']").each(function () {
                var aucid = $(this).attr('id');
                $("#biddingbutton_" + aucid).hide();
                $('#timerCounter_' + aucid).timer('pause');
                if ($('#auctionstarted_' + aucid).val() === '1')
                {
                    $("#auctionstatus_" + aucid).show();
                    $("#auctionstatus_" + aucid).html(window.translations.connError);
                }
            });

        });

        socket.on('update_bid', function (data) {
            PerformUpdate(cus_id, data);
            //timercountdown(15, data.pid);

        });

        socket.on('gp_lack', function (data) {
            swal({
                title: "Insufficient Game Point!",
                text: "Sorry, you didn't have enough Game Point to bid. Please top up your Game Point first.",
                type: "error",
                confirmButtonText: "Close"
            });
        });

        socket.on('batch_update_bid', function (data) {
            //loop data and update each grid, auction status 1 - continue or 2 - winner
            for (var i = 0; i < data.length; i++) {
                PerformUpdate(cus_id, data[i]);
                //timercountdown(15, data[i].pid);
            }
        });

        $('.bidNowBulk').click(function () {
            var auc_id = $(this).attr('data-id');
            var auc_gamepoint = $(this).attr('data-gp');
            var gamepoint;
            var datasend = {"cus_id": cus_id, "auc_id": auc_id};
            checkGP(cus_id, auc_gamepoint, bid_type, function (output) {
                if (output == 'success') {
                    swal({
                        title: "Are you sure?",
                        text: "You are about to place a bid. Please click OK to continue.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: "OK",
                        cancelButtonText: "Cancel",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            socket.emit('bidding', datasend);
                        }
                    });
                }
            });
        });

         $('.request-login').click(function () {
                swal({
                    title: "Please Login",
                    text: "You need to login before placing a bid",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Login Now!",
                    closeOnConfirm: false
                },
                function(){
                    window.location.replace('/login');
                });
        });

        $('.autobidModal').click(function () {
            var auc_id = $(this).attr('data-id');
            var auc_gamepoint = $(this).attr('data-gp');
            $('.bidAutoBulk').attr('data-id', auc_id);
            $('.bidAutoBulk').attr('data-gp', auc_gamepoint);
            $('#autobidNum').modal('show');
        });

        $('.bidAutoBulk').click(function () {
            var auc_id = $(this).attr('data-id');
            var auc_gamepoint = $(this).attr('data-gp');
            var autobid_time = $('#autobidVal').val();
            var autobid = parseInt(autobid_time) * parseInt(auc_gamepoint)
            var datasend = {"cus_id": cus_id, "auc_id": auc_id, "autobid": autobid_time};

            if ($('#autobidVal').val() == '') {
                $('#autobidVal').css("border", "1px solid red");
                $('#autobidVal').focus();
                $('#error_div').html('Please enter auto bid value.');
                return false;
            } else if ($('#autobidVal').val() <= 0) {
                $('#autobidVal').css("border", "1px solid red");
                $('#autobidVal').focus();
                $('#error_div').html('Auto bid value must be bigger than 0.');
                return false;
            } else {
                $('#autobidVal').css("border", "");
                $('#error_div').html('');
                checkGP(cus_id, autobid, bid_type, function (output) {
                    if (output == 'success') {
                        swal({
                            title: "Are you sure?",
                            text: "You are about to place a bid. Please click OK to continue.",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonText: "OK",
                            cancelButtonText: "Cancel",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        }, function (isConfirm) {
                            if (isConfirm) {
                                $.ajax({
                                    url: '/auctions/update_autobid',
                                    type: 'POST',
                                    data: datasend,
                                    success: function (res) {
                                        if (res.status == 'success') {
                                            $('#bidClose').click();
                                            $('#autoBidsLeft').html(res.autobid);
                                            swal({
                                                title: "Auto Bid Success!",
                                                text: "The autobid has been successfully placed for you.",
                                                type: "success",
                                                confirmButtonText: "Close"
                                            });
                                        } else {
                                            swal({
                                                title: "Error!",
                                                text: "Sorry, there's problem update the autobid. Please try again",
                                                type: "error",
                                                confirmButtonText: "Close"
                                            });
                                        }
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });
    }
});
$(document).ready(function () {
    GetServerDate();
    var serverDate = $('#serverdate').val();
    $("input[name='auctionid']").each(function () {
        var aucid = $(this).val();
        var auctionstartdate = $('#auctionstartdate_' + aucid).val();
        if (auctionstartdate != "")
        {
            auctionstartdate = new Date(toJSDate(auctionstartdate));
            auctionstartdate = convertUTCDateToLocalDate(auctionstartdate);
            auctionstartdate = dateFormat(auctionstartdate, "dd mmmm yyyy, hh:MM:ss TT");
            $('#auctionstartdate').html(auctionstartdate);
        }

        var auctionstarted = $('#auctionstarted_' + aucid).val();
        if (auctionstarted === '0')
        {
            auctionstartdate = $('#auctionstartdate_' + aucid).val();
            var nextYear = moment.tz(auctionstartdate, "UTC");
            $('#timerCounter_' + aucid).addClass('tostart');

            $('#timerCounter_' + aucid).countdown(nextYear.toDate(), function (event) {
                if (event.elapsed) {
                    $('#timerCounter_' + aucid).removeClass('tostart');
                    $('#auctionstarted_' + aucid).val("1");
                    $('#timerCounter_' + aucid).html("--");
                    $('#labelStart_' + aucid).html("Time Left");
                    if (typeof io == "undefined")
                    {
                        $("#biddingbutton_" + aucid).hide();
                        $("#auctionstatus_" + aucid).show();
                        $("#auctionstatus_" + aucid).html('<b>Connection Error.</b>');
                    }
                    else
                    {
                        $("#biddingbutton_" + aucid).show();
                        $("#auctionstatus_" + aucid).hide();
                        $("#auctionstatus_" + aucid).html('');
                        $('#labelStart').html("Time Left");
                        startTimerCountdown(aucid, serverDate);
                    }
                }
                else
                {
                    $(this).html(
                            event.strftime('%-D days %H:%M:%S')
                            );
                }
            });
        }
    });
});

var lastBidTime = "";
var serverDate;
var totalTime = 15;
var timer;
var timeleft = totalTime;

function GetServerDate()
{
    var svserverDate;
    $.ajax({
        async: false,
        url: '/getDate',
        dataType: 'json',
        type: 'get',
        success: function (res) {
            svserverDate = res.dateData;
            $('#serverdate').val(svserverDate);
        }
    });
}

function startTimerCountdown(aucid, serverDate)
{
    //get servertime
    //get lastbidtime
    var lastBidDate = $('#lastbiddate_' + aucid).val();
    var StartTimer = 15;
    var timerleft = StartTimer;
    console.log(lastBidDate);
    if (lastBidDate != "")
    {
        subtractedValue = (toJSDate(serverDate) - toJSDate(lastBidDate));
        var diffTime = (StartTimer - (subtractedValue / 1000));
        timerleft = ((diffTime < StartTimer) && (diffTime > 0)) ? diffTime : StartTimer;
        timercountdown(timerleft, aucid);
    }

}

function timercountdown(timerleft, aucid)
{
    $('#timerCounter_' + aucid).timer('remove');
    $('#timerCounter_' + aucid).timer({
        seconds: 0, //Specify start time in seconds
        countdown: true,
        format: '%S',
        repeat: true,
        restart: true,
        duration: timerleft,
        callback: function () {

            timercountdown(15, aucid);
        },
    });
}

//function countdown(aucid) {
//
//    $('#timerCounter_' + aucid).html(timeleft);
//    if (timeleft === 0) {
//        timeleft = totalTime;
//        countdownreset();
//    } else {
//        timeleft--;
//        timer = setTimeout(countdown, 1000);
//    }
//
//    var text = timeleft;
//    if (timeleft < 5)
//        $('#timerCounter_' + aucid).addClass('timingred');
//    else
//        $('#timerCounter_' + aucid).removeClass('timingred');
//}
//
//function countdownpause() {
//    // pauses countdown
//    clearTimeout(timer);
//    $('.auction_countdown').html("--");
//}
//
//function countdownreset() {
//    // resets countdown
//    countdownpause();
//    timeleft = totalTime;
//    countdown();
//}



function toJSDate(d) {
    d = d.toString();
    var full_date_time = d.split(" ");
    var full_date = full_date_time[0].split("-");
    var full_time = full_date_time[1].split(":");
    var year = full_date[0];
    var month = full_date[1] > 1 ? full_date[1] - 1 : "0";
    var date = full_date[2];
    var hour = full_time[0];
    var minute = full_time[1];
    var second = full_time[2];
    return new Date(year, month, date, hour, minute, second);
}

function UpdateBidInfo(aucid, lastbidder, lastbiddate, auctiontotalbid)
{
    var lastbiddate = new Date(Date.parse(lastbiddate));
    //lastbiddate = convertUTCDateToLocalDate(lastbiddate);
    lastbiddate = dateFormat(lastbiddate, "dd mmmm yyyy, hh:MM:ss TT");

    if ($("#page_type").val() === 'grid') {

        $('#lastbidder_' + aucid).html(lastbidder);
        $("#bidsTotal_" + aucid).html(auctiontotalbid);

    } else if ($("#page_type").val() === 'view') {

        var htmlstring = '<li><span class="bid-time">' + lastbiddate + '</span><span class="bidder">' + lastbidder + '</span></li>';
        var count = $("ul.bidder-history li").size();
        if($("ul.bidder-history li").size()==0)
        {
            $("ul.bidder-history").append(htmlstring);
        }
        else
        {
            $("ul.bidder-history").prepend(htmlstring);
        }
        if (count > 10)
            $("ul.bidder-history li").last().remove();
        $("#bidsTotal_" + aucid).html(auctiontotalbid);
    }
}

function convertUTCDateToLocalDate(date) {
    var newDate = new Date(date.getTime() + date.getTimezoneOffset() * 60 * 1000);
    var offset = date.getTimezoneOffset() / 60;
    var hours = date.getHours();
    newDate.setHours(hours - offset);
    return newDate;
}

function PerformUpdate(cusid, data)
{
    //UpdateBidInfo(data.pid, data.lastbidder, data.lastbiddate, data.auctiontotalbid);
    if (data.aucstatus == 1) {
        //check if biddingtype is auto bid by login user, deduct gp
        if (data.biddingtype == 1 && data.lastbidderid == cusid)
        {
            //deduct auto bid
            $.ajax({
                url: '{{ url("/auctions/check_autobid") }}',
                type: 'POST',
                data: {"cus_id": cusid, "auc_id": data.pid},
                success: function (res) {
                    $('#autoBidsLeft').html(res);
                }
            });
        }
        UpdateBidInfo(data.pid, data.lastbidder, data.lastbiddate, data.auctiontotalbid);
        timercountdown(15, data.pid);
    }
    else if (data.aucstatus == 2)
    {
        $('#timerCounter_' + data.pid).timer('pause');
        $('#timerCounter_' + data.pid).timer('remove');
        $("#auctionstatus_" + data.pid).removeClass('startsoon');
        //setup winner
        $("#biddingbutton_" + data.pid).hide();
        $("#bidWin").show();
        $("#winnerName").html(data.lastbidder);
        $("#auctionstatus_" + data.pid).show();
        $('#timerCounter_' + data.pid).html("--");
        if (data.auctiontype != 'demo_product')
        {
            if (data.lastbidderid !=0 && data.lastbidderid == cusid)
            {
                var url = "/auctions/checkout/" + data.pid;
                var htmlstring = '<a href="' + url + '"><button class="button">Checkout</button></a>';
                $("#auctionstatus_" + data.pid).html(htmlstring);
            }
            else
            {
                $("#auctionstatus_" + data.pid).html('Auction is Ended!');
                $("#auctionstatus_" + data.pid).css('color', 'black');
            }
            if(data.lastbidderid !=0 )
                notifyWinner(data.pid);
        }
    }
}

function checkGP(cus_id, auc_gp, bid_type, callback) {
    $.ajax({
        url: '/checkGP',
        type: 'POST',
        data: {"cus_id": cus_id, "bid_type": bid_type},
        success: function (res) {
            var gamepoint = res.gp;
            var winner = res.winner;
            var auction_type = $("#auction_type").val();
            if(res.hour<2 || res.hour>=16)
            {
                swal({
                    title: "Warning!",
                    text: "Auction is closed now",
                    type: "error",
                    confirmButtonText: "Close"
                });
            }
            else if (!winner) {
                if (gamepoint >= parseInt(auc_gp) || auction_type == 'demo_product') {
                    data = 'success';
                    callback(data);
                } else {
                    swal({
                        title: "Insufficient Game Point!",
                        text: "Sorry, you didn't have enough Game Point to bid. Please top up your Game Point first.",
                        type: "error",
                        confirmButtonText: "Close"
                    });
                }
            } else {
                swal({
                    title: "Error!",
                    text: "Sorry, you have won in Rookies Bid before.",
                    type: "error",
                    confirmButtonText: "Close"
                });
            }
        }
    });
}

function notifyWinner(auctionId) {
    $.ajax({
        url: '/auctionwinner',
        type: 'POST',
        data: {"auc_id": auctionId},
        success: function (res) {
        }
    });
}
