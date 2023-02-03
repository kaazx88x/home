<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Filter extends Authenticatable
{
    protected $table = 'filters';
    protected $guarded = ['id'];
    protected $primaryKey = 'id';
}
