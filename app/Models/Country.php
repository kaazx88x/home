<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Country extends Authenticatable
{

    protected $table = 'nm_country';
    protected $fillable = ['co_code', 'co_name', 'co_cursymbol', 'co_curcode', 'co_status', 'co_rate', 'co_offline_rate', 'co_offline_status'];
    protected $primaryKey = 'co_id';
}
