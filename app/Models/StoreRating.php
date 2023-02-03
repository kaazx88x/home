<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class StoreRating extends Authenticatable
{

    protected $table = 'store_rating';
    protected $primaryKey = 'id';
}
