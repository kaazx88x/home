<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LimitAction extends Model
{
    protected $table = 'limit_actions';
    protected $fillable = ['limit_id', 'type', 'action', 'amount', 'number_transaction', 'order', 'per_user'];
    protected $primaryKey = 'id';
}
