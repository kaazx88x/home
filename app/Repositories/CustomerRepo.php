<?php

namespace App\Repositories;
use DB;
use App\Models\Customer;
use App\Models\CustomerInfo;
use App\Models\Order;
use App\Models\Product;
use App\Models\VcoinLog;
use App\Models\Shipping;
use App\Models\Inquiries;
use App\Models\Wallet;
use App\Models\CustomerWallet;

class CustomerRepo
{
    public static function getCustomerVCoinBalance($pid)
    {
        $vc = Customer::where('cus_id', $pid)->pluck('v_token')->first();

        return $vc;
    }

    public static function get_orderHistory($customerid, $perPage, $search)
    {
        $orders = Order::select('nm_order.*', 'nm_courier.id', 'nm_courier.link', 'nm_courier.name')
        ->where('order_cus_id', '=', $customerid)
        ->leftjoin('nm_courier', 'nm_order.order_courier_id', '=', 'nm_courier.id')
        ->orderBy('order_id', 'DESC');

        if ($search) {
            $orders->LeftJoin('nm_product', 'nm_product.pro_id', '=', 'nm_order.order_pro_id');
            $search = '%'.$search.'%';
            $orders->where(function($query) use ($search) {
                $query->whereRaw('nm_product.pro_title_en LIKE ? or nm_product.pro_title_cn LIKE ? or nm_product.pro_title_cnt LIKE ? or nm_order.transaction_id LIKE ?', [$search, $search, $search, $search]);
            });
        }
        $orders = ($perPage != 'all') ? $orders->paginate($perPage) : $orders->get();

        foreach ($orders as $key => $order)
        {
            $string = $order->order_attributes_id;
            $attr_names = json_decode($string);

            $attribute_string = "";
            if(!empty($attr_names)){
                foreach ($attr_names as $attr_name) {
                    $attribute_string .= AttributeRepo::get_pricing_attributes($attr_name);
                }
            }
            if ($order) {
                $product = Product::where('nm_product.pro_id', '=', $order->order_pro_id)
                ->leftjoin("nm_product_image", "nm_product.pro_id", "=", "nm_product_image.pro_id")
                // ->where('nm_product_image.main', '=', 1)
                ->first();

                $orders[$key] = array(
                    'order' => $order,
                    'product' => $product,
                    'attribute' => $attribute_string
                );
            }
            else{
                $orders[$key] = array(
                    'order' => $order,
                    'attribute' => $attribute_string
                );
            }
        }
        return $orders;
    }

    public static function get_vtokenlog($customerid, $perPage)
    {
        $vcoinlogs = VcoinLog::select('v_token_log.*','nm_product.pro_title_'.\App::getLocale().' as title','nm_order.order_id')
        ->with('wallet')
        ->leftJoin('nm_order','nm_order.order_id','v_token_log.order_id')
        ->leftJoin('nm_product','nm_product.pro_id','nm_order.order_pro_id')
        ->where('v_token_log.cus_id', '=', $customerid)
        ->orderBy('v_token_log.created_at','desc');

        if ($perPage != 'all') {
            return $vcoinlogs->paginate($perPage);
        } else {
            return $vcoinlogs->get();
        }
    }

    public static function get_customer_by_id($id)
    {
        return Customer::where('cus_id','=',$id)->first();
    }

    public static function update_customer_accountInfo($id, $data)
    {
        try {
            $cus = Customer::where('cus_id', $id)->update([
                'cus_name' => $data['name'],
                'cus_address1' => $data['address1'],
                'cus_address2' => $data['address2'],
                'cus_country' => $data['country'],
                'cus_state' => $data['state'],
                'cus_city_name' => $data['city'],
                'cus_postalcode' => $data['zipcode'],
            ]);

            $cus_info = CustomerInfo::updateOrCreate(
                ['customer_cus_id' => $id],
                [
                    'cus_title' => $data['cus_title'],
                    'cus_job' => $data['cus_job'],
                    'cus_incomes' => $data['cus_incomes'],
                    'cus_education' => $data['cus_education'],
                    'cus_gender' => isset($data['cus_gender']) ? $data['cus_gender'] : 0,
                    'cus_dob' => date("Y-m-d", strtotime($data['cus_dob'])),
                    'cus_nationality' => $data['cus_nationality'],
                    'cus_race' => $data['cus_race'],
                    'cus_religion' => $data['cus_religion'],
                    'cus_marital' => $data['cus_marital'],
                    'cus_children' => $data['cus_children'],
                    'cus_hobby' => $data['cus_hobby'],
                ]
            );

            return Customer::find($id);
        } catch (Exception $e) {
            return false;
        }

    }

