<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class FilterItem extends Authenticatable
{
    protected $table = 'filter_items';
    protected $guarded = ['id'];
    protected $primaryKey = 'id';
}
