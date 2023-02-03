<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class MerchantReferrer extends Authenticatable
{
    protected $table = 'merchant_referrals';
    protected $guarded = ['created_at', 'updated_at'];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'mer_id', 'merchant_id');
    }
}