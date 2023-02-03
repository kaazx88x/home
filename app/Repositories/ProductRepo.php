<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Store;
use App\Models\Order;
use App\Models\Merchant;
use App\Models\ProductCategory;
use App\Models\ProductPricing;
use App\Models\ProductQuantityLog;
use App\Models\Category;
use App\Models\StoreUserMapping;
use App\Models\PricingAttributeMapping;
use App;
use Cache;

class ProductRepo
{
    public static function all($input)
    {
       $products = Product::leftJoin('nm_store', 'nm_store.stor_id', '=', 'nm_product.pro_sh_id')
            ->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_product.pro_mr_id')
            ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_merchant.mer_co_id');

        if (isset($input['admin_country_id_list'])) {
            $products->whereIn('nm_store.stor_country', $input['admin_country_id_list']);
        }

        if (!empty($input['id']))
            $products->where('nm_product.pro_id', '=', $input['id']);

        if (!empty($input['mid']))
            $products->where('nm_merchant.mer_id', '=', $input['mid']);

        if (!empty($input['sid']))
            $products->where('nm_store.stor_id', '=', $input['sid']);

        if (!empty($input['name'])) {
            $search = '%'.$input['name'].'%';
            $products->where(function($query) use ($search) {
                $query->whereRaw('nm_product.pro_title_en LIKE ? or nm_product.pro_title_cn LIKE ? or nm_product.pro_title_cnt LIKE ? or nm_product.pro_title_my LIKE ? or nm_merchant.mer_fname LIKE ? or nm_store.stor_name LIKE ?', [$search, $search, $search, $search, $search, $search]);
            });
        }

        if (!empty($input['status']) || $input['status'] == '0')
            $products->where('nm_product.pro_status', '=', $input['status']);

        if(!empty($input['countries']))
            $products->whereIn('nm_merchant.mer_co_id', $input['countries']);

        if (!empty($input['sort'])) {
            switch ($input['sort']) {
                case 'name_asc':
                    $products->orderBy('nm_product.pro_title_en');
                    break;
                case 'name_desc':
                    $products->orderBy('nm_product.pro_title_en', 'desc');
                    break;
                case 'id_asc':
                    $products->orderBy('nm_product.pro_id');
                    break;
                case 'id_desc':
                    $products->orderBy('nm_product.pro_id', 'desc');
                    break;
                case 'new':
                    $products->orderBy('nm_product.created_at', 'desc');
                    break;
                case 'old':
                    $products->orderBy('nm_product.created_at', 'asc');
                    break;
                case 'merchant_asc':
                    $products->orderBy('nm_merchant.mer_fname', 'asc');
                    break;
                case 'merchant_desc':
                    $products->orderBy('nm_merchant.mer_fname', 'desc');
                    break;
                case 'store_asc':
                    $products->orderBy('nm_store.stor_name', 'asc');
                    break;
                case 'store_desc':
                    $products->orderBy('nm_store.stor_name', 'desc');
                    break;
                default:
                    $products->orderBy('nm_product.pro_id', 'desc');
                    break;
            }
        } else {
            $products->orderBy('nm_product.created_at', 'desc');
        }

        return $products->paginate(20);
    }

