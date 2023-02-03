<?php

namespace App\Http\Controllers\Api\V2\member;

use App;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;
use App\Models\OrderOffline;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Country;
use App\Models\MerchantVTokenLog;
use App\Models\CustomerVTokenLog;
use App\Models\Store;
use App\Models\OrderOfflineLog;
use App\Models\AdminSetting;
use App\Models\OfflineCategory;
use App\Models\CustomerWallet;
use App\Models\Wallet;
use App\Repositories\StoreRepo;
use App\Repositories\CustomerRepo;
use App\Repositories\LimitRepo;
use App\Repositories\OrderOfflineRepo;

class PaymentController extends Controller
{
    // protected $customer;
    protected $cus_id;

    public function __construct(Mailer $mailer)
    {
        if (\Auth::guard('api_members')->check()) {
            $this->cus_id = \Auth::guard('api_members')->user()->cus_id;
            // $this->customer = Customer::find(trim($this->cus_id));
        }

        $this->mailer = $mailer;
    }

    // member/order/claim
    public function claimOrder()
    {
        $data = \Request::only('merchant_id', 'store_id', 'order_id', 'inv_no', 'lang');

        if (isset($data['lang']))
        {
            App::setLocale($data['lang']);
        }
        unset($data['lang']);

        // input validation
        $validator = Validator::make($data, [
            'merchant_id' => 'required|integer',
            'store_id' => 'required|integer',
            'order_id' => 'required|integer',
            'inv_no' => 'required|alpha_dash|max:50',
        ]);

        if ($validator->fails())
        {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        // merchant validation
        $merchant = Merchant::find(trim($data['merchant_id']));
        if (empty($merchant))
        {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.merchantId') . trim($data['merchant_id']) . trans('api.notFound')
            ]);
        }

