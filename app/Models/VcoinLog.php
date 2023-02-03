<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VcoinLog extends Model
{
    protected $table = 'v_token_log';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function wallet()
    {
        return $this->belongsTo('App\Models\Wallet');
    }
}
