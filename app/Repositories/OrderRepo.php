<?php

namespace App\Repositories;
use DB;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\Merchant;
use App\Models\Shipping;
use App\Models\ProductImage;
use App\Models\ProductPricing;
use App\Models\Country;
use App\Models\MerchantVTokenLog;
use App\Models\StoreUserMapping;
use App\Repositories\LimitRepo;
use Session;
use Carbon\Carbon;

class OrderRepo
{
    public static function update_token($token, $uid)
    {
        return Cart::where('cus_id', '=', $uid)->update(['token' => $token]);
    }

    public static function update_user($token, $uid)
    {
        return Cart::where('token', '=', $token)->whereNull('cus_id')->update(['cus_id' => $uid]);
    }

    public static function get_shopping_carts($token, $uid)
    {
        if (!empty($token) || $uid > 0 ) {
            $cid = session('countryid');
            $shoppingcarts = array();
            $carts = new Cart;
            $carts = $carts->select('temp_cart.*','nm_product.*', 'nm_merchant.mer_platform_charge', 'nm_merchant.mer_service_charge','temp_cart.attributes');
            $carts = $carts->leftJoin('nm_product', 'nm_product.pro_id', '=', 'temp_cart.product_id');
            $carts = $carts->where('nm_product.pro_status', '=', 1);
            $carts = $carts->leftJoin('nm_product_pricing','nm_product_pricing.id','=','temp_cart.pricing_id');
            $carts = $carts->where('nm_product_pricing.country_id','=',$cid);
            $carts = $carts->where('nm_product_pricing.status','=', 1);
            $carts = $carts->leftJoin('nm_store', 'nm_store.stor_id', '=', 'nm_product.pro_sh_id');
            $carts = $carts->where('nm_store.stor_status', '=', 1);
            $carts = $carts->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_product.pro_mr_id');
            $carts = $carts->where('nm_merchant.mer_staus', '=', 1);

            $carts = $carts->where(function($q) use ($token, $uid) {
                if (!empty($token)) {
                    $q->orWhere('token', '=', $token);
                }

                if ($uid != 0) {
                    $q->orWhere('cus_id', '=', $uid);
                }
            });

            $carts = $carts->get();

            foreach ($carts as $key => $cart) {
                $shoppingcarts[$key]['product'] = Product::where('pro_id', '=', $cart->product_id)->first();
                $shoppingcarts[$key]['main_image'] = ProductImage::where('pro_id', '=', $cart->product_id)->orderBy('main', 'desc')->orderBy('order', 'asc')->first();
                $pricing = ProductPricing::where('id', '=', $cart->pricing_id)->where('pro_id','=',$cart->product_id)->first();
                $pricing->attributes = AttributeRepo::get_pricing_attribute_id_json($pricing->id);
                $pricing->attributes_name = AttributeRepo::get_pricing_attributes_name($pricing->id);
                $shoppingcarts[$key]['pricing'] = $pricing;
                $cart->attribute_changes = AttributeRepo::check_attribute_changes_cart_and_pricing($cart->attributes, $pricing->attributes);
                $shoppingcarts[$key]['cart'] = $cart;
                //only triggered when no errors then pricing can be updated
                if(empty(Session::get('errors')))
                    self::update_cart_price($pricing, $cart->id);
            }
            return $shoppingcarts;
        }

        return array();
    }

