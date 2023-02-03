<?php

namespace App\Http\Controllers\Api\V1;

use App;
use App\Http\Controllers\Controller;
use Validator;
use App\Models\Order;
use App\Models\OrderOffline;
use App\Models\Merchant;
use App\Models\Customer;
use App\Models\Store;

class MemberController extends Controller {

    public function online_order_history()
    {
        $data = \Request::only('member_id', 'page', 'size', 'sort', 'lang');

        if (isset($data['lang'])) {
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        // data validation
        $niceNames = array(
            'member_id' => trans('api.memberId'),
            'page' => trans('api.page'),
            'size' => trans('api.size'),
        );

        $v = Validator::make($data, [
            'member_id' => 'required|integer',
            'page' => 'required|integer',
            'size' => 'required|integer',
            'sort' => 'nullable|integer',
        ]);
        $v->setAttributeNames($niceNames);

        if ($v->fails()) {
            $intErrors = $v->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        try {
            // Grab online order history
            $orders = Order::leftJoin('nm_product', 'nm_product.pro_id', '=', 'nm_order.order_pro_id')
                ->where('nm_order.order_cus_id', trim($data['member_id']))
                ->where('nm_order.order_type', 1);

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
                'status' => 0,
                'message' => trans('api.failRetrieve')
            ]);
        }

        $details = [];
        foreach ($orders as $order) {
            $details[] = [
                'order_id' => $order->order_id,
                'transaction_id' => $order->transaction_id,
                'product_id' => $order->order_pro_id,
                'product_name' => $order->pro_title_en,
                'total_vcoin' => $order->order_vtokens,
                'order_status' => $order->order_status,
                'order_date' => $order->order_date,
                'remark' => $order->remark,
            ];
        }

        if (empty($details))
            return \Response::json([
                'status' => 1,
                'message' => 'Online order history not found',
            ]);

        return \Response::json([
            'status' => 1,
            'message' => 'Online order history found',
            'count' => $orders->total(),
            'total_pages' => $orders->lastPage(),
            'data' => $details
        ]);
    }

