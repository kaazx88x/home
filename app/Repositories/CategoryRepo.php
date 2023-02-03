<?php

namespace App\Repositories;

use Cache;
use App\Models\Category;
use App\Repositories\CacheRepo;

class CategoryRepo
{
    public static function all_category_by_parent($parent_id)
    {
        return Category::where('parent_id', $parent_id)->orderBy('created_at', 'DESC')->with('wallet')->get();
    }

    public static function count_child_category($parent_id)
    {
        return Category::where('parent_id', $parent_id)->count();
    }

    public static function get_category_by_id($id)
    {
        return Category::where('id', $id)->first();
    }

    public static function get_category_by_slug($slug)
    {
        return Category::where('url_slug', $slug)->first();
    }

    public static function add_category($data)
    {
        $category = Category::create([
            'parent_id' => $data['parent_id'],
            'name_en' => $data['name_en'],
            'name_cn' => $data['name_cn'],
            'name_cnt' => $data['name_cnt'],
            'name_my' => $data['name_my'],
            'url_slug' => $data['url_slug'],
            'status' => $data['status'],
            'featured' => $data['featured'],
            'parent_list' => (isset($data['parent_list'])) ? $data['parent_list'] : null,
            'image' => (isset($data['image'])) ? $data['image'] : null,
            'banner' => (isset($data['banner'])) ? $data['banner'] : null,
            'wallet_id' => $data['wallet_id'],
        ]);

        CacheRepo::category_caching();
        return $category;
    }

    public static function edit_category($id,$data)
    {
        $category = Category::find($id);
        $category->name_en = $data['name_en'];
        $category->name_cn = $data['name_cn'];
        $category->name_cnt = $data['name_cnt'];
        $category->name_my = $data['name_my'];
        $category->url_slug = $data['url_slug'];
        $category->status = $data['status'];
        $category->featured = $data['featured'];
        $category->wallet_id = $data['wallet_id'];

        if (isset($data['image']))
            $category->image = $data['image'];

        if (isset($data['banner']))
            $category->banner = $data['banner'];

        $category->save();

        CacheRepo::category_caching();
        return $category;
    }

    public static function update_all_child($wallet_id, $childs_id) {
        $childs = explode(',', $childs_id);

        foreach ($childs as $key => $cat_id) {
            Category::where('id', $cat_id)->update(['wallet_id' => $wallet_id]);
        }
    }

    public static function delete_category($ids)
    {
        $delete = Category::whereIn('id', explode(',', $ids))->delete();

        CacheRepo::category_caching();
        return true;
    }

    public static function json_get_category_listing_by_id($parent_id, $ticket = false)
    {
        $cats = Category::where('parent_id', $parent_id)
        ->where('status', 1);

        if($ticket) {
            switch ($ticket) {
                case 'required':
                    $cats = $cats->where('default_ticket', 1);
                    break;

                case 'disable':
                    $cats = $cats->whereNull('default_ticket');
                    break;
            }
        }
        $cats = $cats->orderBy('name_en')->get();

        $jsonCats = [];
        foreach ($cats as $key => $cat) {
            $count = Category::where('parent_id', $cat->id)->where('status', 1)->count();

            $jsonCats[$key] = [
                'text' => ucwords(strtolower($cat->name)),
                // 'parent' => $cat->parent_id,
                'id' => $cat->id,
                'state' => [
                    'opened' => false
                ],
                'children' => ($count > 0) ? true : false
            ];
        }

        return json_encode($jsonCats);
    }

    //for navigation megamenus
    public static function mega_menus()
    {
        $megamenus = Category::where('parent_id','=', 0)->where('status','=',1)->get();
        return $megamenus;
    }

    public static function navigation($request)
    {
        $lists = array();
        $parents = Category::where('parent_id','=', 0)->where('featured','=',1)->where('status','=', 1)->orderBy('sequence','asc')->get();

        switch ($request) {
            case 'featured':

                foreach ($parents as $key => $list) {
                    $lists[$key]['parent'] = $list;
                    $lists[$key]['layer_one'] = Category::where('parent_id',$list->id)->where('status','=', 1)->limit(4)->get();

                    foreach ($lists[$key]['layer_one'] as $index => $layer_one) {
                    $lists[$key]['layer_one'][$index]['layer_two'] = Category::where('parent_id','=', $layer_one->id)->where('status','=', 1)->limit(4)->get();
                    }
                }

                break;

            case 'footer':

                foreach ($parents as $key => $list) {
                    $lists[$key]['parent'] = $list;
                    $array = explode(',', $list->child_list);
                    $lists[$key]['child'] = Category::whereIn('id', $array)->where('status','=', 1)->whereNull('child_list')->get();
                }
                break;
        }

        return $lists;
    }

    public static function set_default_ticket($id)
    {
        $category = Category::find($id);
        if($category->parent_id > 0)
            return false;

        //Unlink current category default for ticket
        Category::where('parent_id', 0)
        ->where('default_ticket', 1)
        ->update([
            'default_ticket' => null
        ]);

        $category->default_ticket = 1;
        $category->save();

        return true;
    }

    // public static function category_caching()
    // {
    //     $parents = Category::where('parent_id','=', 0)->where('featured','=',1)->where('status','=', 1)->orderBy('sequence','asc')->get();

    //     // Footer
    //     $footer = [];
    //     foreach ($parents as $key => $footer_item) {
    //         $footer_childs = explode(',', $footer_item->child_list);

    //         $footer[$footer_item->id]['parent'] = $footer_item->name;
    //         $footer[$footer_item->id]['child'] = Category::whereIn('id', $footer_childs)->where('status', 1)->whereNull('child_list')->get();
    //     }

    //     Cache::forever('cat_footer', $footer);
    //     return true;

    //     // $cats = Category::where('status','=',1)->orderBy('name_en', 'ASC')->get();
    //     // $category = array(
    //     //     'items' => array(),
    //     //     'parents' => array()
    //     // );

    //     // foreach ($cats as $cat) {
    //     //     $category['items'][$cat->id] = $cat;
    //     //     $category['parents'][$cat->parent_id][] = $cat->id;
    //     // }

    //     // return self::build_category_menu(0, $category);
    // }

    // private static function build_category_menu($parent, $menu) {
    //     $category = [];
    //     if (isset($menu['parents'][$parent])) {
    //         foreach ($menu['parents'][$parent] as $itemId) {
    //                 $category[$menu['items'][$itemId]->id]['name'] = $menu['items'][$itemId]->name;
    //                 $category[$menu['items'][$itemId]->id]['childs'] = (isset($menu['parents'][$itemId])) ? self::build_category_menu($itemId, $menu) : [];
    //         }
    //     }

    //     return $category;
    // }
}
