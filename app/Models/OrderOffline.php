<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderOffline extends Model
{
    protected $table = 'order_offline';
    protected $guarded = ['created_at', 'updated_at'];

    public function wallet()
    {
        return $this->belongsTo('App\Models\Wallet');
    }

    public function store()
    {
        return $this->hasOne(Store::class, 'stor_id', 'store_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'cus_id', 'cust_id');
    }
}
