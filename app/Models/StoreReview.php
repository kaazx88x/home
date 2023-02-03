<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class StoreReview extends Authenticatable
{

    protected $table = 'store_review';
    protected $primaryKey = 'id';
}
