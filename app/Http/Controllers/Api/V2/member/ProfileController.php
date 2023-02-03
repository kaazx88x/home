<?php

namespace App\Http\Controllers\Api\V2\member;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderOffline;
use App\Models\Merchant;
use App\Models\Store;
use App\Repositories\CustomerRepo;
use App\Repositories\ProductImageRepo;
use App\Repositories\S3ClientRepo;
use Validator;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;

class ProfileController extends Controller
{
    protected $cus_id;

    public function __construct()
    {
        $this->country_code = 'my';
        $this->country = null;
        $this->lang = "en";

        if (\Auth::guard('api_members')->check()) {
            $this->cus_id = \Auth::guard('api_members')->user()->cus_id;
        }
    }

    public function update_name()
    {
        $data = \Request::only('name', 'lang');

        if (isset($data['lang'])) {
            $this->lang = $data['lang'];
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        $validator = \Validator::make($data, [
            'name' => 'required|max:255'
        ])->setAttributeNames([
            'name' => trans('api.memberName')
        ]);

        if ($validator->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        try {
            Customer::where('cus_id', $this->cus_id)->update(['cus_name' => $data['name']]);
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 403,
                'message' => trans('api.memberName') . trans('api.failUpdate'),
            ]);
        }

        return \Response::json([
            'status' => 200,
            'message' => trans('api.memberName') . trans('api.updated')
        ]);
    }

    public function update_address()
    {
        $data = \Request::only('address_1', 'address_2', 'city', 'state_id', 'country_id', 'postal_code', 'telephone', 'lang');

        if (isset($data['lang'])) {
            $this->lang = $data['lang'];
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        $validator = \Validator::make($data, [
            'address_1' => 'required|max:200',
            'address_2' => 'max:200',
            'city' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
            'telephone' => 'required',
            'postal_code' => 'required',
        ])->setAttributeNames([
            'address_1' => 'address line 1',
            'address_2' => 'address line 2',
        ]);

        if ($validator->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        try {
            CustomerRepo::update_shipping_address($this->cus_id, [
                'address1' => $data['address_1'],
                'address2' => $data['address_2'],
                'city_name' => $data['city'],
                'country' => $data['country_id'],
                'state' => $data['state_id'],
                'phone' => $data['telephone'],
                'zipcode' => $data['postal_code'],
            ]);
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 403,
                'message' => trans('api.memberAddress') . trans('api.failUpdate'),
            ]);
        }

        return \Response::json([
            'status' => 200,
            'message' => trans('api.memberAddress') . trans('api.updated')
        ]);
    }

    public function update_avatar()
    {
        $data = \Request::only('file', 'name', 'lang');

        if (isset($data['lang'])) {
            $this->lang = $data['lang'];
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        $validator = \Validator::make($data, [
            'file' => 'required|image|mimes:jpeg,jpg,png|max:2000'
        ]);

        if ($validator->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        $member = CustomerRepo::get_customer_by_id($this->cus_id);

        if (!$member) {
            return \Response::json([
                'status' => 403,
                'message' => trans('api.member') . trans('api.notFound'),
            ]);
        }

        $old_file_name = $member->cus_pic;
        $cus_id = $member->cus_id;

        try {
            $file = $data['file'];
            $file_name = $file->getClientOriginalName();
            $file_details = explode('.', $file_name);
            $new_file_name = $cus_id.'_'.date('YmdHis').'.'.$file_details[1];
            $path = 'avatar/'.$cus_id;
            if(@file_get_contents($file) && !S3ClientRepo::IsExisted($path, $new_file_name))
                S3ClientRepo::Upload($path, $file, $new_file_name);

            S3ClientRepo::Delete($path, $old_file_name);

            $member->cus_pic = $new_file_name;
            $member->save();
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 403,
                'message' => trans('api.memberAvatar') . trans('api.failUpdate'),
            ]);
        }

        return \Response::json([
            'status' => 200,
            'message' => trans('api.memberAvatar') . trans('api.updated')
        ]);
    }

