<?php

namespace App\Repositories;
use App\Models\Filter;
use App\Models\FilterItem;
use App\Models\CategoryFilter;
use App\Models\ProductCategory;
use App\Models\ProductFilter;
use App\Models\ProductAttribute;

class FilterRepo
{
    public static function filter_all()
    {
        return Filter::all();
    }

    public static function add_filter($data)
    {
        $attribute = Filter::create([
            'name' => ucfirst($data['name']),
            'description' => $data['description'],
            'status' => $data['status'],
        ]);

        return $attribute;
    }

    public static function edit_filter($id, $data)
    {
        $attribute = Filter::where('id',$id)->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'status' => $data['status'],
        ]);

        return $attribute;
    }

    public static function find_filter($id)
    {
        return Filter::find($id);
    }

    public static function count_filter_item($id)
    {
        return FilterItem::where('filter_id',$id)->count();
    }

    public static function get_filter_item($id)
    {
        return FilterItem::where('filter_id',$id)->get();
    }

    public static function add_filter_item($id,$data)
    {
        $attribute = FilterItem::create([
            'name' => ucfirst($data['name']),
            'status' => $data['status'],
            'filter_id' => $id,
        ]);

        return $attribute;
    }

    public static function find_filter_item($id)
    {
        return FilterItem::select('filter_items.*','filters.id as parent_id','filters.name as parent_name')
        ->where('filter_items.id','=',$id)
        ->leftJoin('filters','filters.id','=','filter_items.filter_id')
        ->first();
    }

    public static function edit_filter_item($id, $data)
    {
        $filter = FilterItem::where('id', $id)->update([
            'name' => $data['name'],
            'status' => $data['status'],
        ]);

        return $filter;
    }

    public static function count_category_filter($category_id)
    {
        return CategoryFilter::where('category_id', $category_id)->count();
    }

    //use at setting/category/filter/filter_id
    public static function get_selected_filter_by_category_id($cat_id)
    {
        $filters = Filter::where('status','=',1)->get();

        foreach ($filters as $filter) {
            $count = CategoryFilter::where('category_id','=', $cat_id)->where('filter_id','=',$filter->id)->count();
            $filter->selected = ($count > 0)? 1 : 0;
        }

        return $filters;

    }


    //use at ajax controller add filter into category
    public static function add_category_filter($filter_id, $category_id)
    {
        return CategoryFilter::create([
            'filter_id' => $filter_id,
            'category_id' => $category_id,
        ]);
    }

    //use at ajax controller remove filter from category
    public static function remove_category_filter($filter_id, $category_id)
    {
        CategoryFilter::where('filter_id','=',$filter_id)->where('category_id','=',$category_id)->delete();

        return 'success';
    }

    //use at product manage filter
    public static function get_product_filter_by_selected_category($pro_id)
    {
        $array = [];
        $filters = ProductCategory::select('filters.*')->where('product_id','=',$pro_id)
        ->leftJoin('category_filter','category_filter.category_id','=','nm_product_category.category_id')
        ->leftJoin('filters','filters.id','=','category_filter.filter_id')
        ->where('filters.status','=',1)
        ->groupBy('filters.id')
        ->orderBy('id','asc')
        ->get();

        $count = 0;
        foreach ($filters as $key => $filter) {

            $selected = ProductFilter::where('pro_id','=', $pro_id)
            ->where('filter_id','=', $filter->id)
            ->first();

            $filter->selected = ( !empty($selected) )? 1 : 0;

            $array[$key]['attribute'] = $filter;

            $items = FilterItem::where('filter_id','=',$filter->id)->where('status','=',1)->orderBy('id','asc')->get();
            foreach ($items as $item) {

                $selected = ProductFilter::where('pro_id','=',$pro_id)
                ->where('filter_id','=',$filter->id)
                ->where('filter_item_id','=',$item->id)
                ->first();

                if(!empty($selected))
                    $count++;

                $item->selected = ( !empty($selected) )? 1 : 0;
            }

            $array[$key]['item'] = $items;
        }

        $lists['count'] = $count;
        $lists['array'] = $array;
        return $lists;
    }

    //use at front sidebar filter with/out filter
    public static function get_category_filter($category_id)
    {
        $filters_array = [];
        if($category_id == 0) {
            $filters = Filter::where('status','=',1)->orderBy('name')->get();
        } else {
            $filters = CategoryFilter::select('filters.*')
            ->where('category_id','=',$category_id)
            ->leftJoin('filters','filters.id','=','category_filter.filter_id')
            ->where('filters.status','=',1)
            ->orderBy('filters.name')
            ->get();
        }

        foreach ($filters as $key => $filter) {
            $filters_array[$key]['filter'] = $filter;
            $filters_array[$key]['items'] = FilterItem::where('filter_id','=',$filter->id)->where('status','=',1)->get();
        }

        return $filters_array;
    }

    //use when update user update product filter at product manage filter
    public static function update_product_filter($pro_id, $data)
    {
        try {

            $filter_items = [];
            $filter_keys = [];
            foreach ($data['filter'] as $key => $value) {

                $filter = explode(',', $value);
                $add_filter = ProductFilter::where('pro_id', $pro_id)
                ->where('filter_id', $filter[0])
                ->where('filter_item_id', $filter[1])
                ->firstOrCreate([
                    'pro_id' => $pro_id,
                    'filter_id' => $filter[0],
                    'filter_item_id' => $filter[1]
                ]);

                $filter_keys[$filter[0]][] = $filter[1];
                $filter_items[] = $filter[1];
            }

            $remove_filter = ProductFilter::where('pro_id', $pro_id)->whereNotIn('filter_item_id',$filter_items)->delete();

            if(!empty($data['copy'])) {
                foreach ($data['copy'] as $filter_id) {
                    if(isset($filter_keys[$filter_id])) {

                        $filter_name = Filter::where('id',$filter_id)->value('name');
                        foreach ($filter_keys[$filter_id] as $filter_item_id) {
                            $filter_item_name = FilterItem::where('id',$filter_item_id)->value('name');
                            $insert = ProductAttribute::where('pro_id',$pro_id)
                            ->where('attribute','Like', '%'.$filter_name.'%')
                            ->where('attribute_item','Like', '%'.$filter_item_name.'%')
                            ->firstOrCreate([
                                'pro_id' => $pro_id,
                                'attribute' => $filter_name,
                                'attribute_item' => $filter_item_name,
                            ]);
                        }
                    }
                }
            }

            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    public static function delete_product_filter($pro_id)
    {
        try {
            $remove_filter = ProductFilter::where('pro_id', $pro_id)->delete();
            return true;

        } catch (Exception $e) {
            return false;
        }
    }
}
