<?php

namespace App\Models;
use App;
use Illuminate\Database\Eloquent\Model;

class ProductPricing extends Model
{

    protected $table = 'nm_product_pricing';
    protected $fillable = ['pro_id', 'country_id', 'currency_rate', 'price', 'discounted_price', 'discounted_rate', 'discounted_from', 'discounted_to','delivery_days','status','attributes','attribute_migrated', 'sku', 'quantity', 'attributes', 'attributes_name', 'coupon_value', 'shipping_fees', 'shipping_fees_type', 'coupon_value'];
    protected $primaryKey = 'id';

    public function getTitleAttribute($value)
    {
        $default_title = $this->{'pro_title_en'};
        $title = $this->{'pro_title_'.App::getLocale()};

        if (empty($title)) {
            return $default_title;
        } else {
            return $title;
        }
    }

    public  function ScopeProduct_in_grid($query, $country_id, $category_id_list=null, $take=null, $skip=null,$item=null, $filter=null, $range=null, $sort=null, $search=null)
    {
        $query->select("nm_product_pricing.pro_id",
                    \DB::raw("min( case when now() between nm_product_pricing.discounted_from and nm_product_pricing.discounted_to then nm_product_pricing.discounted_price else price end) as purchase_price"),
                    \DB::raw("min(nm_product_pricing.price) as price"),
                     "nm_product.pro_title_en", "nm_product.pro_title_cn", "nm_product.pro_title_cnt", "nm_product.pro_qty","nm_product_image.image as main_image","nm_country.*","nm_product.pro_mr_id","nm_product_pricing.shipping_fees_type","nm_product_pricing.shipping_fees","nm_product_pricing.discounted_rate")
                    ->join("nm_product", "nm_product_pricing.pro_id","=", "nm_product.pro_id")
                    ->leftjoin("nm_product_image", "nm_product_pricing.pro_id","=", "nm_product_image.pro_id")
                    ->join("nm_merchant", "nm_merchant.mer_id","=", "nm_product.pro_mr_id")
                    ->join("nm_store", "nm_store.stor_id","=", "nm_product.pro_sh_id")
                    ->join('nm_country', 'nm_country.co_id','=','nm_product_pricing.country_id')
                    ->where('nm_product_pricing.country_id', '=', $country_id)
                    ->where('nm_product_pricing.status', '=', 1)
                    ->where('nm_product.pro_status', '=', 1)
                    // ->where('nm_product.pro_qty', '>', 0)
                    ->where('nm_merchant.mer_staus', '=', 1)
                    ->where('nm_store.stor_status', '=', 1)
                    ->where(\DB::raw("CASE WHEN ((nm_product.end_date IS NOT NULL) and (CAST(nm_product.end_date AS DATETIME) > NOW())) THEN 1 WHEN (nm_product.end_date IS NULL or nm_product.pro_type = 4) THEN 1 ELSE 0 END"), 1)
                    // ->where('nm_product_image.main', '=', 1)
                    ->groupBy('nm_product_pricing.pro_id');



        if(!empty($category_id_list))
            $query->leftJoin('nm_product_category', 'nm_product_category.product_id', '=', 'nm_product.pro_id')
                                ->whereIn('nm_product_category.category_id', $category_id_list);
        if($range)
        {
            $query->whereBetween(\DB::raw("CASE WHEN ( (((nm_product_pricing.discounted_price IS NOT NULL )) and (nm_product_pricing.discounted_price > 0 )) and (NOW() >= (CAST(nm_product_pricing.discounted_from AS DATETIME)) and NOW() <= (CAST(nm_product_pricing.discounted_to AS DATETIME))) ) THEN nm_product_pricing.discounted_price ELSE nm_product_pricing.price END"), $range);

        }

        if($filter) {
            $query->leftJoin('nm_product_filters', 'nm_product_filters.pro_id','=','nm_product.pro_id');
            $query->whereIn('nm_product_filters.filter_item_id', $filter);
        }

        if($search)
        {
             $query->where(function ($inquery) use ($search) {
                $inquery->Where('nm_product.pro_title_en', 'LIKE', '%'.$search.'%')
                        ->orWhere('nm_product.pro_title_cn', 'LIKE', '%'.$search.'%');
                });
        }

        if($sort)
        {
            switch ($sort)
            {
                case 'name_asc':
                    $query->orderBy('pro_title_en');
                    break;
                case 'name_desc':
                    $query->orderBy('pro_title_en', 'desc');
                    break;
                case 'price_asc':
                    $query->orderBy('purchase_price');
                    break;
                case 'price_desc':
                    $query->orderBy('purchase_price', 'desc');
                    break;
                case 'new':
                    $query->orderBy('nm_product.pro_id', 'desc');
                    break;

                default:
                    $query->orderBy('nm_product.pro_id');
                    break;
            }
        }
        else
            $query->orderBy('nm_product.pro_id', 'DESC');

        if($item)
            return $query->paginate($item);
        else
        {
            $take = empty($take)?20:$take;
            return $query->take($take)->get();
        }
    }
}
