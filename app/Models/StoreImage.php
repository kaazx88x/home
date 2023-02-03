<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class StoreImage extends Authenticatable
{

    protected $table = 'store_images';
    protected $primaryKey = 'id';
    protected $fillable = ['store_id', 'image_name'];
}