    public static function all_active($filter, $range, $item, $sort, $search)
    {
        $platform_charge = round(\Config::get('settings.platform_charge'));
        $countryid = \Cookie::get('country_locale')==null?\Session::get('countryid'):\Cookie::get('country_locale');

        $products = ProductPricing::product_in_grid($countryid, null, null,null, $item, $filter, $range, $sort, $search);
        return $products;
//        $products = Product::select(
//        \DB::raw("nm_product.*, nm_product_pricing.*, nm_country.*,
//           CASE WHEN ( (((nm_product_pricing.discounted_price IS NOT NULL )) and (nm_product_pricing.discounted_price > 0 )) and (NOW() >= (CAST(nm_product_pricing.discounted_from AS DATETIME)) and NOW() <= (CAST(nm_product_pricing.discounted_to AS DATETIME))) ) THEN nm_product_pricing.discounted_price ELSE nm_product_pricing.price END AS purchase_price,
//           (select image from nm_product_image where pro_id = nm_product.pro_id order by `main` desc, `order` asc limit 1) as main_image
//           ")
//        );
//        $products->where('pro_status', '=', 1);
//        $products->where('pro_qty', '>', 0);
//        $products->whereRaw('CASE WHEN ( (((nm_product_pricing.discounted_price IS NOT NULL )) and (nm_product_pricing.discounted_price > 0 )) and (NOW() >= (CAST(nm_product_pricing.discounted_from AS DATETIME)) and NOW() <= (CAST(nm_product_pricing.discounted_to AS DATETIME))) ) THEN nm_product_pricing.discounted_price ELSE nm_product_pricing.price END > 0');
//        $products->leftJoin('nm_product_pricing', function($join) use ($countryid) {
//            $join->on('nm_product_pricing.id', '=', \DB::raw('
//                (select id FROM nm_product_pricing where nm_product_pricing.pro_id = nm_product.pro_id and status = 1 and country_id = '.$countryid.' and
//                CASE WHEN ( (((discounted_price IS NOT NULL )) and (discounted_price > 0 )) and (NOW() >= (CAST(discounted_from AS DATETIME)) and NOW() <= (CAST(discounted_to AS DATETIME))) )
//                THEN
//                discounted_price = (select CASE WHEN ( (((discounted_price IS NOT NULL )) and (discounted_price > 0 )) and (NOW() >= (CAST(discounted_from AS DATETIME)) and NOW() <= (CAST(discounted_to AS DATETIME))) ) THEN discounted_price ELSE price END AS purchase_price FROM nm_product_pricing where nm_product_pricing.pro_id = nm_product.pro_id and status = 1 and country_id = '.$countryid.' order by purchase_price asc limit 1)
//                ELSE
//                price = (select CASE WHEN ( (((discounted_price IS NOT NULL )) and (discounted_price > 0 )) and (NOW() >= (CAST(discounted_from AS DATETIME)) and NOW() <= (CAST(discounted_to AS DATETIME))) ) THEN discounted_price ELSE price END AS purchase_price FROM nm_product_pricing where nm_product_pricing.pro_id = nm_product.pro_id and status = 1 and country_id = '.$countryid.' order by purchase_price asc limit 1)
//                END limit 1)
//            '));
//        });
//        $products->leftJoin('nm_country', 'nm_country.co_id','=','nm_product_pricing.country_id');
//        $products->LeftJoin('nm_store', 'nm_store.stor_id', '=', 'nm_product.pro_sh_id');
//        $products->where('nm_store.stor_status', '=', 1);
//        $products->LeftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_product.pro_mr_id');
//        $products->where('nm_merchant.mer_staus', '=', 1);
//
//        if ($range) {
//            $products->whereBetween(\DB::raw("CASE WHEN ( (((nm_product_pricing.discounted_price IS NOT NULL )) and (nm_product_pricing.discounted_price > 0 )) and (NOW() >= (CAST(nm_product_pricing.discounted_from AS DATETIME)) and NOW() <= (CAST(nm_product_pricing.discounted_to AS DATETIME))) ) THEN nm_product_pricing.discounted_price ELSE nm_product_pricing.price END"), $range);
//        }
//
//        if($filter) {
//            $products->leftJoin('nm_product_filters', 'nm_product_filters.pro_id','=','nm_product.pro_id');
//            $products->whereIn('nm_product_filters.filter_item_id', $filter);
//            $products->groupBy('nm_product.pro_id');
//        }
//
//        switch ($sort) {
//            case 'name_asc':
//                $products->orderBy('pro_title_en');
//                break;
//            case 'name_desc':
//                $products->orderBy('pro_title_en', 'desc');
//                break;
//            case 'price_asc':
//                $products->orderBy('purchase_price');
//                break;
//            case 'price_desc':
//                $products->orderBy('purchase_price', 'desc');
//                break;
//            case 'new':
//                $products->orderBy('nm_product.pro_id', 'desc');
//                break;
//
//            default:
//                $products->orderBy('nm_product.pro_id');
//                break;
//        }
//
//        // dd($products->toSql());
//        return $products->paginate($item);
    }

