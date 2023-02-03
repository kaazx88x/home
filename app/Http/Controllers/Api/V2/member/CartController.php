<?php

namespace App\Http\Controllers\Api\V2\member;

use App\Http\Controllers\Controller;
use App\Repositories\OrderRepo;
use App\Models\Country;
use App\Repositories\CountryRepo;
use App\Repositories\CustomerRepo;
use App\Repositories\ProductRepo;

class CartController extends Controller
{
    protected $cus_id;
    protected $secure_code;

    public function __construct()
    {
        $this->country_code = 'my';
        $this->country = null;
        $this->lang = "en";

        if (\Auth::guard('api_members')->check()) {
            $this->cus_id = \Auth::guard('api_members')->user()->cus_id;
            $this->secure_code = \Auth::guard('api_members')->user()->payment_secure_code;
        }
    }

    //retrieve cart
    public function cart()
    {
        $data = \Request::only('lang','country_code');

        if (isset($data['lang'])) {
            $this->lang = $data['lang'];
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        // if country code not give will get default country code : my
        if (isset($data['country_code']))
            $this->country_code = strtolower($data['country_code']);

        $this->country = CountryRepo::get_country_by_code($this->country_code);

        //retrieve cart by user id
        $carts = OrderRepo::get_shopping_carts(null,  $this->cus_id, $this->country->co_id);

        $cart_details = [];
        $cart_total = [];

        if ($carts) {
            foreach ($carts['list'] as $key => $cart) {
                $cart_details[] = [
                    'id' => $cart['cart']->id,
                    'product_id' => $cart['product']->pro_id,
                    'product_name' => $cart['product']->pro_title_en,
                    'product_image' => $cart['main_image']->image,
                    'product_attribute' => json_decode($cart['cart']->attributes_name),
                    'product_price' => $cart['pricing']->product_price,
                    'product_credit' => $cart['pricing']->product_credit,
                    'quantity' => $cart['cart']->quantity,
                    'purchasing_price' => $cart['pricing']->purchasing_price,
                    'purchasing_credit' => $cart['pricing']->purchasing_credit,
                    'platform_charge' => $cart['pricing']->platform_charge,
                    'service_charge' => $cart['pricing']->service_charge,
                ];
            }

            $cart_total = $carts['total'];
        }

        // dd($cart_details);
        return \Response::json([
            'status' => 200,
            'message' => 'get carts success',
            'carts' => $cart_details,
            'total' => $cart_total
        ]);
    }

    //remove product from cart
    public function delete()
    {
        $data = \Request::only('cart_id', 'country_code', 'lang');

        if (isset($data['lang'])) {
            $this->lang = $data['lang'];
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        $validator = \Validator::make($data, [
            'cart_id' => 'required|integer',
        ])->setAttributeNames([
            'cart_id' => trans('api.cartId'),
        ]);

        if ($validator->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => $validator->messages()->messages()
            ]);
        }

        // if country code not give will get default country code : my
        if (isset($data['country_code']))
            $this->country_code = strtolower($data['country_code']);

        $this->country = CountryRepo::get_country_by_code($this->country_code);

        // Check product exist
        $cart = OrderRepo::get_cart_by_id($data['cart_id'], $this->cus_id, $this->country->co_id);

        if (!$cart) {
            return \Response::json([
                'status' => 403,
                'message' => trans('localize.cart') . trans('api.notFound')
            ]);
        }

        // delete cart
        if (OrderRepo::delete($data['cart_id'])) {
            return \Response::json([
                'status' => 200,
                'message' => trans('localize.cart') . trans('api.deleted')
            ]);
        }

        return \Response::json([
            'status' => 403,
            'message' => trans('localize.cart') . trans('api.failDelete')
        ]);
    }

    //update cart item
    public function update()
    {
        $data = \Request::only('cart_id', 'quantity', 'country_code', 'lang');

        if (isset($data['lang'])) {
            $this->lang = $data['lang'];
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        $validator = \Validator::make($data, [
            'cart_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ])->setAttributeNames([
            'cart_id' => trans('api.cartId'),
            'quantity' => trans('api.quantity'),
        ]);

        if ($validator->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => $validator->messages()->messages()
            ]);
        }

        // if country code not give will get default country code : my
        if (isset($data['country_code']))
            $this->country_code = strtolower($data['country_code']);

        $this->country = CountryRepo::get_country_by_code($this->country_code);

