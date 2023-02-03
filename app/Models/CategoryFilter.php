<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class CategoryFilter extends Authenticatable
{
    protected $table = 'category_filter';
    protected $guarded = ['id'];
    protected $primaryKey = 'id';
}
