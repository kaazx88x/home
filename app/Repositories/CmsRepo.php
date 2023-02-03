<?php

namespace App\Repositories;
use DB;
use App\Models\Cms;
use App\Models\CmsType;


class CmsRepo
{
    public static function all()
    {
        return Cms::all();
    }

    public static function add($data, $cms_type = null)
    {
        if ($cms_type->type == 'web') {
            $sequence = Cms::where('cp_footer', 1)->where('cp_status', 1)->count() + 1;
            $cms = Cms::create([
                'cp_title_en' => $data['title_en'],
                'cp_title_cn' => $data['title_cn'],
                'cp_title_cnt' => $data['title_cnt'],
                'cp_description_en' => $data['desc_en'],
                'cp_description_cn' => $data['desc_cn'],
                'cp_description_cnt' => $data['desc_cnt'],
                'cp_status' => $data['status'],
                'cp_url' => $data['url'],
                'cp_footer' => $data['footer'],
                'cp_sequence' => ($data['footer'] == 1)? $sequence : null,
                'cp_cms_type' => $cms_type->id
            ]);
        } else {
            $cms = Cms::create([
                'cp_title_en' => $data['title_en'],
                'cp_title_cn' => $data['title_cn'],
                'cp_title_cnt' => $data['title_cnt'],
                'cp_description_en' => $data['desc_en'],
                'cp_description_cn' => $data['desc_cn'],
                'cp_description_cnt' => $data['desc_cnt'],
                'cp_status' => $data['status'],
                'cp_url' => '',
                'cp_footer' => 0,
                'cp_sequence' => null,
                'cp_cms_type' => $cms_type->id
            ]);
        }


        return $cms;
    }

    public static function find($id)
    {
        return Cms::with('type')->where('cp_id', $id)->first();
    }

    public static function update($id,$data)
    {
        $cms = Cms::find($id);
        $cms->cp_title_en = $data['title_en'];
        $cms->cp_title_cn = $data['title_cn'];
        $cms->cp_title_cnt = $data['title_cnt'];
        $cms->cp_description_en = $data['desc_en'];
        $cms->cp_description_cn = $data['desc_cn'];
        $cms->cp_description_cnt = $data['desc_cnt'];
        $cms->cp_status = $data['status'];

        if (isset($data['url'])) {
            $cms->cp_url = $data['url'];
        }

        if (isset($data['footer'])) {
            $cms->cp_footer = $data['footer'];
        }

        $cms->save();

        return $cms;
    }

    public static function delete($id)
    {
        $cms = Cms::find($id);
        $cms->delete();
    }

    public static function get_footer()
    {
        return Cms::where('cp_status', 1)->where('cp_footer', 1)->orderBy('cp_sequence','asc')->get();
    }

    public static function update_footer_sorting($input)
    {
        $result = explode(',',$input['newOrder']);
        foreach ($result as $key => $i)
        {
            $cms = Cms::find($i);
            $cms->cp_sequence = ($key+1);
            $cms->save();
        }

        return 'success';
    }

    public static function cms_type($id = null)
    {
        $cms_type = CmsType::with('cms');


        if ($id) {
            return $cms_type->where('id', $id)->first();
        }

        return $cms_type->get();
    }

    public static function cms_by_type($type)
    {
        return CMS::where('cp_cms_type', $type)->paginate();
    }
}