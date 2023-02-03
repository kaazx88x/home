<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class CustomerInfo extends Model
{
    protected $table = 'nm_customer_informations';
    protected $guarded = ['created_at', 'updated_at'];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }
}