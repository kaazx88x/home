<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantVTokenLog extends Model
{
    protected $table = 'merchant_vtoken_log';
    protected $guarded = [];
    protected $fillable = ['mer_id','credit_amount','debit_amount','order_id','offline_order_id','withdraw_id','remark'];
}
