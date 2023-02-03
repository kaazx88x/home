<?php namespace App\Http\Controllers\Front;

use App\Http\Controllers\Front\Controller;
use App\Repositories\OrderRepo;
use App\Repositories\CountryRepo;
use App\Repositories\CustomerRepo;
use App\Repositories\AttributeRepo;
use App\Repositories\ProductRepo;
use App\Repositories\LimitRepo;
use App\Models\Cart;
use App\Models\City;
use App\Models\Shipping;
use App\Models\ProductPricing;
use Auth;
use Cookie;

class CartController extends Controller
{
    public function cart()
    {
        return view('front.cart.cart');
    }

    public function add()
    {
        $data = \Request::all();

        if (Cookie::get('cart_token') == null) {
            $cart_token = md5(uniqid(microtime()));
            Cookie::queue(Cookie::forever('cart_token', $cart_token));
        } else {
            $cart_token = Cookie::get('cart_token');
        }

        $user_id = (Auth::user()) ? Auth::user()->cus_id : null;
        $data['remarks'] = '';

        if($data['merchant_id'] == 407) {
            $remark = array('name' => $data['name'], 'IDno' => $data['IDno'], 'phone' => $data['phone'], 'homephone' => $data['homephone'],'address' => $data['address'], 'email' => $data['email']);
            $data['remarks'] = json_encode($remark);
        }

        // Check product store accept payment or not
        $product = ProductRepo::get_product_store($data['product_id']);
        if ($product->accept_payment != 1) {
            return back()->with('error', trans('localize.store_not_accept_payment'));
        }

        if ($data['qty'] <= 0) {
            return back()->with('error', trans('localize.product_quantity_required'));
        }

        $check_exist = OrderRepo::check_exist($cart_token, $user_id, $data);

        if ($check_exist) {
            $qty = $check_exist->quantity + $data['qty'];
            $cart_update = OrderRepo::update($check_exist->id, $qty);
        } else {
            $pricing = ProductPricing::select('*',
            \DB::raw("
                CASE WHEN ( (((nm_product_pricing.discounted_price IS NOT NULL )) and (nm_product_pricing.discounted_price > 0 )) and (NOW() >= (CAST(nm_product_pricing.discounted_from AS DATETIME)) and NOW() <= (CAST(nm_product_pricing.discounted_to AS DATETIME))) ) THEN nm_product_pricing.discounted_price ELSE nm_product_pricing.price END AS purchase_price")
            )
            ->where('id','=', $data['price_id'])
            ->leftJoin('nm_country','nm_country.co_id','=','nm_product_pricing.country_id')
            ->first();

            $cart = Cart::create([
                'token' => $cart_token,
                'cus_id' => $user_id,
                'product_id' => $data['product_id'],
                'quantity' => $data['qty'],
                'remarks' => $data['remarks'],
                'pricing_id' => $pricing->id,
                'currency' => $pricing->co_curcode,
                'currency_rate' => $pricing->co_rate,
                'purchasing_price' => $pricing->purchase_price,
                'product_price' => $pricing->purchase_price,
                'attributes' => AttributeRepo::get_pricing_attribute_id_json($pricing->id),
                'attributes_name' => AttributeRepo::get_pricing_attributes_name_json($pricing->id),
            ]);
        }

        return redirect('products/detail/'.$data['product_id'])->with('success', trans('localize.carts.add'));
    }

    public function update()
    {
        $status = 'error';
        $data = \Request::all();
        if (OrderRepo::update($data['id'], $data['qty'])) {
            $status = 'success';
        }

        return $status;
    }

    public function delete()
    {
        $status = 'error';
        $data = \Request::all();
        if (OrderRepo::delete($data['id'])) {
            $status = 'success';
        }
        return $status;
    }

    public function checkout()
    {
        $cus_id = (Auth::user()) ? Auth::user()->cus_id : null;

        $customer = CustomerRepo::get_customer_by_id($cus_id);
        $cus_vc = $customer->v_token;
        // $cus_vc = CustomerRepo::get_customer_online_wallet($cus_id);
        $wallets = CustomerRepo::get_customer_wallet_array($cus_id);
        $cid = session('countryid');
        $cart_token = Cookie::get('cart_token');

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();

            $niceNames = array(
                'name' => 'Name',
                'address_1' => 'Address Line 1',
                'address_2' => 'Address Line 2',
                'city' => 'City',
                'country' => 'Country',
                'state' => 'State',
                'telephone' => 'Telephone',
                'postal_code' => 'Postal Code',
            );

            $v = \Validator::make($data, [
                'name' => 'required|max:255',
                'address_1' => 'required|max:200',
                'address_2' => 'max:200',
                'city' => 'required',
                'country' => 'required|not_in:0|in:'.$cid,
                'state' => 'required|not_in:0',
                'telephone' => 'required|numeric',
                'telephone' => 'required',
                'postal_code' => 'required',
                'securecode' => 'required|numeric|digits:6|valid_hash:'.$customer->payment_secure_code,
            ]);

            $v->setAttributeNames($niceNames);
            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $payment_type = $data['payment_type'];
            $carts_total = OrderRepo::get_carts_total($cart_token, $cus_id, $payment_type);
            //Using Mei Point payment method
            if($payment_type == 'credit')
            {
                # check total < cus credit.
                // if (round($carts_total['from_pricing'], 4) > round($cus_vc, 4))
                //     return back()->withInput()->withErrors(['msg' => trans('localize.checkouts.insufficient.credit')]);

                if (round($carts_total['from_cart']) <= 0) {
                    return redirect('/carts')->withInput()->withErrors(['msg' => trans('localize.checkouts.invalid_credit')]);
                }

                # check total price based on current pricing and current total price based on temp cart
                if (round($carts_total['from_pricing'], 2) > round($carts_total['from_cart'], 2)) {
                    return redirect('/carts')->withInput()->withErrors(['msg' => trans('localize.checkouts.price_changed')]);
                }

                //convert credit into customer customer country currency
                $customer_country = $customer->country;
                if(!$customer_country)
                {
                    return back()->withInput()->withErrors(['msg' => trans('localize.profile_update_country')]);
                }

                //check member payment limitation
                $order_amount = round($carts_total['price_credit'] * $customer_country->co_rate, 2);
                $check = LimitRepo::check_payment_limitation('customerLimit', $order_amount, null, $customer->cus_id);
                if($check)
                {
                    return back()->withInput()->withErrors(['msg' => $check]);
                }

                foreach ($carts_total['total_by_wallet'] as $key => $total) {
                    if(array_key_exists($key, $wallets)) {
                        // if customer wallet value negative
                        if (round($wallets[$key]['credit'], 4) < 0) {
                            // $lock_customer = CustomerRepo::lock_with_remarks($data['member_id'], 'Negative Wallet Value');
                            return back()->withInput()->withErrors(['msg' => trans('localize.insufficient_wallet_for_checkout', ['wallet_name'=>$wallets[$key]['name']])]);
                        }

                        if(round($total, 4) > round($wallets[$key]['credit'], 4)) {
                            return back()->withInput()->withErrors(['msg' => trans('localize.insufficient_wallet_for_checkout', ['wallet_name'=>$wallets[$key]['name']])]);
                        }
                    } else {
                        return back()->withInput()->withErrors(['msg' => trans('you_dont_have_required_wallet_to_checkout_these_items')]);
                    }
                }
            }

            // Check availability of item quantity
            $items = OrderRepo::check_cart_quantity($cart_token, $cus_id, $cid);
            foreach ($items as $key => $item) {
                if ($item['total_quantity_in_cart'] <= 0) {
                    $delete_item = OrderRepo::delete($item['cart_id']);
                    return back()->withInput()->withErrors(['msg' => trans('localize.checkouts.invalid_quantity')]);
                }

                // Check product store accept payment or not
                if ($item['accept_payment'] != 1) {
                    return back()->withInput()->withErrors(['msg' => trans('localize.store_not_accept_payment')]);
                }

                if($item['status'] == 0) {
                    return back()->withInput()->withErrors(['msg' => trans('localize.checkouts.insufficient.quantity', ['name' => $item['pro_title']]) ]);
                }

                if($item['expired']) {
                    return back()->withInput()->withErrors(['msg' => trans('localize.checkouts.product.expired', ['name' => $item['pro_title']]) ]);
                }

                if($item['exceed']['productLimit'])
                {
                    return back()->withInput()->withErrors(['msg' => $item['exceed']['productLimit']]);
                }
            }
            
            do {
                $trans_id = str_random(8);
                $check_trans_id = OrderRepo::check_trans_id($trans_id);
            } while (!empty($check_trans_id));

            // $checkouts = \DB::select('CALL cart_checkout(?,?,?,?,?,?,?,?,?)',[
            //     $data['cus_id'], $trans_id, $data['name'], $data['address_1'], $data['address_2'], $data['city'], $data['country'], $data['postal_code'], $data['telephone']
            // ]);

            switch ($payment_type) {
                case 'credit':
                        $payment_method = 1;
                    break;
                case 'cash':
                        $payment_method = 2;
                    break;
            }

            $pdo = \DB::connection()->getPdo();
            $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
            $stmt = $pdo->prepare('CALL cart_checkout(?,?,?,?,?,?,?,?,?,?,?,?)');
            $stmt->bindParam(1, $cus_id);
            $stmt->bindParam(2, $cid);
            $stmt->bindParam(3, $trans_id);
            $stmt->bindParam(4, $data['name']);
            $stmt->bindParam(5, $data['address_1']);
            $stmt->bindParam(6, $data['address_2']);
            $stmt->bindParam(7, $data['city']);
            $stmt->bindParam(8, $data['country']);
            $stmt->bindParam(9, $data['postal_code']);
            $stmt->bindParam(10, $data['telephone']);
            $stmt->bindParam(11, $data['state']);
            $stmt->bindParam(12, $payment_method);
            $stmt->execute();
            $checkouts = $stmt->fetchAll();
            $stmt->closeCursor();

            if ($checkouts) {

                LimitRepo::update_limit_transaction('online', $trans_id);

                if($data['new_address'] == 1) {
                    $entry = [
                        'ship_name' => $data['name'],
                        'phone' => $data['telephone'],
                        'address1' => $data['address_1'],
                        'address2' => $data['address_2'],
                        'city_name' => $data['city'],
                        'state' => $data['state'],
                        'country' => $data['country'],
                        'zipcode' => $data['postal_code'],
                        'isdefault' => 0,
                        'areacode' => $data['areacode'],
                    ];

                    CustomerRepo::create_shipping_address($cus_id, $entry);
                }

                foreach($checkouts as $key => $checkout){
                    $attribute='';
                    if($checkout['json_attributes']){
                        foreach(json_decode($checkout['json_attributes']) as $key_checkout => $checkout_attribute){
                            $attribute .= AttributeRepo::get_pricing_attributes_success($key_checkout,$checkout_attribute,$checkout['product_name']);
                        }
                        $checkouts[$key]['json_attributes_lang'] = $attribute;
                    }
                }

                return view('front.cart.checkout_success', ['name' => $data['name'], 'transaction_id' => $trans_id, 'checkouts' => $checkouts]);
            } else {
                return view('front.cart.checkout_fail');
            }
        }

        $cities = City::all();
        $countries = CountryRepo::get_all_countries();
        $shippings = Shipping::where('ship_cus_id', '=' , $cus_id)->where('ship_order_id', '=', 0)->get();
        $carts = OrderRepo::get_shopping_carts($cart_token, $cus_id);
        if(!$carts)
            return redirect('/carts');

        return view('front.cart.checkout', compact('cities', 'countries', 'shippings', 'carts_total','cid','cus_vc'));
    }

    public function update_cart_attribute()
    {
        $input = \Request::only('cart_id','pricing_id');
        $update = AttributeRepo::update_cart_attribute($input['cart_id'],$input['pricing_id']);

        return back();
    }
}
