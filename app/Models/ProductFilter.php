<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class ProductFilter extends Authenticatable
{
    protected $table = 'nm_product_filters';
    protected $guarded = ['id'];
    protected $primaryKey = 'id';
}
