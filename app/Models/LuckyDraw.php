<?php

namespace App\Models;

use App;
use Illuminate\Foundation\Auth\User as Authenticatable;

class LuckyDraw extends Authenticatable
{
    protected $primaryKey = 'id';

    public function getDescAttribute($value)
    {
        $default_desc = $this->{'description_en'};
        $desc = $this->{'description_'.App::getLocale()};

        if (empty($desc)) {
            return $default_desc;
        } else {
            return $desc;
        }
    }
}
