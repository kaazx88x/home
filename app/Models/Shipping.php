<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Shipping extends Authenticatable
{

    protected $table = 'nm_shipping';
    protected $fillable = ['ship_name', 'ship_address1', 'ship_address2', 'ship_ci_id', 'ship_country', 'ship_postalcode', 'ship_phone', 'ship_cus_id','ship_state_id', 'ship_city_name','isdefault', 'areacode'];
    protected $primaryKey = 'ship_id';

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }
}
