var mysql = require('mysql');
var nodemailer = require('nodemailer');
var schedule = require('node-schedule');
var dbvar = require('./dbconfig.js');
var Curl = require('node-libcurl').Curl;

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

var ioconnectionfail = {
    from: dbvar.emailfrom,
    to: dbvar.emailto,
    subject: 'socket.io Fail',
    text: 'Something wrong!',
    html: 'Something wrong!'
};
var secondcronerror = {
    from: dbvar.emailfrom,
    to: dbvar.emailto,
    subject: 'Auction Update Error',
    text: 'Something wrong!',
    html: 'Something wrong!'
};

var dailycronerror = {
    from: dbvar.emailfrom,
    to: dbvar.emailto,
    subject: 'Daily Cron Error',
    text: 'Something wrong!',
    html: 'Something wrong!'
};

var dailycronsuccess = {
    from: dbvar.emailfrom,
    to: dbvar.emailto,
    subject: 'Daily Cron Success',
    text: 'Well done!',
    html: 'Well done!'
};

var transporter = nodemailer.createTransport(smtpConfig);
// verify connection configuration
//transporter.verify(function(error, success) {
//   if (error) {
//        console.log(error);
//   } else {
//        console.log('Server is ready to take our messages');
//   }
//});
var socket = require('socket.io-client')('http://' + dbvar.domainurl + ':' + dbvar.socketport);

socket.on('connect_error', function () {
    if (dbvar.sendemail)
        transporter.sendMail(ioconnectionfail);
});

socket.on('disconnect', function () {
    if (dbvar.sendemail)
        transporter.sendMail(ioconnectionfail);
});

schedule.scheduleJob({hour: 02, minute: 00}, function () {
    socket.emit('checkrunning');
});

schedule.scheduleJob({hour: 16, minute: 00}, function () {
    socket.emit('checkrunning');
});

schedule.scheduleJob('* * * * * *', function () {
    secondcron();
});

function secondcron() {
    var date = new Date();
    var current_hour = date.getHours();
    if (current_hour >= 2 && current_hour < 16) {

        connection.query("CALL auction_update_incall()", function (err, rows, fields) {
            if (err)
            {
                if (dbvar.sendemail)
                    transporter.sendMail(secondcronerror);
            }
            else
            {
                socket.emit('batch_update', rows[0]);
            }
        });
    }


}

schedule.scheduleJob('*/60 * * * *', function () {
    var date = new Date();
    var current_hour = date.getHours();
    if (current_hour >= 2 && current_hour < 16) {
        connection.query("CALL refresh_demo_auction()", function (err, rows, fields) {
            if (err) {
                if (dbvar.sendemail)
                    transporter.sendMail(ioconnectionfail);
            }
        });
    }
});

//schedule.scheduleJob({hour: 17, minute: 00}, function () {
//    var curl = new Curl();
//
//    curl.setOpt('URL', dbvar.siteurl+ '/cron/svi_daily_cron');
//    curl.setOpt('FOLLOWLOCATION', true);
//
//    curl.on('end', function (statusCode, body, headers) {
//        if (dbvar.sendemail)
//                    transporter.sendMail(dailycronsuccess);
//        this.close();
//    });
//
//    curl.on('error', function (statusCode, body, headers) {
//        if (dbvar.sendemail)
//                    transporter.sendMail(dailycronerror);
//    });
//     
//    curl.perform();
//});