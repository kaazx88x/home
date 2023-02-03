<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class BannerCountry extends Authenticatable
{

    protected $table = 'nm_banner_countries';
    protected $fillable = ['banner_id', 'country_id'];
    protected $primaryKey = 'id';

    public function country()
    {
        return $this->hasOne('App\Models\Country', 'co_id', 'country_id');
    }
}