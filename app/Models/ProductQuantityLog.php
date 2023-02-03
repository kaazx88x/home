<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class ProductQuantityLog extends Authenticatable
{
    protected $table = 'nm_product_quantity_log';
    protected $guarded = ['id'];
    protected $primaryKey = 'id';
}