<?php
namespace App\Http\Controllers\Admin;

use DB;
use App\Http\Controllers\Admin\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\ProductCategory;
use App\Models\ProductPricing;
use App\Models\Country;
use App\Models\ProductQuantityLog;
use App\Models\Merchant;
use App\Models\AdminSetting;
use Carbon\Carbon;

class MigrationController extends Controller
{
    public function product_image($limit) {
        $products = Product::where('image_migrated', 0)->take($limit)->get();

        foreach ($products as $product)
        {
            if(!empty($product->pro_Img))
            {
                $images = explode('/**/', $product->pro_Img);
                foreach($images as $key => $image)
                {
                    if (!empty($image)) {
                        ProductImage::create([
                            'pro_id' => $product->pro_id,
                            'title' => $product->pro_title_en,
                            'image' => $image,
                            'status' => 1,
                            'order' => $key+1,
                            'main' => ($key == 0)? 1 : 0, //temp set main image to first image in loop
                        ]);
                    }
                }
                $pro = Product::find($product->pro_id);
                $pro->image_migrated = 1;
                $pro->save();

                echo 'Successfully export ('. $product->pro_id.'-'.$product->pro_title_en.') image to nm_product_image<br>';
            }
        }
    }

    public function migrate_pricing($limit)
    {
        $products = Product::where('pricing_migrated','=', 0)
        ->leftJoin('nm_store','nm_store.stor_id','=','nm_product.pro_sh_id')
        ->take($limit)
        ->get();

        foreach ($products as $key => $product) {
            if(!empty($product->pro_vtoken_value) && $product->pro_vtoken_value > 0)
            {
                $country = Country::where('co_id','=', $product->stor_country)->where('co_status', '=', 1)->first();
                if($country)
                {
                    ProductPricing::create([
                        'pro_id' => $product->pro_id,
                        'country_id' => $country->co_id,
                        'currency_rate' => $country->co_rate,
                        'price' => ($product->pro_vtoken_value * $country->co_rate),
                        'discounted_price' => 0.00,
                        'discounted_from' => null,
                        'discounted_to' => null,
                        'status' => 1,
                    ]);

                    echo "Successfully export pricing : ". $product->pro_id;
                    echo "<br>";
                } else {

                    echo "Failed export : ". $product->pro_id." (stor_country not active / exsisted)";
                    echo "<br>";
                }

            } else {
                echo "Failed export : ". $product->pro_id." (empty pro_vtoken_value)";
                echo "<br>";
            }

            Product::where('pro_id','=',$product->pro_id)->update([
                'pricing_migrated' => 1,
            ]);
        }
    }

    public function product_quantity($limit) {
        $products = Product::where('quantity_migrated', 0)->take($limit)->get();

        foreach ($products as $key => $product) {

            $current = $product->pro_qty;
            ProductQuantityLog::create([
                'pro_id' => $product->pro_id,
                'credit' => $product->pro_qty,
                'current_quantity' => $current,
                'remarks' => 'Initialize product quantity',
            ]);

            if($product->pro_no_of_purchase > 0) {
                $current = $product->pro_qty - $product->pro_no_of_purchase;
                ProductQuantityLog::create([
                    'pro_id' => $product->pro_id,
                    'debit' => $product->pro_no_of_purchase,
                    'current_quantity' => $current,
                    'remarks' => 'Migrate product purchased quantity',
                    'created_at' => Carbon::now()->addSecond(2),
                ]);
            }

            Product::where('pro_id','=',$product->pro_id)->update([
                'pro_qty' => $current,
                'quantity_migrated' => 1,
            ]);

            echo 'Successfully export ('. $product->pro_id.'-'.$product->pro_title_en.') product quantity<br>';
        }
    }

    public function merchant_charges($limit)
    {
        $merchants = Merchant::where('mer_platform_charge', 0)->orWhere('mer_service_charge', 0)->take($limit)->get();
        $setting = AdminSetting::first();
        foreach ($merchants as $key => $mer) {
            $pcharge = ($mer->mer_type == 1) ? $setting->offline_platform_charge : $setting->platform_charge;
            $scharge = ($mer->mer_type == 1) ? $setting->offline_service_charge : $setting->service_charge;

            if ($mer->mer_platform_charge == 0) {
                Merchant::where('mer_id','=',$mer->mer_id)->update([
                    'mer_platform_charge' => $pcharge,
                ]);
            }

            if ($mer->mer_service_charge == 0) {
                Merchant::where('mer_id','=',$mer->mer_id)->update([
                    'mer_service_charge' => $scharge,
                ]);
            }

            echo 'Merchant ID : ' . $mer->mer_id . ' platform_charge/service_charge updated.<br>';
        }
    }
}