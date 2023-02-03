<?php

namespace App\Repositories;

use Cache;
use App\Models\OfflineCategory;

class OfflineCategoryRepo
{
    public static function all_by_parent_id($parent_id)
    {
        return OfflineCategory::where('parent_id', $parent_id)->orderBy('created_at', 'DESC')->with('wallet')->get();
    }

    public static function count_childs($parent_id)
    {
        return OfflineCategory::where('parent_id', $parent_id)->count();
    }

    public static function get_detail_by_id($id)
    {
        return OfflineCategory::where('id', $id)->first();
    }

    public static function add_category($data)
    {
        $category = OfflineCategory::create([
            'parent_id' => $data['parent_id'],
            'name_en' => $data['name_en'],
            'name_cn' => $data['name_cn'],
            'name_my' => $data['name_my'],
            'status' => $data['status'],
            'parent_list' => (isset($data['parent_list'])) ? $data['parent_list'] : null,
            'featured' => $data['featured'],
            'banner' => (isset($data['banner'])) ? $data['banner'] : null,
            'image' => (isset($data['image'])) ? $data['image'] : null,
            'wallet_id' => $data['wallet_id'],
        ]);

        return $category;
    }

    public static function edit_category($id,$data)
    {
        $category = OfflineCategory::find($id);
        $category->name_en = $data['name_en'];
        $category->name_cn = $data['name_cn'];
        $category->name_my = $data['name_my'];
        $category->status = $data['status'];
        $category->featured = $data['featured'];
        $category->wallet_id = $data['wallet_id'];

        if (isset($data['banner']))
            $category->banner = $data['banner'];

        if (isset($data['image']))
            $category->image = $data['image'];

        $category->save();

        return $category;
    }

    public static function update_all_child($wallet_id, $childs_id) {
        $childs = explode(',', $childs_id);

        foreach ($childs as $key => $cat_id) {
            OfflineCategory::where('id', $cat_id)->update(['wallet_id' => $wallet_id]);
        }
    }

    public static function delete_category($ids)
    {
        return $delete = OfflineCategory::whereIn('id', explode(',', $ids))->delete();
    }

    public static function json_get_category_listing_by_id($parent_id)
    {
        $cats = OfflineCategory::where('parent_id', $parent_id)->where('status', 1)->orderBy('name_en')->get();

        $jsonCats = [];
        foreach ($cats as $key => $cat) {
            $count = OfflineCategory::where('parent_id', $cat->id)->where('status', 1)->count();

            $jsonCats[$key] = [
                'text' => $cat->name,
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
}
