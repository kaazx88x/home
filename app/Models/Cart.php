<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Cart extends Authenticatable
{

    protected $table = 'temp_cart';
    protected $fillable = ['token', 'user_id', 'product_id', 'color_id', 'size_id', 'quantity' , 'remarks', 'pricing_id','currency', 'currency_rate', 'purchasing_price', 'product_price','attributes','attributes_name'];
}