    public function online_order_history()
    {
        $data = \Request::only('order_status', 'page', 'size', 'sort', 'lang');

        if (isset($data['lang'])) {
            $this->lang = $data['lang'];
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        $validator = \Validator::make($data, [
            'order_status' => 'integer|in:1,2,3,4,5',
            'page' => 'integer',
            'size' => 'integer',
            'sort' => 'integer|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        $data['page'] = (isset($data['page'])) ? $data['page'] : 1;
        $data['size'] = (isset($data['size'])) ? $data['size'] : 25;
        $data['sort'] = (isset($data['sort'])) ? $data['sort'] : 1;

        $member = CustomerRepo::get_customer_by_id($this->cus_id);

        if (!$member) {
            return \Response::json([
                'status' => 403,
                'message' => trans('api.member') . trans('api.notFound'),
            ]);
        }

        try {
            // Grab online order history
            $orders = Order::leftJoin('nm_product', 'nm_product.pro_id', '=', 'nm_order.order_pro_id')
                ->leftJoin('nm_courier', 'nm_courier.id', '=', 'nm_order.order_courier_id')
                ->where('nm_order.order_cus_id', $this->cus_id)
                ->where('nm_order.order_type', 1);

            if (!empty($data['order_status']))
                $orders->where('nm_order.order_status', $data['order_status']);

            switch (trim($data['sort'])) {
                // Sort : Latest
                case '1':
                    $orders->orderBy('nm_order.created_at', 'desc');
                    break;
                // Sort : Highest Amount
                case '2':
                    $orders->orderBy('nm_order.order_credit', 'desc');
                    break;
                // Sort : Lowest Amount
                case '3':
                    $orders->orderBy('nm_order.order_credit', 'asc');
                    break;

                default:
                    $orders->orderBy('nm_order.order_id', 'desc');
                    break;
            }

            $orders = $orders->paginate(trim($data['size']));
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 403,
                'message' => trans('api.failRetrieve')
            ]);
        }

        $details = [];
        foreach ($orders as $order) {
            $product_image = ProductImageRepo::get_product_main_image($order->order_pro_id);
            if (!str_contains($product_image, 'http://'))
                $product_image = env('IMAGE_DIR').'/product/'.$order->pro_mr_id.'/thumbnail_'.$product_image;

            $details[] = [
                'order_id' => $order->order_id,
                'transaction_id' => $order->transaction_id,
                'product_id' => $order->order_pro_id,
                'product_name' => $order->pro_title_en,
                'total_credit' => $order->order_vtokens,
                'order_status' => $order->order_status,
                'order_date' => $order->order_date,
                'remark' => $order->remark,
                'courier_company' => $order->name,
                'courier_link' => $order->link,
                'tracking_no' => $order->order_tracking_no,
                'shipment_date' => $order->order_shipment_date,
                'product_image' => $product_image,
            ];
        }

        if (empty($details))
            return \Response::json([
                'status' => 403,
                'message' => 'Online order history not found',
            ]);

        return \Response::json([
            'status' => 200,
            'message' => 'Online order history found',
            'count' => $orders->total(),
            'total_pages' => $orders->lastPage(),
            'data' => $details
        ]);
    }

    public function update_password()
    {
        $data = \Request::only('old_password', 'password', 'password_confirmation', 'lang');

        if (isset($data['lang'])) {
            $this->lang = $data['lang'];
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        $member = CustomerRepo::get_customer_by_id($this->cus_id);

        if (!$member) {
            return \Response::json([
                'status' => 403,
                'message' => trans('api.member') . trans('api.notFound'),
            ]);
        }

        // validate password
        \Validator::extend('old_password', function ($attribute, $value, $parameters, $validator) {
            return \Hash::check($value, current($parameters));
        });

        $validator = \Validator::make($data, [
            'password' => 'required|min:6|confirmed',
            'old_password' => 'required|min:6|old_password:'.$member->password
        ], [
            'old_password.old_password' => trans('localize.old_password_validation'),
            'old_password.required' => trans('localize.oldpasswordInput'),
            'password.required' => trans('localize.newpasswordInput'),
            'old_password.min' => trans('localize.minpassword'),
            'password.confirmed' => trans('localize.matchpassword'),
        ]);

        if ($validator->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        try {
            $member->password= bcrypt($data['password']);
            $member->save();
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 403,
                'message' => trans('api.password') . trans('api.failUpdate'),
            ]);
        }

        return \Response::json([
            'status' => 200,
            'message' => trans('api.password') . trans('api.updated'),
        ]);
    }

    public function update_securecode()
    {
        $data = \Request::only('old_securecode', 'securecode', 'securecode_confirmation', 'lang');

        if (isset($data['lang'])) {
            $this->lang = $data['lang'];
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        $member = CustomerRepo::get_customer_by_id($this->cus_id);

        if (!$member) {
            return \Response::json([
                'status' => 403,
                'message' => trans('api.member') . trans('api.notFound'),
            ]);
        }

        // validate secure code
        $validator = \Validator::make($data, [
            'securecode' => 'required|integer|digits:6|confirmed',
            'old_securecode' => 'required|integer|digits:6|valid_hash:'.$member->payment_secure_code
        ])->setAttributeNames([
            'securecode' => trans('api.secureCode'),
            'old_securecode' => trans('api.secureCodeOld'),
        ]);

        if ($validator->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        try {
            $member->payment_secure_code = \Hash::make($data['securecode']);
            $member->save();
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 403,
                'message' => trans('api.secureCode') . trans('api.failUpdate'),
            ]);
        }

        return \Response::json([
            'status' => 200,
            'message' => trans('api.secureCode') . trans('api.updated'),
        ]);
    }

    public function autogenerate_securecode()
    {
        $data = \Request::only('email', 'lang');

        if (isset($data['lang'])) {
            $this->lang = $data['lang'];
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        $validator = \Validator::make($data, [
            'email' => 'required|email|max:150',
        ]);

        if ($validator->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        $member = Customer::where('cus_id', $this->cus_id)->where('email','=',$data['email'])->first();

        if ($member) {
            $rand_securecode = mt_rand(100000, 999999);

            try {
                $member->payment_secure_code = \Hash::make($rand_securecode);
                $member->save();

                // email to member new generated secure code
                if ($member->email_verified) {
                    \Mail::send('front.emails.reset_secure_code', ['securecode' => $rand_securecode, 'email' => $member->email], function (Message $m) use ($member) {
                        $m->to($member->email, $member->cus_name)->subject('MeiHome Payment Secure Code');
                    });
                }
            } catch (\Exception $e) {
                return \Response::json([
                    'status' => 403,
                    'message' => trans('api.secureCode') . trans('api.failGenerate'),
                ]);
            }

            return \Response::json([
                'status' => 200,
                'message' => trans('api.secureCode') . trans('api.generated'),
            ]);
        }

        return \Response::json([
            'status' => 403,
            'message' => trans('api.member') . trans('api.notFound'),
        ]);
    }

    public function offline_order_history()
    {
        $data = \Request::only('page', 'size', 'sort', 'lang');

        if (isset($data['lang'])) {
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        // data validation
        $niceNames = array(
            'page' => trans('api.page'),
            'size' => trans('api.size'),
        );

        $v = Validator::make($data, [
            'page' => 'required|integer',
            'size' => 'required|integer',
            'sort' => 'nullable|integer',
        ]);
        $v->setAttributeNames($niceNames);

        if ($v->fails())
        {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ]);
        }

        try {
            // Grab online order history
            $orders = OrderOffline::with('wallet')
            ->where('order_offline.cust_id', $this->cus_id)
            ->where('order_offline.status', 1);

            switch (trim($data['sort'])) {
                // Sort : Latest
                case '1':
                    $orders->orderBy('order_offline.created_at', 'desc');
                    break;
                // Sort : Highest Amount
                case '2':
                    $orders->orderBy('order_offline.order_total_token', 'desc');
                    break;
                // Sort : Lowest Amount
                case '3':
                    $orders->orderBy('order_offline.order_total_token', 'asc');
                    break;

                default:
                    $orders->orderBy('order_offline.id', 'desc');
                    break;
            }

            $orders = $orders->paginate(trim($data['size']));
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.failRetrieve')
            ]);
        }

        $details = [];
        foreach ($orders as $order) {
            $merchant = Merchant::find($order->mer_id);
            $merchantName = null;
            if (!empty($merchant->mer_fname))
                $merchantName = trim($merchant->mer_fname);
            if (!empty($merchant->mer_lname))
                $merchantName .= ' ' . trim($merchant->mer_lname);

            $wallet_type = '-';

            if ($order->wallet_id == 99) {
                $wallet_type = 'Hemma';
            } else {
                $wallet_type = ($order->wallet) ? $order->wallet->name : '-';
            }

            $details[] = [
                'order_id' => $order->id,
                'invoice_no' => $order->inv_no,
                'merchant_id' => $order->mer_id,
                'merchant_name' => $merchantName,
                'store_id' => $order->store_id,
                'store_name' => $this->getStoreNameById($order->store_id),
                'currency' => $order->currency,
                'amount' => $order->amount,
                'credit' => $order->v_token,
                'merchant_platform_charge_percentage' => $order->merchant_platform_charge_percentage,
                'merchant_platform_charge_credit' => $order->merchant_platform_charge_token,
                'customer_charge_percentage' => $order->customer_charge_percentage,
                'customer_charge_credit' => $order->customer_charge_token,
                'total' => $order->order_total_token,
                'type' => $order->type,
                'status' => $order->status,
                'remark' => $order->remark,
                'paid_date' => $order->paid_date,
                'created_date' => $order->created_at,
                'wallet_type' => $wallet_type,
            ];
        }


        if (empty($details))
            return \Response::json([
                'status' => 404,
                'message' => 'Offline order history not found',
            ]);

        return \Response::json([
            'status' => 200,
            'message' => 'Offline order history found',
            'count' => $orders->total(),
            'total_pages' => $orders->lastPage(),
            'data' => $details
        ]);
    }

    private function getStoreNameById($id) {
        $store = Store::find($id);

        return ($store) ? $store->stor_name : '';
    }

    public function memberInfo()
    {
        $input = \Request::only(['lang']);

        if (isset($input['lang'])) {
            \App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // validate member
        $member = Customer::leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_customer.cus_country')->where('cus_id', trim($this->cus_id))->first();
        if (empty($member)) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.memberId') . trans('api.notFound')
            ]);
        }

        $cus_wallets = CustomerRepo::get_customer_available_wallet($member->cus_id);
        $wallets = [];
        foreach ($cus_wallets as $cw) {
            $wallets[$cw->wallet->name] = $cw->credit;
        }

        return \Response::json([
            'status' => 200,
            'member_id' => $member->cus_id,
            'member_name' => $member->cus_name,
            'member_avatar' => ($member->cus_pic) ? env('IMAGE_DIR') . '/avatar/' . $member->cus_id . '/' . $member->cus_pic : null,
            'member_credit_balance' => $member->v_token,
            'member_special_wallet' => $member->special_wallet,
            'member_wallet' => $wallets
        ]);
    }
}