    public static function check_cart_quantity($token, $uid, $cid)
    {
        $carts = Cart::select('temp_cart.*', 'nm_product.pro_title_en', 'nm_product_pricing.quantity as curr_qty', 'nm_product.end_date as expired_at', 'nm_product.pro_type as product_type', 'nm_product.pro_id', 'nm_store.accept_payment')
        ->leftJoin('nm_product', 'nm_product.pro_id', '=', 'temp_cart.product_id')
        ->leftJoin('nm_product_pricing','nm_product_pricing.id','=','temp_cart.pricing_id')
        ->leftJoin('nm_store', 'nm_store.stor_id', '=', 'nm_product.pro_sh_id')
        ->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_product.pro_mr_id')
        ->where('nm_product.pro_status', '=', 1)
        ->where('nm_product_pricing.country_id','=',$cid)
        ->where('nm_product_pricing.status','=', 1)
        ->where('nm_store.stor_status', '=', 1)
        ->where('nm_merchant.mer_staus', '=', 1)
        ->where(function($q) use ($token, $uid) {
            if (!empty($token)) {
                $q->orWhere('token', '=', $token);
            }

            if ($uid != 0) {
                $q->orWhere('cus_id', '=', $uid);
            }
        })->get();

        $response = array();
        foreach ($carts as $key => $cart) {
            $total_pro_quantity_in_cart = (int)$cart->quantity;
            $total_pro_quantity = (int)$cart->curr_qty;

            $response[$key] = [
                'pro_title' => $cart->pro_title_en,
                'total_quantity_in_cart' => $total_pro_quantity_in_cart,
                'pro_qty' => $total_pro_quantity,
                'status' => ($total_pro_quantity_in_cart<1 || $total_pro_quantity_in_cart > $total_pro_quantity) ? 0 : 1,
                'expired' => ($cart->product_type == 3 && !empty($cart->expired_at) && Carbon::now('UTC') >= Carbon::parse($cart->expired_at))? true : false,
                'pro_id' => $cart->pro_id,
                'accept_payment' => $cart->accept_payment,
                'cart_id' => $cart->id,
                'exceed' => [
                    'productLimit' => $uid? LimitRepo::check_payment_limitation('productLimit', $total_pro_quantity_in_cart, null, $uid, $cart->pro_id) : false
                ],
            ];
        }

        return $response;
    }

    public static function check_exist($token, $uid, $data)
    {
        return Cart::where('product_id', '=', $data['product_id'])
            ->where('pricing_id','=',$data['price_id'])
            ->where('remarks', '=', $data['remarks'])
            ->where(function($q) use ($token, $uid) {
                if (isset($uid))
                    $q->where('cus_id', '=', $uid);
                else
                    $q->where('token', '=', $token);
            })->first();
    }

    public static function get_quantity_of_product($token, $uid, $pro_id)
    {
        $total = Cart::select('pricing_id','quantity')->where('product_id', '=', $pro_id)
        ->where(function($q) use ($token, $uid) {
            if (isset($uid))
                $q->where('cus_id', '=', $uid);
            else
                $q->where('token', '=', $token);
        })
        ->get();

        $total_array = [];
        foreach ($total as $key => $price) {
            $total_array[$price->pricing_id] = $price->quantity;
        }

        return $total_array;
    }

    public static function update($id, $qty)
    {
        return Cart::where('id', '=', $id)->update(['quantity' => $qty]);
    }

    public static function delete($id)
    {
        return Cart::where('id', '=', $id)->delete();
    }

    public static function check_trans_id($id)
    {
        return Order::where('transaction_id', '=', $id)->first();
    }