        // Check product exist
        $cart = OrderRepo::get_cart_by_id($data['cart_id'], $this->cus_id, $this->country->co_id);

        if (!$cart) {
            return \Response::json([
                'status' => 403,
                'message' => trans('localize.cart') . trans('api.notFound')
            ]);
        }

        // Update cart
        if (OrderRepo::update($data['cart_id'], $data['quantity'])) {
            return \Response::json([
                'status' => 200,
                'message' => trans('localize.cart') . trans('api.updated')
            ]);
        }

        return \Response::json([
            'status' => 403,
            'message' => trans('localize.cart') . trans('api.failUpdate')
        ]);
    }

    //add product to cart
    public function add()
    {
        $data = \Request::only('product_id', 'pricing_id', 'quantity', 'country_code', 'lang');

        if (isset($data['lang'])) {
            $this->lang = $data['lang'];
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        $validator = \Validator::make($data, [
            'product_id' => 'required|integer',
            'pricing_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ])->setAttributeNames([
            'product_id' => trans('api.productId'),
            'pricing_id' => trans('api.pricingId'),
            'quantity' => trans('api.quantity'),
        ]);

        if ($validator->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => $validator->messages()->messages()
            ]);
        }

        // if country code not give will get default country code : my
        if (isset($data['country_code']))
            $this->country_code = strtolower($data['country_code']);

        $this->country = CountryRepo::get_country_by_code($this->country_code);

        $cart_data = [
            'product_id' => $data['product_id'],
            'price_id' => $data['pricing_id'],
            'qty' => $data['quantity'],
            'remarks' => ''
        ];

        // Check product store accept payment or not
        $product = ProductRepo::get_product_store($data['product_id']);
        if ($product->accept_payment != 1) {
            return \Response::json([
                'status' => 403,
                'message' => trans('localize.store_not_accept_payment'),
            ]);
        }

        if ($data['quantity'] <= 0) {
            return \Response::json([
                'status' => 403,
                'message' => trans('localize.product_quantity_required'),
            ]);
        }

        // get cart if already exist in cart
        $check_exist = OrderRepo::check_exist('', $this->cus_id, $cart_data);

        if ($check_exist) {
            $qty = $check_exist->quantity + $data['quantity'];
            try {
                $cart_update = OrderRepo::update($check_exist->id, $qty);
            } catch (\Exception $e) {
                return \Response::json([
                    'status' => 403,
                    'message' => trans('api.addCart') . trans('api.fail'),
                ]);
            }
        } else {
            $cart_token = md5(uniqid(microtime()));
            try {
                $cart_add = OrderRepo::add_cart($cart_token, $this->cus_id, $cart_data);
            } catch (\Exception $e) {
                return \Response::json([
                    'status' => 403,
                    'message' => trans('api.addCart') . trans('api.fail'),
                ]);
            }
        }

         return \Response::json([
            'status' => 200,
            'message' => trans('api.addCart') . trans('api.success'),
        ]);
    }

    public function checkout()
    {
        $data = \Request::only('name', 'address_1', 'address_2', 'city', 'country_id', 'state_id', 'postal_code', 'telephone', 'country_code', 'lang', 'securecode');

        if (isset($data['lang'])) {
            $this->lang = $data['lang'];
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        $validator = \Validator::make($data, [
            'name' => 'required|max:255',
            'address_1' => 'required|max:200',
            'address_2' => 'max:200',
            'city' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
            'telephone' => 'required',
            'postal_code' => 'required',
        ])->setAttributeNames([
            'name' => 'Name',
            'address_1' => 'Address Line 1',
            'address_2' => 'Address Line 2',
            'city' => 'City',
            'country_id' => 'Country',
            'state_id' => 'State',
            'telephone' => 'Telephone',
            'postal_code' => 'postal code',
            'securecode' => 'required|numeric|digits:6|valid_hash:'.$this->secure_code,
        ]);

        if ($validator->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => $validator->messages()->messages()
            ]);
        }

        // if country code not give will get default country code : my
        if (isset($data['country_code']))
            $this->country_code = strtolower($data['country_code']);

        $this->country = CountryRepo::get_country_by_code($this->country_code);

        //check cart attribute is same with pricing attribute
        $attributes = OrderRepo::check_cart_and_pricing_attribute('', $this->cus_id, $this->country->co_id);

        if($attributes > 0) {
            return \Response::json([
                'status' => 403,
                'message' => 'Missmatch product options in your cart. Please update or remove the item to proceed'
            ]);
        }

        // check credit
        $wallets = CustomerRepo::get_customer_wallet_array($this->cus_id);
        $carts_total = OrderRepo::get_carts_total('', $this->cus_id, 'credit', $this->country->co_id);

        foreach ($carts_total['total_by_wallet'] as $key => $total) {
            if(array_key_exists($key, $wallets)) {
                if(round($total, 4) > round($wallets[$key]['credit'], 4)) {
                    return \Response::json([
                        'status' => 403,
                        'message' => 'Insufficient credit in your '.$wallets[$key]['name']
                    ]);
                }
            } else {
                return \Response::json([
                    'status' => 403,
                    'message' => 'You dont have required wallet to checkout these items'
                ]);
            }
        }

        // check total price based on current pricing and current total price based on temp cart
        if (round($carts_total['from_pricing'], 2) > round($carts_total['from_cart'], 2)) {
            return \Response::json([
                'status' => 403,
                'message' => 'Sorry, Total price in your cart has changed due to discounted price has been expired OR merchant has changed the product price. Proceed again if you agreed with this price.'
            ]);
        }

        // Check availability of item quantity
        $items = OrderRepo::check_cart_quantity('', $this->cus_id, $this->country->co_id);
        foreach ($items as $key => $item) {
            if ($item['total_quantity_in_cart'] <= 0) {
                $delete_item = OrderRepo::delete($item['cart_id']);
            }

            // Check product store accept payment or not
            if ($item['accept_payment'] != 1) {
                return \Response::json([
                    'status' => 403,
                    'message' => trans('localize.store_not_accept_payment'),
                ]);
            }

            if($item['status'] == 0) {
                return \Response::json([
                    'status' => 403,
                    'message' => 'Unable to proceed, Insufficient '.$item['pro_title'].' quantity'
                ]);
            }

            if($item['expired']) {
                return \Response::json([
                    'status' => 403,
                    'message' => trans('localize.checkouts.product.expired', ['name' => $item['pro_title']])
                ]);
            }

            if($item['exceed']['productLimit'])
            {
                return \Response::json([
                    'status' => 403,
                    'message' => $item['exceed']['productLimit']
                ]);
            }
        }

        do {
            $trans_id = str_random(8);
            $check_trans_id = OrderRepo::check_trans_id($trans_id);
        } while (!empty($check_trans_id));

        $country_id = $this->country->co_id;
        $customer_id = $this->cus_id;
        $payment_type = 1;

        $pdo = \DB::connection()->getPdo();
        $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
        $stmt = $pdo->prepare('CALL cart_checkout(?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->bindParam(1, $customer_id);
        $stmt->bindParam(2, $country_id);
        $stmt->bindParam(3, $trans_id);
        $stmt->bindParam(4, $data['name']);
        $stmt->bindParam(5, $data['address_1']);
        $stmt->bindParam(6, $data['address_2']);
        $stmt->bindParam(7, $data['city']);
        $stmt->bindParam(8, $data['country_id']);
        $stmt->bindParam(9, $data['postal_code']);
        $stmt->bindParam(10, $data['telephone']);
        $stmt->bindParam(11, $data['state_id']);
        $stmt->bindParam(12, $payment_type);
        $stmt->execute();
        $checkouts = $stmt->fetchAll();
        $stmt->closeCursor();

        if ($checkouts) {
            return \Response::json([
                'status' => 200,
                'message' => 'Checkout Success',
                'data' => $checkouts
            ]);
        } else {
            OrderRepo::cart_log('', $customer_id, $this->country->co_id);
            return \Response::json([
                'status' => 403,
                'message' => 'Checkout Fail'
            ]);
        }
    }

    public function empty_cart()
    {
        $data = \Request::only('country_code', 'lang');

        if (isset($data['lang'])) {
            $this->lang = $data['lang'];
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        // if country code not give will get default country code : my
        if (isset($data['country_code']))
            $this->country_code = strtolower($data['country_code']);

        $this->country = CountryRepo::get_country_by_code($this->country_code);

        // empty cart
        if (OrderRepo::empty_cart($this->cus_id, $this->country->co_id)) {
            return \Response::json([
                'status' => 200,
                'message' => trans('api.emptied')
            ]);
        }

        return \Response::json([
            'status' => 403,
            'message' => trans('api.failEmpty')
        ]);
    }
}