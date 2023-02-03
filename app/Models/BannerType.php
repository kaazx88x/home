<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class BannerType extends Authenticatable
{
    protected $table = 'nm_banner_type';
    protected $fillable = ['name','description'];
    protected $primaryKey = 'id';
}