    public function offline_order_history()
    {
        $data = \Request::only('member_id', 'page', 'size', 'sort', 'lang');

        if (isset($data['lang'])) {
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        // data validation
        $niceNames = array(
            'member_id' => trans('api.memberId'),
            'page' => trans('api.page'),
            'size' => trans('api.size'),
        );

        $v = Validator::make($data, [
            'member_id' => 'required|integer',
            'page' => 'required|integer',
            'size' => 'required|integer',
            'sort' => 'nullable|integer',
        ]);
        $v->setAttributeNames($niceNames);

        if ($v->fails()) {
            $intErrors = $v->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        try {
            // Grab online order history
            $orders = OrderOffline::where('order_offline.cust_id', trim($data['member_id']))->where('order_offline.status', 1);

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
                'status' => 0,
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
                      'merchant_platform_charge_token' => $order->merchant_platform_charge_token,
                      'customer_charge_percentage' => $order->customer_charge_percentage,
                      'customer_charge_token' => $order->customer_charge_token,
                      'total' => $order->order_total_token,
                      'type' => $order->type,
                      'status' => $order->status,
                      'remark' => $order->remark,
                      'paid_date' => $order->paid_date,
                      'created_date' => $order->created_at,
                  ];
              }


        if (empty($details))
            return \Response::json([
                'status' => 1,
                'message' => 'Offline order history not found',
            ]);

        return \Response::json([
            'status' => 1,
            'message' => 'Offline order history found',
            'count' => $orders->total(),
            'total_pages' => $orders->lastPage(),
            'data' => $details
        ]);
    }

    public function merchant_offline_order_history()
    {
        $data = \Request::only('merchant_id', 'store_id', 'page', 'size', 'sort', 'lang');

        if (isset($data['lang'])) {
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        // data validation
        $niceNames = array(
            'merchant_id' => trans('api.merchantId'),
            'page' => trans('api.page'),
            'size' => trans('api.size'),
        );

        $v = Validator::make($data, [
            'merchant_id' => 'required|integer',
            'store_id' => 'nullable|integer',
            'page' => 'required|integer',
            'size' => 'required|integer',
            'sort' => 'nullable|integer',
        ]);
        $v->setAttributeNames($niceNames);

        if ($v->fails()) {
            $intErrors = $v->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        try {
            // Grab online order history
            $orders = OrderOffline::where('order_offline.mer_id', trim($data['merchant_id']))->where('order_offline.status', 1);

            if (!empty(trim($data['store_id']))) {
                $orders->where('order_offline.store_id', trim($data['store_id']));
            }

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
                'status' => 0,
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

            $customer = Customer::find($order->cust_id);
            $cusName = (!empty($customer->cus_name)) ? trim($customer->cus_name) : '';

            $details[] = [
                    'order_id' => $order->id,
                    'invoice_no' => $order->inv_no,
                    'merchant_id' => $order->mer_id,
                    'merchant_name' => $merchantName,
                    'store_id' => $order->store_id,
                    'store_name' => $this->getStoreNameById($order->store_id),
                    'customer_id' => $order->cust_id,
                    'customer_name' => $cusName,
                    'currency' => $order->currency,
                    'amount' => $order->amount,
                    'credit' => $order->v_token,
                    'merchant_platform_charge_percentage' => $order->merchant_platform_charge_percentage,
                    'merchant_platform_charge_token' => $order->merchant_platform_charge_token,
                    'customer_charge_percentage' => $order->customer_charge_percentage,
                    'customer_charge_token' => $order->customer_charge_token,
                    'total' => $order->order_total_token,
                    'type' => $order->type,
                    'status' => $order->status,
                    'remark' => $order->remark,
                    'paid_date' => $order->paid_date,
                    'created_date' => $order->created_at,
                ];
          }



        if (empty($details))
            return \Response::json([
                'status' => 1,
                'message' => 'Offline order history not found',
            ]);

        return \Response::json([
            'status' => 1,
            'message' => 'Offline order history found',
            'count' => $orders->total(),
            'total_pages' => $orders->lastPage(),
            'data' => $details
        ]);
    }

    public function memberInfo()
    {
        $input = \Request::only(['member_id', 'lang']);

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $niceNames = array(
            'member_id' => trans('api.memberId')
        );

        $validator = Validator::make($input, [
            'member_id' => 'required|integer',
        ]);
        $validator->setAttributeNames($niceNames);

        if ($validator->fails()) {
            $intErrors = $validator->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];
            return $errors;
        }

        // validate member
        $member = Customer::leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_customer.cus_country')->where('cus_id', trim($input['member_id']))->first();
        if (empty($member)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.memberId') . trim($input['member_id']) . trans('api.notFound')
            ]);
        }


        return \Response::json([
            'status' => 1,
            'member_id' => $member->cus_id,
            'member_name' => $member->cus_name,
            'member_vcoin_balance' => $member->v_token,
        ]);
    }

    public function merchantInfo()
    {
        $input = \Request::only(['merchant_id', 'lang']);

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $niceNames = array(
            'merchant_id' => trans('api.merchantId')
        );

        $validator = Validator::make($input, [
            'merchant_id' => 'required|integer',
        ]);
        $validator->setAttributeNames($niceNames);

        if ($validator->fails()) {
            $intErrors = $validator->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];
            return $errors;
        }

        // validate member
        $merchant = Merchant::where('mer_id', trim($input['merchant_id']))->select('mer_id', 'mer_fname', 'mer_lname', 'username','mer_vtoken')->first();
        if (empty($merchant)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.merchantId') . trim($input['merchant_id']) . trans('api.notFound')
            ]);
        }


        return \Response::json([
            'status' => 1,
            'merchant_id' => $merchant->mer_id,
            'merchant_name' => $merchant->mer_fname . ' ' . $merchant->mer_lname,
            'merchant_vcoin_balance' => $merchant->mer_vtoken,
        ]);
    }

     private function getStoreNameById($id) {
        $store = Store::find($id);

        return ($store) ? $store->stor_name : '';
    }

}
