<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class GeneratedCode extends Authenticatable
{
    protected $table = 'generated_codes';
    protected $guarded = ['id'];
    protected $primaryKey = 'id';
}
