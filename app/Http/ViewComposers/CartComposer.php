<?php

namespace App\Http\ViewComposers;

use Cookie;
use Auth;
use Illuminate\View\View;
use App\Repositories\OrderRepo;
use App\Models\Country;

class CartComposer
{
    public function compose(View $view)
    {
        $cart_token = Cookie::get('cart_token');
        $user_id = (Auth::user()) ? Auth::user()->cus_id : 0;
        if ($user_id != 0) {
            OrderRepo::update_token($cart_token, $user_id);
            OrderRepo::update_user($cart_token, $user_id);
        }

        $co_id = (session('countryid')) ? session('countryid') : '';

        $co_cursymbol = Country::where('co_id','=',$co_id)->value('co_cursymbol');
        $co_rate = Country::where('co_id','=',$co_id)->value('co_rate');

        $commission = [
            'platform_charge' => round(\Config::get('settings.platform_charge')),
            'service_charge' => round(\Config::get('settings.service_charge')),
        ];

        $grandtotal_price = 0.00;
        $grandtotal_credit = 0.00;
        $merchantcharge_total = 0.00;
        $servicecharge_total = 0.00;
        $shippingfees_total = 0.00;
        $shippingfees_total_price = 0.00;

        $carts = OrderRepo::get_shopping_carts($cart_token, $user_id);

        if ($carts) {
            foreach ($carts as $key => $c) {
                if($c['pricing']) {
                    $product_quantity = $c['cart']->quantity;

                    $price = $c['pricing']->price;

                    if ( $c['pricing']->discounted_price > 0.00) {
                        $today = new \DateTime;
                        $discounted_from = new \DateTime($c['pricing']->discounted_from);
                        $discounted_to = new \DateTime($c['pricing']->discounted_to);

                        if (($today >= $discounted_from) && ($today <= $discounted_to))
                        {
                            $price = $c['pricing']->discounted_price;
                        }
                    }

                    // shipping fees
                    if ($c['pricing']->shipping_fees_type == 1) {
                        $shippingfees = round(($c['pricing']->shipping_fees * $product_quantity), 2);
                    } elseif ($c['pricing']->shipping_fees_type == 2) {
                        $shippingfees = round(($c['pricing']->shipping_fees), 2);
                    } else {
                        $shippingfees = 0.00;
                    }
                    $shippingfees_credit = round(($shippingfees / $co_rate), 4);

                    $purchasing_price = round(($price * $product_quantity), 2);
                    $product_credit = round(($purchasing_price / $co_rate), 4);

                    $platform_charge_value = round(($product_credit * ($c['cart']->mer_platform_charge/100)), 4);
                    $service_charge_value = round((($product_credit + $platform_charge_value) * ($c['cart']->mer_service_charge/100)), 4);
                    $purchasing_credit = round(($product_credit + $platform_charge_value + $service_charge_value + $shippingfees_credit), 4);

                    $grandtotal_price += $purchasing_price;
                    $grandtotal_credit += $product_credit;
                    $merchantcharge_total += $platform_charge_value;
                    $servicecharge_total += $service_charge_value;
                    $shippingfees_total += $shippingfees_credit;
                    $shippingfees_total_price += $shippingfees;

                    $c['pricing']->product_price = $price;
                    $c['pricing']->purchasing_price = $purchasing_price;
                    $c['pricing']->platform_charge = $platform_charge_value;

                    $c['pricing']->product_credit = $product_credit;
                    $c['pricing']->purchasing_credit = $purchasing_credit;
                    $c['pricing']->service_charge = $service_charge_value;
                    $c['pricing']->product_shippingfees = $shippingfees;
                    $c['pricing']->product_shippingfees_credit = $shippingfees_credit;
                }
            }
        }

        $view->with('carts', $carts)->with('commission',$commission)->with('co_cursymbol', $co_cursymbol)->with('grandtotal_price',$grandtotal_price)->with('merchantcharge_total',$merchantcharge_total)->with('grandtotal_credit',$grandtotal_credit)->with('servicecharge_total',$servicecharge_total)->with('shippingfees_total',$shippingfees_total)->with('shippingfees_total_price',$shippingfees_total_price);
    }
}