    public static function get_carts_total($token, $uid, $payment_type)
    {
        $cid = session('countryid');
        $shoppingcarts = array();

        $carts = Cart::select('temp_cart.*', 'nm_product.pro_title_en', 'nm_product.pro_qty', 'nm_merchant.mer_platform_charge', 'nm_merchant.mer_service_charge')
        ->leftJoin('nm_product', 'nm_product.pro_id', '=', 'temp_cart.product_id')
        ->leftJoin('nm_product_pricing','nm_product_pricing.id','=','temp_cart.pricing_id')
        ->leftJoin('nm_store', 'nm_store.stor_id', '=', 'nm_product.pro_sh_id')
        ->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_product.pro_mr_id')
        ->where('nm_product.pro_status', '=', 1)
        ->where('nm_product_pricing.country_id','=',$cid)
        ->where('nm_product_pricing.status','=', 1)
        ->where('nm_store.stor_status', '=', 1)
        ->where('nm_merchant.mer_staus', '=', 1)
        ->where(function($q) use ($token, $uid) {
            if (!empty($token)) {
                $q->orWhere('token', '=', $token);
            }

            if ($uid != 0) {
                $q->orWhere('cus_id', '=', $uid);
            }
        })->get();

        $co_rate = Country::where('co_id','=',$cid)->value('co_rate');
        $platform_charge = round(\Config::get('settings.platform_charge'));
        $service_charge = round(\Config::get('settings.service_charge'));
        $grandtotal_price = 0;
        $grandtotal_credit = 0;
        $total = 0;
        $cart_grandtotal_price = 0;
        $cart_grandtotal_credit = 0;
        $cart_total = 0;

        $total_for_wallet_type = [];

        foreach ($carts as $key => $cart) {
            $pro_price = ProductPricing::where('id', '=', $cart->pricing_id)->where('pro_id','=',$cart->product_id)->first();
            $wallet_id = WalletRepo::get_wallet_id_by_product_id_based_on_product_category($cart->product_id);

            $quantity = $cart->quantity;
            $price = $pro_price->price;

            if ( $pro_price->discounted_price > 0.00) {
                $today = new \DateTime;
                $discounted_from = new \DateTime($pro_price->discounted_from);
                $discounted_to = new \DateTime($pro_price->discounted_to);

                if (($today >= $discounted_from) && ($today <= $discounted_to))
                {
                    $price = $pro_price->discounted_price;
                }
            }

            // shipping fees
            if ($pro_price->shipping_fees_type == 1) {
                $shippingfees = round(($pro_price->shipping_fees * $quantity), 2);
            } elseif ($pro_price->shipping_fees_type == 2) {
                $shippingfees = round(($pro_price->shipping_fees), 2);
            } else {
                $shippingfees = 0.00;
            }
            $shippingfees_credit = round(($shippingfees / $co_rate), 4);

            $purchasing_price = round(($price * $quantity), 2);
            $product_credit = round(($purchasing_price / $co_rate), 4);

            $platform_charge_value = round($product_credit * ($cart->mer_platform_charge/100), 4);
            $service_charge_value = round($product_credit * ($cart->mer_service_charge/100), 4);

            $purchasing_credit = round(($product_credit + $platform_charge_value + $service_charge_value + $shippingfees_credit), 4);

            $grandtotal_price += $purchasing_price;
            $grandtotal_credit += $purchasing_credit;

            // cart
            $cart_purchasing_price = round(($cart->product_price * $quantity), 2);
            $cart_product_credit = round(($cart_purchasing_price / $co_rate), 4);

            $cart_platform_charge_value = round($cart_product_credit * ($cart->mer_platform_charge/100), 4);
            $cart_service_charge_value = round($cart_product_credit * ($cart->mer_service_charge/100), 4);

            $cart_purchasing_credit = round(($cart_product_credit + $cart_platform_charge_value + $cart_service_charge_value + $shippingfees_credit), 4);

            $cart_grandtotal_price += ($cart_purchasing_price + $shippingfees);
            $cart_grandtotal_credit += $cart_purchasing_credit;

            if(array_key_exists($wallet_id, $total_for_wallet_type)) {
                $total_for_wallet_type[$wallet_id] += $cart_purchasing_credit;
            } else {
                $total_for_wallet_type[$wallet_id] = $cart_purchasing_credit;
            }
        }

        if($payment_type == 'credit') {
            $total = $grandtotal_credit;
            $cart_total = $cart_grandtotal_credit;
        } else if ($payment_type == 'cash') {
            $total = $grandtotal_price;
            $cart_total = $cart_grandtotal_price;
        }

        $response = array();
        $response = [
            'from_pricing' => $total,
            'from_cart' => $cart_total,
            'total_by_wallet' => $total_for_wallet_type,
            'price_credit' => round($cart_grandtotal_price / $co_rate, 2),
        ];

        return $response;
    }

    public static function get_order_by_id($id)
    {
        return $order = Order::leftjoin('nm_product', 'nm_product.pro_id', '=', 'nm_order.order_pro_id')
        ->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_product.pro_mr_id')
        ->where('nm_order.order_id', '=', $id)->first();
    }

    public static function update_order_status($id, $status)
    {
        return $order = Order::where('order_id', $id)
        ->update(array('order_status' => $status));
    }

    public static function update_order_shipment($data,$id)
    {
        return $order = Order::where('order_id', '=', $id)
        ->update($data);
    }

    public static function update_merchant_order($order_id, $mer_id, $mer_vtoken, $order_vtoken)
    {
        $merchant = Merchant::where('mer_id', '=', $mer_id)->update(['mer_vtoken' => ($mer_vtoken + $order_vtoken)]);

        $merchant_vlog = new MerchantVTokenLog;
        $merchant_vlog->mer_id = $mer_id;
        $merchant_vlog->credit_amount = $order_vtoken;
        $merchant_vlog->debit_amount = 0;
        $merchant_vlog->order_id = $order_id;
        $merchant_vlog->remark = 'Order Delivered';
        $merchant_vlog->created_at = date('Y-m-d H:i:s');
        $merchant_vlog->updated_at = date('Y-m-d H:i:s');
        $merchant_vlog->save();

        return true;
    }

