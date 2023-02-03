<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $guarded = ['created_at', 'updated_at'];

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice');
    }
}
