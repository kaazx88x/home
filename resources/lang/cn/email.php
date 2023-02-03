<?php

return [

    'greeting' => '您好！',
    'regards' => '最好的祝福',
    'subcopy' => '如果您在点击:actionText按钮时遇到问题，请将以下网址复制并粘贴到您的网络浏览器中: [:actionUrl](:actionUrl)',

    'member' => [

        'reset_password' => [
            'title' => '重设密码',
            'msg' => '您收到这封电子邮件是因为我们收到了您帐户的密码重设要求。',
            'msg2' => '如果您未请求重设密码，则无需进一步操作。',
            'btn' => '重设密码',
        ],

        'account_activation' => [
            'title' => '验证电子邮件',
            'msg' => '为了让你以后能够接收 :mall_name 的电子邮件，请点击以下按钮验证您的电子邮件',
            'msg2' => '此帐户是经由我们的管理员注册，为了安全原因，请更改您的密码。',
            'btn' => '点击这里验证',
        ],

        'auction_won' => '你赢了:mall_name拍卖!',

    ],

    'merchant' => [
        'account_activation' => [
            'title' => '还差一步',
            'msg' => '感谢您的加入. 在开始体验我们的购物平台之前，请点击下面的按钮以激活您的账户',
            'msg2' => '此账户注册于管理员，为了您的账户安全请更改现有密码.',
            'btn' => '按此激活',
        ],

    ],

    'admin' => [

        'welcome' => '欢迎来到:mall_name管理网关',

        'reset_password' => ':mall_name重设密码',
        'reset_secure_code' => ':mall_name重设付款安全密码',

        'reset_password_merchant' => ':mall_name商家重设密码',

    ],

];