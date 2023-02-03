<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class MerchantGuarantor extends Authenticatable
{

    protected $guarded = ['created_at', 'updated_at'];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'mer_id', 'merchant_id');
    }
}