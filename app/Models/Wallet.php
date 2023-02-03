<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $guarded = ['created_at', 'updated_at'];

    public function getNameAttribute($value)
    {
        $default = $this->{'name_en'};
        $title = $this->{'name_'.App::getLocale()};

        if (empty($title)) {
            return $default;
        } else {
            return $title;
        }
    }

    public function customer_wallets()
    {
        return $this->hasMany('App\Models\CustomerWallet');
    }

    public function credit_log_wallets()
    {
        return $this->hasMany('App\Models\VcoinLog');
    }

    public function offline_order_wallets()
    {
        return $this->hasMany('App\Models\OrderOffline');
    }
}