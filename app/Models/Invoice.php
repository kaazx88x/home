<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = ['created_at', 'updated_at'];

    public function items()
    {
        return $this->hasMany('App\Models\InvoiceItem');
    }
}