    public static function completing_merchant_order($order_id, $order_tracking_no = null)
    {
        $order = Order::leftJoin('nm_product', 'nm_order.order_pro_id', '=', 'nm_product.pro_id')->where('order_id', '=', $order_id)->first();

        if(!is_null($order))
        {
            OrderRepo::update_order_status($order_id, 4);
            if(!is_null($order_tracking_no))
                OrderRepo::update_order_tracking_no($order_id, $order_tracking_no);

            if ($order->currency_rate > 0) {
                $original_vtoken = round((($order->product_original_price * $order->order_qty) / $order->currency_rate), 4);
            } else {
                $original_vtoken = $order->order_vtokens;
            }

            $merchant = Merchant::where('mer_id', '=', $order->pro_mr_id)->first();
            $credit_amount = $original_vtoken - $order->merchant_charge_vtoken;
            $merchant->mer_vtoken = $merchant->mer_vtoken + $credit_amount + $order->total_product_shipping_fees_credit;
            $merchant->save();

            $merchant_vlog = new MerchantVTokenLog;
            $merchant_vlog->mer_id = $merchant->mer_id;
            $merchant_vlog->credit_amount = $credit_amount;
            $merchant_vlog->debit_amount = 0;
            $merchant_vlog->order_id = $order_id;
            $merchant_vlog->remark = 'Order Delivered';
            $merchant_vlog->created_at = date('Y-m-d H:i:s');
            $merchant_vlog->updated_at = date('Y-m-d H:i:s');
            $merchant_vlog->save();

            if($order->total_product_shipping_fees_credit>0)
            {
                $merchant_vlog = new MerchantVTokenLog;
                $merchant_vlog->mer_id = $merchant->mer_id;
                $merchant_vlog->credit_amount = $order->total_product_shipping_fees_credit;
                $merchant_vlog->debit_amount = 0;
                $merchant_vlog->order_id = $order_id;
                $merchant_vlog->remark = 'Shipping Fees';
                $merchant_vlog->created_at = date('Y-m-d H:i:s');
                $merchant_vlog->updated_at = date('Y-m-d H:i:s');
                $merchant_vlog->save();
            }

            $merchant = Merchant::where('mer_id', '=', 180)->first();
            $credit_amount = $order->merchant_charge_vtoken + $order->cus_service_charge_value + $order->cus_platform_charge_value;
            $merchant->mer_vtoken = $merchant->mer_vtoken + $credit_amount;
            $merchant->save();

            $merchant_vlog = new MerchantVTokenLog;
            $merchant_vlog->mer_id = $merchant->mer_id;
            $merchant_vlog->credit_amount = $order->merchant_charge_vtoken;
            $merchant_vlog->debit_amount = 0;
            $merchant_vlog->order_id = $order_id;
            $merchant_vlog->remark = 'Order Commission';
            $merchant_vlog->created_at = date('Y-m-d H:i:s');
            $merchant_vlog->updated_at = date('Y-m-d H:i:s');
            $merchant_vlog->save();

            if($order->cus_service_charge_value>0)
            {
                $merchant_vlog = new MerchantVTokenLog;
                $merchant_vlog->mer_id = $merchant->mer_id;
                $merchant_vlog->credit_amount = $order->cus_service_charge_value;
                $merchant_vlog->debit_amount = 0;
                $merchant_vlog->order_id = $order_id;
                $merchant_vlog->remark = 'Order Service Charge';
                $merchant_vlog->created_at = date('Y-m-d H:i:s');
                $merchant_vlog->updated_at = date('Y-m-d H:i:s');
                $merchant_vlog->save();
            }

            if($order->cus_platform_charge_value>0)
            {
                $merchant_vlog = new MerchantVTokenLog;
                $merchant_vlog->mer_id = $merchant->mer_id;
                $merchant_vlog->credit_amount = $order->cus_platform_charge_value;
                $merchant_vlog->debit_amount = 0;
                $merchant_vlog->order_id = $order_id;
                $merchant_vlog->remark = 'Order Platform Charge';
                $merchant_vlog->created_at = date('Y-m-d H:i:s');
                $merchant_vlog->updated_at = date('Y-m-d H:i:s');
                $merchant_vlog->save();
            }

            return true;
        }
        return false;
    }

