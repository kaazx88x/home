<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Store extends Authenticatable
{

    protected $table = 'nm_store';
    protected $primaryKey = 'stor_id';
    protected $fillable = ['stor_name', 'stor_phone', 'stor_address1', 'stor_address2', 'stor_zipcode' , 'stor_country' , 'stor_city' , 'stor_state' , 'stor_city_name' , 'stor_metadesc' , 'stor_metakeywords' , 'stor_website' , 'stor_type' , 'stor_merchant_id' , 'stor_img' , 'stor_status', 'stor_addedby', 'stor_latitude', 'stor_longitude', 'short_description', 'long_description','office_hour','featured', 'accept_payment', 'listed', 'stor_office_number', 'map_type', 'default_price'];

    public function limit()
    {
        return $this->hasOne(Limit::class, 'id', 'limit_id');
    }

    public function country()
    {
        return $this->hasOne(Country::class, 'co_id', 'stor_country');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'stor_merchant_id', 'mer_id');
    }
}
