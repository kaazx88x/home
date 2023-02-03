//declare mysql connection
var mysql = require('mysql');
var nodemailer = require('nodemailer');
var dbvar = require('./dbconfig.js');

var connection = mysql.createConnection({
    host: dbvar.dbhost,
    user: dbvar.dbuser,
    password: dbvar.dbpsw,
    database: dbvar.dbname,
    multipleStatements: true
});


var smtpConfig = {
    host: dbvar.smtphost,
    port: dbvar.smtpport,
    secure: false, // use SSL
    auth: {
        user: dbvar.smtpusername,
        pass: dbvar.smptpsw
    }
};

var transporter = nodemailer.createTransport(smtpConfig);

//return result from stored procedure
//pid - auction id,
//lastbiddate - last bidding datetime,
//lastbidderid - last bidder cus_id,
//lastbidder - last bidder name,
//auctiontotalbid - auction total gp,
//aucstatus - #1=continue; 2=end with winnder; 3=cust not enough gp
//biddingtype - #1=user auto bid; 2=user manual bid; 3=bot bid
//auctiontype - normal_product,rookie_product,demo_product
//
//declare socket.io connection
var app = require('http').createServer(function (req, res) {
var headers = {};
  headers["Access-Control-Allow-Origin"] = "*";
  headers["Access-Control-Allow-Methods"] = "POST, GET, PUT, DELETE, OPTIONS";
  headers["Access-Control-Allow-Headers"] = "X-Requested-With, Access-Control-Allow-Origin, X-HTTP-Method-Override, Content-Type, Authorization, Accept";
  res.writeHead(200, headers);
  res.end();
});
var io = require('socket.io')(app);
var fs = require('fs');

app.listen(dbvar.socketport);

io.on('connection', function (socket) {

    socket.on('checkrunning', function () {
        var date = new Date();
        var current_hour = date.getHours();
        if(current_hour>=2 && current_hour< 16){
            io.sockets.emit('canbid', true);
        }else
            io.sockets.emit('canbid', false);
    });

     socket.on('join', function (pathname) {
        socket.join(pathname);
    });

    socket.on('bidding', function (data) {
        //check user has enough gp, if not, return not enough gp error, else continue bidding
        var userid = data["cus_id"];
        var aucid = data["auc_id"];
        var checkusergp = 0;
        IsSufficientToBid(userid, aucid, function (content) {
            checkusergp = content;
            if (checkusergp == 1)
            {
                connection.query("CALL user_bidding(" + userid + "," + aucid + ",2)", function (err, rows, fields) {
                    //broadcast to all in same auction view
                    //broadcast to auction type page
                    for (var i = 0; i < rows[0].length; i++) {
                        if (rows[0][i].aucstatus == 3)
                            SendLackGPEmail(rows[0][i]);
                        else
                        {
                            io.sockets.in('auctionview-' + rows[0][i].pid).emit('update_bid', rows[0][i]);
                            io.sockets.in('gridview-' + rows[0][i].auctiontype).emit('update_bid', rows[0][i]);
                        }
                    }

                });

                //after manual bid, next auto bid should kick in
                connection.query("CALL userauto_bidding(" + aucid + ",1, @none)", function (err, rows, fields) {
                    //broadcast to all in same auction view
                    //broadcast to auction type page
                    for (var i = 0; i < rows[0].length; i++) {
                        if (rows[0][i].aucstatus == 3)
                            SendLackGPEmail(rows[0][i]);
                        else
                        {
                            io.sockets.in('auctionview-' + rows[0][i].pid).emit('update_bid', rows[0][i]);
                            io.sockets.in('gridview-' + rows[0][i].auctiontype).emit('update_bid', rows[0][i]);
                        }
                    }
                });
            }
            else
                io.sockets.emit('gp_lack');
        });
    });

    socket.on('batch_update', function (data) {
        //categorize results into auction type
        //push to auction type page
        //push to client base on auction id
        var normal_product=[];
        var rookie_product=[];
        var demo_product=[];

        for (var i = 0; i < data.length; i++) {
            if (data[i].aucstatus == 3)
                SendLackGPEmail(data[i]);
            else
            {
                io.sockets.in('auctionview-' + data[i].pid).emit('update_bid', data[i]);
                if(data[i].auctiontype=='normal_product')
                {
                    normal_product.push({ "pid": data[i].pid,
                                        "lastbiddate": data[i].lastbiddate,
                                        "lastbidderid":data[i].lastbidderid,
                                        "lastbidder":data[i].lastbidder,
                                        "auctiontotalbid":data[i].auctiontotalbid,
                                        "aucstatus":data[i].aucstatus,
                                        "biddingtype":data[i].biddingtype,
                                        "auctiontype":data[i].auctiontype });
                }
                else if(data[i].auctiontype=='rookie_product')
                {
                    rookie_product.push({ "pid": data[i].pid,
                                        "lastbiddate": data[i].lastbiddate,
                                        "lastbidderid":data[i].lastbidderid,
                                        "lastbidder":data[i].lastbidder,
                                        "auctiontotalbid":data[i].auctiontotalbid,
                                        "aucstatus":data[i].aucstatus,
                                        "biddingtype":data[i].biddingtype,
                                        "auctiontype":data[i].auctiontype });
                }
                else
                {
                    demo_product.push({ "pid": data[i].pid,
                                        "lastbiddate": data[i].lastbiddate,
                                        "lastbidderid":data[i].lastbidderid,
                                        "lastbidder":data[i].lastbidder,
                                        "auctiontotalbid":data[i].auctiontotalbid,
                                        "aucstatus":data[i].aucstatus,
                                        "biddingtype":data[i].biddingtype,
                                        "auctiontype":data[i].auctiontype });
                }
            }

        }
        io.sockets.in('gridview-normal_product').emit('batch_update_bid', normal_product);
        io.sockets.in('gridview-rookie_product').emit('batch_update_bid', rookie_product);
        io.sockets.in('gridview-demo_product').emit('batch_update_bid', demo_product);
    });


});

function IsSufficientToBid(userid, aucid, callback)
{

    var querystr = "SELECT CASE WHEN a.game_point >= b.auc_game_point or b.auc_product_type='demo_product'  THEN 1 ELSE 0 END  AS valid FROM nm_customer a, nm_auction b WHERE a.cus_id = " + userid + " and b.auc_id = " + aucid + ";";

    connection.query(querystr, function (err, rows, fields) {
        if (err)
            throw err;
        callback(rows[0].valid);
    });

    //connection.end();
}

function SendLackGPEmail(data)
{
    var querystr = 'SELECT a.email, a.cus_name, b.auc_title FROM nm_customer a, nm_auction b WHERE a.cus_id = ' + data.lastbidderid + ' and b.auc_id = ' + data.pid + ';';

    connection.query(querystr, function (err, rows, fields) {
        if (err)
            throw err;
        var notenoughgp = {
            from: dbvar.emailfrom,
            to: rows[0][0].email,
            subject: 'Please Top Up Your Game Point',
            text: 'Dear ' + rows[0][0].cus_name + ', You do not have enough game point to place bid for product ' + rows[0][0].auc_title + '! Please top it up and continue bidding.',
            html: 'Dear ' + rows[0][0].cus_name + ',<br><p>You do not have enough game point to place bid  for peoduct ' + rows[0][0].auc_title + '! </p><p>Please top it up and continue bidding.</p><br><p>Best Regards,<br>MeiHome</p>'
        };
        transporter.sendMail(notenoughgp);
    });


}
