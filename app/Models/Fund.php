<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Fund extends Authenticatable
{

    protected $table = 'nm_deals';
    protected $primaryKey = 'id';
}