    public static function update_shipping_address($cus_id,$data)
    {
        try {
            $shipping = Shipping::where('ship_id', '=', $data['ship_id'])->first();
            $customer = Customer::findOrfail($cus_id);

            // if($data['isdefault'] == 1){
            //     $ship_add = Shipping::where('ship_cus_id','=',$cus_id)->where('isdefault','=', 1)->update(array('isdefault' => 0));
            // }

            if ($shipping) {
                $shipping->ship_name = $data['ship_name'];
                $shipping->ship_cus_id = $customer->cus_id;
                $shipping->ship_phone = $data['phone'];
                $shipping->ship_address1 = $data['address1'];
                $shipping->ship_address2 = $data['address2'];
                $shipping->ship_state_id = (isset($data['state']))?$data['state']:0;
                $shipping->ship_country = $data['country'];
                $shipping->ship_city_name = $data['city_name'];
                $shipping->ship_postalcode = $data['zipcode'];
                $shipping->isdefault = $data['isdefault'];
                $shipping->areacode = $data['areacode'];
                $shipping->save();
            } else {
                $shipping = Shipping::create([
                    'ship_cus_id' => $customer->cus_id,
        			'ship_phone' => $data['phone'],
        			'ship_address1' => $data['address1'],
        			'ship_address2' => $data['address2'],
        			'ship_city_name' => $data['city_name'],
        			'ship_state_id' => $data['state'],
                    'ship_country' => $data['country'],
                    'ship_postalcode' => $data['zipcode'],
                    'ship_name' => $data['ship_name'],
                    'isdefault' => $data['isdefault'],
                    'areacode' => $data['areacode'],
                ]);
            }

            if ($data['isdefault']) {
                $update = Shipping::where('ship_cus_id', $shipping->ship_cus_id)
                ->where('ship_id', '!=', $shipping->ship_id)
                ->update([
                    'isdefault' => 0
                ]);
            }

            return $shipping;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getCustomerPaymentSecureCode($pid)
    {
        $paymentcode = Customer::where('cus_id', $pid)->pluck('payment_secure_code')->first();

        return $paymentcode;
    }

    public static function all($input)
    {
        $customers = Customer::query();

        $customers->where(function($query) use ($input){
            $query->whereIn('cus_country',$input['admin_country_id_list'])
            ->orwhere('cus_country','=',NULL)
            ->orwhere('cus_country','=',0);
        });

        if (!empty($input['id']))
            $customers->where('cus_id', '=', $input['id']);

        if (!empty($input['search']))
            $customers->where('cus_name', 'LIKE', '%'.$input['search'].'%');

        if (!empty($input['phone']))
            $customers->where(\DB::raw("CONCAT(phone_area_code, '', cus_phone)"), 'LIKE', '%'.$input['phone'].'%');

        if (!empty($input['email']))
            $customers->where('email', 'LIKE', '%'.$input['email'].'%');

        if (!empty($input['status']) || $input['status'] == '0')
            $customers->where('cus_status', '=', $input['status']);

        if (!empty($input['type']))
            $customers->where('cus_logintype', '=', $input['type']);

        if (!empty($input['sort'])) {
            switch ($input['sort']) {
                case 'id_asc':
                    $customers->orderBy('cus_id', 'asc');
                    break;
                case 'id_desc':
                    $customers->orderBy('cus_id', 'desc');
                    break;
                case 'new':
                    $customers->orderBy('created_at', 'desc');
                    break;
                case 'old':
                    $customers->orderBy('created_at', 'asc');
                    break;
                default:
                    $customers->orderBy('cus_id', 'desc');
                    break;
            }
        } else {
            $customers->orderBy('cus_id', 'desc');
        }

        if(!empty($input['countries']))
            $customers->whereIn('cus_country', $input['countries']);

        $customers->with(['customer_wallets' => function ($q) {
            $q->select('customer_wallets.*', 'v_token_log.id as log_id');
            $q->leftJoin('v_token_log', function ($join) {
                $join->on('customer_wallets.customer_id', '=', 'v_token_log.cus_id');
                $join->on('customer_wallets.wallet_id', '=', 'v_token_log.wallet_id');
                $join->where('v_token_log.credit_amount', '>', '0');
                $join->whereNull('v_token_log.order_id');
                $join->whereNull('v_token_log.offline_order_id');
            });
            $q->groupBy(['customer_id', 'wallet_id']);
        }]);

        return $customers->paginate(50);
    }

    public static function get_customer_status($cus_id,$type)
    {

        $customers = customer::findorfail($cus_id);

        switch ($type) {
            case 'unblocked':
                $customers->cus_status = 1;
                break;

            case 'blocked':
                $customers->cus_status = 0;
                break;

            default:
                break;
        }
        $customers->save();

        return $customers;
    }

    public static function get_customer_details($cus_id)
    {
        $customer = Customer::where('cus_id', $cus_id)->first();

        return $customer;
    }

    public static function get_customer_shipping_detail($cus_id)
    {
        return Shipping::where('ship_cus_id','=',$cus_id)
        ->where('ship_order_id', '=', 0)
        ->paginate();

    }

    public static function get_customer_shipping_info($ship_id)
    {
        return Shipping::where('ship_id','=',$ship_id)->first();

    }


    public static function create_customer($data)
    {
        $login_type = 2;
        if (\Auth::guard('admins')->check()) {
            $login_type = 1;
        }

        $customer = Customer::create([
            'cus_name' => $data['name'],
            // 'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'cus_phone' => $data['cus_phone'],
            'phone_area_code' => !empty($data['cus_phone'])? $data['areacode'] : null,
            'cus_address1' => $data['cus_address1'],
            'cus_address2' => $data['cus_address2'],
            'cus_country' => !empty($data['cus_country'])? $data['cus_country'] : 0,
            'cus_state' => !empty($data['cus_state'])? $data['cus_state'] : 0,
            'cus_city_name' => $data['cus_city'],
            'cus_postalcode' => $data['cus_postalcode'],
            'cus_logintype' => $login_type,
            'cus_status' => true,
        ]);

        $cus_info = CustomerInfo::updateOrCreate([
            'customer_cus_id' => $customer->cus_id
        ],
        [
            'cus_title' => $data['cus_title'],
            'cus_job' => $data['cus_job'],
            'cus_incomes' => $data['cus_incomes'],
            'cus_education' => $data['cus_education'],
            'cus_gender' => $data['cus_gender'],
            'cus_dob' => date("Y-m-d", strtotime($data['cus_dob'])),
            'cus_nationality' => $data['cus_nationality'],
            'cus_race' => $data['cus_race'],
            'cus_religion' => $data['cus_religion'],
            'cus_marital' => $data['cus_marital'],
            'cus_children' => $data['cus_children'],
            'cus_hobby' => $data['cus_hobby'],
        ]);

        // create wallet
        $wallets = Wallet::get();
        foreach ($wallets as $wallet) {
            CustomerWallet::create([
                'customer_id' => $customer->cus_id,
                'wallet_id' => $wallet->id,
                'credit' => 0
            ]);
        }

        return $customer;
    }

    public static function update_customer_details($data)
    {
        try {
            $customer = Customer::find($data['cus_id']);

            $email = $customer->email;
            $email_verified = $customer->email_verified;
            if(array_key_exists('email', $data) && $data['email'] != $email) {
                $email = $data['email'];
                $email_verified = 0;
            }

            $customer->update([
                'cus_name' => $data['name'],
                // 'username' => $data['username'],
                'email' => $email,
                'cus_phone' => (isset($data['cus_phone']) && !empty($data['cus_phone']))? $data['cus_phone'] : $customer->cus_phone,
                'phone_area_code' => (isset($data['cus_phone']) && !empty($data['cus_phone']))? $data['areacode'] : $customer->phone_area_code,
                'cus_address1' => $data['cus_address1'],
                'cus_address2' => $data['cus_address2'],
                'cus_country' => !empty($data['cus_country'])? $data['cus_country'] : 0,
                'cus_state' => !empty($data['cus_state'])? $data['cus_state'] : 0,
                'cus_city_name' => $data['cus_city'],
                'cus_status' => $data['cus_status'],
                'cus_postalcode' => $data['cus_postalcode'],
                'email_verified' => $email_verified,
                'identity_card' => (isset($data['identity_card'])) ? $data['identity_card'] : $customer->identity_card,
            ]);

            $cus_info = CustomerInfo::updateOrCreate([
                'customer_cus_id' => $customer->cus_id
            ],
            [
                'cus_title' => $data['cus_title'],
                'cus_job' => $data['cus_job'],
                'cus_incomes' => $data['cus_incomes'],
                'cus_education' => $data['cus_education'],
                'cus_gender' => isset($data['cus_gender']) ? $data['cus_gender'] : null,
                'cus_dob' => !empty($data['cus_dob']) ? date("Y-m-d", strtotime($data['cus_dob'])) : null,
                'cus_nationality' => $data['cus_nationality'],
                'cus_race' => $data['cus_race'],
                'cus_religion' => $data['cus_religion'],
                'cus_marital' => $data['cus_marital'],
                'cus_children' => $data['cus_children'],
                'cus_hobby' => $data['cus_hobby'],
            ]);

            // $shipping = Shipping::updateOrCreate([
            //     'ship_cus_id' => $customer->cus_id,
            //     'ship_order_id' => 0,
            // ],[
            //     'ship_name' => $customer->cus_name,
            //     'ship_cus_id' => $customer->cus_id,
            //     'ship_phone' => $data['phone'],
            //     'ship_address1' => $data['address1'],
            //     'ship_address2' => $data['address2'],
            //     'ship_city_name' => $data['city_name'],
            //     'ship_state_id' => $data['state'],
            //     'ship_country' => $data['country'],
            //     'ship_postalcode' => $data['zipcode'],
            // ]);

            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    public static function update_secure_code($cus_id, $new_secure_code)
    {
        try {
            $customer = Customer::find($cus_id);
            $customer->update([
                'payment_secure_code' => \Hash::make($new_secure_code),
            ]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function reset_password($data)
    {
        $customer = Customer::find($data['cus_id']);

        $customer->password = bcrypt($data['password']);
        $customer->save();

        return $customer;
    }

    public static function reset_secure_code($data)
    {
        $customer = Customer::find($data['cus_id']);

        $customer->payment_secure_code = \Hash::make($data['securecode']);
        $customer->save();

        return $customer;
    }

    public static function check_api_key($api_key)
    {
        return Customer::where('app_session', $api_key)->first();
    }

    public static function create_temp_shipping_address($cus_id)
    {
        return Shipping::create([
            'ship_cus_id' => $cus_id,
            'ship_order_id' => 0,
            'ship_name' => '',
            'ship_phone' => '',
            'ship_address1' => '',
            'ship_address2' => '',
            'ship_city_name' => '',
            'ship_state_id' => '',
            'ship_country' => '',
            'ship_postalcode' => '',
        ]);
    }

    public static  function create_shipping_address($cus_id, $data)
    {

        if($data['isdefault'] == 1){
            $ship_add = Shipping::where('ship_cus_id','=',$cus_id)->update(array('isdefault' => 0));
        }

        return Shipping::create([
            'ship_name' => $data['ship_name'],
            'ship_cus_id' => $cus_id,
            'ship_phone' => $data['phone'],
            'ship_address1' => $data['address1'],
            'ship_address2' => $data['address2'],
            'ship_city_name' => $data['city_name'],
            'ship_state_id' => $data['state'],
            'ship_country' => $data['country'],
            'ship_postalcode' => $data['zipcode'],
            'isdefault' => $data['isdefault'],
            'areacode' => $data['areacode'],
        ]);
    }

    public static function delete_shipping_address($ship_id)
    {
        return Shipping::where('ship_id', '=', $ship_id)->delete();
    }

    public static function set_shipping_default($ship_id,$cus_id)
    {

        $ship_add = Shipping::where('ship_cus_id','=',$cus_id)->update(array('isdefault' => 0));

        return Shipping::where('ship_id', '=',$ship_id)->update(array('isdefault' => 1));
    }

    public static function get_customer_shipping_default($cus_id)
    {
        return Shipping::where('ship_cus_id','=',$cus_id)->where('isdefault','=', 1)->first();
    }

    public static function manage_credit($cus_id, $data)
    {
        $customer = Customer::find($cus_id);
        $customer_wallet =  CustomerWallet::where('customer_id', $cus_id)->where('wallet_id', $data['wallet'])->first();
        $amount = round($data['amount'], 4);
        $total = $amount;

        switch ($data['type']) {
            case 'credit':
                $customer->v_token = $customer->v_token + $amount;

                if ($customer_wallet) {
                    $total = $customer_wallet->credit + $amount;
                }
                break;

            case 'debit':
                $customer->v_token = $customer->v_token - $amount;

                if ($customer_wallet) {
                    $total = $customer_wallet->credit - $amount;
                }
                break;
        }

        $customer->save();

        // Update Wallet
        $cus_wallet = CustomerWallet::updateOrCreate(
            [
                'customer_id' => $cus_id,
                'wallet_id' => $data['wallet']
            ],
            [
                'credit' => $total
            ]
        );

        // Add Log
        VcoinLog::create([
            'cus_id' => $cus_id,
            'credit_amount' => ($data['type'] == 'credit') ? $amount : 0,
            'debit_amount' => ($data['type'] == 'debit') ? $amount : 0,
            'remark' => $data['remark'],
            'wallet_id' => $data['wallet']
        ]);
    }

    public static function verified_cellphone($cus_id, $verify = true)
    {
        try {
            $customer = Customer::find($cus_id);
            if(!$customer)
                return false;

            $customer->cellphone_verified = $verify;
            $customer->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function get_customer_wallet($cus_id)
    {
        return CustomerWallet::with(['wallet'])
        ->where('customer_id', $cus_id)
        ->get();
    }

    public static function get_customer_online_wallet($cus_id)
    {
        $cus_wallet = CustomerWallet::where('customer_id', $cus_id)
        ->where('wallet_id', 1)
        ->first();

        return $cus_wallet->credit;
    }

    public static function get_customer_offline_wallet($cus_id)
    {
        $cus_wallet = CustomerWallet::where('customer_id', $cus_id)
        ->where('wallet_id', 2)
        ->first();

        return $cus_wallet->credit;
    }

    public static function get_customer_wallet_array($cus_id)
    {
        $wallet_array = [];
        $wallets = CustomerWallet::select('customer_wallets.*')
        ->leftJoin('wallets','wallets.id','=','customer_wallets.wallet_id')
        // ->orderBy('main','desc')
        ->with(['wallet'])
        ->where('customer_id', $cus_id)
        ->get();

        foreach($wallets as $wallet)
        {
            $wallet_array[$wallet->wallet_id] = [
                'credit' => $wallet->credit,
                'name' => $wallet->wallet->name,
            ];
        }

        return $wallet_array;

    }

    public static function get_customer_available_wallet($cus_id)
    {
        $customer_wallets =  CustomerWallet::with(['wallet'])
        ->select('customer_wallets.*', 'v_token_log.id as log_id')
        ->leftJoin('v_token_log', function ($join) {
            $join->on('customer_wallets.customer_id', '=', 'v_token_log.cus_id');
            $join->on('customer_wallets.wallet_id', '=', 'v_token_log.wallet_id');
            $join->where('v_token_log.credit_amount', '>', '0');
            $join->whereNull('v_token_log.order_id');
            $join->whereNull('v_token_log.offline_order_id');
        })
        ->where('customer_wallets.customer_id', $cus_id)
        ->groupBy(['customer_id', 'wallet_id'])
        ->get();

        $wallets = [];
        foreach ($customer_wallets as $key => $cw) {
            if ($cw->wallet->percentage == 0) {
                if ($cw->log_id) {
                    $wallets[] = $cw;
                }
            }
            else {
                $wallets[] = $cw;
            }
        }

        return $wallets;
    }

}
