<?php

namespace App\Http\Controllers\Api\V1;

use App;
use Validator;
use App\Http\Controllers\Controller;
use App\Models\OrderOffline;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Country;
use App\Models\MerchantVTokenLog;
use App\Models\CustomerVTokenLog;
use App\Models\Store;
use App\Models\OrderOfflineLog;
use App\Repositories\StoreRepo;
use Illuminate\Support\Str;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;

class OfflinePaymentController extends Controller
{

    public function __construct(Mailer $mailer) {
        $this->mailer = $mailer;
    }

    // START merchant
    public function createOrder()
    {
        $input = \Request::only(
            'merchant_id',
            'store_id',
            'inv_no',
            'currency',
            'amount',
            'remark',
            'lang'
        );

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $niceNames = array(
            'merchant_id' => trans('api.merchantId'),
            'inv_no' => trans('api.invoice')
        );

        $validator = Validator::make($input, [
            'merchant_id' => 'required|integer',
            'store_id' => 'required|integer',
            'inv_no' => 'required|alpha_dash|max:50',
            'currency' => 'required|alpha|max:3',
            'amount' => 'required|numeric|between:0.01,9999999999999.99',
            'remark' => 'nullable|max:500',
        ]);
        $validator->setAttributeNames($niceNames);

        if ($validator->fails()) {
            $intErrors = $validator->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];
            return $errors;
        }

