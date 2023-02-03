<?php

namespace App\Repositories;
use App\Models\ProductPricing;
use App\Models\ProductAttribute;
use App\Models\Cart;
use App\Models\PricingAttributeMapping;
use App\Models\Product;

class AttributeRepo
{
    public static function get_product_attribute($pro_id)
    {
        return ProductAttribute::where('pro_id', $pro_id)
        ->orderBy('attribute','asc')
        ->get();
    }

    public static function get_product_attribute_simplified($pro_id)
    {
        $attributes = ProductAttribute::selectRaw("id, attribute, attribute_cn, attribute_cnt, GROUP_CONCAT(NULLIF(attribute_item, '') SEPARATOR  ', ') as 'attribute_item', GROUP_CONCAT(NULLIF(attribute_item_cn, '') SEPARATOR  ', ') as 'attribute_item_cn', GROUP_CONCAT(NULLIF(attribute_item_cnt, '') SEPARATOR  ', ') as 'attribute_item_cnt'")
        ->where('pro_id', $pro_id)
        ->orderBy('attribute','asc')
        ->groupBy('attribute')
        ->get();

        return $attributes;
    }

    public static function add_product_attribute($pro_id, $data)
    {
        $exists = ProductAttribute::where('pro_id', $pro_id)
        ->where('attribute', trim($data['attribute']))
        ->where('attribute_item', trim($data['attribute']))
        ->exists();

        if(!$exists) {
            ProductAttribute::create([
                'pro_id' => $pro_id,
                'attribute' => trim($data['attribute']),
                'attribute_item' => trim($data['attribute_item']),
                'attribute_cn' => $data['attribute_cn'],
                'attribute_item_cn' => trim($data['attribute_item_cn']),
                'attribute_cnt' => $data['attribute_cnt'],
                'attribute_item_cnt' => trim($data['attribute_item_cnt']),
            ]);

            return 'success';
        } else {
            return 'existed';
        }

        return 'failed';
    }

    public static function delete_product_attribute($attribute_id)
    {
        ProductAttribute::find($attribute_id)->delete();
        return true;
    }

    public static function get_product_attribute_for_pricing($pro_id)
    {
        return ProductAttribute::where('pro_id',$pro_id)->orderBy('attribute')->get()->groupBy('attribute');
    }

    public static function update_product_pricing_attribute($pro_id, $pricing_id, $attributes)
    {
        $pricing_id = json_decode($pricing_id, true);

        foreach ($pricing_id as $id) {
            $price = ProductPricing::find($id);
            //only check of mapping that are not supposed to be in the table
            if(!$price) {
                PricingAttributeMapping::where('pricing_id', $id)->where('pro_id', $pro_id)->delete();
            } else {
                PricingAttributeMapping::where('pro_id',$price->pro_id)
                ->where('pricing_id', $price->id)
                ->where('country_id', $price->country_id)
                ->delete();

                foreach ($attributes as $attribute_id) {
                    PricingAttributeMapping::where('pro_id', $price->pro_id)
                    ->where('pricing_id', $price->pro_id)
                    ->where('attribute_id', $attribute_id)
                    ->firstOrCreate([
                        'pro_id' => $price->pro_id,
                        'country_id' => $price->country_id,
                        'pricing_id' => $price->id,
                        'attribute_id' => $attribute_id,
                    ]);
                }

                $price->attributes = self::get_pricing_attribute_id_json($price->id);
                $price->attributes_name = self::get_pricing_attributes_name_json($price->id);
                $price->attribute_status = 1;
                $price->save();
            }
        }
        return true;
    }

    public static function check_set_pricing_attribute_exist($pro_id, $country_id, $attributes)
    {
        $check = PricingAttributeMapping::where('pro_id',$pro_id)
        ->where('country_id', $country_id)
        ->whereIn('attribute_id', $attributes)
        ->groupBy('pricing_id')
        ->havingRaw('COUNT(*) = '.count($attributes))
        ->first();

        if($check)
            return false;

        return true;
    }