    public static function all_by_category($category_id, $filter, $range, $item, $sort, $search)
    {
        $products = null;
        $countryid = \Cookie::get('country_locale')==null?\Session::get('countryid'):\Cookie::get('country_locale');

        $category = Category::where('id','=', $category_id)->first();
        $child_id = [];
        if ($category) {
            $child_id = explode(',',$category->child_list);
            array_push($child_id, $category_id);
        }

        $products = ProductPricing::product_in_grid($countryid, $child_id, null,null, $item, $filter, $range, $sort, $search);
        return $products;

//        $products = Product::where('pro_status', '=', 1)->where('pro_qty', '>', 0);
//        $products->leftJoin('nm_product_category', 'nm_product_category.product_id', '=', 'nm_product.pro_id')
//        ->whereIn('nm_product_category.category_id', $child_id)
//        ->groupBy('nm_product_category.product_id');
//
//        $products->where('nm_product.pro_qty', '>', 0);
//        $platform_charge = round(\Config::get('settings.platform_charge'));
//        $products->select(
//        \DB::raw("nm_product.*, nm_product_pricing.*, nm_country.*,
//            CASE WHEN ( (((nm_product_pricing.discounted_price IS NOT NULL )) and (nm_product_pricing.discounted_price > 0 )) and (NOW() >= (CAST(nm_product_pricing.discounted_from AS DATETIME)) and NOW() <= (CAST(nm_product_pricing.discounted_to AS DATETIME))) ) THEN nm_product_pricing.discounted_price ELSE nm_product_pricing.price END AS purchase_price,
//            (select image from nm_product_image where pro_id = nm_product.pro_id order by `main` desc, `order` asc limit 1) as main_image
//            "));
//        $products->whereRaw('CASE WHEN ( (((nm_product_pricing.discounted_price IS NOT NULL )) and (nm_product_pricing.discounted_price > 0 )) and (NOW() >= (CAST(nm_product_pricing.discounted_from AS DATETIME)) and NOW() <= (CAST(nm_product_pricing.discounted_to AS DATETIME))) ) THEN nm_product_pricing.discounted_price ELSE nm_product_pricing.price END > 0');
//        $products->leftJoin('nm_product_pricing', function($join) use ($countryid) {
//            $join->on('nm_product_pricing.id', '=', \DB::raw('
//                (select id FROM nm_product_pricing where nm_product_pricing.pro_id = nm_product.pro_id and status = 1 and country_id = '.$countryid.' and
//                CASE WHEN ( (((discounted_price IS NOT NULL )) and (discounted_price > 0 )) and (NOW() >= (CAST(discounted_from AS DATETIME)) and NOW() <= (CAST(discounted_to AS DATETIME))) )
//                THEN
//                discounted_price = (select CASE WHEN ( (((discounted_price IS NOT NULL )) and (discounted_price > 0 )) and (NOW() >= (CAST(discounted_from AS DATETIME)) and NOW() <= (CAST(discounted_to AS DATETIME))) ) THEN discounted_price ELSE price END AS purchase_price FROM nm_product_pricing where nm_product_pricing.pro_id = nm_product.pro_id and status = 1 and country_id = '.$countryid.' order by purchase_price asc limit 1)
//                ELSE
//                price = (select CASE WHEN ( (((discounted_price IS NOT NULL )) and (discounted_price > 0 )) and (NOW() >= (CAST(discounted_from AS DATETIME)) and NOW() <= (CAST(discounted_to AS DATETIME))) ) THEN discounted_price ELSE price END AS purchase_price FROM nm_product_pricing where nm_product_pricing.pro_id = nm_product.pro_id and status = 1 and country_id = '.$countryid.' order by purchase_price asc limit 1)
//                END limit 1)
//            '));
//        });
//        $products->leftJoin('nm_country', 'nm_country.co_id','=','nm_product_pricing.country_id');
//        $products->LeftJoin('nm_store', 'nm_store.stor_id', '=', 'nm_product.pro_sh_id');
//        $products->where('nm_store.stor_status', '=', 1);
//        $products->LeftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_product.pro_mr_id');
//        $products->where('nm_merchant.mer_staus', '=', 1);
//
//
//        if ($range) {
//            $products->whereBetween(\DB::raw("CASE WHEN ( (((nm_product_pricing.discounted_price IS NOT NULL )) and (nm_product_pricing.discounted_price > 0 )) and (NOW() >= (CAST(nm_product_pricing.discounted_from AS DATETIME)) and NOW() <= (CAST(nm_product_pricing.discounted_to AS DATETIME))) ) THEN nm_product_pricing.discounted_price ELSE nm_product_pricing.price END"), $range);
//        }
//
//        if($filter) {
//            $products->leftJoin('nm_product_filters', 'nm_product_filters.pro_id','=','nm_product.pro_id');
//            $products->whereIn('nm_product_filters.filter_item_id', $filter);
//            $products->groupBy('nm_product.pro_id');
//        }
//
//        switch ($sort) {
//            case 'name_asc':
//                $products->orderBy('pro_title_en');
//                break;
//            case 'name_desc':
//                $products->orderBy('pro_title_en', 'desc');
//                break;
//            case 'price_asc':
//                $products->orderBy('purchase_price');
//                break;
//            case 'price_desc':
//                $products->orderBy('purchase_price', 'desc');
//                break;
//            case 'new':
//                $products->orderBy('nm_product.pro_id', 'desc');
//                break;
//
//            default:
//                $products->orderBy('nm_product.pro_id');
//                break;
//        }
//
//        return $products->paginate($item);
    }

