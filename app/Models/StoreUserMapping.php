<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class StoreUserMapping extends Authenticatable
{
    protected $table = 'nm_store_user_mappings';
    protected $guarded = ['created_at', 'updated_at'];
    protected $primaryKey = 'id';
}