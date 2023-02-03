<?php

return [

    'greeting' => '您好！',
    'regards' => '最好的祝福',
    'subcopy' => '如果您在點擊:actionText按鈕時遇到問題，請將以下網址複製並粘貼到您的網絡瀏覽器中: [:actionUrl](:actionUrl)',

    'member' => [

        'reset_password' => [
            'title' => '重設密碼',
            'msg' => '您收到這封電子郵件是因為我們收到了您帳戶的密碼重設要求。',
            'msg2' => '如果您未請求重設密碼，則無需進一步操作。',
            'btn' => '重設密碼',
        ],

        'account_activation' => [
            'title' => '驗證電子郵件',
            'msg' => '為了讓你以後能夠接收 :mall_name 的電子郵件，請點擊以下按鈕驗證您的電子郵件',
            'msg2' => '此帳戶是經由我們的管理員註冊，為了安全原因，請更改您的密碼。',
            'btn' => '點擊這裡驗證',
        ],

        'auction_won' => '你赢了:mall_name拍卖!',

    ],

    'merchant' => [
        'account_activation' => [
            'title' => '還差一步',
            'msg' => '感謝您的加入. 在開始體驗我們的購物平台之前，請點擊下面的按鈕以激活您的賬戶',
            'msg2' => '此賬戶註冊於管理員，為了您的賬戶安全請更改現有密碼.',
            'btn' => '按此激活',
        ],

    ],

    'admin' => [

        'welcome' => '歡迎來到:mall_name管理網關',

        'reset_password' => ':mall_name重設密碼',
        'reset_secure_code' => ':mall_name重設付款安全密碼',

        'reset_password_merchant' => ':mall_name商家重設密碼',

    ],

];