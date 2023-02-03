<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'nm_order';
    protected $primaryKey = 'order_id';

    public function generatedCode()
    {
        return $this->hasMany(GeneratedCode::class, 'order_id');
    }

    public function coupons()
    {
        return $this->generatedCode()->where('type', 2);
    }

    public function tickets()
    {
        return $this->generatedCode()->where('type', 3);
    }

    public function ecards()
    {
        return $this->generatedCode()->where('type', 4);
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'pro_id', 'order_pro_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'cus_id', 'order_cus_id');
    }
}
