<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class PricingAttributeMapping extends Authenticatable
{
    protected $table = 'nm_pricing_attribute_mappings';
    protected $guarded = ['id'];
    protected $primaryKey = 'id';

    public function product_attribute()
    {
        return $this->hasOne('App\Models\ProductAttribute', 'id', 'attribute_id');
    }
}