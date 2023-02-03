<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class StoreCategory extends Authenticatable
{

    protected $table = 'nm_store_offline_category';
    protected $guarded = ['created_at', 'updated_at'];
}