    public static function detail($id)
    {
        $countryid = \Cookie::get('country_locale')==null?\Session::get('countryid'):\Cookie::get('country_locale');
        $product = array();
        $product['details'] = Product::select('nm_product.*')
        ->where('nm_product.pro_id', '=', $id)->where('nm_product.pro_status', '=', 1)
        ->leftJoin('nm_product_pricing', 'nm_product_pricing.pro_id','=','nm_product.pro_id')
        ->where('nm_product_pricing.country_id', '=', $countryid)
        ->where('nm_product_pricing.status', '=', 1)
        ->leftJoin('nm_country', 'nm_country.co_id','=','nm_product_pricing.country_id')
        ->LeftJoin('nm_store', 'nm_store.stor_id', '=', 'nm_product.pro_sh_id')
        ->where('nm_store.stor_status', '=', 1)
        ->LeftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_product.pro_mr_id')
        ->where('nm_merchant.mer_staus', '=', 1)
        ->where(\DB::raw("CASE WHEN ((nm_product.end_date IS NOT NULL) and (CAST(nm_product.end_date AS DATETIME) > NOW())) THEN 1 WHEN (nm_product.end_date IS NULL or nm_product.pro_type = 4) THEN 1 ELSE 0 END"), 1)
        ->first();

        if (!empty($product['details'])) {
            $product['images'] = ProductImage::where('pro_id', '=', $id)->where('status','=', 1)->orderBy('order','asc')->get();
            $product['main_image'] = ProductImage::where('pro_id', $id)->orderBy('main', 'desc')->orderBy('order', 'asc')->value('image');
            $product['pricing'] = ProductPricing::select('nm_product_pricing.*','nm_country.co_cursymbol',
                \DB::raw("
                    CASE WHEN ( (((nm_product_pricing.discounted_price IS NOT NULL )) and (nm_product_pricing.discounted_price > 0 )) and (NOW() >= (CAST(nm_product_pricing.discounted_from AS DATETIME)) and NOW() <= (CAST(nm_product_pricing.discounted_to AS DATETIME))) ) THEN nm_product_pricing.discounted_price ELSE nm_product_pricing.price END AS purchase_price")
            )
            ->where('pro_id',$id)->where('country_id', $countryid)
            ->where('status', 1)
            ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_product_pricing.country_id')
            ->orderBy('purchase_price','asc')
            ->first();

            $selected_attributes = PricingAttributeMapping::select('nm_pricing_attribute_mappings.attribute_id')->where('nm_pricing_attribute_mappings.pro_id', $id)
            ->leftJoin('nm_product_attributes','nm_product_attributes.id','=','nm_pricing_attribute_mappings.attribute_id')
            ->where('nm_pricing_attribute_mappings.country_id', $countryid)
            ->where('nm_pricing_attribute_mappings.pricing_id', $product['pricing']->id)
            ->orderBy('nm_product_attributes.attribute')
            ->get()
            ->pluck('attribute_id')
            ->toArray();

            $product['attributes'] = $selected_attributes;
            $product['category'] = Category::LeftJoin('nm_product_category', 'categories.id', '=', 'nm_product_category.category_id')->where('nm_product_category.product_id', $id)->first();
            if (!empty($product['category']))
                $product['category_links'] = Category::whereIn('id', explode(',', $product['category']->parent_list))->get();

            return $product;

        } else {
            return false;
        }
    }

    public static function sold($input)
    {
        $products = Product::where('pro_qty', '=', 0)
        ->leftJoin('nm_store', 'nm_store.stor_id', '=', 'nm_product.pro_sh_id')
        ->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_product.pro_mr_id');

        if (isset($input['admin_country_id_list'])) {
            $products->whereIn('nm_store.stor_country',  $input['admin_country_id_list']);
        }

        if (!empty($input['id']))
            $products->where('nm_product.pro_id', '=', $input['id']);

        if (!empty($input['name']))
            $products->where('nm_product.pro_title_en', 'LIKE', '%'.$input['name'].'%')
            ->orWhere('nm_product.pro_title_cn', 'LIKE', '%'.$input['name'].'%')
            ->orWhere('nm_product.pro_title_my', 'LIKE', '%'.$input['name'].'%');

        if (!empty($input['sort'])) {
            switch ($input['sort']) {
            case 'name_asc':
                $products->orderBy('nm_product.pro_title_en');
                break;
            case 'name_desc':
                $products->orderBy('nm_product.pro_title_en', 'desc');
                break;
            case 'id_asc':
                $products->orderBy('nm_product.pro_id');
                break;
            case 'id_desc':
                $products->orderBy('nm_product.pro_id', 'desc');
                break;
            case 'new':
                $products->orderBy('nm_product.created_at', 'desc');
                break;
            case 'old':
                $products->orderBy('nm_product.created_at', 'asc');
                break;
            default:
                $products->orderBy('nm_product.pro_id', 'desc');
                break;
            }
        }
        return $products->paginate(50);
    }

    public static function featured_product()
    {
        $featured = array();
        // $parents = Category::where('parent_id','=', 0)->where('featured','=',1)->where('status','=', 1)->orderBy('sequence','asc')->get();
        $parents = Category::where('featured','=',1)->where('status','=', 1)->orderBy('sequence','asc')->get();
        $countryid = \Cookie::get('country_locale')==null?\Session::get('countryid'):\Cookie::get('country_locale');
        $hour = date("G");

        $pkey=0;
        $ckey =0;

        if($hour >=0 && $hour<=7)
        {
            $pkey=3;
            $ckey =1;
        }
        elseif($hour >=8 && $hour<=15)
        {
            $pkey=1;
            $ckey =2;
        }
        elseif($hour >=16 && $hour<=23)
        {
            $pkey=2;
        }

        foreach ($parents as $key => $list) {
            $featured[$list->id]['parent'] = $list;
            $featured[$list->id]['child'] = Category::where('parent_id',$list->id)->where('status','=', 1)->limit(14)->get();

            $child_id = explode(',', $list->child_list);
            array_push($child_id, $list->id);

            $pCacheKey = $countryid.'+'.$list->id.'+'.$pkey;
            $cacheKey=$countryid.'+'.$list->id.'+'.$ckey;
            if (Cache::has($pCacheKey))
            {
                Cache::pull($pCacheKey);
            }

            //assign feature category product list by either find from cache, or reset if cache expired.
            $products = Cache::rememberForever($cacheKey, function( ) use ($countryid,$child_id)
            {
                $products = ProductPricing::product_in_grid($countryid, $child_id, 6);
                return $products;
             });


            $featured[$list->id]['products'] = $products;
        }
        return $featured;
    }

    public static function get_merchant_products($mer_id, $input)
    {
        $products = Product::leftJoin('nm_store', 'nm_store.stor_id', '=', 'nm_product.pro_sh_id')
            ->where('nm_product.pro_mr_id', '=', $mer_id);

        if(\Auth::guard('storeusers')->check()) {
            $assigned_stores = StoreUserMapping::where('storeuser_id','=',\Auth::guard('storeusers')->user()->id)->pluck('store_id')->toArray();
            $products->whereIn('nm_store.stor_id', $assigned_stores);
        }

        if (!empty($input['id']))
            $products->where('nm_product.pro_id', '=', $input['id']);

        if (!empty($input['name'])) {
            $search = '%'.$input['name'].'%';
            $products->where(function($query) use ($search) {
                $query->whereRaw('nm_product.pro_title_en LIKE ? or nm_product.pro_title_cn LIKE ? or nm_product.pro_title_cnt LIKE ? or nm_product.pro_title_my LIKE ?', [$search, $search, $search, $search]);
            });
        }

        if (!empty($input['status']) || $input['status'] == '0')
            $products->where('nm_product.pro_status', '=', $input['status']);

        if (!empty($input['sort'])) {
            switch ($input['sort']) {
                case 'name_asc':
                    $products->orderBy('nm_product.pro_title_en');
                    break;
                case 'name_desc':
                    $products->orderBy('nm_product.pro_title_en', 'desc');
                    break;
                case 'id_asc':
                    $products->orderBy('nm_product.pro_id');
                    break;
                case 'id_desc':
                    $products->orderBy('nm_product.pro_id', 'desc');
                    break;
                case 'new':
                    $products->orderBy('nm_product.created_at', 'desc');
                    break;
                case 'old':
                    $products->orderBy('nm_product.created_at', 'asc');
                    break;
                default:
                    $products->orderBy('nm_product.pro_id', 'desc');
                    break;
            }
        } else {
            $products->orderBy('nm_product.pro_id', 'desc');
        }

        return $products->paginate(50);
    }

    public static function get_merchant_sold_products($mer_id, $input)
    {
        $products = Product::where('pro_qty', '=', 0)
        ->where('pro_mr_id', '=', $mer_id)
        ->leftJoin('nm_store', 'nm_store.stor_id', '=', 'nm_product.pro_sh_id');

        if (!empty($input['id']))
            $products->where('nm_product.pro_id', '=', $input['id']);

        if (!empty($input['name'])) {
            $search = '%'.$input['name'].'%';
            $products->where(function($query) use ($search) {
                $query->whereRaw('nm_product.pro_title_en LIKE ? or nm_product.pro_title_cn LIKE ? or nm_product.pro_title_cnt LIKE ? or nm_product.pro_title_my LIKE ?', [$search, $search, $search, $search]);
            });
        }

        if (!empty($input['sort'])) {
            switch ($input['sort']) {
                case 'name_asc':
                    $products->orderBy('nm_product.pro_title_en');
                    break;
                case 'name_desc':
                    $products->orderBy('nm_product.pro_title_en', 'desc');
                    break;
                case 'id_asc':
                    $products->orderBy('nm_product.pro_id');
                    break;
                case 'id_desc':
                    $products->orderBy('nm_product.pro_id', 'desc');
                    break;
                case 'new':
                    $products->orderBy('nm_product.created_at', 'desc');
                    break;
                case 'old':
                    $products->orderBy('nm_product.created_at', 'asc');
                    break;
                default:
                    $products->orderBy('nm_product.pro_id', 'desc');
                    break;
            }
        }

        return $products->paginate(50);
    }

    public static function get_shipping_details($mer_id, $input)
	{
		$query = Order::leftjoin('nm_product', 'nm_order.order_pro_id', '=', 'nm_product.pro_id')
    		->leftjoin('nm_customer', 'nm_order.order_cus_id', '=', 'nm_customer.cus_id')
            ->leftjoin('nm_shipping', 'nm_order.order_id', '=', 'nm_shipping.ship_order_id')
            ->leftjoin('nm_store', 'nm_store.stor_id', '=', 'nm_product.pro_sh_id');

        if (isset($input['admin_country_id_list'])) {
            $query->whereIn('nm_store.stor_country', $input['admin_country_id_list']);
        }

        if( $mer_id != 'all'){
            $query->where('nm_product.pro_mr_id', '=', $mer_id);
        }

        if (!empty($input['status'])) {
            $query->where('nm_order.order_status', '=', $input['status']);
        } else {
            $query->where('nm_order.order_status', '>=', 3);
        }

        if (!empty($input['id']))
            $query->where('nm_order.transaction_id', '=', $input['id']);

        if (!empty($input['name'])) {
            $search = '%'.$input['name'].'%';
            $query->where(function($q) use ($search) {
                $q->whereRaw('nm_product.pro_title_en LIKE ? or nm_product.pro_title_cn LIKE ? or nm_product.pro_title_cnt LIKE ? or nm_product.pro_title_my LIKE ?', [$search, $search, $search, $search]);
            });
        }

        if (!empty($input['sort'])) {
            switch ($input['sort']) {
                case 'name_asc':
                    $query->orderBy('nm_product.pro_title_en');
                    break;
                case 'name_desc':
                    $query->orderBy('nm_product.pro_title_en', 'desc');
                    break;
                case 'new':
                    $query->orderBy('nm_order.order_date', 'desc');
                    break;
                case 'old':
                    $query->orderBy('nm_order.order_date', 'asc');
                    break;
                default:
                    $query->orderBy('nm_order.order_date', 'desc');
                    break;
            }
        } else {
            $query->orderBy('nm_order.order_date', 'desc');
        }

        return $query->paginate(50);
	}

    public static function get_merchant_product_details($mer_id, $pro_id)
    {
        $product = array();
        $product['details'] = Product::where('pro_id', '=', $pro_id)->where('pro_mr_id', '=', $mer_id);
        if(\Auth::guard('storeusers')->check()) {
            $assigned_stores = StoreUserMapping::where('storeuser_id','=',\Auth::guard('storeusers')->user()->id)->pluck('store_id')->toArray();
            $product['details'] = $product['details']->whereIn('pro_sh_id', $assigned_stores);
        }
        $product['details'] = $product['details']->first();

        if (!empty($product['details'])) {
            $product['store'] = Store::where('stor_id', '=', $product['details']->pro_sh_id)->first();
            $product['merchant'] = Merchant::where('mer_id', '=', $product['details']->pro_mr_id)->first();
            $selected_cats = ProductCategory::where('product_id', '=', $pro_id)->LeftJoin('categories', 'categories.id', '=', 'nm_product_category.category_id')->get();
            $product['category'] = [];
            foreach ($selected_cats as $key => $selected_cat) {
                $product['category'][$key]['details'] = $selected_cat;
                $product['category'][$key]['parents'] = Category::selectRaw('GROUP_CONCAT(name_en SEPARATOR " > ") as names')->whereIn('id', explode(',', $selected_cat->parent_list))->orderBy('parent_id', 'ASC')->first();
            }
            return $product;
        } else {
            return false;
        }
    }

    public static function update_product_status($pro_id, $status)
    {
        $product = Product::where('pro_id','=',$pro_id)->first();
        $product->pro_status = $status;
        $product->save();

        return $product;
    }

    public static function get_product_image($pro_id)
    {
        return Product::where('pro_id','=',$pro_id)->pluck('pro_Img')->first();
    }

    public static function add_product($data, $mer_id)
    {
        $product = Product::create([
            'pro_type' => $data['pro_type'],
            'pro_title_en' => $data['pro_title_en'],
            'pro_title_cn' => $data['pro_title_cn'],
            'pro_title_cnt' => $data['pro_title_cnt'],
            'pro_title_my' => $data['pro_title_my'],
            // 'pro_price' => $data['pro_price'],
            // 'pro_disprice' => $data['pro_dprice'],
            'pro_inctax' => 0, //skip for now
            // 'pro_shippamt' => $data['pro_shipping'],
            'pro_isspec' => '', //skip for now
            // 'pro_delivery' => $data['pro_delivery'],
            'pro_mr_id' => $mer_id,
            'pro_sh_id' => $data['stor_id'],
            'pro_mkeywords' => $data['metakeyword'],
            'pro_mdesc' => $data['metadescription'],
            // 'pro_Img' => $pro_img,
            'pro_image_count' => 1,
            'pro_qty' => 0,
            // 'pro_vcoin_value' => $data['pro_credit'],
            // 'pro_vtoken_value' => $data['pro_credit'],
            'pro_status' => 2,
            'category_migrated' => 1,
            'image_migrated' => 1,
            'pricing_migrated' => 1,
            'quantity_migrated' => 1,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'limit_enabled' => isset($data['limit_enabled']) ? $data['limit_enabled'] : 0,
            'limit_quantity' => isset($data['limit_quantity']) ? $data['limit_quantity'] : 0,
            'limit_type' => isset($data['limit_type']) ? $data['limit_type'] : 0,
        ]);

        return $product;
    }

    public static function edit_product($data, $mer_id, $pro_id)
    {
        $product = Product::find($pro_id);
        $product->pro_title_en = $data['pro_title_en'];
        $product->pro_title_cn = $data['pro_title_cn'];
        $product->pro_title_cnt = $data['pro_title_cnt'];
        $product->pro_title_my = $data['pro_title_my'];
        // $product->pro_price = $data['pro_price'];
        // $product->pro_disprice = $data['pro_dprice'];
        // $product->pro_shippamt = $data['pro_shipping'];
        // $product->pro_delivery = $data['pro_delivery'];
        $product->pro_sh_id = $data['stor_id'];
        $product->pro_mkeywords = $data['metakeyword'];
        $product->pro_mdesc = $data['metadescription'];
        // $product->pro_Img = $pro_img;
        // $product->pro_image_count = $img_count;
        // $product->pro_qty = $data['pro_quantity'];
        // $product->pro_vtoken_value = $data['pro_credit'];

        // if(!empty($data['pro_status']))
        //     $product->pro_status = $data['pro_status'];

        $product->start_date = ($product->pro_type == 3 || ($product->pro_type == 4 && $data['start_date']))? $data['start_date'] : null;
        $product->end_date = ($product->pro_type == 3 || ($product->pro_type == 4 && $data['end_date']))? $data['end_date'] : null;

        if(isset($data['limit_enabled']) && isset($data['limit_quantity']) && isset($data['limit_type']))
        {
            $product->limit_enabled = $data['limit_enabled'];
            $product->limit_quantity = $data['limit_quantity'];
            $product->limit_type = $data['limit_type'];
        }

        $product->save();

        return $product;
    }

    public static function insert_product_category_details($entry)
    {
        $productcategory = ProductCategory::create($entry);
        return $productcategory;
    }

    public static function delete_product_category($pro_id)
    {
        return ProductCategory::where('product_id', '=', $pro_id)->delete();
    }

    public static function get_product_quantity_log($pro_id)
    {
        return ProductQuantityLog::where('pro_id','=', $pro_id)->orderBy('id','desc')->paginate(20);
    }

    public static function get_product_parent_category($pro_id)
    {
        $main = ProductCategory::where('product_id',$pro_id)->orderBy('id','asc')->pluck('category_id')->first();
        if(!$main)
            return false;

        return $main;
    }

    public static function check_product_is_belongs_to_merchant($pro_id, $mer_id)
    {
        $check = Product::where('pro_id',$pro_id)->where('pro_mr_id', $mer_id)->first();
        if(!$check)
            return false;

        return true;
    }

    public static function get_product_main_category($pro_id)
    {
        $category = ProductCategory::where('product_id', $pro_id)
        ->orderBy('id', 'asc')
        ->first();

        if($category)
            return $category->Category->name;

        return null;
    }

    public static function product_description($data, $mer_id, $pro_id)
    {
        $product = Product::find($pro_id);
        $product->pro_desc_en = $data['pro_desc_en'];
        $product->pro_desc_cn = $data['pro_desc_cn'];
        $product->pro_desc_cnt = $data['pro_desc_cnt'];
        $product->pro_desc_my = $data['pro_desc_my'];
        $product->save();

        return $product;
    }

    public static function get_product_store($pro_id)
    {
        $product = Product::leftJoin('nm_store', 'nm_store.stor_id', '=', 'nm_product.pro_sh_id')
        ->where('pro_id', $pro_id)
        ->first();

        return $product;
    }
}
