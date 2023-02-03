<?php

namespace App\Repositories;

use Cache;
use App\Models\Category;

class CacheRepo
{
    public static function parent_category()
    {
        $parents = Category::where('parent_id','=', 0)->where('status','=', 1)->get();

        return $parents;
    }

    public static function footer_category()
    {
        $parents = Category::where('parent_id','=', 0)->where('featured','=',1)->where('status','=', 1)->orderBy('sequence','asc')->get();
        $footer = [];
        foreach ($parents as $key => $footer_item) {
            $footer_childs = explode(',', $footer_item->child_list);

            $footer[$key]['parent'] = $footer_item;
            $footer[$key]['child'] = Category::whereIn('id', $footer_childs)->where('status', 1)->whereNull('child_list')->get();
        }

        return $footer;
    }

    public static function nav_category()
    {
        // $parents = Category::where('parent_id','=', 0)->where('featured','=',1)->where('status','=', 1)->orderBy('sequence','asc')->get();
        $parents = Category::where('featured','=',1)->where('status','=', 1)->orderBy('sequence','asc')->get();
        $nav = [];
        foreach ($parents as $key => $list) {
            $nav[$key]['parent'] = $list;
            $nav[$key]['layer_one'] = Category::where('parent_id',$list->id)->where('status','=', 1)->limit(4)->get();

            foreach ($nav[$key]['layer_one'] as $index => $layer_one) {
            $nav[$key]['layer_one'][$index]['layer_two'] = Category::where('parent_id','=', $layer_one->id)->where('status','=', 1)->limit(4)->get();
            }
        }

        return $nav;
    }

    public static function nav_parent_category()
    {
        $parents = Category::where('parent_id','=', 0)->where('status','=', 1)->get();
        $nav = [];
        foreach ($parents as $key => $list) {
            $nav[$key]['parent'] = $list;
            $nav[$key]['layer_one'] = Category::where('parent_id',$list->id)->where('status','=', 1)->limit(4)->get();

            foreach ($nav[$key]['layer_one'] as $index => $layer_one) {
                $nav[$key]['layer_one'][$index]['layer_two'] = Category::where('parent_id','=', $layer_one->id)->where('status','=', 1)->limit(4)->get();
            }
        }

        return $nav;
    }

    public static function category_caching()
    {
        Cache::forget('cat_parent'); # forget cache
        Cache::forever('cat_parent', self::parent_category());

        Cache::forget('cat_nav'); # forget cache
        Cache::forever('cat_nav', self::nav_category());

        Cache::forget('cat_footer'); # forget cache
        Cache::forever('cat_footer', self::footer_category());

        Cache::forget('cat_nav_parent'); # forget cache
        Cache::forever('cat_nav_parent', self::nav_parent_category());
    }
}