    public static function get_orders_by_status($mer_id, $type, $input = null)
    {
        $order = Order::select(
            'nm_order.*', 'nm_order.updated_at as updated_at', 'nm_order.created_at as created_at',
            // 'nm_shipping.*',
            'nm_country.co_name',
            'nm_customer.cus_id', 'nm_customer.cus_name',
            'nm_product.pro_id', 'nm_product.pro_title_en','nm_product.pro_mr_id',
            'nm_merchant.mer_id', 'nm_merchant.mer_fname', 'nm_merchant.mer_lname',
            'nm_store.stor_id', 'nm_store.stor_name'
        )
        ->leftJoin('nm_customer', 'nm_order.order_cus_id', '=', 'nm_customer.cus_id')
        ->leftJoin('nm_product', 'nm_order.order_pro_id', '=', 'nm_product.pro_id')
        ->leftJoin('nm_merchant', 'nm_product.pro_mr_id', '=', 'nm_merchant.mer_id')
        ->leftJoin('nm_shipping', 'nm_order.order_id', '=', 'nm_shipping.ship_order_id')
        ->leftJoin('nm_country','nm_country.co_id', '=', 'nm_shipping.ship_country')
        ->leftJoin('nm_state','nm_state.id', '=', 'nm_shipping.ship_state_id')
        ->leftJoin('nm_store','nm_store.stor_id', '=', 'nm_product.pro_sh_id')
        ->where('nm_order.order_type', '=', $type);

        if (isset($input['admin_country_id_list'])) {
            $order->whereIn('nm_store.stor_country', $input['admin_country_id_list']);
        }

        if(\Auth::guard('storeusers')->check()) {
            $assigned_stores = StoreUserMapping::where('storeuser_id','=',\Auth::guard('storeusers')->user()->id)->pluck('store_id')->toArray();
            $order->whereIn('nm_store.stor_id', $assigned_stores);
        }

        if($mer_id != 'all')
            $order->where('nm_product.pro_mr_id', '=', $mer_id);

        if (!empty($input['id'])) {
            $order->where(function($query) use ($input) {
                $query->where('nm_order.transaction_id', '=', $input['id'])
                ->orWhere('nm_order.order_id', '=', $input['id'])
                ->orWhere('nm_product.pro_id', '=', $input['id'])
                ->orWhere('nm_customer.cus_id', '=', $input['id']);
            });
        }

        if (!empty($input['tid'])) {
            $order->where('nm_order.transaction_id', 'LIKE', '%'.$input['tid'].'%');
        }

        if (!empty($input['oid'])) {
            $order->Where('nm_order.order_id', '=', $input['oid']);
        }

        if (!empty($input['pid']))
            $order->where('nm_product.pro_id', '=', $input['pid']);

        if (!empty($input['cid']))
            $order->where('nm_customer.cus_id', '=', $input['cid']);

        if (!empty($input['mid']))
            $order->where('nm_merchant.mer_id', '=', $input['mid']);

        if (!empty($input['sid']))
            $order->where('nm_store.stor_id', '=', $input['sid']);

        if(!empty($input['merchant_countries'])) {
            $order->where(function($query) use ($input){
                $query->whereIn('nm_merchant.mer_co_id', $input['merchant_countries']);
            });
        }

        if(!empty($input['customer_countries'])) {
            $order->where(function($query) use ($input){
                $query->whereIn('nm_customer.cus_country', $input['customer_countries']);
            });
        }

        if (!empty($input['name'])) {
            $title = '%'.$input['name'].'%';
            $order->where(function($query) use ($title) {
                $query->whereRaw('nm_product.pro_title_en LIKE ? or nm_product.pro_title_cn LIKE ? or nm_product.pro_title_cnt LIKE ? or nm_product.pro_title_my LIKE ?', [$title, $title, $title, $title]);
            });
        }

        if (!empty($input['start']) && !empty($input['end'])) {
            $input['start'] = Carbon::createFromFormat('d/m/Y', $input['start'])->startOfDay()->toDateTimeString();
            $input['end'] = Carbon::createFromFormat('d/m/Y', $input['end'])->endOfDay()->toDateTimeString();

            $order->where('nm_order.order_date', '>=', \Helper::TZtoUTC($input['start']));
            $order->where('nm_order.order_date', '<=', \Helper::TZtoUTC($input['end']));
        }

        if (!empty($input['status']))
                $order->where('nm_order.order_status', '=', $input['status']);

        if (!empty($input['sort'])) {
            switch ($input['sort']) {
                case 'name_asc':
                    $order->orderBy('nm_product.pro_title_en');
                    break;
                case 'name_desc':
                    $order->orderBy('nm_product.pro_title_en', 'desc');
                    break;
                case 'new':
                    $order->orderBy('nm_order.order_id', 'desc');
                    break;
                case 'old':
                    $order->orderBy('nm_order.order_id', 'asc');
                    break;
            }
        }
        else {
            $order->orderBy('nm_order.order_id', 'desc');
        }

        if (!empty($input['action']) && $input['action'] == 'export')
            return $order->get();

        return $order->paginate(50);
    }

