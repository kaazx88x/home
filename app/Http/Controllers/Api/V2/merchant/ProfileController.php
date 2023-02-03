<?php

namespace App\Http\Controllers\Api\V2\merchant;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\OrderOffline;
use App\Models\Customer;
use App\Models\Store;
use Validator;

class ProfileController extends Controller
{
    protected $mer_id;

    public function __construct()
    {
        if (\Auth::guard('api_storeusers')->check()) {
            $this->mer_id = \Auth::guard('api_storeusers')->user()->mer_id;
        }

        if (\Auth::guard('api_merchants')->check()) {
            $this->mer_id = \Auth::guard('api_merchants')->user()->mer_id;
        }
    }

    public function merchantInfo()
    {
        $input = \Request::only('lang');

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // validate member
        $merchant = Merchant::where('mer_id', trim($this->mer_id))->select('mer_id', 'mer_fname', 'mer_lname', 'username','mer_vtoken')->first();
        if (empty($merchant)) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.merchantId') . trim($this->mer_id) . trans('api.notFound')
            ], 404);
        }

        return \Response::json([
            'status' => 200,
            'merchant_id' => $merchant->mer_id,
            'merchant_name' => $merchant->mer_fname . ' ' . $merchant->mer_lname,
            'merchant_credit_balance' => $merchant->mer_vtoken,
        ]);
    }

    public function offline_order_history()
    {
        $data = \Request::only('store_id', 'page', 'size', 'sort', 'lang');

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
            'store_id' => 'nullable|integer',
            'page' => 'required|integer',
            'size' => 'required|integer',
            'sort' => 'nullable|integer',
        ]);
        $v->setAttributeNames($niceNames);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ], 422);
        }

        try {
            // Grab online order history
            $orders = OrderOffline::select('order_offline.*', 'wallets.name_en as wallet_name')
            ->leftJoin('wallets', 'wallets.id', 'order_offline.wallet_id')
            ->where('order_offline.mer_id', trim($this->mer_id))
            ->where('order_offline.status', 1);

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

            if ($order->wallet_id == 2) {
                $wallet_type = $order->wallet_name;
            } else if ($order->wallet_id == 99) {
                $wallet_type = 'Hemma';
            }

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
                'vcredit' => $order->v_token,
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
}