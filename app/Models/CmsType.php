<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsType extends Model
{
    protected $table = 'cms_page_type';
    protected $primaryKey = 'id';
    protected $guarded = ['created_at', 'updated_at'];

    public function cms()
    {
        return $this->hasMany('App\Models\Cms', 'cp_cms_type', 'id');
    }
}
