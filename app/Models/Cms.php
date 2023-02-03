<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App;

class Cms extends Authenticatable
{

    protected $table = 'nm_cms_pages';
    protected $fillable = ['cp_title_en', 'cp_title_cn', 'cp_title_cnt', 'cp_description_en', 'cp_description_cn', 'cp_description_cnt', 'cp_url', 'cp_sequence', 'cp_footer', 'cp_cms_type'];
    protected $primaryKey = 'cp_id';

    public function type()
    {
        return $this->belongsTo('App\Models\CmsType', 'cp_cms_type', 'id');
    }

    public function getTitleAttribute($value)
    {
        $default_title = $this->{'cp_title_en'};
        $title = $this->{'cp_title_'.App::getLocale()};

        if (empty($title)) {
            return $default_title;
        } else {
            return $title;
        }
    }

    public function getDescriptionAttribute($value)
    {
        $default_desc = $this->{'cp_description_en'};
        $desc = $this->{'cp_description_'.App::getLocale()};

        if (empty($desc)) {
            return $default_desc;
        } else {
            return $desc;
        }
    }
}
