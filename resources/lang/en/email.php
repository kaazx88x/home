<?php

return [

    'greeting' => 'Hello!',
    'regards' => 'Regards',
    'subcopy' => 'If you’re having trouble clicking the :actionText button, copy and paste the URL below into your web browser: [:actionUrl](:actionUrl)',

    'member' => [

        'reset_password' => [
            'title' => 'Reset Password',
            'msg' => 'You are receiving this email because we received a password reset request for your account.',
            'msg2' => 'If you did not request a password reset, no further action is required.',
            'btn' => 'Reset Password',
        ],

        'account_activation' => [
            'title' => 'Email Verification',
            'msg' => 'Please click the button below to verify your email in order to receive email from :mall_name in the future.',
            'msg2' => 'This account is registered by our administrator, please change your password for security reason.',
            'btn' => 'Click Here to Verify',
        ],

        'auction_won' => 'You Have Won A :mall_name Auction!',
    ],

    'merchant' => [
        'account_activation' => [
            'title' => 'One More Step',
            'msg' => 'Thank you for signing up an account with :mall_name. Before you start experiencing our shopping platform, please click the button below to activate your :mall_name account.',
            'msg2' => 'This account is registered by our administrator, please change your password for security reason.',
            'btn' => 'Click Here to Activate',
        ],
    ],

    'admin' => [
        'welcome' => 'Welcome to Admin Portal of :mall_name',

        'reset_password' => ':mall_name Reset Password',
        'reset_secure_code' => ':mall_name Reset Payment Secure Code',

        'reset_password_merchant' => ':mall_name Merchant Reset Password',
    ],

];