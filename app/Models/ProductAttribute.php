<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class ProductAttribute extends Authenticatable
{
    protected $table = 'nm_product_attributes';
    protected $guarded = ['created_at', 'updated_at'];

    public function getTitleAttribute($value)
    {
        $default_title = $this->{'attribute'};
        $title = $this->{'attribute_'.\App::getLocale()};
        if (empty($title)) {
            return $default_title;
        } else {
              return $title;
        }
    }

    public function getItemAttribute($value)
    {
        $default_title = $this->{'attribute_item'};
        $item = $this->{'attribute_item_'.\App::getLocale()};
        if (empty($item)) {
            return $default_title;
        } else {
              return $item;
        }
    }

}