    public static function check_single_pricing_attribute_exist($pro_id, $attribute_id)
    {
        $check = PricingAttributeMapping::where('pro_id', $pro_id)->where('attribute_id', $attribute_id)->first();

        if($check)
            return 1;

        return 0;
    }

    public static function check_parent_pricing_attribute_exist($pro_id, $attribute_id)
    {
        $attribute_name = ProductAttribute::where('id', $attribute_id)->pluck('attribute')->first();

        $attribute_id = ProductAttribute::where('pro_id', $pro_id)->where('attribute', $attribute_name)->pluck('id')->toArray();
        $check = PricingAttributeMapping::where('pro_id', $pro_id)->whereIn('attribute_id', $attribute_id)->count();

        if($check > 0)
            return 1;

        return 0;
    }

    public static function get_pricing_attributes_name($pricing_id)
    {
        $attributes = PricingAttributeMapping::where('pricing_id',$pricing_id)
        ->leftJoin('nm_product_attributes','nm_product_attributes.id','=','nm_pricing_attribute_mappings.attribute_id')
        ->orderBy('attribute')
        ->get();

        $attribute_string = "";
        foreach ($attributes as $attribute) {
            // $attribute_string .= "<p><b>".$attribute->product_attribute->title."</b> : ".$attribute->product_attribute->item."</p>";
            $attribute_string .= "<b>".$attribute->product_attribute->title."</b> : ".$attribute->product_attribute->item."<br>";
        }

        return $attribute_string;
    }

    public static function get_pricing_attributes_success($attribute,$item,$product_name)
    {
        $attributes = ProductAttribute::where('attribute',$attribute)->where('attribute_item',$item)->where('pro_title_en',$product_name)
        ->leftJoin('nm_product','nm_product.pro_id','=','nm_product_attributes.pro_id')
        ->first();

       $attribute_string = "<p><b>".$attributes->title."</b> : ".$attributes->item."</p>";

        return $attribute_string;
    }

    public static function get_pricing_attributes($attribute)
    {
        $attributes = ProductAttribute::where('id',$attribute)
        ->get();

        $attribute_string = "";
        foreach ($attributes as $attribute) {
            $attribute_string .= "<b>".$attribute->title."</b> : ".$attribute->item."<br>";
        }

        return $attribute_string;
    }

    public static function delete_pricing_attribute($pricing_id)
    {
        PricingAttributeMapping::where('pricing_id','=',$pricing_id)->delete();
    }

    public static function check_pricing_attribute_selected($pricing_id, $attribute_id)
    {
        $check = PricingAttributeMapping::where('pricing_id',$pricing_id)->where('attribute_id',$attribute_id)->first();

        if($check)
            return 1;

        return 0;
    }

    //use at front product detail for listing all attributes based on pricing active pricing
    public static function get_product_attribute_listing_by_pro_id($pro_id, $country_id)
    {
        $attribute_from_active_pricing = PricingAttributeMapping::where('nm_pricing_attribute_mappings.pro_id', $pro_id)
        ->leftJoin('nm_product_pricing', 'nm_product_pricing.id', '=', 'nm_pricing_attribute_mappings.pricing_id')
        ->where('nm_product_pricing.status', 1)
        ->where('nm_product_pricing.country_id', $country_id)
        ->groupBy('attribute_id')
        ->get()
        ->pluck('attribute_id')
        ->toArray();

        $attributes = ProductAttribute::where('pro_id', $pro_id)
        ->whereIn('id', $attribute_from_active_pricing)
        ->orderBy('attribute')
        ->get()
        ->groupBy('attribute');

        $attribute_sets = [];
        $parent_array = [];
        $child_array = [];
        $index = 0;

        foreach ($attributes as $attribute => $items) {
            $attribute_item = [];
            $attr_name = '';

            foreach ($items as $item) {
                $attribute_item[$item['id']] = $item->item;
                $attr_name = $item->title;
            }

            $parent_array[$index] = [
                'name' => $attr_name,
                'items' => $attribute_item,
            ];


            $attribute_sets[$index] = $attribute_item;
            $index++;
        }

        $pricing_set = PricingAttributeMapping::where('nm_pricing_attribute_mappings.pro_id', $pro_id)
        ->leftJoin('nm_product_pricing', 'nm_product_pricing.id', '=', 'nm_pricing_attribute_mappings.pricing_id')
        ->where('nm_product_pricing.status', 1)
        ->where('nm_product_pricing.country_id', $country_id)
        ->select('pricing_id','attribute_id')
        ->get()
        ->groupBy('pricing_id')
        ->toArray();

        foreach ($pricing_set as $pricing_id => $items) {
            foreach ($items as $key => $val) {
                foreach ($attribute_sets as $attribute_id => $attribute_item) {
                    foreach ($attribute_item as $attribute_item_id => $value) {
                        if($val['attribute_id'] == $attribute_item_id)
                            $pricing_set[$pricing_id][$attribute_id] = $val['attribute_id'];
                    }
                }
            }
        }

        $array = [
            'parent' => $parent_array,
            'pricing' => json_encode($pricing_set)
        ];

        return $array;
    }

