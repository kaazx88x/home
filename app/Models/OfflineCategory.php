<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class OfflineCategory extends Model
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

    public function wallet()
    {
        return $this->belongsTo('App\Models\Wallet');
    }
}