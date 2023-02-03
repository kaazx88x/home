<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderOfflineLog extends Model
{
    protected $table = 'order_offline_log';
    protected $guarded = ['created_at', 'updated_at'];
}
