<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Banner extends Authenticatable
{

    protected $table = 'nm_banner';
    protected $fillable = ['bn_title', 'bn_img', 'bn_status', 'bn_redirecturl', 'bn_open', 'type', 'order', 'bn_type'];
    protected $primaryKey = 'bn_id';

    public function countries()
    {
        return $this->belongsToMany('App\Models\Country', 'nm_banner_countries', 'banner_id', 'country_id');
    }
}
