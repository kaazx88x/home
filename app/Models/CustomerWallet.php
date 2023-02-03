<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class CustomerWallet extends Model
{
    protected $guarded = ['created_at', 'updated_at'];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function wallet()
    {
        return $this->belongsTo('App\Models\Wallet');
    }
}