<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Courier extends Authenticatable
{

    protected $table = 'nm_courier';
    protected $fillable = ['name', 'link', 'status'];
}