        // merchant validation
        $merchant = Merchant::find(trim($input['merchant_id']));
        if (empty($merchant)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.merchant') . trans('api.notFound')
            ]);
        }

        // store validation
        $store = Store::where('stor_id', trim($input['store_id']))->where('stor_status', 1)->where('stor_type', '>', 0)->first();

        if (empty($store)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.store') . trans('api.notFound')
            ]);
        }

        if ($store->stor_merchant_id != trim($input['merchant_id'])) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.storeId') . trim($input['store_id']) . trans('api.doesNotBelongToThis') . trans('api.merchantId') . trim($input['merchant_id'])
            ]);
        }

        // check store's accept payment
        if ($store->accept_payment == 0) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.store') . trim($input['store_id']) . trans('api.notAcceptPayment')
            ]);
        }

        // check trans exceed monthly limit or not
        if ($store->monthly_limit) {
            if ((trim($input['amount']) + $store->monthly_trans) > $store->monthly_limit) {
                return \Response::json([
                    'status' => 0,
                    'message' => trans('api.store') . trans('api.exceedLimit')
                ]);
            }
        }

        // currency validation
        // $currency = Country::where(['co_curcode' => Str::upper(trim($input['currency'])), 'co_offline_status' => 1])->first();
        $currency = Country::where(['co_id' => $store->stor_country, 'co_offline_status' => 1])->first();
        if (empty($currency)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.currency') . trans('api.notFound')
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
        $serviceCharge = round(($credit+$platformCharge) * ($serviceChargeRate/100), 4);
        $totalcredit = round($credit + $platformCharge + $serviceCharge, 4);

        // Calculate Merchant Commission
        $commissionRate = $merchant->mer_commission;
        $commission = round($credit * ($commissionRate/100), 4);

        // put into $data
        $data = [
            'mer_id' => trim($input['merchant_id']),
            'store_id' => $store->stor_id,
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
            'type' => 1, // Create by Merchant indicator
        ];

        $order = OrderOffline::where('mer_id', $data['mer_id'])
                                ->where('inv_no', $data['inv_no'])
                                ->orderBy('id', 'desc')
                                ->first();

        $stores = StoreRepo::api_get_stores($merchant->mer_id);

        if (empty($order)) {
            try {
                $order = OrderOffline::create($data);

                return \Response::json([
                    'status' => 1,
                    'message' => trans('api.createOrder') . trans('api.success'),
                    'order_id' => $order->id,
                    'merchant_name' => $this->getMerchantNameById($merchant->mer_id),
                    'stores' => $stores,
                ]);
            } catch (\Exception $e) {
                return \Response::json([
                    'status' => 0,
                    'message' => trans('api.createOrder') . trans('api.fail'),
                ]);
            }
        } else {
            switch ($order->status) {
                case '0':
                    $order->status = 3;
                    $order->save();

                    try {
                        $newOrder = OrderOffline::create($data);

                        return \Response::json([
                            'status' => 1,
                            'message' => trans('api.createOrder') . trans('api.success'),
                            'order_id' => $newOrder->id,
                            'merchant_name' => $this->getMerchantNameById($merchant->mer_id),
                            'stores' => $stores,
                        ]);
                    } catch (\Exception $e) {
                        return \Response::json([
                            'status' => 0,
                            'message' => trans('api.createOrder') . trans('api.fail'),
                        ]);
                    }
                    break;
                case '1':
                    return \Response::json([
                        'status' => 0,
                        'message' => trans('api.billNo') . $data['inv_no'] . trans('api.hasAlreadyBeen') . trans('api.paid')
                    ]);
                    break;
                case '2':
                case '3':
                    return \Response::json([
                        'status' => 0,
                        'message' => trans('api.billNo') . $data['inv_no'] . trans('api.hasAlreadyBeen') . trans('api.cancelled')
                    ]);
                default:
                    return \Response::json([
                        'status' => 0,
                        'message' => trans('api.createOrder') . trans('api.fail')
                    ]);
                    break;
            }
        }
    }

    // order status /merchant/order/status
    // 0 - unpaid
    // 1 - paid
    // 2 - cancelled by member
    // 3 - cancelled by merchant
    public function checkOrderStatusByMerchant()
    {
        $input = \Request::only(
            'merchant_id',
            'store_id',
            'order_id',
            'inv_no',
            'lang'
        );

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $validator = Validator::make($input, [
            'merchant_id' => 'required|integer',
            'store_id' => 'required|integer',
            'order_id' => 'required|integer',
            'inv_no' => 'required|alpha_dash|max:50',
        ]);

        if ($validator->fails()) {
            $intErrors = $validator->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];
            return $errors;
        }

        // merchant validation
        $merchant = Merchant::find(trim($input['merchant_id']));
        if (empty($merchant)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.merchantId') . trim($input['merchant_id']) . trans('api.notFound')
            ]);
        }

        // store validation
        $store = Store::where('stor_id', trim($input['store_id']))->where('stor_status', 1)->where('stor_type', '>', 0)->first();
        if (empty($store)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.storeId') . trim($input['store_id']) . trans('api.notFound')
            ]);
        }

        if ($store->stor_merchant_id != trim($input['merchant_id'])) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.storeId') . trim($input['store_id']) . trans('api.doesNotBelongToThis') . trans('api.merchantId') . trim($input['merchant_id'])
            ]);
        }

        $order = OrderOffline::find(trim($input['order_id']));
        if (empty($order)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.billNo') . trim($input['inv_no']) . trans('api.notFound')
            ]);
        }

        if ($order->mer_id != trim($input['merchant_id'])) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.billNo') . trim($input['inv_no']) . trans('api.doesNotBelongToThis') . trans('api.merchant')
            ]);
        }

        if ($order->store_id != trim($input['store_id'])) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.billNo') . trim($input['inv_no']) . trans('api.doesNotBelongToThis') . trans('api.store')
            ]);
        }

        $stores = StoreRepo::api_get_stores($merchant->mer_id);

        $retObj = [
            'status' => $order->status,
            'inv_no' => $order->inv_no,
            'order_id' => $order->id,
            'order_status' => '',
            'merchant_id' => $order->mer_id,
            'store_id' => $order->store_id,
            'currency' => $order->currency,
            'amount' => $order->amount,
            'vcoin' => $order->v_token,
            'service_charge_percentage' => $order->customer_charge_percentage,
            'service_charge_vcoin' => $order->customer_charge_token,
            'platform_charge_percentage' => $order->merchant_platform_charge_percentage,
            'platform_charge_vcoin' => $order->merchant_platform_charge_token,
            'total_vcoin' => $order->order_total_token,
            'stores' => $stores,
        ];

        switch ($order->status) {
            case '0':
                $retObj['order_status'] = 'unpaid';
                break;
            case '1':
                $retObj['order_status'] = 'paid';
                $retObj['merchant_vcoin_balance'] = $merchant->mer_vtoken;
                break;
            case '2':
                $retObj['order_status'] = 'cancelled by member';
                break;
            case '3':
                $retObj['order_status'] = 'cancelled by merchant';
                break;
            default:
                throw new \Exception('invalid order status');
                break;
        }

        return \Response::json($retObj);
    }

    public function merchantCancelOrder()
    {
        $input = \Request::only(
            'merchant_id',
            'store_id',
            'order_id',
            'inv_no',
            'lang'
        );

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $validator = Validator::make($input, [
            'merchant_id' => 'required|integer',
            'store_id' => 'required|integer',
            'order_id' => 'required|integer',
            'inv_no' => 'required|alpha_dash|max:50',
        ]);

        if ($validator->fails()) {
            $intErrors = $validator->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        // merchant validation
        $merchant = Merchant::find(trim($input['merchant_id']));
        if (empty($merchant)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.merchantId') . trim($input['merchant_id']) . trans('api.notFound')
            ]);
        }

        // store validation
        $store = Store::where('stor_id', trim($input['store_id']))->where('stor_status', 1)->where('stor_type', '>', 0)->first();
        if (empty($store)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.storeId') . trim($input['store_id']) . trans('api.notFound')
            ]);
        }

        if ($store->stor_merchant_id != trim($input['merchant_id'])) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.storeId') . trim($input['store_id']) . trans('api.doesNotBelongToThis') . trans('api.merchantId') . trim($input['merchant_id'])
            ]);
        }

        $order = OrderOffline::find(trim($input['order_id']));
        if (empty($order)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.billNo') . $order->inv_no . trans('api.notFound')
            ]);

        }

        if ($order->mer_id != trim($input['merchant_id'])) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.billNo') . $order->inv_no . trans('api.doesNotBelongToThis') . trans('api.merchant')
            ]);
        }

        if ($order->store_id != trim($input['store_id'])) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.billNo') . trim($input['inv_no']) . trans('api.doesNotBelongToThis') . trans('api.store')
            ]);
        }

        switch ($order->status) {
            case 0:
                $order->status = 3;
                $order->save();

                return \Response::json([
                    'status' => 1,
                    'message' => trans('api.orderCancelledByMerchant')
                ]);
                break;
            case 1:
                return \Response::json([
                    'status' => 0,
                    'message' => trans('api.billNo') . $order->inv_no . trans('api.cannotCancelAsPaymentMade'),
                ]);
            case 2:
            case 3:
                return \Response::json([
                    'status' => 0,
                    'message' => trans('api.billNo') . $order->inv_no . trans('api.hasAlreadyBeen') . trans('api.cancelled'),
                ]);
            default:
                throw new \Exception('invalid order status');
                break;
        }
    }
    // END merchant

    // START member
    // /member/order/claim
    public function claimOrder()
    {
        $input = \Request::only(
            'member_id',
            'merchant_id',
            'store_id',
            'order_id',
            'inv_no',
            'lang'
        );

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $validator = Validator::make($input, [
            'member_id' => 'required|integer',
            'merchant_id' => 'required|integer',
            'store_id' => 'required|integer',
            'order_id' => 'required|integer',
            'inv_no' => 'required|alpha_dash|max:50',
        ]);

        if ($validator->fails()) {
            $intErrors = $validator->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        // merchant validation
        $merchant = Merchant::find(trim($input['merchant_id']));
        if (empty($merchant)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.merchantId') . trim($input['merchant_id']) . trans('api.notFound')
            ]);
        }

        // store validation
        $store = Store::where('stor_id', trim($input['store_id']))->where('stor_status', 1)->where('stor_type', '>', 0)->first();
        if (empty($store)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.storeId') . trim($input['store_id']) . trans('api.notFound')
            ]);
        }

        if ($store->stor_merchant_id != trim($input['merchant_id'])) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.storeId') . trim($input['store_id']) . trans('api.doesNotBelongToThis') . trans('api.merchantId') . trim($input['merchant_id'])
            ]);
        }

        // check store's accept payment
        if ($store->accept_payment == 0) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.store') . trim($input['store_id']) . trans('api.notAcceptPayment')
            ]);
        }

        $data = [
            'cust_id' => trim($input['member_id']),
            'mer_id' => trim($input['merchant_id']),
            'store_id' => trim($input['store_id']),
            'id' => trim($input['order_id']),
            'inv_no' => trim($input['inv_no'])
        ];

        $order = OrderOffline::find($data['id']);
        if (empty($order)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.billNo') . $data['inv_no'] . trans('api.notFound')
            ]);
        }

        if ($order->mer_id != $data['mer_id']) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.billNo') . $data['inv_no'] . trans('api.doesNotBelongToThis') . trans('api.merchant')
            ]);
        }

        if ($order->store_id != $data['store_id']) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.billNo') . $data['inv_no'] . trans('api.doesNotBelongToThis') . trans('api.store')
            ]);
        }

        if (empty($order->cust_id)) {
            $order->cust_id = $data['cust_id'];
            $order->save();
        } else {
            if ($order->cust_id != $data['cust_id']) {
                return \Response::json([
                    'status' => 0,
                    'message' => trans('api.billNo') . $data['inv_no'] . trans('api.hasAlreadyBeen') . trans('api.claimed')
                ]);
            }
        }

        switch ($order->status) {
            case '0':
                $stores = StoreRepo::api_get_stores($order->mer_id);

                $retObj['status'] = 1;
                $retObj['message'] = trans('api.billNo') . $order->inv_no . trans('api.hasBeenClaimedSuccessfully');
                $retObj['order_status'] = 'unpaid';

                $retObj['order_id'] = $order->id;
                $retObj['inv_no'] = $order->inv_no;
                $retObj['merchant_name'] = $this->getMerchantNameById($order->mer_id);
                $retObj['currency'] = $order->currency;
                $retObj['amount'] = $order->amount;
                $retObj['vcoin'] = $order->v_token;
                $retObj['service_charge_percentage'] = $order->customer_charge_percentage;
                $retObj['service_charge_vcoin'] = $order->customer_charge_token;
                $retObj['platform_charge_percentage'] = $order->merchant_platform_charge_percentage;
                $retObj['platform_charge_vcoin'] = $order->merchant_platform_charge_token;
                $retObj['total_vcoin'] = $order->order_total_token;
                $retObj['stores'] = $stores;

                // // order's vcoin should be replaced with order_total_token if it's not empty
                // if (!empty($order->order_total_token))
                //     $retObj['vcoin'] = $order->order_total_token;

                return \Response::json($retObj);
                break;
            case '1':
                return \Response::json([
                    'status' => 0,
                    'message' => trans('api.billNo') . $order->inv_no . trans('api.hasAlreadyBeen') . trans('api.paid')
                ]);
                break;
            case '2':
            case '3':
                return \Response::json([
                    'status' => 0,
                    'message' => trans('api.billNo') . $order->inv_no . trans('api.hasAlreadyBeen') . trans('api.cancelled')
                ]);
                break;
            default:
                throw new \Exception('invalid order status');
                break;
        }
    }

    // /member/order
    public function getOrder()
    {
        $input = \Request::only('member_id', 'order_id', 'inv_no', 'lang');

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $validator = Validator::make($input, [
            'member_id' => 'required|integer',
            'order_id' => 'required|integer',
            'inv_no' => 'required|alpha_dash|max:50',
        ]);

        if ($validator->fails()) {
            $intErrors = $validator->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        $data = [
            'cust_id' => trim($input['member_id']),
            'id' => trim($input['order_id']),
            'inv_no' => trim($input['inv_no'])
        ];

        $order = OrderOffline::find($data['id']);
        if (empty($order)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.billNo') . $data['inv_no'] . trans('api.notFound')
            ]);
        }

        if (isset($order->cust_id)) {
            if ($order->cust_id != $data['cust_id']) {
                return \Response::json([
                    'status' => 0,
                    'message' => trans('api.billNo') . $data['inv_no'] . trans('api.doesNotBelongToThis') . trans('api.member')
                ]);
            }
        } else {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.billNo') . $data['inv_no'] . trans('api.hasNotYetBeen') . trans('api.claimed')
            ]);
        }

        switch ($order->status) {
            case '0':
                $retObj['status'] = 0;
                $retObj['order_status'] = 'unpaid';
                break;
            case '1':
                $retObj['status'] = 1;
                $retObj['order_status'] = 'paid';
                break;
            case '2':
                $retObj['status'] = 0;
                $retObj['order_status'] = 'cancelled by member';
                break;
            case '3':
                $retObj['status'] = 0;
                $retObj['order_status'] = 'cancelled by merchant';
                break;
            default:
                throw new \Exception('invalid order status');
                break;
        }

        $retObj['order_id'] = $order->id;
        $retObj['inv_no'] = $order->inv_no;
        $retObj['merchant_name'] = $this->getMerchantNameById($order->mer_id);
        $retObj['currency'] = $order->currency;
        $retObj['amount'] = $order->amount;
        $retObj['vcoin'] = $order->v_token;

        // order's vcoin should be replaced with order_total_token if it's not empty
        if (!empty($order->order_total_token))
            $retObj['vcoin'] = $order->order_total_token;

        return \Response::json($retObj);
    }

    // /member/order/confirm
    public function confirmOrder()
    {
        $data = \Request::only(
            'merchant_id',
            'store_id',
            'order_id',
            'inv_no',
            'member_id',
            'security_code',
            'lang'
        );

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
            'member_id' => 'required|integer',
            'security_code' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            $intErrors = $validator->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];
            return $errors;
        }


        $cust_id = null;
        $mer_id = null;
        $transactedVToken = null;

        // merchant validation
        $merchant = Merchant::find(trim($data['merchant_id']));
        if (empty($merchant)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.merchantId') . trim($data['merchant_id']) . trans('api.notFound')
            ]);
        }

        // store validation
        $store = Store::where('stor_id', trim($data['store_id']))->where('stor_status', 1)->where('stor_type', '>', 0)->first();
        if (empty($store)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.storeId') . trim($data['store_id']) . trans('api.notFound')
            ]);
        }

        if ($store->stor_merchant_id != trim($data['merchant_id'])) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.storeId') . trim($data['store_id']) . trans('api.doesNotBelongToThis') . trans('api.merchantId') . trim($data['merchant_id'])
            ]);
        }

        // check store's accept payment
        if ($store->accept_payment == 0) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.store') . trim($data['store_id']) . trans('api.notAcceptPayment')
            ]);
        }

        // member validation
        $customer = Customer::find( trim($data['member_id']));
        if (empty($customer)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.memberId') . $cust_id . trans('api.notFound')
            ]);
        }

        if (!\Hash::check(trim($data['security_code']), $customer->payment_secure_code)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.wrongCode')
            ]);
        }

        $order = OrderOffline::find(trim($data['order_id']));
        if (empty($order)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.billNo') . trim($data['inv_no']) . trans('api.notFound')
            ]);
        }

        if ($order->mer_id != trim($data['merchant_id'])) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.billNo') . trim($data['inv_no']) . trans('api.doesNotBelongToThis') . trans('api.merchant')
            ]);
        }

        if ($order->store_id != trim($data['store_id'])) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.billNo') . trim($data['inv_no']) . trans('api.doesNotBelongToThis') . trans('api.store')
            ]);
        }

        // check trans exceed monthly limit or not
        if ($store->monthly_limit) {
            if (($order->amount + $store->monthly_trans) > $store->monthly_limit) {
                return \Response::json([
                    'status' => 0,
                    'message' => trans('api.store') . trans('api.exceedLimit')
                ]);
            }
        }

        // initialization
        $cust_id = $customer->cus_id;
        $mer_id = $order->mer_id;
        $transactedVToken = $order->v_token;

        // total token should replace original token due to service charges
        if (!empty($order->order_total_token))
            $transactedVToken = $order->order_total_token;

        // retrieve merchant name
        $merchantName = $this->getMerchantNameById($mer_id);

        if ($order->status == 1) {
            $retObj = [
                'status' => 1,
                'message' => trans('api.payment') . trans('api.success'),
                'order_id' => $order->id,
                'inv_no' => $order->inv_no,
                'currency' => $order->currency,
                'amount' => $order->amount,
                'vcoin' => $order->v_token,
                'service_charge_percentage' => $order->customer_charge_percentage,
                'service_charge_vcoin' => $order->customer_charge_token,
                'platform_charge_percentage' => $order->merchant_platform_charge_percentage,
                'platform_charge_vcoin' => $order->merchant_platform_charge_token,
                'total_vcoin' => $order->order_total_token,
                'merchant_name' => $merchantName,
                'member_vcoin_balance' => $customer->v_token,
                'paid_date' => $order->paid_date,
            ];

            return \Response::json($retObj);
        }

        // validate customer's v_token
        if ($customer->v_token < $transactedVToken) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.insufficientFund'),
                'member_vcoin_balance' => $customer->v_token
            ]);
        }

        $mainAcc = Merchant::find(180);
        if (empty($mainAcc)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.primaryAcc') . trans('api.notFound'),
            ]);
        }

        /// BEGIN transaction
        try {
            \DB::transaction(function() use ($merchant, $customer, $order, $merchantName, $mainAcc, $store)
            {
                $origToken = $order->v_token;
                $customerVToken = $order->order_total_token;
                $customerChargeToken = $order->customer_charge_token;
                $platformChargeToken = $order->merchant_platform_charge_token;
                $merchantCommission = $order->merchant_charge_token;
                $merchantVToken = $origToken - $merchantCommission;

                // deduct token from customer
                $customer->v_token -= $customerVToken;
                // this part should not happen but as a safeguard
                if ($customer->v_token < 0) {
                    $customer->v_token = 0;
                }
                $customer->save();

                $arrCustomerVTokenLog = [
                    'cus_id' => $customer->cus_id,
                    'credit_amount' => 0,
                    'debit_amount' => $customerVToken,
                    'offline_order_id' => $order->id,
                    'remark' => 'order payment' . (isset($order->remark) ? ': ' . $order->remark : '')
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

                $store->monthly_trans += $order->amount;
                $store->save();

                // email if store single limit exceed
                if (!is_null($store->single_limit)) {
                    if ($order->amount > $store->single_limit) {
                        $data = [
                            'trans_type' => 'Offline Order',
                            'merchant_name' => $merchant->mer_fname . ' ' . $merchant->mer_lname,
                            'store_name' => $store->stor_name,
                            'order_id' => $order->id,
                            'trans_no' => $order->inv_no,
                            'credit' => $merchantVToken,
                            'amount' => $order->amount,
                            'currency' => $order->currency_rate,
                            'currency_code' => $order->currency,
                        ];

                        $this->mailer->send('front.emails.single_limit', $data, function (Message $m) use ($merchant) {
                            $m->to($merchant->email, $merchant->mer_fname)->cc('operation@meihome.asia', 'MeiHome Operation')->subject('Single Transaction Exceed Limit');
                        });
                    }
                }

                // email if monthly transaction amount reach 80% from limit
                if ($store->monthly_limit) {
                    $monthly_limit_percentage = (($store->monthly_trans/$store->monthly_limit) * 100);
                    if ($monthly_limit_percentage >= 80) {
                        $data = [
                            'merchant_name' => $merchant->mer_fname . ' ' . $merchant->mer_lname,
                            'store_name' => $store->stor_name,
                            'percentage' => $monthly_limit_percentage,
                        ];

                        $this->mailer->send('front.emails.monthly_limit', $data, function (Message $m) use ($merchant) {
                            $m->to($merchant->email, $merchant->mer_fname)->cc('operation@meihome.asia', 'MeiHome Operation')->subject('Monthly Transaction Alert');
                        });
                    }
                }

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

                // update order
                $order->status = 1;
                $order->cust_id = $customer->cus_id;
                $order->paid_date = new \DateTime;
                $order->save();
            });

            return \Response::json([
                'status' => 1,
                'message' => trans('api.payment') . trans('api.success'),
                'order_id' => $order->id,
                'inv_no' => $order->inv_no,
                'currency' => $order->currency,
                'amount' => $order->amount,
                'vcoin' => $order->v_token,
                'service_charge_percentage' => $order->customer_charge_percentage,
                'service_charge_vcoin' => $order->customer_charge_token,
                'platform_charge_percentage' => $order->merchant_platform_charge_percentage,
                'platform_charge_vcoin' => $order->merchant_platform_charge_token,
                'total_vcoin' => $order->order_total_token,
                'merchant_name' => $merchantName,
                'member_vcoin_balance' => $customer->v_token,
            ]);

        } catch (\Exception $e) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.payment') . trans('api.fail')
            ]);
        }
        /// END transaction
    }

    // /member/order/cancel
    public function memberCancelOrder()
    {
        $input = \Request::only('member_id', 'order_id', 'inv_no');

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $validator = Validator::make($input, [
            'member_id' => 'required|integer',
            'order_id' => 'required|integer',
            'inv_no' => 'required|alpha_dash|max:50',
        ]);

        if ($validator->fails()) {
            $intErrors = $validator->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        $data = [
            'cust_id' => trim($input['member_id']),
            'id' => trim($input['order_id']),
            'inv_no' => trim($input['inv_no'])
        ];

        $order = OrderOffline::find($data['id']);
        if (empty($order)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.billNo') . $data['inv_no'] . trans('api.notFound')
            ]);
        }

        if (isset($order->cust_id)) {
            if ($order->cust_id != $data['cust_id']) {
                return \Response::json([
                    'status' => 0,
                    'message' => trans('api.billNo') . $order->inv_no . trans('api.doesNotBelongToThis') . trans('api.member')
                ]);
            }
        } else {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.billNo') . $order->inv_no . trans('api.hasNotBeen') . trans('api.claimed')
            ]);
        }

        switch ($order->status) {
            case 0:
                $order->status = 2;
                $order->save();

                return \Response::json([
                    'status' => 1,
                    'message' => trans('api.orderCancelledByMember')
                ]);
                break;
            case 1:
                return \Response::json([
                    'status' => 0,
                    'message' => trans('api.billNo') . $order->inv_no . trans('api.cannotCancelAsPaymentMade')
                ]);
            case 2:
            case 3:
                return \Response::json([
                    'status' => 0,
                    'message' => trans('api.billNo') . $order->inv_no . trans('api.hasAlreadyBeen') . trans('api.cancelled')
                ]);
            default:
                throw new \Exception('invalid order status');
                break;
        }
    }
    // END member

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

    public function memberOrderHistory()
    {
        $input = \Request::only('member_id', 'status');

        // input validation
        $validator = Validator::make($input, [
            'member_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $intErrors = $validator->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        $orders = OrderOffline::leftJoin('nm_store', 'nm_store.stor_merchant_id', '=', 'order_offline.mer_id')
        ->where('cust_id', '=', trim($input['member_id']));

        if (!empty($input['status'])) {
            $orders = $orders->where('status', '=', trim($input['status']));
        }

        $orders = $orders->paginate();

        if (empty($orders)) {
            return \Response::json([
                'status' => 0,
                'message' => 'No order history found'
            ]);
        } else {
            $details = [];

            foreach ($orders as $order) {
                $details[] = [
                    'order_id' => $order->id,
                    'inv_no' => $order->inv_no,
                    'store_id' => $order->stor_id,
                    'store_name' => $order->stor_name,
                    'currency' => $order->currency,
                    'amount' => $order->amount,
                    'vcoin' => $order->v_token,
                    'total_vcoin' => $order->order_total_token,
                    'status' => $order->status,
                    'paid_date' => $order->paid_date,
                    'remark' => $order->remark,
                ];
            }
            return \Response::json([
                'status' => 1 ,
                'message' => 'Order history found',
                'data' => $details
            ]);
        }
    }

    public function checkMerchant() {
        $input = \Request::only('merchant_id', 'lang');

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $niceNames = array(
            'merchant_id' => trans('api.merchantId'),
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

        $merchant = Merchant::find(trim($input['merchant_id']));

        if ($merchant) {
            switch ($merchant->mer_staus) {
                case '0':
                    return \Response::json([
                        'status' => 0,
                        'message' => trans('api.merchantId') . $input['merchant_id'] . trans('api.notActive'),
                    ]);
                    break;
                case '1':
                    return \Response::json([
                        'status' => 1,
                        'message' => trans('api.merchantId') . $input['merchant_id'] . trans('api.found'),
                    ]);
                    break;
                default:
                    return \Response::json([
                        'status' => 0,
                        'message' => trans('api.merchantId') . $input['merchant_id'] . trans('api.notFound'),
                    ]);
                    break;
            }
        }

        return \Response::json([
            'status' => 0,
            'message' => trans('api.merchantId') . $input['merchant_id'] . trans('api.notFound'),
        ]);
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

        if ($validator->fails()) {
            $intErrors = $validator->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        $store = StoreRepo::get_store_by_id(trim($input['store_id']));

        if (!$store)
            return \Response::json([
                'status' => 0,
                'message' => trans('api.store_id') . $input['store_id'] . trans('api.notFound'),
            ]);

        if ($store->stor_status == 0)
            return \Response::json([
                'status' => 0,
                'message' => trans('api.store_id') . $input['store_id'] . trans('api.notActive'),
            ]);

        // if ($store->stor_type == 0)
        if ($store->stor_type != 1)
            return \Response::json([
                'status' => 0,
                'message' => trans('api.store_id') . $input['store_id'] . trans('api.notOffline'),
            ]);

        // check store's accept payment
        if ($store->accept_payment == 0) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.store')  . trim($input['store_id']) . trans('api.notAcceptPayment')
            ]);
        }

        $merchant = Merchant::find($store->stor_merchant_id);

        if (!$merchant)
            return \Response::json([
                'status' => 0,
                'message' => trans('api.merchantId') . $store->stor_merchant_id . trans('api.notFound'),
            ]);

        if ($merchant->mer_staus == 0)
            return \Response::json([
                'status' => 0,
                'message' => trans('api.merchantId') . $store->stor_merchant_id . trans('api.notActive'),
            ]);

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
                'status' => 0,
                'message' => trans('api.currency') . trans('api.notFound')
            ]);
        }

        return \Response::json([
            'status' => 1,
            'message' => trans('api.store_id') . $input['store_id'] . trans('api.found'),
            'data' => [
                'store_id' => $store->stor_id,
                'store_name' => $store->stor_name,
                'store_address' => $store->stor_address1.', '.$store->stor_address2.', '.$store->stor_zipcode.' '.$store->stor_city_name.', '.(!empty($store-> stor_state) ? $store->name : $store->ci_name).', '.$store->co_name,
                'store_main_image' => env('IMAGE_DIR').'/store/'.$store->stor_merchant_id.'/'.$store->stor_img,
                'store_image' => $images,
                'store_short_desc' => $store->short_description,
                'store_type' => $store->stor_type,
                'store_status' => $store->stor_status,
                'store_country' => $store->stor_country,
                'store_currency' => $store->co_curcode,
                'accept_payment' => $store->accept_payment,
                'merchant_id' => $merchant->mer_id,
                'merchant_name' => $this->getMerchantNameById($merchant->mer_id),
                'merchant_status' => $merchant->mer_staus,
                'merchant_currency' => $currency->co_curcode,
            ],
        ]);
    }

    public function memberCreateOrder()
    {
//        return \Response::json([
//                'status' => 0,
//                'message' => '系统升级当中。06-01-17 12pm 即将恢复。'
//            ]);
        $input = \Request::only('merchant_id', 'member_id', 'currency', 'currency_rate', 'amount', 'vcoin', 'member_service_charge_rate', 'member_service_charge_vcoin', 'total_vcoin', 'remark', 'lang');

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $niceNames = array(
            'merchant_id' => trans('api.merchantId'),
            'member_id' => trans('api.memberId')
        );

        $validator = Validator::make($input, [
            'merchant_id' => 'required|integer',
            'member_id' => 'required|integer',
            'currency' => 'required|alpha|max:3',
            'currency_rate' => 'required|numeric|between:0.01,999999999.99',
            'amount' => 'required|numeric|between:0.01,9999999999999.99',
            'vcoin' => 'required|numeric|between:0.01,99999999999.99',
            'member_service_charge_rate' => 'required|numeric|between:0.01,99.99',
            'member_service_charge_vcoin' => 'required|numeric|between:0.01,999999999.99',
            'total_vcoin' => 'required|numeric|between:0.01,9999999999999.99',
            'remark' => 'max:500',
        ]);
        $validator->setAttributeNames($niceNames);

        if ($validator->fails()) {
            $intErrors = $validator->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];
            return $errors;
        }

        $data = [
            'mer_id' => trim($input['merchant_id']),
            'cust_id' => trim($input['member_id']),
            'inv_no' => trim($input['merchant_id']).'_'.date('YmdHis'),
            'currency' => $input['currency'],
            'currency_rate' => trim($input['currency_rate']),
            'amount' => trim($input['amount']),
            'v_token' => trim($input['vcoin']),
            'customer_charge_percentage' => trim($input['member_service_charge_rate']),
            'customer_charge_token' => trim($input['member_service_charge_vcoin']),
            'order_total_token' => trim($input['total_vcoin']),
            'remark' => $input['remark'],
            'type' => 2, // Created by Customer indicator
        ];

        // customer validation
        $customer = Customer::find($data['cust_id']);
        if (!$customer) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.member') . trans('api.notFound')
            ]);
        }

        if ($customer->v_token < $data['order_total_token']) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.insufficientFund')
            ]);
        }

        // merchant validation
        $merchant = Merchant::find($data['mer_id']);
        if (!$merchant) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.merchant') . trans('api.notFound')
            ]);
        }

        // currency validation
        $currency = Country::where(['co_curcode' => Str::upper($data['currency']), 'co_offline_status' => 1])->first();
        if (empty($currency)) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.currency') . trans('api.notFound')
            ]);
        }

        // merchant service charge
        $merchantServiceCharge = $merchant->mer_commission;

        // default merchant commission
        // if (empty($merchantServiceCharge))
        //     $merchantServiceCharge = 10;

        $data['merchant_charge_percentage'] = $merchantServiceCharge;
        $decimalMerchantServiceCharge = round(($merchantServiceCharge / 100), 4);
        $data['merchant_charge_token'] = round((double)$input['vcoin'] * $decimalMerchantServiceCharge, 4);
        $data['merchant_platform_charge'] = $merchant->mer_platform_charge;

        $order = OrderOffline::where('mer_id', $data['mer_id'])
                                ->where('inv_no', $data['inv_no'])
                                ->orderBy('id', 'desc')
                                ->first();

        if (empty($order)) {
            try {
                $order = OrderOffline::create($data);

                return \Response::json([
                    'status' => 1,
                    'message' => trans('api.createOrder') . trans('api.success'),
                    'order_id' => $order->id,
                    'merchant_name' => $this->getMerchantNameById($merchant->mer_id)
                ]);
            } catch (\Exception $e) {
                return \Response::json([
                    'status' => 0,
                    'message' => trans('api.createOrder') . trans('api.fail'),
                ]);
            }
        } else {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.createOrder') . trans('api.fail')
            ]);
        }
    }

    public function memberCreatePayment()
    {
        $input = \Request::only(
            'merchant_id',
            'store_id',
            'member_id',
            'currency',
            'amount',
            'remark',
            'security_code',
            'lang',
            'order_id',
            'inv_no'
        );

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $niceNames = array(
            'merchant_id' => trans('api.merchantId'),
            'member_id' => trans('api.memberId')
        );

        $validator = Validator::make($input, [
            'merchant_id' => 'required|integer',
            'store_id' => 'required|integer',
            'member_id' => 'required|integer',
            'currency' => 'required|alpha|max:3',
            'amount' => 'required|numeric|between:0.01,9999999999999.99',
            'remark' => 'max:500',
            'security_code' => 'required|digits:6',
            'order_id' => 'nullable|integer',
            'inv_no' => 'nullable|alpha_dash|max:50',
        ]);
        $validator->setAttributeNames($niceNames);

        if ($validator->fails()) {
            $intErrors = $validator->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];
            return $errors;
        }

        // if invoiceNo input not given then autogenerated invoiceNo
        $invoice_no = trim($input['inv_no']);
        if (empty($invoice_no))
            $invoice_no = trim($input['merchant_id']).'_'.date('YmdHis');

        $data = [
            'mer_id' => trim($input['merchant_id']),
            'store_id' => trim($input['store_id']),
            'cust_id' => trim($input['member_id']),
            'inv_no' => $invoice_no,
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
                'status' => 0,
                'message' => trans('api.merchantId') . trim($input['merchant_id']) . trans('api.notFound')
            ]);
        }

        // store validation
        $store = Store::where('stor_id', trim($input['store_id']))->where('stor_status', 1)->where('stor_type', '>', 0)->first();
        if (empty($store)) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Store Not Found']);
            return \Response::json([
                'status' => 0,
                'message' => trans('api.storeId') . trim($input['store_id']) . trans('api.notFound')
            ]);
        }

        if ($store->stor_merchant_id != trim($input['merchant_id'])) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Store Does Not Belong To Merchant']);
            return \Response::json([
                'status' => 0,
                'message' => trans('api.storeId') . trim($input['store_id']) . trans('api.doesNotBelongToThis') . trans('api.merchantId') . trim($input['merchant_id'])
            ]);
        }

        // check store's accept payment
        if ($store->accept_payment == 0) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.store') . trans('api.notAcceptPayment')
            ]);
        }

        // check trans exceed monthly limit or not
        if ($store->monthly_limit) {
            if ((trim($input['amount']) + $store->monthly_trans) > $store->monthly_limit) {
                return \Response::json([
                    'status' => 0,
                    'message' => trans('api.store') . trans('api.exceedLimit')
                ]);
            }
        }

        // member validation
        $customer = Customer::find(trim($input['member_id']));
        if (empty($customer)) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Customer Not Found']);
            return \Response::json([
                'status' => 0,
                'message' => trans('api.memberId') . trim($input['member_id']) . trans('api.notFound')
            ]);
        }

        if (!\Hash::check(trim($input['security_code']), $customer->payment_secure_code)) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Wrong Security Code']);
            return \Response::json([
                'status' => 0,
                'message' => trans('api.wrongCode')
            ]);
        }

        // Check currency
        // $currency = Country::where(['co_curcode' => Str::upper($data['currency']), 'co_offline_status' => 1])->first();
        $currency = Country::where(['co_id' => $store->stor_country, 'co_offline_status' => 1])->first();
        if (empty($currency)) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Currency Not Found']);
            return \Response::json([
                'status' => 0,
                'message' => trans('api.currency') . trans('api.notFound')
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
        $serviceCharge = round(($credit+$platformCharge) * ($serviceChargeRate/100), 4);
        $totalcredit = round($credit + $platformCharge + $serviceCharge, 4);

        // Calculate Merchant Commission
        $commissionRate = $merchant->mer_commission;
        $commission = round($credit * ($commissionRate/100), 4);

        // put into $data
        $data = [
            'mer_id' => trim($input['merchant_id']),
            'store_id' => trim($input['store_id']),
            'cust_id' => trim($input['member_id']),
            'inv_no' => trim($input['merchant_id']).'_'.date('YmdHis'),
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

        // Check customer credit
        if ($customer->v_token < $data['order_total_token']) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Insufficient Mei Point']);
            return \Response::json([
                'status' => 0,
                'message' => trans('api.insufficientFund')
            ]);
        }

        // retrieve main acc details
        $mainAcc = Merchant::find(180);
        if (empty($mainAcc)) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Primary Acc Not Found']);
            return \Response::json([
                'status' => 0,
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
                'status' => 0,
                'message' => trans('api.createOrder') . trans('api.fail')
            ]);
        }

        if ($input['order_id']) {
            // dd($input['order_id']);
            OrderOffline::where('id', trim($input['order_id']))->update(['status' => 2]);
        }

        try {
            $order = OrderOffline::create($data);
        } catch (\Exception $e) {
            OrderOfflineLog::where('id', $request_log->id)->update(['description' => 'Error : Fail create order']);
            return \Response::json([
                'status' => 0,
                'message' => trans('api.createOrder') . trans('api.fail'),
            ]);
        }

        // retrieve merchant name
        $merchantName = $this->getMerchantNameById($data['mer_id']);
        /// BEGIN transaction
        try {
            \DB::transaction(function() use ($merchant, $customer, $order, $merchantName, $mainAcc, $store)
            {
                $origToken = $order->v_token;
                $customerVToken = $order->order_total_token;
                $customerChargeToken = $order->customer_charge_token;
                $platformChargeToken = $order->merchant_platform_charge_token;
                $merchantCommission = $order->merchant_charge_token;
                $merchantVToken = $origToken - $merchantCommission;

                // deduct token from customer
                $customer->v_token -= $customerVToken;
                // this part should not happen but as a safeguard
                if ($customer->v_token < 0) {
                    $customer->v_token = 0;
                }
                $customer->save();

                $arrCustomerVTokenLog = [
                    'cus_id' => $customer->cus_id,
                    'credit_amount' => 0,
                    'debit_amount' => $customerVToken,
                    'offline_order_id' => $order->id,
                    'remark' => 'order payment' . (isset($order->remark) ? ': ' . $order->remark : '')
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

                // update monthly transaction amount
                $store->monthly_trans += $order->amount;
                $store->save();

                // email if store single limit exceed
                if (!is_null($store->single_limit)) {
                    if ($order->amount > $store->single_limit) {
                        $data = [
                            'trans_type' => 'Offline Order',
                            'merchant_name' => $merchant->mer_fname . ' ' . $merchant->mer_lname,
                            'store_name' => $store->stor_name,
                            'order_id' => $order->id,
                            'trans_no' => $order->inv_no,
                            'credit' => $merchantVToken,
                            'amount' => $order->amount,
                            'currency' => $order->currency_rate,
                            'currency_code' => $order->currency,
                        ];

                        $this->mailer->send('front.emails.single_limit', $data, function (Message $m) use ($merchant) {
                            $m->to($merchant->email, $merchant->mer_fname)->cc('operation@meihome.asia', 'MeiHome Operation')->subject('Single Transaction Exceed Limit');
                        });
                    }
                }

                // email if monthly transaction amount reach 80% from limit
                if ($store->monthly_limit) {
                    $monthly_limit_percentage = (($store->monthly_trans/$store->monthly_limit) * 100);
                    if ($monthly_limit_percentage >= 80) {
                        $data = [
                            'merchant_name' => $merchant->mer_fname . ' ' . $merchant->mer_lname,
                            'store_name' => $store->stor_name,
                            'percentage' => $monthly_limit_percentage,
                        ];

                        $this->mailer->send('front.emails.monthly_limit', $data, function (Message $m) use ($merchant) {
                            $m->to($merchant->email, $merchant->mer_fname)->cc('operation@meihome.asia', 'MeiHome Operation')->subject('Monthly Transaction Alert');
                        });
                    }
                }

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

                // update order
                $order->status = 1;
                $order->cust_id = $customer->cus_id;
                $order->paid_date = new \DateTime;
                $order->save();
            });

            return \Response::json([
                'status' => 1,
                'message' => trans('api.payment') . trans('api.success'),
                'order_id' => $order->id,
                'inv_no' => $order->inv_no,
                'currency' => $order->currency,
                'amount' => $order->amount,
                'vcoin' => $order->v_token,
                'service_charge_percentage' => $order->customer_charge_percentage,
                'service_charge_vcoin' => $order->customer_charge_token,
                'platform_charge_percentage' => $order->merchant_platform_charge_percentage,
                'platform_charge_vcoin' => $order->merchant_platform_charge_token,
                'total_vcoin' => $order->order_total_token,
                'merchant_name' => $merchantName,
                'member_vcoin_balance' => $customer->v_token,
            ]);

        } catch (\Exception $e) {
            return \Response::json([
                'status' => 0,
                'order_id' => $order->id,
                'message' => trans('api.payment') . trans('api.fail')
            ]);
        }

    }
}