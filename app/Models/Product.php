<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;

class Product extends Model
{

    protected $table = 'nm_product';
    protected $guarded = ['pro_id'];
    protected $primaryKey = 'pro_id';

    public function getTitleAttribute($value)
    {
        $default_title = $this->{'pro_title_en'};
        $title = $this->{'pro_title_'.App::getLocale()};

        if (empty($title)) {
            return $default_title;
        } else {
            return $title;
        }
    }

    public function getDescAttribute($value)
    {
        $default_desc = $this->{'pro_desc_en'};
        $desc = $this->{'pro_desc_'.App::getLocale()};

        if (empty($desc)) {
            return $default_desc;
        } else {
            return $desc;
        }
    }

    public function getShortDescAttribute($value)
    {
        $defaultShortDesc = $this->{'short_desc_en'};
        $shortDesc = $this->{'short_desc_'.App::getLocale()};

        if (empty($shortDesc))
            return $defaultShortDesc;
        return $shortDesc;
    }

    public function store()
    {
        return $this->hasOne(Store::class, 'stor_id', 'pro_sh_id');
    }
}
