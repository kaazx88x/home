<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class ProductImage extends Authenticatable
{

    protected $table = 'nm_product_image';
    protected $fillable = ['pro_id', 'title','image','status','order','main'];
    protected $primaryKey = 'id';
}