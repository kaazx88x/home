<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class State extends Authenticatable
{

    protected $table = 'nm_state';
    protected $fillable = ['name', 'status', 'country_id'];
    protected $primaryKey = 'id';

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'co_id');
    }
}
