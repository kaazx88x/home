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

    'accepted'             => ' :attribute 必须被接受.',
    'active_url'           => ' :attribute 不正确',
    'after'                => ' :attribute 只能设 :date 之后',
    'alpha'                => ' :attribute只能包含字母。',
    'alpha_dash'           => ' :attribute 只能包含字母，数字, 与连字符号',
    'alpha_num'            => ' :attribute只能包含字母和数字。',
    'array'                => 'The :attribute must be an array.',
    'before'               => ' :attribute 只能设 :date之前.',
    'between'              => [
        'numeric' => ':attribute必须介于:min与:max之间。',
        'file'    => ':attribute必须介于:min与:maxKB之间。',
        'string'  => ':attribute必须介于:min与:max个文字之间。',
        'array'   => ':attribute必须介于:min与:max个物品之间。',
    ],
    'boolean'              => ' :attribute 只能对或错',
    'confirmed'            => ' :attribute和确认的不一样。',
    'date'                 => ' :attribute 是不正确的日期',
    'date_format'          => ' :attribute 不符合 :format.',
    'different'            => ' :attribute 和 :other 不能一样.',
    'digits'               => ' :attribute必须是:digits个数字。',
    'digits_between'       => ' :attribute必须是由:min至:max个数字。',
    'distinct'             => ' :attribute 已经存在了.',
    'email'                => ':attribute必须是正确的电邮号。',
    'exists'               => '所选择的:attribute是错误的。',
    'filled'               => ' :attribute 必须填的.',
    'image'                => ' :attribute 只能是照片别类.',
    'in'                   => '所选择的:attribute是错误的。',
    'in_array'             => ' :attribute 不存在于 :other.',
    'integer'              => ':attribute必须是整数。',
    'ip'                   => ' :attribute 必须是正确的 IP 址.',
    'json'                 => 'The :attribute must be a valid JSON string.',
    'max'                  => [
        'numeric' => ':attribute不能大于:max。',
        'file'    => ':attribute不能大于:maxKB。',
        'string'  => ':attribute不能大于:max个文字。',
        'array'   => ':attribute不能多于:max个物品。',
    ],
    'mimes'                => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => ':attribute必须至少:min.',
        'file'    => ':attribute必须至少:min kilobytes.',
        'string'  => ':attribute必须至少:min个文字。',
        'array'   => ':attribute必须至少:min个物品。',
    ],
    'not_in'               => '所选择的:attribute是无效的。',
    'numeric'              => ':attribute必须是数字。',
    'present'              => ':attribute必须存在。',
    'regex'                => ':attribute格式错误。',
    'required'             => ':attribute是必要的。',
    'required_if'          => ' :attribute 是必要的当 :other 是 :value.',
    'required_unless'      => ' :attribute 是必要的除了 :other 是 :values.',
    'required_with'        => ' :attribute 是必要的当 :values 存在.',
    'required_with_all'    => ' :attribute 是必要的当 :values 存在.',
    'required_without'     => ' :attribute 是必要的当 :values 不存在.',
    'required_without_all' => ' :attribute 是必要的当没有任何一个 :values 存在.',
    'same'                 => ' :attribute 和 :other 必须符合.',
    'size'                 => [
        'numeric' => ' :attribute 必须是 :size.',
        'file'    => ' :attribute 必须是 :size kilobytes.',
        'string'  => ' :attribute 必须是 :size 字母.',
        'array'   => ' :attribute 必须拥有 :size 货品.',
    ],
    'string'               => ':attribute必须是文字。',
    'timezone'             => ':attribute必须是有效区。',
    'unique'               => ':attribute已经存在了。',
    'url'                  => ':attribute格式错误。',
    'valid_hash'           => '您输入的:attribute不符合',
    'password'             => ':attribute必须至少包含1个大写，1个小写和1个数字',
    'username'             => ':attribute只能包含字母和数字',

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
            'regex' => '昵称必须是字母或号码',
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
        'password' => '密码',
        'login' => '登入',
        'username' => '昵称',
    ],

    'captcha' => '输入的验证码不吻合。请再试一次',

];
