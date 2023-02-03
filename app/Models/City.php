<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class City extends Authenticatable
{

    protected $table = 'nm_city';
    protected $fillable = ['ci_name', 'ci_con_id', 'ci_lati', 'ci_long', 'ci_status'];
    protected $primaryKey = 'ci_id';
}
