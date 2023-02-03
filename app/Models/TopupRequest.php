<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TopupRequest extends Authenticatable
{

    protected $table = 'topup_request';
     protected $fillable = ['key', 'secret', 'email', 'email', 'vtoken', 'gpoint', 'from', 'wallet', 'secureHash','ip','type','ref','status','special_wallet'];
}

