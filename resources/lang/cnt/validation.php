<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ' :attribute 必須被接受.',
    'active_url'           => ' :attribute 不正確',
    'after'                => ' :attribute 只能設 :date 之後',
    'alpha'                => ' :attribute 只能包含字母。',
    'alpha_dash'           => ' :attribute 只能包含字母，數字, 與連字符號',
    'alpha_num'            => ' :attribute 只能包含字母和數字。',
    'array'                => 'The :attribute must be an array.',
    'before'               => ' :attribute 只能設 :date之前.',
    'between'              => [
        'numeric' => ':attribute必須介於:min與:max之間。',
        'file'    => ':attribute必須介於:min與:maxKB之間。',
        'string'  => ':attribute必須介於:min與:max個文字之間。',
        'array'   => ':attribute必須介於:min與:max個物品之間。',
    ],
    'boolean'              => ' :attribute 只能對或錯',
    'confirmed'            => ' :attribute和確認的不一樣。',
    'date'                 => ' :attribute 是不正確的日期',
    'date_format'          => ' :attribute 不符合 :format.',
    'different'            => ' :attribute 和 :other 不能一樣.',
    'digits'               => ' :attribute必須是:digits個數字。',
    'digits_between'       => ' :attribute必須是由:min至:max個數字。',
    'distinct'             => ' :attribute 已經存在了.',
    'email'                => ':attribute必須是正確的電郵號。',
    'exists'               => '所選擇的:attribute是錯誤的。',
    'filled'               => ' :attribute 必須填的.',
    'image'                => ' :attribute 只能是照片別類.',
    'in'                   => '所選擇的:attribute是錯誤的。',
    'in_array'             => ' :attribute 不存在於 :other.',
    'integer'              => ':attribute必須是整數。',
    'ip'                   => ' :attribute 必須是正確的 IP 址.',
    'json'                 => 'The :attribute must be a valid JSON string.',
    'max'                  => [
        'numeric' => ':attribute不能大於:max。',
        'file'    => ':attribute不能大於:maxKB。',
        'string'  => ':attribute不能大於:max個文字。',
        'array'   => ':attribute不能多於:max個物品。',
    ],
    'mimes'                => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => ':attribute必須至少:min.',
        'file'    => ':attribute必須至少:min kilobytes.',
        'string'  => ':attribute必須至少:min個文字。',
        'array'   => ':attribute必須至少:min個物品。',
    ],
    'not_in'               => '所選擇的:attribute是無效的。',
    'numeric'              => ':attribute必須是數字。',
    'present'              => ':attribute必須存在。',
    'regex'                => ':attribute格式錯誤。',
    'required'             => ':attribute是必要的。',
    'required_if'          => ' :attribute 是必要的當 :other 是 :value.',
    'required_unless'      => ' :attribute 是必要的除了 :other 是 :values.',
    'required_with'        => ' :attribute 是必要的當 :values 存在.',
    'required_with_all'    => ' :attribute 是必要的當 :values 存在.',
    'required_without'     => ' :attribute 是必要的當 :values 不存在.',
    'required_without_all' => ' :attribute 是必要的當沒有任何一個 :values 存在.',
    'same'                 => ' :attribute 和 :other 必須符合.',
    'size'                 => [
        'numeric' => ' :attribute 必須是 :size.',
        'file'    => ' :attribute 必須是 :size kilobytes.',
        'string'  => ' :attribute 必須是 :size 字母.',
        'array'   => ' :attribute 必須擁有 :size 貨品.',
    ],
    'string'               => ':attribute必須是文字。',
    'timezone'             => ':attribute必須是有效區。',
    'unique'               => ':attribute已經存在了。',
    'url'                  => ':attribute格式錯誤。',
    'valid_hash'           => '您输入的:attribute不符合',
    'password'             => ':attribute必須至少包含1個大寫，1個小寫和1個數字',
    'username'             => 'attribute只能包含字母和數字',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'username' => [
            'regex' => '暱稱必須是字母或號碼',
        ],
        'securecode' => [
            'digits' => '支付密码必须是6个数字',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'payment_secure_code' => '支付密码',
        'securecode' => '支付密码',
        'oldcode' => '旧的支付密码',
        'password' => '密碼',
        'login' => '登入',
        'username' => '暱稱'
    ],

    'captcha' => '输入的验证码不吻合。请再试一次',

];