    public static function json_update_product_attribute($pro_id)
    {
        $array = [];
        $attribute_array = [];
        $countries = Country::where('co_status', '=', 1)->orderBy('co_id','asc')->get();

        foreach ($countries as $country) {
            $co_id = $country->co_id;
            $attributes = ProductPricing::where('pro_id','=',$pro_id)->where('status','=', 1)->where('country_id','=',$co_id)->whereNotNull('attributes')->pluck('attributes');

            if( count($attributes) > 0) {
                foreach ($attributes as $attribute) {
                    $lists = json_decode($attribute, true);
                    $count = 0;

                    foreach ($lists as $attribute_id => $item_id) {
                        $attribute_name = Attribute::where('id',$attribute_id)->value('name');
                        $item_name = AttributeItem::where('id',$item_id)->value('name');

                        if(!in_array($attribute_name, $attribute_array) && !in_array($item_name, $attribute_array) ) {
                            $attribute_array[$co_id][$attribute_id]['name'] = $attribute_name;
                            $attribute_array[$co_id][$attribute_id]['item'][($count == 0)? $count : $parent][$item_id] = $item_name;
                        }
                        $count++;
                        $parent = $item_id;
                    }
                }
            }

        }

        $json_attributes = null;
        if( count($attribute_array) > 0) {
            $json_attributes = json_encode($attribute_array);
        }

        $update = Product::where('pro_id',$pro_id)->update([
            'attributes' => $json_attributes,
        ]);
        return 'updated';
    }

