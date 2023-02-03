<?php

namespace App\Repositories;
use DB;
use App\Models\Product;
use App\Models\ProductImage;

class ProductImageRepo
{
    public function product_image_saveorder($input)
    {
        $result = explode(',',$input['newOrder']);
        foreach ($result as $key => $i)
        {
            $banner = ProductImage::find($i);
            $banner->order = ($key+1);
            $banner->save();
        }

        return 'success';
    }

    public static function get_product_image_by_pro_id($pro_id)
    {
        return ProductImage::where('pro_id',$pro_id)->orderBy('order','asc')->get();
    }

    public static function get_product_image_by_id($id)
    {
        return ProductImage::find($id);
    }

    public static function set_product_main_image($id)
    {
        $image = ProductImage::find($id);
        $images = ProductImage::where('pro_id',$image->pro_id)->get();

        foreach ($images as $image)
        {
            $image->update([
                    'main' => ($image->id == $id)? 1 : 0,
                ]);
        }

        return $image;

    }

    public static function toggle_image_status($id)
    {
        $image = ProductImage::find($id);

        $image->update([
            'status' => ($image->status == 0)? 1 : 0,
        ]);

        return $image;
    }

    public static function add_product_image($pro_id,$data)
    {
        $count_image = ProductImage::where('pro_id',$pro_id)->get()->count();
        $image = ProductImage::create([
            'pro_id' => $pro_id,
            'title' => $data['title'],
            'image' => $data['new_file_name'],
            'status' => $data['status'],
            'order' => $count_image+1,
            'main' => 0,
        ]);

        $product = Product::where('pro_id','=',$pro_id)->update([
            'pro_image_count' => $count_image+1,
        ]);

        return $image;
    }

    public static function get_image_count_by_pro_id($pro_id)
    {
        return ProductImage::where('pro_id',$pro_id)->get()->count();
    }

    public static function delete_product_image($id)
    {
        $image = ProductImage::find($id);
        $pro_id = $image->pro_id;
        $image->delete();

        $count = ProductImage::where('pro_id',$pro_id)->get()->count();
        $product = Product::find($pro_id);
        $product->pro_image_count = $count;
        $product->save();
    }

    public static function edit_product_image($id,$data)
    {
        $image = ProductImage::find($id);
        $image->title = $data['title'];
        $image->image = $data['new_file_name'];
        $image->status = $data['status'];
        $image->save();
    }

    public static function add_product_main_image($pro_id,$data)
    {
        $image = ProductImage::create([
            'pro_id' => $pro_id,
            'title' => $data['title'],
            'image' => $data['new_file_name'],
            'status' => 1,
            'order' => 1,
            'main' => 1,
        ]);

        return $image;
    }

    public static function get_product_main_image($pro_id)
    {
        $image = ProductImage::where('pro_id',$pro_id)->orderBy('main', 'desc')->orderBy('order', 'asc')->value('image');

        if(!empty($image)) {
            return $image;
        } else {
            return null;
        }

    }
}