    // public static function get_all_product_orders_by_status($status, $mer_id, $input)
    // {
    //     $orders = Order::orderBy('order_date', 'desc')
    //     ->leftjoin('nm_customer','nm_order.order_cus_id','=','nm_customer.cus_id')
    //     ->leftjoin('nm_product','nm_order.order_pro_id','=','nm_product.pro_id')
    //     ->where('nm_order.order_type','=', 1);

    //     if($mer_id != 'all'){
    //        $orders->where('nm_product.pro_mr_id', '=', $mer_id);
    //     }
    //     if ($status != '') {
    //        $orders->where('nm_order.order_status','=', $status);
    //     }

    //     if (!empty($input)) {
    //        $orders->where(function($q) use ($input) {
    //             $orders->where('nm_product.pro_title_en', 'LIKE', '%'.$input.'%')
    //             ->orWhere('nm_customer.cus_name', 'LIKE', '%'.$input.'%')
    //             ->orWhere('nm_order.transaction_id', 'LIKE', '%'.$input.'%');
    //        });
    //     }

    //     return $orders->paginate(50);
    // }

    public static function get_all_deal_orders_by_status($status, $mer_id, $input)
	{
       $orders = Order::orderBy('order_date', 'desc')
        ->leftjoin('nm_customer','nm_order.order_cus_id','=','nm_customer.cus_id')
		->leftjoin('nm_deals','nm_order.order_pro_id','=','nm_deals.deal_id')
		->where('nm_order.order_type','=',2);

        if($mer_id != 'all'){
           $orders->where('nm_product.pro_mr_id', '=', $mer_id);
        }
        if (!empty($status)) {
           $orders->where('order_status','=', $status);
        }

        if (!empty($input)) {
           $orders->where(function($q) use ($input) {
                $orders->where('nm_product.pro_title_en', 'LIKE', '%'.$input.'%')
                ->orWhere('nm_customer.cus_name', 'LIKE', '%'.$input.'%')
                ->orWhere('nm_order.transaction_id', 'LIKE', '%'.$input.'%');
           });
        }

        return $orders->get();
	}

