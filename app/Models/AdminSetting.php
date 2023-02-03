<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminSetting extends Authenticatable
{
    protected $table = 'admin_setting';
    protected $fillable = ['platform_charge', 'service_charge'];
    protected $primaryKey = 'id';
}
