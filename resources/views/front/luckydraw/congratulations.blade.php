<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Congratulations</title>
        <link rel="stylesheet" type="text/css" href="/web/lib/bootstrap/css/bootstrap.min.css" />

        <style media="screen">
            .centered {
                margin-left: auto;
                margin-right: auto;
                width: 70%;
                min-height: 400px;
                padding: 30px;
                position: absolute;
                transform: translate(23%, 30%);
                -webkit-box-shadow: 15px 10px 56px 9px rgba(204,198,204,0.53);
                -moz-box-shadow: 15px 10px 56px 9px rgba(204,198,204,0.53);
                box-shadow: 15px 10px 56px 9px rgba(204,198,204,0.53);
                }
            </style>
    </head>
    <body>
        <div class="centered">
            <br>
            <center><img alt="MeiHome" src="/assets/images/meihome_logo.png"></center>
            <br>
            <center><b><h3>幸运抽奖</h3></b></center>
            <center><b><h3>Launching Lucky Draw</h3></b></center>
            <br>
            <center><b><h3>Congratulation! You have won - 恭喜您，赢取了</h3></b></center>
            @if ($rewardtype == 'v')
                <center><b><h4>Mei Point - {{$rewarddesc}}</h4></b></center>
            @elseif ($rewardtype == 'g')
                <center><b><h4>Gamepoint - {{$rewarddesc}}</h4></b></center>
            @else
                <center><b><h4>{{$rewarddesc}}</h4></b></center>
            @endif
        </div>
    </body>
</html>