    public static function get_order_details($order_id)
    {
        $order = Order::selectRaw('
            nm_order.*, nm_product.*, nm_customer.*, nm_courier.*,
            nm_order.created_at as order_created_at,
            concat_ws(" ", nm_merchant.mer_fname, nm_merchant.mer_lname) as merchant_name,
            nm_merchant.mer_id,
            nm_merchant.mer_address1,
            nm_merchant.mer_address2,
            nm_merchant.zipcode,
            nm_merchant.mer_city_name as mer_city,
            nm_state.name as mer_state,
            nm_country.co_name as mer_country
        ')
        ->leftJoin('nm_product', 'nm_order.order_pro_id', '=', 'nm_product.pro_id')
        ->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_product.pro_mr_id')
        ->leftJoin('nm_state', 'nm_state.id', '=', 'nm_merchant.mer_state')
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_merchant.mer_co_id')
        ->leftJoin('nm_customer', 'nm_order.order_cus_id', '=', 'nm_customer.cus_id')
        ->leftJoin('nm_courier', 'nm_order.order_courier_id', '=', 'nm_courier.id')
        ->where('nm_order.order_id', '=', $order_id);

        if (\Auth::guard('merchants')->check()) {
            $mer_id = \Auth::guard('merchants')->user()->mer_id;
            $order->where('nm_product.pro_mr_id', '=', $mer_id);
        }
        // dd($order->toSql());
        return $order->first();
    }

    public static function get_shipping_address($order_id)
    {
        $ship = Shipping::where('nm_shipping.ship_order_id', '=', $order_id)
        ->leftJoin('nm_city','nm_city.ci_id', '=', 'nm_shipping.ship_ci_id')
        ->leftJoin('nm_state','nm_state.id', '=', 'nm_shipping.ship_state_id')
        ->leftJoin('nm_country','nm_country.co_id', '=', 'nm_shipping.ship_country');

        return $ship->first();
    }

    public static function get_shipment_detail($order_id)
    {
        $shipment = Order::where('nm_order.order_id', '=', $order_id)
        ->leftjoin('nm_courier', 'nm_order.order_courier_id', '=', 'nm_courier.id');

        return $shipment->first();
    }

    public static function get_product_quantity_sold($pro_id)
    {
        return Order::where('order_pro_id','=',$pro_id)->where('order_status','=',4)->get()->sum('order_qty');
    }

    public static function update_cart_price($pricing,$cart_id)
    {
        $price = $pricing->price;
        if ( $pricing->discounted_price > 0.00) {
            $today = new \DateTime;
            $discounted_from = new \DateTime($pricing->discounted_from);
            $discounted_to = new \DateTime($pricing->discounted_to);

            if (($today >= $discounted_from) && ($today <= $discounted_to))
            {
                $price = $pricing->discounted_price;
            }
        }

        $update_cart = Cart::find($cart_id)->update([
            'purchasing_price' => $price,
            'product_price' => $price,
        ]);
    }

    public static function check_cart_and_pricing_attribute($token, $uid)
    {
        $cid = \Cookie::get('country_locale');
        $shoppingcarts = array();
        $carts = new Cart;
        $carts = $carts->select('temp_cart.id','temp_cart.attributes as cart_attribute_json','nm_product_pricing.id as pricing_id');
        $carts = $carts->leftJoin('nm_product', 'nm_product.pro_id', '=', 'temp_cart.product_id');
        $carts = $carts->where('nm_product.pro_status', '=', 1);
        $carts = $carts->leftJoin('nm_product_pricing','nm_product_pricing.id','=','temp_cart.pricing_id');
        $carts = $carts->where('nm_product_pricing.country_id','=',$cid);
        $carts = $carts->where('nm_product_pricing.status','=', 1);
        $carts = $carts->leftJoin('nm_store', 'nm_store.stor_id', '=', 'nm_product.pro_sh_id');
        $carts = $carts->where('nm_store.stor_status', '=', 1);
        $carts = $carts->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_product.pro_mr_id');
        $carts = $carts->where('nm_merchant.mer_staus', '=', 1);

        $carts = $carts->where(function($q) use ($token, $uid) {
            if (!empty($token)) {
                $q->orWhere('token', '=', $token);
            }

            if ($uid != 0) {
                $q->orWhere('cus_id', '=', $uid);
            }
        });

        $carts = $carts->get();

        $count = 0;
        foreach ($carts as $key => $cart) {

            $from_pricing = (array)json_decode(AttributeRepo::get_pricing_attribute_id_json($cart->pricing_id),true);
            ksort($from_pricing);

            $from_cart = (array)json_decode($cart->cart_attribute_json,true);
            ksort($from_cart);

            $check_diff = array_diff($from_pricing, $from_cart);
            if(!empty($check_diff))
                $count++;
        }

        return $count;

    }

    public static function get_online_total_transaction($mer_id, $type, $input = null)
    {
        $order = Order::query();

        if ($mer_id != 'all') {
            $order->select(\DB::raw("SUM(order_vtokens - cus_service_charge_value - cus_platform_charge_value) as total_credit, SUM(merchant_charge_vtoken) as merchant_charge, SUM(order_vtokens - merchant_charge_vtoken - cus_service_charge_value - cus_platform_charge_value) as merchant_earned, SUM(total_product_shipping_fees_credit) as shipping_fees"))
            // ->leftJoin('nm_product', 'nm_product.pro_id', '=', 'nm_order.order_pro_id')
            ->where('nm_product.pro_mr_id', $mer_id);
        } else {
            $order->select(\DB::raw("SUM(order_vtokens) as total_credit, SUM(cus_platform_charge_value) as transaction_fees, SUM(cus_service_charge_value) as service_fees, SUM(merchant_charge_vtoken) as merchant_charge, SUM(order_vtokens - merchant_charge_vtoken - cus_service_charge_value - cus_platform_charge_value) as merchant_earned, SUM(total_product_shipping_fees_credit) as shipping_fees"));
        }

        $order->leftJoin('nm_customer', 'nm_order.order_cus_id', '=', 'nm_customer.cus_id')
        ->leftJoin('nm_product', 'nm_order.order_pro_id', '=', 'nm_product.pro_id')
        ->leftJoin('nm_merchant', 'nm_product.pro_mr_id', '=', 'nm_merchant.mer_id')
        ->leftJoin('nm_shipping', 'nm_order.order_id', '=', 'nm_shipping.ship_order_id')
        ->leftJoin('nm_country','nm_country.co_id', '=', 'nm_shipping.ship_country')
        ->leftJoin('nm_state','nm_state.id', '=', 'nm_shipping.ship_state_id')
        ->leftJoin('nm_store','nm_store.stor_id', '=', 'nm_product.pro_sh_id')
        ->where('nm_order.order_type', '=', $type);

        if (isset($input['admin_country_id_list'])) {
            $order->whereIn('nm_store.stor_country', $input['admin_country_id_list']);
        }

        if(\Auth::guard('storeusers')->check()) {
            $assigned_stores = StoreUserMapping::where('storeuser_id','=',\Auth::guard('storeusers')->user()->id)->pluck('store_id')->toArray();
            $order->whereIn('nm_store.stor_id', $assigned_stores);
        }

        if (!empty($input['id'])) {
            $order->where(function($query) use ($input) {
                $query->where('nm_order.transaction_id', '=', $input['id'])
                ->orWhere('nm_order.order_id', '=', $input['id'])
                ->orWhere('nm_product.pro_id', '=', $input['id'])
                ->orWhere('nm_customer.cus_id', '=', $input['id']);
            });
        }

        if (!empty($input['tid'])) {
            $order->where(function($query) use ($input) {
                $query->where('nm_order.transaction_id', $input['tid'])
                ->orWhere('nm_order.order_id', $input['tid']);
            });
        }

        if (!empty($input['pid']))
            $order->where('nm_product.pro_id', '=', $input['pid']);

        if (!empty($input['cid']))
            $order->where('nm_customer.cus_id', '=', $input['cid']);

        if (!empty($input['mid']))
            $order->where('nm_merchant.mer_id', '=', $input['mid']);

        if(!empty($input['merchant_countries'])) {
            $order->where(function($query) use ($input){
                $query->whereIn('nm_merchant.mer_co_id', $input['merchant_countries']);
            });
        }

        if(!empty($input['customer_countries'])) {
            $order->where(function($query) use ($input){
                $query->whereIn('nm_customer.cus_country', $input['customer_countries']);
            });
        }

        if (!empty($input['name'])) {
            $title = '%'.$input['name'].'%';
            $order->where(function($query) use ($title) {
                $query->whereRaw('nm_product.pro_title_en LIKE ? or nm_product.pro_title_cn LIKE ? or nm_product.pro_title_cnt LIKE ? or nm_product.pro_title_my LIKE ?', [$title, $title, $title, $title]);
            });
        }

        if (!empty($input['start']) && !empty($input['end'])) {
            $input['start'] = Carbon::createFromFormat('d/m/Y', $input['start'])->startOfDay()->toDateTimeString();
            $input['end'] = Carbon::createFromFormat('d/m/Y', $input['end'])->endOfDay()->toDateTimeString();

            $order->where('nm_order.order_date', '>=', \Helper::TZtoUTC($input['start']));
            $order->where('nm_order.order_date', '<=', \Helper::TZtoUTC($input['end']));
        }

        if (!empty($input['status']))
            $order->where('nm_order.order_status', '=', $input['status']);

        if (!empty($input['sort'])) {
            switch ($input['sort']) {
                case 'name_asc':
                    $order->orderBy('nm_product.pro_title_en');
                    break;
                case 'name_desc':
                    $order->orderBy('nm_product.pro_title_en', 'desc');
                    break;
                case 'new':
                    $order->orderBy('nm_order.order_date', 'desc');
                    break;
                case 'old':
                    $order->orderBy('nm_order.order_date', 'asc');
                    break;
            }
        }
        else {
            $order->orderBy('nm_order.order_date', 'desc');
        }


        return $order->first();
    }

    public static function update_batch_status_transaction($operation, $order_id)
    {

        $orders = Order::query();
        switch ($operation) {
            case 'accept_order':
                $orders->where('order_status', 1);
                $orders->whereIn('order_id', $order_id)
                ->update([
                    'order_status' => 2,
                ]);
        }
        return true;
    }

    public static function update_order_tracking_no($order_id, $remarks)
    {
        return $order = Order::where('order_id', $order_id)
        ->update([
            'order_tracking_no' => $remarks
        ]);
    }
}