    //use for ajax, get pricing when user change attribute selection at product details
    public static function get_pricing_by_selected_attributes($pro_id, $attributes)
    {
        $country_id = \Cookie::get('country_locale')==null?\Session::get('countryid'):\Cookie::get('country_locale');

        $pricing = PricingAttributeMapping::select('nm_product_pricing.price','nm_product_pricing.discounted_price','nm_product_pricing.discounted_rate','nm_product_pricing.id','nm_product_pricing.quantity',
        \DB::raw("
                CASE WHEN ( (((nm_product_pricing.discounted_price IS NOT NULL )) and (nm_product_pricing.discounted_price > 0 )) and (NOW() >= (CAST(nm_product_pricing.discounted_from AS DATETIME)) and NOW() <= (CAST(nm_product_pricing.discounted_to AS DATETIME))) ) THEN nm_product_pricing.discounted_price ELSE nm_product_pricing.price END AS purchase_price")
        )
        ->leftJoin('nm_product_pricing','nm_product_pricing.id','=','nm_pricing_attribute_mappings.pricing_id')
        ->where('nm_product_pricing.pro_id', $pro_id)
        ->where('nm_product_pricing.country_id', $country_id)
        ->whereIn('nm_pricing_attribute_mappings.attribute_id', $attributes)
        ->groupBy('nm_pricing_attribute_mappings.pricing_id')
        ->havingRaw('COUNT(*) = '.count($attributes))
        ->first();

        if($pricing)
            return $pricing;

        return null;
    }

    public static function get_pricing_attributes_name_json($pricing_id)
    {
        $attributes = PricingAttributeMapping::select('nm_product_attributes.attribute','nm_product_attributes.attribute_item')
        ->where('pricing_id', $pricing_id)
        ->leftJoin('nm_product_attributes','nm_product_attributes.id','=','nm_pricing_attribute_mappings.attribute_id')
        ->get()
        ->toArray();

        $response = [];
        if(!empty($attributes)) {
            $json_attributes = [];
            foreach ($attributes as $items) {
                $json_attributes[$items['attribute']] = $items['attribute_item'];
            }
            ksort($json_attributes);
            return json_encode($json_attributes);
        }

        return null;
    }

    public static function get_pricing_attribute_id_json($pricing_id)
    {
        $attribute = PricingAttributeMapping::where('pricing_id', $pricing_id)
        ->get()
        ->pluck('attribute_id')
        ->toArray();

        if(!empty($attribute)) {
            sort($attribute);
            return json_encode($attribute);
        }

        return null;
    }

    public static function check_attribute_changes_cart_and_pricing($cart_attributes_json, $pricing_attributes_json)
    {
        if($cart_attributes_json == null)
            $cart_attributes_json = json_encode([0 => null]);

        if($pricing_attributes_json == null)
            $pricing_attributes_json = json_encode([0 => null]);

        $cart_attributes = json_decode($cart_attributes_json);
        $pricing_attributes = json_decode($pricing_attributes_json);

        if($cart_attributes == $pricing_attributes)
            return 0;

        return 1;
    }

    public static function update_cart_attribute($cart_id, $pricing_id)
    {
        $attributes = self::get_pricing_attribute_id_json($pricing_id);
        $attributes_name = self::get_pricing_attributes_name_json($pricing_id);

        return Cart::where('id', $cart_id)->update([
            'attributes' => $attributes,
            'attributes_name' => $attributes_name,
        ]);
    }

    public static function update_attribute_status_flag_to_inactive($pro_id, $attribute_id)
    {
        $related_pricing = PricingAttributeMapping::where('pro_id',$pro_id)
        ->where('attribute_id', $attribute_id)
        ->get()
        ->pluck('pricing_id')
        ->toArray();

        ProductPricing::whereIn('id', $related_pricing)->update([
            'status' => 0,
            'attribute_status' => 0,
        ]);

        Cart::whereIn('pricing_id', $related_pricing)->delete();
    }

    public static function update_attribute_status_flag_to_active($pricing_id)
    {
        return ProductPricing::where('id',$pricing_id)->update([
            'attribute_status' => 1,
        ]);
    }

    //use for edit pricing attribute function, to determine each attribute set count is same for every pricing, otherwise inactive pricing and set flag to inactive
    public static function check_pricing_attribute_set($pro_id, $country_id, $attribute_count)
    {
        $attribute_sets = PricingAttributeMapping::select(\DB::raw('pricing_id, COUNT(attribute_id) as attribute_count'))
        ->where('pro_id', $pro_id)
        ->where('country_id', $country_id)
        ->groupBy('pricing_id')
        ->get()->toArray();

        $missmatch_attribute_set = [];
        $pricing_with_empty_attribute = ProductPricing::select('nm_product_pricing.id','nm_pricing_attribute_mappings.attribute_id')
        ->where('nm_product_pricing.pro_id', $pro_id)
        ->leftJoin('nm_pricing_attribute_mappings','nm_pricing_attribute_mappings.pricing_id','=','nm_product_pricing.id')
        ->whereNull('attribute_id')
        ->get()
        ->groupBy('id')
        ->toArray();

        if(!empty($pricing_with_empty_attribute)) {
            foreach ($pricing_with_empty_attribute as $price_id => $val) {
                array_push($missmatch_attribute_set, $price_id);
            }
        }

        foreach ($attribute_sets as $set) {
            if($set['attribute_count'] != $attribute_count)
                array_push($missmatch_attribute_set, $set['pricing_id']);
        }

        if(!empty($missmatch_attribute_set)) {
            ProductPricing::whereIn('id', $missmatch_attribute_set)->update([
                'status' => 0,
                'attribute_status' => 0,
            ]);

            Cart::whereIn('pricing_id', $missmatch_attribute_set)->delete();

            return 'force';
        }

        return 'success';
    }

    public static function get_attribute_item_by($attribute_id, $pro_id, $mer_id)
    {
        $attribute_name = ProductAttribute::where('id', $attribute_id)->pluck('attribute')->first();
        $results = [];
        $items = ProductAttribute::where('attribute', $attribute_name)->where('pro_id', $pro_id)->get();

        if ($items) {
            foreach ($items as $key => $item) {
                $results['attribute_name_en'] = $item->attribute;
                $results['attribute_name_cn'] = $item->attribute_cn;
                $results['attribute_name_cnt'] = $item->attribute_cnt;
                $results['items'][] = $item;
            }
        }

        return $results;
    }

    public static function add_attribute_item($data, $pro_id)
    {
        return ProductAttribute::create([
            'pro_id' => $pro_id,
            'attribute' => trim($data['attribute']),
            'attribute_cn' => trim($data['attribute_cn']),
            'attribute_cnt' => trim($data['attribute_cnt']),
            'attribute_item' => trim($data['item']),
            'attribute_item_cn' => trim($data['itemcn']),
            'attribute_item_cnt' => trim($data['itemcnt']),
        ]);
    }

    public static function update_attribute_parent($data, $pro_id)
    {
        $attribute = ProductAttribute::where('pro_id', $pro_id)->where('attribute','!=', $data['old_attribute'])->where('attribute','=', $data['attribute'])->count();

        if($attribute > 0)
            return false;

        $update = ProductAttribute::where('pro_id', $pro_id)->where('attribute', $data['old_attribute'])->update([
            'attribute' => $data['attribute'],
            'attribute_cn' => $data['attribute_cn'],
            'attribute_cnt' => $data['attribute_cnt'],
        ]);

        $pricing = ProductPricing::where('pro_id', $pro_id)->where('attributes_name', 'LIKE', '%'.$data['old_attribute'].'%')->get();
        foreach ($pricing as $key => $price) {
            $price->attributes_name = strtr($price->attributes_name, [$data['old_attribute'] => $data['attribute']]);
            $price->save();
        }

        return true;
    }

    public static function delete_product_attribute_parent($pro_id, $attribute_id, $option)
    {
        $attribute_name = ProductAttribute::where('id', $attribute_id)->pluck('attribute')->first();
        $attribute_id = ProductAttribute::where('pro_id', $pro_id)->where('attribute', $attribute_name)->pluck('id')->toArray();

        //delete all product attribute by parent
        ProductAttribute::where('pro_id', $pro_id)->whereIn('id', $attribute_id)->delete();

        //delete related attribute on product pricing
        if($option == 'force') {
            $affected_pricing = PricingAttributeMapping::where('pro_id', $pro_id)
            ->whereIn('attribute_id', $attribute_id)
            ->get()
            ->pluck('pricing_id')
            ->toArray();

            PricingAttributeMapping::where('pro_id', $pro_id)
            ->whereIn('attribute_id', $attribute_id)
            ->delete();

            foreach ($affected_pricing as $key => $price_id) {
                $price = ProductPricing::find($price_id);
                $price->status = 0;
                $price->attribute_status = 0;
                $price->attributes = self::get_pricing_attribute_id_json($price->id);
                $price->attributes_name = self::get_pricing_attributes_name_json($price->id);
                $price->save();
            }

            $check_exist = self::get_product_attribute_for_pricing($pro_id);
            $check = ProductPricing::where('pro_id', $pro_id)->where('attribute_status', 1)->count();
            if($check == 0 && $check_exist->isEmpty()) {
                ProductPricing::where('pro_id', $pro_id)->update([
                    'attribute_status' => 1,
                    'quantity' =>Product::where('pro_id',$pro_id)->value('pro_qty'),
                ]);
            }

            Cart::whereIn('pricing_id', $affected_pricing)->delete();
        }
    }

}