        if ($merchant->mer_staus != 1) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.merchantId') . $merchant->mer_id . trans('api.notActive'),
            ]);
        }

        // store validation
        if (!empty(trim($data['store_id'])))
        {
            $store = Store::where('stor_id', trim($data['store_id']))
            ->where('stor_merchant_id', trim($data['merchant_id']))
            ->where('stor_status', 1)
            ->where('stor_type', '>', 0)
            ->first();

            if (empty($store))
            {
                $store = Store::where('stor_merchant_id', trim($data['merchant_id']))
                ->where('stor_status', 1)
                ->where('stor_type', '>', 0)
                ->first();
            }
        }
        else
        {
            $store = Store::where('stor_merchant_id', trim($data['merchant_id']))
            ->where('stor_status', 1)
            ->where('stor_type', '>', 0)
            ->first();
        }

        if (empty($store))
        {
            return \Response::json([
                'status' => 404,
                'message' => $this->getStoreNameById(trim($data['store_id'])) . trans('api.notFound')
            ]);
        }

        // check store's accept payment
        if ($store->accept_payment != 1) {
            return \Response::json([
                'status' => 400,
                'message' => $this->getStoreNameById(trim($data['store_id'])) . trans('api.notAcceptPayment')
            ]);
        }

        $transactedVToken = null;
        $order = OrderOffline::find(trim($data['order_id']));

        if (empty($order))
        {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.billNo') . trim($data['inv_no']) . trans('api.notFound')
            ]);
        }

        if (!empty(trim($data['merchant_id'])))
        {
            if ($order->mer_id != trim($data['merchant_id']))
            {
                return \Response::json([
                    'status' => 400,
                    'message' => trans('api.billNo') . $order->inv_no . trans('api.doesNotBelongToThis') . trans('api.merchant')
                ]);
            }
        }

        if (!empty(trim($data['store_id'])))
        {
            if ($order->store_id != trim($data['store_id']))
            {
                return \Response::json([
                    'status' => 400,
                    'message' => trans('api.billNo') . trim($data['inv_no']) . trans('api.doesNotBelongToThis') . trans('api.store_id')
                ]);
            }
        }

        // get wallet_id
        $store_wallet = Store::select('nm_store.stor_id', 'offline_categories.wallet_id', 'offline_categories.id as offline_categories_id', 'offline_categories.parent_list')
                ->leftJoin('nm_store_offline_category', 'nm_store_offline_category.store_id', '=', 'nm_store.stor_id')
                ->leftJoin('offline_categories', 'offline_categories.id', '=', 'nm_store_offline_category.offline_category_id')
                ->where('stor_merchant_id', $merchant->mer_id)
                ->where('stor_id', $store->stor_id)
                ->where('stor_status', 1)
                ->where('stor_type', '>', 0)
                ->orderBy('nm_store_offline_category.id', 'asc')
                ->first();

        if (is_null($store_wallet->wallet_id))
        {
            $parent_id = intval(explode(",", $store_wallet->parent_list)[0]);
            $update_wallet = OfflineCategory::select('wallet_id')->where('id', $parent_id)->first();
            $store_wallet->wallet_id = $update_wallet->wallet_id;
        }

        if (!isset($store_wallet->wallet_id))
        {
            return \Response::json([
                'status' => 404,
                'message' => 'Store wallet' . trans('api.notFound')
            ]);
        }

        $customer = Customer::find($this->cus_id);
        if (empty($customer))
        {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.memberId') . $this->cus_id . trans('api.notFound')
            ]);
        }

        $transactedVToken = $order->v_token;
        // total token should replace original token due to service charges
        if (!empty($order->order_total_token))
            $transactedVToken = $order->order_total_token;

        // Check sufficient credit
        // $cus_credit = $customer->v_token;
        // $cus_credit = CustomerRepo::get_customer_offline_wallet($customer->cus_id);
        $cus_credit = CustomerWallet::where('customer_id', $customer->cus_id)
        ->where('wallet_id', $store_wallet->wallet_id)
        ->pluck('credit')
        ->first();

        if ((in_array($data['store_id'], explode(',', env('SPECIAL_STORE_ID')))) && ($customer->special_wallet >= $transactedVToken)) {
            $cus_credit = $customer->special_wallet;
        }

        if ($cus_credit < $transactedVToken)
        {
            $cus_wallets = CustomerRepo::get_customer_available_wallet($customer->cus_id);
            $wallets = [];
            foreach ($cus_wallets as $cw) {
                $wallets[$cw->wallet->name] = $cw->credit;
            }

            return \Response::json([
                'status' => 400,
                'message' => trans('api.insufficientFund', [
                    'wallet_type' => $this->getWalletName($store_wallet->wallet_id),
                ]),
                'member_credit_balance' => $customer->v_token,
                'member_special_wallet' => $customer->special_wallet,
                'member_wallet' => $wallets
            ]);
        }

        if (empty($order->cust_id))
        {
            $order->cust_id = $this->cus_id;
            $order->save();
        }
        else
        {
            if ($order->cust_id != $this->cus_id)
            {
                return \Response::json([
                    'status' => 400,
                    'message' => trans('api.billNo') . $data['inv_no'] . trans('api.hasAlreadyBeen') . trans('api.claimed')
                ]);
            }
        }


        switch ($order->status)
        {
            case '0':
                $stores = StoreRepo::api_get_stores($order->mer_id);

                $retObj['status'] = 200;
                $retObj['message'] = trans('api.billNo') . $order->inv_no . trans('api.hasBeenClaimedSuccessfully');
                $retObj['order_status'] = 'unpaid';

                $retObj['order_id'] = $order->id;
                $retObj['inv_no'] = $order->inv_no;
                $retObj['merchant_name'] = $this->getMerchantNameById($order->mer_id);
                $retObj['currency'] = $order->currency;
                $retObj['amount'] = $order->amount;
                $retObj['credit'] = $order->v_token;
                $retObj['service_charge_percentage'] = $order->customer_charge_percentage;
                $retObj['service_charge_credit'] = $order->customer_charge_token;
                $retObj['platform_charge_percentage'] = $order->merchant_platform_charge_percentage;
                $retObj['platform_charge_credit'] = $order->merchant_platform_charge_token;
                $retObj['total_credit'] = $order->order_total_token;
                $retObj['stores'] = $stores;


                return \Response::json($retObj);
                break;
            case '1':
                return \Response::json([
                    'status' => 400,
                    'message' => trans('api.billNo') . $order->inv_no . trans('api.hasAlreadyBeen') . trans('api.paid')
                ]);
                break;
            case '2':
            case '3':
                return \Response::json([
                    'status' => 400,
                    'message' => trans('api.billNo') . $order->inv_no . trans('api.hasAlreadyBeen') . trans('api.cancelled')
                ]);
                break;
            default:
                throw new \Exception('invalid order status');
                break;
        }
    }

    // member/order/confirm
    public function confirmOrder()
    {
        $data = \Request::only('merchant_id', 'store_id', 'order_id', 'inv_no', 'security_code', 'lang');

        if (isset($data['lang'])) {
            App::setLocale($data['lang']);
        }
        unset($data['lang']);

        // input validation
        $validator = Validator::make($data, [
            'merchant_id' => 'required|integer',
            'store_id' => 'required|integer',
            'order_id' => 'required|integer',
            'inv_no' => 'required|alpha_dash|max:50',
            'security_code' => 'required|digits:6',
        ]);

        if ($validator->fails())
        {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        // merchant validation
        $merchant = Merchant::find(trim($data['merchant_id']));
        if (empty($merchant)) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.merchantId') . trim($data['merchant_id']) . trans('api.notFound')
            ]);
        }

        if ($merchant->mer_staus != 1) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.merchantId') . $merchant->mer_id . trans('api.notActive'),
            ]);
        }

        // store validation
        $store = Store::where('stor_id', trim($data['store_id']))->where('stor_status', 1)->where('stor_type', '>', 0)->first();
        if (empty($store)) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.storeId') . trim($data['store_id']) . trans('api.notFound')
            ]);
        }

        if ($store->stor_merchant_id != trim($data['merchant_id'])) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.storeId') . trim($data['store_id']) . trans('api.doesNotBelongToThis') . trans('api.merchantId') . trim($data['merchant_id'])
            ]);
        }

        // check store's accept payment
        if ($store->accept_payment != 1) {
            return \Response::json([
                'status' => 400,
                'message' => $this->getStoreNameById(trim($data['store_id'])) . trans('api.notAcceptPayment')
            ]);
        }

        // member validation
        $customer = Customer::find($this->cus_id);
        if (empty($customer)) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.memberId') . $this->cus_id . trans('api.notFound')
            ]);
        }

        if (!\Hash::check(trim($data['security_code']), $customer->payment_secure_code)) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.wrongCode')
            ]);
        }

        $order = OrderOffline::find(trim($data['order_id']));
        if (empty($order)) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.billNo') . trim($data['inv_no']) . trans('api.notFound')
            ]);
        }

        if ($order->mer_id != trim($data['merchant_id'])) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.billNo') . trim($data['inv_no']) . trans('api.doesNotBelongToThis') . trans('api.merchant')
            ]);
        }

        if ($order->store_id != trim($data['store_id'])) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.billNo') . trim($data['inv_no']) . trans('api.doesNotBelongToThis') . trans('api.store')
            ]);
        }

        // initialization
        $transactedVToken = $order->v_token;

        // total token should replace original token due to service charges
        if (!empty($order->order_total_token))
            $transactedVToken = $order->order_total_token;

        // retrieve merchant name
        $merchantName = $this->getMerchantNameById($order->mer_id);

        if ($order->status == 1) {
            $cus_wallets = CustomerRepo::get_customer_available_wallet($customer->cus_id);
            $wallets = [];
            foreach ($cus_wallets as $cw) {
                $wallets[$cw->wallet->name] = $cw->credit;
            }

            $retObj = [
                'status' => 200,
                'message' => trans('api.payment') . trans('api.success'),
                'order_id' => $order->id,
                'inv_no' => $order->inv_no,
                'tax_inv_no' => $order->tax_inv_no,
                'currency' => $order->currency,
                'amount' => $order->amount,
                'credit' => $order->v_token,
                'service_charge_percentage' => $order->customer_charge_percentage,
                'service_charge_credit' => $order->customer_charge_token,
                'platform_charge_percentage' => $order->merchant_platform_charge_percentage,
                'platform_charge_credit' => $order->merchant_platform_charge_token,
                'total_credit' => $order->order_total_token,
                'merchant_name' => $merchantName,
                'member_credit_balance' => $customer->v_token,
                'member_special_wallet' => $customer->special_wallet,
                'paid_date' => $order->paid_date,
                'member_wallet' => $wallets
            ];

            return \Response::json($retObj);
        }

        // get wallet_id
        $store_wallet = Store::select('nm_store.stor_id', 'offline_categories.wallet_id', 'offline_categories.id as offline_categories_id', 'offline_categories.parent_list')
                ->leftJoin('nm_store_offline_category', 'nm_store_offline_category.store_id', '=', 'nm_store.stor_id')
                ->leftJoin('offline_categories', 'offline_categories.id', '=', 'nm_store_offline_category.offline_category_id')
                ->where('stor_merchant_id', $merchant->mer_id)
                ->where('stor_id', $store->stor_id)
                ->where('stor_status', 1)
                ->where('stor_type', '>', 0)
                ->orderBy('nm_store_offline_category.id', 'asc')
                ->first();

        if (is_null($store_wallet->wallet_id))
        {
            $parent_id = intval(explode(",", $store_wallet->parent_list)[0]);
            $update_wallet = OfflineCategory::select('wallet_id')->where('id', $parent_id)->first();
            $store_wallet->wallet_id = $update_wallet->wallet_id;
        }

        if (!isset($store_wallet->wallet_id))
        {
            return \Response::json([
                'status' => 404,
                'message' => 'Store wallet' . trans('api.notFound')
            ]);
        }

        // validate customer's v_token
        // $cus_credit = $customer->v_token;
        // $cus_credit = CustomerRepo::get_customer_offline_wallet($customer->cus_id);
        $cus_credit = CustomerWallet::where('customer_id', $customer->cus_id)
        ->where('wallet_id', $store_wallet->wallet_id)
        ->pluck('credit')
        ->first();
        $credit_type = 'v_token';

        if ((in_array($data['store_id'], explode(',', env('SPECIAL_STORE_ID')))) && ($customer->special_wallet >= $transactedVToken)) {
            $cus_credit = $customer->special_wallet;
            $credit_type = 'special_wallet';
        }

        if ($cus_credit < $transactedVToken) {
            $cus_wallets = CustomerRepo::get_customer_available_wallet($customer->cus_id);
            $wallets = [];
            foreach ($cus_wallets as $cw) {
                $wallets[$cw->wallet->name] = $cw->credit;
            }

            return \Response::json([
                'status' => 400,
                'message' => trans('api.insufficientFund', [
                    'wallet_type' => $this->getWalletName($store_wallet->wallet_id),
                ]),
                'member_credit_balance' => $customer->v_token,
                'member_special_wallet' => $customer->special_wallet,
                'member_wallet' => $wallets
            ]);
        }

        $mainAcc = Merchant::find(180);
        if (empty($mainAcc)) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.primaryAcc') . trans('api.notFound'),
            ]);
        }

        // check trans exceed limit or not
        $check = LimitRepo::check_payment_limitation('storeLimit', $order->amount, $store->stor_id, $customer->cus_id);
        if($check) {
            return \Response::json([
                'status' => 400,
                'message' => $check
            ]);
        }

        //check member limit
        $check = LimitRepo::check_payment_limitation('customerLimit', $order->amount, null, $customer->cus_id);
        if($check) {
            return \Response::json([
                'status' => 400,
                'message' => $check
            ]);
        }

        /// BEGIN transaction
        try {
            \DB::transaction(function() use ($merchant, $customer, $order, $merchantName, $mainAcc, $store, $credit_type, $store_wallet)
            {
                $origToken = $order->v_token;
                $customerVToken = $order->order_total_token;
                $customerChargeToken = $order->customer_charge_token;
                $platformChargeToken = $order->merchant_platform_charge_token;
                $merchantCommission = $order->merchant_charge_token;
                $merchantVToken = $origToken - $merchantCommission;

                if ($credit_type == 'v_token') {
                    // deduct token from customer
                    $customer->v_token -= $customerVToken;
                    // this part should not happen but as a safeguard
                    if ($customer->v_token < 0) {
                        $customer->v_token = 0;
                    }

                    // deduct from customer wallet
                    $cus_wallet = CustomerWallet::where('customer_id', $customer->cus_id)->where('wallet_id', $store_wallet->wallet_id)->first();
                    $cus_wallet->credit -= $customerVToken;
                    $cus_wallet->save();
                    $wallet_id = $store_wallet->wallet_id;
                } else {
                    // deduct token from customer
                    $customer->special_wallet -= $customerVToken;
                    // this part should not happen but as a safeguard
                    if ($customer->special_wallet < 0) {
                        $customer->special_wallet = 0;
                    }
                    $wallet_id = 99;
                }

                $customer->save();

                $arrCustomerVTokenLog = [
                    'cus_id' => $customer->cus_id,
                    'credit_amount' => 0,
                    'debit_amount' => $customerVToken,
                    'offline_order_id' => $order->id,
                    'remark' => 'order payment' . (($credit_type == 'special_wallet') ? ' - Hemma Wallet ' : '') . (isset($order->remark) ? ': ' . $order->remark : ''),
                    'wallet_id' => ($credit_type == 'v_token') ? 2 : 0
                ];
                CustomerVTokenLog::firstOrCreate($arrCustomerVTokenLog);

                // add vtoken to merchant
                $merchant->mer_vtoken += $merchantVToken;
                $merchant->save();

                $arrMerchantVTokenLog = [
                    'mer_id' => $merchant->mer_id,
                    'credit_amount' => $merchantVToken,
                    'debit_amount' => 0,
                    'offline_order_id' => $order->id,
                    'remark' => 'sales' . (isset($order->remark) ? ': ' . $order->remark : '')
                ];
                MerchantVTokenLog::firstOrCreate($arrMerchantVTokenLog);

                // add merchant & customer service charge vtoken to main account
                $mainAcc->mer_vtoken += $merchantCommission;
                $mainAcc->mer_vtoken += $customerChargeToken;
                $mainAcc->mer_vtoken += $platformChargeToken;
                $mainAcc->save();

                // add merchant commission vtoken to main account
                $arrMerchantToMainAccVTokenLog = [
                    'mer_id' => $mainAcc->mer_id,
                    'credit_amount' => $merchantCommission,
                    'debit_amount' => 0,
                    'offline_order_id' => $order->id,
                    'remark' => 'merchant commission: ' . $merchant->mer_id . ' ' . $merchantName
                ];
                MerchantVTokenLog::firstOrCreate($arrMerchantToMainAccVTokenLog);

                // add customer service charge vtoken to main account
                $arrCustomerToMainAccVTokenLog = [
                    'mer_id' => $mainAcc->mer_id,
                    'credit_amount' => $customerChargeToken,
                    'debit_amount' => 0,
                    'offline_order_id' => $order->id,
                    'remark' => trans('localize.gst') . ':' . $customer->cus_id . ' ' . $customer->cus_name
                ];
                MerchantVTokenLog::firstOrCreate($arrCustomerToMainAccVTokenLog);

                // add merchant platform charge vtoken to main account
                $arrMerPlatformToMainAccVTokenLog = [
                    'mer_id' => $mainAcc->mer_id,
                    'credit_amount' => $platformChargeToken,
                    'debit_amount' => 0,
                    'offline_order_id' => $order->id,
                    'remark' => trans('localize.platform_charges') . ':' . $merchant->mer_id . ' ' . $merchantName
                ];
                MerchantVTokenLog::firstOrCreate($arrMerPlatformToMainAccVTokenLog);

                //Generate Tax Inv Number
                $order_tax_inv = OrderOfflineRepo::generate_tax_inv();

                // update order
                $order->status = 1;
                $order->cust_id = $customer->cus_id;
                $order->tax_inv_no = $order_tax_inv;
                $order->paid_date = new \DateTime;
                $order->wallet_id = $wallet_id;
                $order->save();

                // update store transaction
                $update_limit = LimitRepo::update_limit_transaction('offline', $order->id);
            });

            $cus_wallets = CustomerRepo::get_customer_available_wallet($customer->cus_id);
            $wallets = [];
            foreach ($cus_wallets as $cw) {
                $wallets[$cw->wallet->name] = $cw->credit;
            }

            return \Response::json([
                'status' => 200,
                'message' => trans('api.payment') . trans('api.success'),
                'order_id' => $order->id,
                'inv_no' => $order->inv_no,
                'tax_inv_no' => $order->tax_inv_no,
                'currency' => $order->currency,
                'amount' => $order->amount,
                'credit' => $order->v_token,
                'service_charge_percentage' => $order->customer_charge_percentage,
                'service_charge_credit' => $order->customer_charge_token,
                'platform_charge_percentage' => $order->merchant_platform_charge_percentage,
                'platform_charge_credit' => $order->merchant_platform_charge_token,
                'total_credit' => $order->order_total_token,
                'merchant_name' => $merchantName,
                'member_credit_balance' => $customer->v_token,
                'member_special_wallet' => $customer->special_wallet,
                'member_wallet' => $wallets,
                'deduct_from_wallet' => (($order->wallet_id == 99) ? 'Hemma' : $this->getWalletName($order->wallet_id))
            ]);

        } catch (\Exception $e) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.payment') . trans('api.fail')
            ]);
        }
        /// END transaction
    }

    // /member/order/cancel
    public function cancelOrder()
    {
        $data = \Request::only('order_id', 'inv_no');

        if (isset($data['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($data['lang']);

        // input validation
        $validator = Validator::make($data, [
            'order_id' => 'required|integer',
            'inv_no' => 'required|alpha_dash|max:50',
        ]);

        if ($validator->fails())
        {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        $order = OrderOffline::find(trim($data['order_id']));
        if (empty($order)) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.billNo') . trim($data['inv_no']) . trans('api.notFound')
            ]);
        }

        if (isset($order->cust_id)) {
            if ($order->cust_id != $this->cus_id) {
                return \Response::json([
                    'status' => 400,
                    'message' => trans('api.billNo') . $order->inv_no . trans('api.doesNotBelongToThis') . trans('api.member')
                ]);
            }
        } else {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.billNo') . $order->inv_no . trans('api.hasNotBeen') . trans('api.claimed')
            ]);
        }

        switch ($order->status) {
            case 0:
                $order->status = 2;
                $order->save();

                return \Response::json([
                    'status' => 200,
                    'message' => trans('api.orderCancelledByMember')
                ]);
                break;
            case 1:
                return \Response::json([
                    'status' => 400,
                    'message' => trans('api.billNo') . $order->inv_no . trans('api.cannotCancelAsPaymentMade')
                ]);
            case 2:
            case 3:
                return \Response::json([
                    'status' => 400,
                    'message' => trans('api.billNo') . $order->inv_no . trans('api.hasAlreadyBeen') . trans('api.cancelled')
                ]);
            default:
                throw new \Exception('invalid order status');
                break;
        }
    }

    public function checkStore()
    {
        $input = \Request::only('store_id', 'lang');

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $niceNames = array(
            'store_id' => trans('api.store_id'),
        );

        $validator = Validator::make($input, [
            'store_id' => 'required|integer',
        ]);
        $validator->setAttributeNames($niceNames);

        if ($validator->fails())
        {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        $store = StoreRepo::get_store_by_id(trim($input['store_id']));

        if (!$store)
            return \Response::json([
                'status' => 404,
                'message' => $this->getStoreNameById(trim($input['store_id'])) . trans('api.notFound'),
            ]);

        if ($store->stor_status != 1)
            return \Response::json([
                'status' => 400,
                'message' => $this->getStoreNameById(trim($input['store_id'])) . trans('api.notActive'),
            ]);

        // if ($store->stor_type == 0)
        if ($store->stor_type != 1)
            return \Response::json([
                'status' => 400,
                'message' => $this->getStoreNameById(trim($input['store_id'])) . trans('api.notOffline'),
            ]);

        // check store's accept payment
        if ($store->accept_payment != 1) {
            return \Response::json([
                'status' => 400,
                'message' => $this->getStoreNameById(trim($input['store_id'])) . trans('api.notAcceptPayment')
            ]);
        }

        $merchant = Merchant::find($store->stor_merchant_id);

        if (!$merchant)
            return \Response::json([
                'status' => 404,
                'message' => trans('api.merchantId') . $store->stor_merchant_id . trans('api.notFound'),
            ]);

        if ($merchant->mer_staus != 1) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.merchantId') . $store->stor_merchant_id . trans('api.notActive'),
            ]);
        }

        // Get Store Image Gallery
        $store_images = StoreRepo::get_images($store->stor_id);
        $images = [];
        foreach ($store_images as $key => $sm) {
            $link = env('IMAGE_DIR').'/store/'.$sm->stor_merchant_id.'/'.$sm->image_name;
            array_push($images, $link);
        }

        // Get Merchant Currency
        $currency = Country::where(['co_id' => $merchant->mer_co_id, 'co_offline_status' => 1])->first();
        if (empty($currency)) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.currency') . trans('api.notFound')
            ]);
        }

        return \Response::json([
            'status' => 200,
            'message' => trans('api.store_id') . $input['store_id'] . trans('api.found'),
            'store_id' => $store->stor_id,
            'store_name' => $store->stor_name,
            'store_address' => $store->stor_address1.', '.$store->stor_address2.', '.$store->stor_zipcode.' '.$store->stor_city_name.', '.(!empty($store-> stor_state) ? $store->name : $store->ci_name).', '.$store->co_name,
            'store_main_image' => env('IMAGE_DIR').'/store/'.$store->stor_merchant_id.'/'.$store->stor_img,
            'store_image' => $images,
            'store_short_desc' => $store->short_description,
            'store_long_desc' => $store->long_description,
            'store_type' => $store->stor_type,
            'store_status' => $store->stor_status,
            'store_country' => $store->stor_country,
            'store_currency' => $store->co_curcode,
            'accept_payment' => $store->accept_payment,
            'merchant_id' => $merchant->mer_id,
            'merchant_name' => $this->getMerchantNameById($merchant->mer_id),
            'merchant_status' => $merchant->mer_staus,
            'merchant_currency' => $currency->co_curcode,
            'default_price' => $store->default_price,
        ]);
    }

    public function createPayment()
    {
        $input = \Request::only('merchant_id', 'store_id', 'currency', 'amount', 'remark', 'security_code', 'lang', 'order_id', 'inv_no');

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $niceNames = array(
            'merchant_id' => trans('api.merchantId'),
            'store_id' => trans('api.storeId'),
            'inv_no' => trans('api.invoice'),
        );

        $store_id = trim($input['store_id']);

        $validator = Validator::make($input, [
            'merchant_id' => 'required|integer',
            'store_id' => 'required|integer',
            'currency' => 'required|alpha|max:3',
            'amount' => 'required|numeric|between:0.01,9999999999999.99',
            'remark' => 'max:500',
            'security_code' => 'required|digits:6',
            'order_id' => 'nullable|integer',
            'inv_no' => 'required|alpha_dash|max:50|unique:order_offline,inv_no,NULL,id,store_id,'.$store_id,
        ]);
        $validator->setAttributeNames($niceNames);

        if ($validator->fails())
        {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        $data = [
            'mer_id' => trim($input['merchant_id']),
            'store_id' => trim($input['store_id']),
            'cust_id' => $this->cus_id,
            'inv_no' => trim($input['inv_no']),
            'currency' => $input['currency'],
            'amount' => trim($input['amount']),
            'remark' => $input['remark'],
            'type' => 2, // Created by Customer indicator
        ];

        // Add into request log
        $request_log = OrderOfflineLog::create($data);

        // merchant validation
        $merchant = Merchant::find(trim($input['merchant_id']));
        if (empty($merchant)) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Merchant Not Found']);
            return \Response::json([
                'status' => 404,
                'message' => trans('api.merchantId') . trim($input['merchant_id']) . trans('api.notFound')
            ]);
        }

        if ($merchant->mer_staus != 1) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Merchant Not Active']);
            return \Response::json([
                'status' => 400,
                'message' => trans('api.merchantId') . $merchant->mer_id . trans('api.notActive'),
            ]);
        }

        // store validation
        $store = Store::where('stor_id', trim($input['store_id']))->where('stor_status', 1)->where('stor_type', '>', 0)->first();
        if (empty($store)) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Store Not Found']);
            return \Response::json([
                'status' => 404,
                'message' => trans('api.storeId') . trim($input['store_id']) . trans('api.notFound')
            ]);
        }

        if ($store->stor_merchant_id != trim($input['merchant_id'])) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Store Does Not Belong To Merchant']);
            return \Response::json([
                'status' => 400,
                'message' => trans('api.storeId') . trim($input['store_id']) . trans('api.doesNotBelongToThis') . trans('api.merchantId') . trim($input['merchant_id'])
            ]);
        }

        // check store's accept payment
        if ($store->accept_payment != 1) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Store Does Not Accept Payment']);
            return \Response::json([
                'status' => 400,
                'message' => $this->getStoreNameById(trim($input['store_id'])) . trans('api.notAcceptPayment')
            ]);
        }

        // member validation
        $customer = Customer::find($this->cus_id);
        if (empty($customer)) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Customer Not Found']);
            return \Response::json([
                'status' => 404,
                'message' => trans('api.memberId') . $this->cus_id . trans('api.notFound')
            ]);
        }

        if (!\Hash::check(trim($input['security_code']), $customer->payment_secure_code)) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Wrong Security Code']);
            return \Response::json([
                'status' => 400,
                'message' => trans('api.wrongCode')
            ]);
        }

        // Check currency
        // $currency = Country::where(['co_curcode' => Str::upper($data['currency']), 'co_offline_status' => 1])->first();
        $currency = Country::where(['co_id' => $store->stor_country, 'co_offline_status' => 1])->first();
        if (empty($currency)) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Currency Not Found']);
            return \Response::json([
                'status' => 404,
                'message' => trans('api.currency') . trans('api.notFound')
            ]);
        }

        //check store and per user store limit transaction
        $check = LimitRepo::check_payment_limitation('storeLimit', trim($input['amount']), $store->stor_id, $customer->cus_id);
        if($check) {
            return \Response::json([
                'status' => 400,
                'message' => $check
            ]);
        }

        //check member limit
        $check = LimitRepo::check_payment_limitation('customerLimit', trim($input['amount']), null, $customer->cus_id);
        if($check) {
            return \Response::json([
                'status' => 400,
                'message' => $check
            ]);
        }

        // Recalculate total
        $amount = trim($input['amount']);
        $currencyRate = (double)$currency->co_offline_rate;
        $platformChargeRate = (double)$merchant->mer_platform_charge;
        // $serviceChargeRate = (double)\Config::get('settings.offline_service_charge');
        $serviceChargeRate = (double)$merchant->mer_service_charge;

        $credit = round(($amount / $currencyRate), 4);
        $platformCharge = round($credit * ($platformChargeRate/100), 4);
        $serviceCharge = round(($credit + $platformCharge) * ($serviceChargeRate/100), 4);
        $totalcredit = round($credit + $platformCharge + $serviceCharge, 4);

        // Calculate Merchant Commission
        $commissionRate = $merchant->mer_commission;
        $commission = round($credit * ($commissionRate/100), 4);

        // put into $data
        $data = [
            'mer_id' => trim($input['merchant_id']),
            'store_id' => trim($input['store_id']),
            'cust_id' => $this->cus_id,
            'inv_no' => trim($input['inv_no']),
            'currency' => $currency->co_curcode,
            'currency_rate' => $currency->co_offline_rate,
            'amount' => $amount,
            'v_token' => $credit,
            'merchant_charge_percentage' => $commissionRate,
            'merchant_charge_token' => $commission,
            'customer_charge_percentage' => $serviceChargeRate,
            'customer_charge_token' => $serviceCharge,
            'merchant_platform_charge_percentage' => $platformChargeRate,
            'merchant_platform_charge_token' => $platformCharge,
            'order_total_token' => $totalcredit,
            'remark' => $input['remark'],
            'type' => 2, // Created by Customer indicator
        ];

        OrderOfflineLog::where('id', $request_log->id)->update($data);

        // get wallet_id
        $store_wallet = Store::select('nm_store.stor_id', 'offline_categories.wallet_id', 'offline_categories.id as offline_categories_id', 'offline_categories.parent_list')
                ->leftJoin('nm_store_offline_category', 'nm_store_offline_category.store_id', '=', 'nm_store.stor_id')
                ->leftJoin('offline_categories', 'offline_categories.id', '=', 'nm_store_offline_category.offline_category_id')
                ->where('stor_merchant_id', $merchant->mer_id)
                ->where('stor_id', $store->stor_id)
                ->where('stor_status', 1)
                ->where('stor_type', '>', 0)
                ->orderBy('nm_store_offline_category.id', 'asc')
                ->first();

        if (is_null($store_wallet->wallet_id))
        {
            $parent_id = intval(explode(",", $store_wallet->parent_list)[0]);
            $update_wallet = OfflineCategory::select('wallet_id')->where('id', $parent_id)->first();
            $store_wallet->wallet_id = $update_wallet->wallet_id;
        }

        if (!isset($store_wallet->wallet_id))
        {
            return \Response::json([
                'status' => 404,
                'message' => 'Store wallet' . trans('api.notFound')
            ]);
        }

        // Check customer credit
        // $cus_credit = $customer->v_token;
        // $cus_credit = CustomerRepo::get_customer_offline_wallet($customer->cus_id);
        $cus_credit = CustomerWallet::where('customer_id', $customer->cus_id)
        ->where('wallet_id', $store_wallet->wallet_id)
        ->pluck('credit')
        ->first();

        $credit_type = 'v_token';
        $data['wallet_id'] = $store_wallet->wallet_id;

        if ((in_array($data['store_id'], explode(',', env('SPECIAL_STORE_ID')))) && ($customer->special_wallet >= $data['order_total_token'])) {
            $cus_credit = $customer->special_wallet;
            $credit_type = 'special_wallet';
            $data['wallet_id'] = 99;
        }

        if ($cus_credit < $data['order_total_token']) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Insufficient Mei Point']);

            $cus_wallets = CustomerRepo::get_customer_available_wallet($customer->cus_id);
            $wallets = [];
            foreach ($cus_wallets as $cw) {
                $wallets[$cw->wallet->name] = $cw->credit;
            }

            return \Response::json([
                'status' => 400,
                'message' => trans('api.insufficientFund', [
                    'wallet_type' => $this->getWalletName($store_wallet->wallet_id),
                ]),
                'member_credit_balance' => $customer->v_token,
                'member_special_wallet' => $customer->special_wallet,
                'member_wallet' => $wallets
            ]);
        }

        // retrieve main acc details
        $mainAcc = Merchant::find(180);
        if (empty($mainAcc)) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Primary Acc Not Found']);
            return \Response::json([
                'status' => 404,
                'message' => trans('api.primaryAcc') . trans('api.notFound'),
            ]);
        }

        // Add into order_offline : status 0 unpaid
        $order = OrderOffline::where('mer_id', $data['mer_id'])
        ->where('cust_id', $data['cust_id'])
        ->where('inv_no', $data['inv_no'])
        ->orderBy('id', 'desc')
        ->first();

        if (!empty($order)) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Duplicate Order']);
            return \Response::json([
                'status' => 400,
                'message' => trans('api.createOrder') . trans('api.fail')
            ]);
        }

        if ($input['order_id']) {
            OrderOffline::where('id', trim($input['order_id']))->update(['status' => 2]);
        }

        try {
            $order = OrderOffline::create($data);
        } catch (\Exception $e) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Fail create order']);
            return \Response::json([
                'status' => 400,
                'message' => trans('api.createOrder') . trans('api.fail'),
            ]);
        }

        // retrieve merchant name
        $merchantName = $this->getMerchantNameById($data['mer_id']);

        /// BEGIN transaction
        try {
            \DB::transaction(function() use ($merchant, $customer, $order, $merchantName, $mainAcc, $store, $credit_type, $store_wallet)
            {
                $origToken = $order->v_token;
                $customerVToken = $order->order_total_token;
                $customerChargeToken = $order->customer_charge_token;
                $platformChargeToken = $order->merchant_platform_charge_token;
                $merchantCommission = $order->merchant_charge_token;
                $merchantVToken = $origToken - $merchantCommission;

                if ($credit_type == 'v_token') {
                    // deduct token from customer
                    $customer->v_token -= $customerVToken;
                    // this part should not happen but as a safeguard
                    if ($customer->v_token < 0) {
                        $customer->v_token = 0;
                    }

                    // deduct from customer wallet
                    $cus_wallet = CustomerWallet::where('customer_id', $customer->cus_id)->where('wallet_id', $store_wallet->wallet_id)->first();
                    $cus_wallet->credit -= $customerVToken;
                    $cus_wallet->save();
                } else {
                    // deduct token from customer
                    $customer->special_wallet -= $customerVToken;
                    // this part should not happen but as a safeguard
                    if ($customer->special_wallet < 0) {
                        $customer->special_wallet = 0;
                    }
                }

                $customer->save();

                $arrCustomerVTokenLog = [
                    'cus_id' => $customer->cus_id,
                    'credit_amount' => 0,
                    'debit_amount' => $customerVToken,
                    'offline_order_id' => $order->id,
                    'remark' => 'order payment' . (($credit_type == 'special_wallet') ? ' - Hemma Wallet ' : '') . (isset($order->remark) ? ': ' . $order->remark : ''),
                    'wallet_id' => ($credit_type == 'v_token') ? $store_wallet->wallet_id : 0
                ];
                CustomerVTokenLog::firstOrCreate($arrCustomerVTokenLog);

                // add vtoken to merchant
                $merchant->mer_vtoken += $merchantVToken;
                $merchant->save();

                $arrMerchantVTokenLog = [
                    'mer_id' => $merchant->mer_id,
                    'credit_amount' => $merchantVToken,
                    'debit_amount' => 0,
                    'offline_order_id' => $order->id,
                    'remark' => 'sales' . (isset($order->remark) ? ': ' . $order->remark : '')
                ];
                MerchantVTokenLog::firstOrCreate($arrMerchantVTokenLog);

                // add merchant & customer service charge vtoken to main account
                $mainAcc->mer_vtoken += $merchantCommission;
                $mainAcc->mer_vtoken += $customerChargeToken;
                $mainAcc->mer_vtoken += $platformChargeToken;
                $mainAcc->save();

                // add merchant commission vtoken to main account
                $arrMerchantToMainAccVTokenLog = [
                    'mer_id' => $mainAcc->mer_id,
                    'credit_amount' => $merchantCommission,
                    'debit_amount' => 0,
                    'offline_order_id' => $order->id,
                    'remark' => 'merchant commission: ' . $merchant->mer_id . ' ' . $merchantName
                ];
                MerchantVTokenLog::firstOrCreate($arrMerchantToMainAccVTokenLog);

                // add customer service charge vtoken to main account
                $arrCustomerToMainAccVTokenLog = [
                    'mer_id' => $mainAcc->mer_id,
                    'credit_amount' => $customerChargeToken,
                    'debit_amount' => 0,
                    'offline_order_id' => $order->id,
                    'remark' => trans('localize.gst') . ':' . $customer->cus_id . ' ' . $customer->cus_name
                ];
                MerchantVTokenLog::firstOrCreate($arrCustomerToMainAccVTokenLog);

                // add merchant platform charge vtoken to main account
                $arrMerPlatformToMainAccVTokenLog = [
                    'mer_id' => $mainAcc->mer_id,
                    'credit_amount' => $platformChargeToken,
                    'debit_amount' => 0,
                    'offline_order_id' => $order->id,
                    'remark' => trans('localize.platform_charges') . ':' . $merchant->mer_id . ' ' . $merchantName
                ];
                MerchantVTokenLog::firstOrCreate($arrMerPlatformToMainAccVTokenLog);

                //Generate inv tax number
                $order_tax_inv = OrderOfflineRepo::generate_tax_inv();

                // update order
                $order->status = 1;
                $order->tax_inv_no = $order_tax_inv;
                $order->cust_id = $customer->cus_id;
                $order->paid_date = new \DateTime;
                $order->save();

                // update store transaction
                $update_limit = LimitRepo::update_limit_transaction('offline', $order->id);
            });

            $cus_wallets = CustomerRepo::get_customer_available_wallet($customer->cus_id);
            $wallets = [];
            foreach ($cus_wallets as $cw) {
                $wallets[$cw->wallet->name] = $cw->credit;
            }

            return \Response::json([
                'status' => 200,
                'message' => trans('api.payment') . trans('api.success'),
                'order_id' => $order->id,
                'inv_no' => $order->inv_no,
                'inv_tax_no' => $order->tax_inv_no,
                'currency' => $order->currency,
                'amount' => $order->amount,
                'credit' => $order->v_token,
                'service_charge_percentage' => $order->customer_charge_percentage,
                'service_charge_credit' => $order->customer_charge_token,
                'platform_charge_percentage' => $order->merchant_platform_charge_percentage,
                'platform_charge_credit' => $order->merchant_platform_charge_token,
                'total_credit' => $order->order_total_token,
                'merchant_name' => $merchantName,
                'member_credit_balance' => $customer->v_token,
                'member_special_wallet' => $customer->special_wallet,
                'member_wallet' => $wallets,
                'deduct_from_wallet' => (($order->wallet_id == 99) ? 'Hemma' : $this->getWalletName($order->wallet_id))
            ]);

        } catch (\Exception $e) {
            return \Response::json([
                'status' => 400,
                'order_id' => $order->id,
                'message' => trans('api.payment') . trans('api.fail')
            ]);
        }

    }

    private function getMerchantNameById($id) {
        $merchant = Merchant::find($id);

        $merchantName = null;
        if (!empty($merchant->mer_fname)) {
            $merchantName = trim($merchant->mer_fname);
        }
        if (!empty($merchant->mer_lname)) {
            $merchantName .= ' ' . trim($merchant->mer_lname);
        }

        return $merchantName;
    }

    private function getStoreNameById($id) {
        $store = Store::find($id);

        return ($store) ? $store->stor_name : '';
    }

    private function getWalletName($id) {
        $wallet = Wallet::find($id);

        return ($wallet) ? $wallet->name : '';
    }

}
