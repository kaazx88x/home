<?php

namespace App\Repositories;
use DB;
use App\Models\Inquiries;
use App\Models\Cms;
use App\Models\Product;
use App\Models\Category;

class HomeRepo
{
    public static function find_cms($url_slug)
    {
        return Cms::where('cp_url', '=', $url_slug)->first();
    }

    public static function insert_contactUs($data)
    {
        try {

            $inquiries = new Inquiries;
            $inquiries->iq_subject = $data['subject'];
            $inquiries->iq_emailid = $data['email'];
            $inquiries->or_id = $data['order_reference'];
            $inquiries->iq_message = $data['message'];
            $inquiries->save();
            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    public function search_product($category_id, $search, $item, $sort)
    {
        $countryid = \Cookie::get('country_locale')==null?\Session::get('countryid'):\Cookie::get('country_locale');

        $itemParams = (\Request::get('items')) ? \Request::get('items') : '15';
        $sortParams = (\Request::get('sort')) ? \Request::get('sort') : 'new';
        $searchParams = (\Request::get('search')) ? \Request::get('search') : '';
        if ($category_id != 'all')
        {
            $products = ProductRepo::all_by_category($category_id, null, null, $itemParams, $sortParams, $searchParams);
        }
        else
        {
             $products = ProductRepo::all_active(null, null, $itemParams, $sortParams, $searchParams);
        }

        return $products;

        // $products = Product::select('nm_product.*','nm_product_pricing.*','nm_country.*',
        // \DB::raw("nm_product.*, nm_product_pricing.price, nm_country.*,
        //         CASE WHEN ( (((nm_product_pricing.discounted_price IS NOT NULL )) and (nm_product_pricing.discounted_price > 0 )) and (NOW() >= (CAST(nm_product_pricing.discounted_from AS DATETIME)) and NOW() <= (CAST(nm_product_pricing.discounted_to AS DATETIME))) ) THEN nm_product_pricing.discounted_price ELSE nm_product_pricing.price END AS purchase_price,
        //         (select image from nm_product_image where pro_id = nm_product.pro_id order by `main` desc, `order` asc limit 1) as main_image
        //         "));
        // $products->where('pro_status','=',1);
        // $products->where('pro_qty','>', 0);
        // $products->whereRaw('CASE WHEN ( (((nm_product_pricing.discounted_price IS NOT NULL )) and (nm_product_pricing.discounted_price > 0 )) and (NOW() >= (CAST(nm_product_pricing.discounted_from AS DATETIME)) and NOW() <= (CAST(nm_product_pricing.discounted_to AS DATETIME))) ) THEN nm_product_pricing.discounted_price ELSE nm_product_pricing.price END > 0');
        // $products->leftJoin('nm_product_pricing', function($join) use ($countryid){
        //     $join->on('nm_product_pricing.id', '=', \DB::raw('
        //         (select id FROM nm_product_pricing where nm_product_pricing.pro_id = nm_product.pro_id and status = 1 and country_id = '.$countryid.' and
        //         CASE WHEN ( (((discounted_price IS NOT NULL )) and (discounted_price > 0 )) and (NOW() >= (CAST(discounted_from AS DATETIME)) and NOW() <= (CAST(discounted_to AS DATETIME))) )
        //         THEN
        //         discounted_price = (select CASE WHEN ( (((discounted_price IS NOT NULL )) and (discounted_price > 0 )) and (NOW() >= (CAST(discounted_from AS DATETIME)) and NOW() <= (CAST(discounted_to AS DATETIME))) ) THEN discounted_price ELSE price END AS purchase_price FROM nm_product_pricing where nm_product_pricing.pro_id = nm_product.pro_id and status = 1 and country_id = '.$countryid.' order by purchase_price asc limit 1)
        //         ELSE
        //         price = (select CASE WHEN ( (((discounted_price IS NOT NULL )) and (discounted_price > 0 )) and (NOW() >= (CAST(discounted_from AS DATETIME)) and NOW() <= (CAST(discounted_to AS DATETIME))) ) THEN discounted_price ELSE price END AS purchase_price FROM nm_product_pricing where nm_product_pricing.pro_id = nm_product.pro_id and status = 1 and country_id = '.$countryid.' order by purchase_price asc limit 1)
        //         END limit 1)
        //     '));
        // });
        // $products->leftJoin('nm_country', 'nm_country.co_id','=','nm_product_pricing.country_id');
        // $products->LeftJoin('nm_store', 'nm_store.stor_id', '=', 'nm_product.pro_sh_id');
        // $products->where('nm_store.stor_status', '=', 1);
        // $products->LeftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_product.pro_mr_id');
        // $products->where('nm_merchant.mer_staus', '=', 1);

        // $lang = \App::getLocale();
        // if ($category_id != 'all')
        // {
        //     $category = Category::where('id','=', $category_id)->first();
        //     $child_id = explode(',',$category->child_list);
        //     array_push($child_id, $category_id);

        //     $products->leftJoin('nm_product_category', 'nm_product_category.product_id', '=', 'nm_product.pro_id')
        //     ->whereIn('nm_product_category.category_id', $child_id)
        //     ->groupBy('nm_product_category.product_id');
        // }
        // $products->where(function ($query) use ($search) {
        // $query->Where('nm_product.pro_title_en', 'LIKE', '%'.$search.'%')
        //         ->orWhere('nm_product.pro_title_cn', 'LIKE', '%'.$search.'%');
        // });

        // switch ($sort) {
        //     case 'name_asc':
        //         $products->orderBy('pro_title_en');
        //         break;
        //     case 'name_desc':
        //         $products->orderBy('pro_title_en', 'desc');
        //         break;
        //     case 'price_asc':
        //         $products->orderBy('pro_vtoken_value');
        //         break;
        //     case 'price_desc':
        //         $products->orderBy('pro_vtoken_value', 'desc');
        //         break;
        //     case 'new':
        //         $products->orderBy('nm_product.pro_id', 'desc');
        //         break;

        //     default:
        //         $products->orderBy('nm_product.pro_id');
        //         break;
        // }
        // return $products->paginate($item);
    }

    function check_string($search)
    {
        if (preg_match("/^\p{Han}+$/u", $search))
        {
            return true;
        }
        return false;
    }

}
