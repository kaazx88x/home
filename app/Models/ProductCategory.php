<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class ProductCategory extends Authenticatable
{

    protected $table = 'nm_product_category';
    protected $guarded = ['created_at', 'updated_at'];

    public function category()
    {
        return $this->hasOne('App\Models\Category', 'id', 'category_id');
    }
}