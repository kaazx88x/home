<?php

namespace App\Http\Controllers\Api\V2\merchant;

use App\Http\Controllers\Controller;
use App\Repositories\StoreRepo;
use App\Repositories\LimitRepo;
use App\Repositories\GeneratedCodeRepo;
use Illuminate\Support\Str;
use App\Models\OrderOffline;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Country;
use App\Models\Store;
use App\Models\Order;
use Carbon\Carbon;
use Validator;
use App;

class PaymentController extends Controller
{
    protected $niceNames;
    protected $mer_id;
    protected $app_session;

    public function __construct()
    {
        if (\Auth::guard('api_storeusers')->check()) {
            $this->mer_id = \Auth::guard('api_storeusers')->user()->mer_id;
            $this->app_session = \Auth::guard('api_storeusers')->user()->app_session;
        }

        if (\Auth::guard('api_merchants')->check()) {
            $this->mer_id = \Auth::guard('api_merchants')->user()->mer_id;
            $this->app_session = \Auth::guard('api_merchants')->user()->app_session;
        }

        $this->niceNames = [
            'store_id' => trans('api.store_id'),
            'customer_id' => trans('api.customer_id'),
            'inv_no' => trans('api.inv_no'),
            'merchant_id' => trans('api.merchant_id'),
            'currency' => trans('api.currency'),
            'amount' => trans('api.amount'),
            'remark' => trans('api.remark'),
            'order_id' => trans('api.order_id'),
            'coupon_code' => trans('localize.coupon_code'),
            'ticket_number' => trans('localize.ticket_number'),
        ];
    }

    public function createOrder()
    {
        $input = \Request::only('store_id', 'inv_no', 'currency', 'amount', 'remark', 'lang');

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        $store_id = trim($input['store_id']);

        $v = Validator::make($input, [
            'store_id' => 'required|integer',
            'inv_no' => 'required|alpha_dash|max:50|unique:order_offline,inv_no,NULL,id,store_id,'.$store_id,
            'currency' => 'required|alpha|max:3',
            'amount' => 'required|numeric|between:0.01,9999999999999.99',
            'remark' => 'nullable|max:500',
        ])->setAttributeNames($this->niceNames);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ], 422);
        }

        // merchant validation
        $merchant = Merchant::find(trim($this->mer_id));
        if (empty($merchant)) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.merchant') . trans('api.notFound')
            ], 404);
        }

        if ($merchant->mer_staus != 1) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.merchant') . ' ' . trans('api.notActive'),
            ]);
        }

        // store validation
        $store = Store::where('stor_id', trim($input['store_id']))->where('stor_status', 1)->where('stor_type', '>', 0)->first();

        if (empty($store)) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.store') . trans('api.notFound')
            ], 404);
        }

        if ($store->stor_merchant_id != trim($this->mer_id)) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.storeId') . trim($input['store_id']) . trans('api.doesNotBelongToThis') . trans('api.merchantId') . trim($this->mer_id)
            ], 400);
        }

        // check store's accept payment
        if ($store->accept_payment != 1) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.store') . trim($input['store_id']) . trans('api.notAcceptPayment')
            ], 400);
        }

        // currency validation
        // $currency = Country::where(['co_curcode' => Str::upper(trim($input['currency'])), 'co_offline_status' => 1])->first();
        $currency = Country::where(['co_id' => $store->stor_country, 'co_offline_status' => 1])->first();
        if (empty($currency)) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.currency') . trans('api.notFound')
            ], 404);
        }

        $check = LimitRepo::check_payment_limitation('storeLimit', $input['amount'], $store->stor_id);
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
            'mer_id' => trim($this->mer_id),
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
                    'status' => 200,
                    'message' => trans('api.createOrder') . trans('api.success'),
                    'order_id' => $order->id,
                    'merchant_name' => $this->getMerchantNameById($merchant->mer_id),
                    'stores' => $stores,
                ]);
            } catch (\Exception $e) {
                return \Response::json([
                    'status' => 500,
                    'message' => trans('api.createOrder') . trans('api.fail'),
                ], 500);
            }
        } else {
            switch ($order->status) {
                case '0':
                    $order->status = 3;
                    $order->save();

                    try {
                        $newOrder = OrderOffline::create($data);

                        return \Response::json([
                            'status' => 200,
                            'message' => trans('api.createOrder') . trans('api.success'),
                            'order_id' => $newOrder->id,
                            'merchant_name' => $this->getMerchantNameById($merchant->mer_id),
                            'stores' => $stores,
                        ]);
                    } catch (\Exception $e) {
                        return \Response::json([
                            'status' => 500,
                            'message' => trans('api.createOrder') . trans('api.fail'),
                        ], 500);
                    }
                    break;
                case '1':
                    return \Response::json([
                        'status' => 400,
                        'message' => trans('api.billNo') . $data['inv_no'] . trans('api.hasAlreadyBeen') . trans('api.paid')
                    ], 400);
                    break;
                case '2':
                case '3':
                    return \Response::json([
                        'status' => 400,
                        'message' => trans('api.billNo') . $data['inv_no'] . trans('api.hasAlreadyBeen') . trans('api.cancelled')
                    ], 400);
                default:
                    return \Response::json([
                        'status' => 500,
                        'message' => trans('api.createOrder') . trans('api.fail'),
                    ], 500);
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
        $input = \Request::only('store_id', 'order_id', 'inv_no', 'lang');

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $v = Validator::make($input, [
            'store_id' => 'required|integer',
            'order_id' => 'required|integer',
            'inv_no' => 'required|alpha_dash|max:50',
        ])->setAttributeNames($this->niceNames);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ], 422);
        }

        // merchant validation
        $merchant = Merchant::find(trim($this->mer_id));
        if (empty($merchant)) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.merchantId') . trim($this->mer_id) . trans('api.notFound')
            ], 404);
        }

        // store validation
        $store = Store::where('stor_id', trim($input['store_id']))->where('stor_status', 1)->where('stor_type', '>', 0)->first();
        if (empty($store)) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.storeId') . trim($input['store_id']) . trans('api.notFound')
            ], 404);
        }

        if ($store->stor_merchant_id != trim($this->mer_id)) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.storeId') . trim($input['store_id']) . trans('api.doesNotBelongToThis') . trans('api.merchantId') . trim($this->mer_id)
            ], 400);
        }

        $order = OrderOffline::find(trim($input['order_id']));
        if (empty($order)) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.billNo') . trim($input['inv_no']) . trans('api.notFound')
            ], 404);
        }

        if ($order->mer_id != trim($this->mer_id)) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.billNo') . trim($input['inv_no']) . trans('api.doesNotBelongToThis') . trans('api.merchant')
            ], 400);
        }

        if ($order->store_id != trim($input['store_id'])) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.billNo') . trim($input['inv_no']) . trans('api.doesNotBelongToThis') . trans('api.store')
            ], 400);
        }

        $stores = StoreRepo::api_get_stores($merchant->mer_id);

        $retObj = [
            'status' => 200,
            'inv_no' => $order->inv_no,
            'order_id' => $order->id,
            'order_status' => $order->status,
            'merchant_id' => $order->mer_id,
            'store_id' => $order->store_id,
            'currency' => $order->currency,
            'amount' => $order->amount,
            'credit' => $order->v_token,
            'service_charge_percentage' => $order->customer_charge_percentage,
            'service_charge_credit' => $order->customer_charge_token,
            'platform_charge_percentage' => $order->merchant_platform_charge_percentage,
            'platform_charge_credit' => $order->merchant_platform_charge_token,
            'total_credit' => $order->order_total_token,
            'stores' => $stores,
        ];

        switch ($order->status) {
            case '0':
                $retObj['order_status'] = 'unpaid';
                break;
            case '1':
                $retObj['order_status'] = 'paid';
                $retObj['merchant_credit_balance'] = $merchant->mer_vtoken;
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
        $input = \Request::only('store_id', 'order_id', 'inv_no', 'lang');

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $v = Validator::make($input, [
            'store_id' => 'required|integer',
            'order_id' => 'required|integer',
            'inv_no' => 'required|alpha_dash|max:50',
        ])->setAttributeNames($this->niceNames);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ], 422);
        }

        // merchant validation
        $merchant = Merchant::find(trim($this->mer_id));
        if (empty($merchant)) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.merchantId') . trim($this->mer_id) . trans('api.notFound')
            ], 404);
        }

        if ($merchant->mer_staus != 1) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.merchant') . ' ' . trans('api.notActive'),
            ]);
        }

        // store validation
        $store = Store::where('stor_id', trim($input['store_id']))->where('stor_status', 1)->where('stor_type', '>', 0)->first();
        if (empty($store)) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.storeId') . trim($input['store_id']) . trans('api.notFound')
            ], 404);
        }

        if ($store->stor_merchant_id != trim($this->mer_id)) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.storeId') . trim($input['store_id']) . trans('api.doesNotBelongToThis') . trans('api.merchantId') . trim($this->mer_id)
            ], 400);
        }

        // check store's accept payment
        if ($store->accept_payment != 1) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.store') . trim($input['store_id']) . trans('api.notAcceptPayment')
            ], 400);
        }

        $order = OrderOffline::find(trim($input['order_id']));
        if (empty($order)) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.billNo') . $order->inv_no . trans('api.notFound')
            ], 404);

        }

        if ($order->mer_id != trim($this->mer_id)) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.billNo') . $order->inv_no . trans('api.doesNotBelongToThis') . trans('api.merchant')
            ], 400);
        }

        if ($order->store_id != trim($input['store_id'])) {
            return \Response::json([
                'status' => 400,
                'message' => trans('api.billNo') . trim($input['inv_no']) . trans('api.doesNotBelongToThis') . trans('api.store')
            ], 400);
        }

        switch ($order->status) {
            case 0:
                $order->status = 3;
                $order->save();

                return \Response::json([
                    'status' => 200,
                    'message' => trans('api.orderCancelledByMerchant')
                ]);
                break;
            case 1:
                return \Response::json([
                    'status' => 400,
                    'message' => trans('api.billNo') . $order->inv_no . trans('api.cannotCancelAsPaymentMade'),
                ], 400);
            case 2:
            case 3:
                return \Response::json([
                    'status' => 400,
                    'message' => trans('api.billNo') . $order->inv_no . trans('api.hasAlreadyBeen') . trans('api.cancelled'),
                ], 400);
            default:
                throw new \Exception('invalid order status');
                break;
        }
    }

    public function couponStatus()
    {
        $type = 'coupon';
        $input = \Request::only('coupon_code', 'lang');

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $v = Validator::make($input, [
            'coupon_code' => 'required|alpha_num|min:16|max:16',
        ])->setAttributeNames($this->niceNames);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ], 422);
        }

        return $this->codeStatus($input['coupon_code'], $type);
    }

    public function ticketStatus()
    {
        $type = 'ticket';
        $input = \Request::only('ticket_number', 'lang');

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $v = Validator::make($input, [
            'ticket_number' => 'required|alpha_dash',
        ])->setAttributeNames($this->niceNames);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ], 422);
        }

        return $this->codeStatus($input['ticket_number'], $type);
    }

    protected function codeStatus($serial_number, $type)
    {
        try {
            switch ($type) {
                case 'coupon':
                    $code = GeneratedCodeRepo::find_coupon($serial_number);
                    $lang = trans('localize.coupon_code');
                    break;

                case 'ticket':
                    $code = GeneratedCodeRepo::find_ticket($serial_number);
                    $lang = trans('localize.ticket_number');
                    break;

                default:
                    return \Response::json([
                        'status' => 500,
                        'message' => trans('api.systemError')
                    ]);
                    break;
            }

            if(!$code) {
                return \Response::json([
                    'status' => 404,
                    'message' => $lang . trans('api.notFound')
                ]);
            }

            $order = Order::select('nm_product.pro_mr_id as mer_id')
            ->where('order_id', $code->order_id)
            ->leftJoin('nm_product', 'nm_product.pro_id', '=', 'nm_order.order_pro_id')
            ->first();

            if(!$order || $order->mer_id != trim($this->mer_id) || $code->merchant_id != trim($this->mer_id)) {
                return \Response::json([
                    'status' => 404,
                    'message' => $lang .' '. $serial_number . trans('api.doesNotBelongToThis') . trans('api.merchant')
                ]);
            }

            $status = '';
            $date = '';
            switch ($code->status) {
                case 1:
                    $status = trans('localize.open');
                    if(!empty($code->valid_to)) {
                        $now = Carbon::now('UTC');
                        $expired = Carbon::createFromFormat('Y-m-d H:i:s', $code->valid_to, 'UTC');
                        if($now >= $expired) {
                            $status = trans('localize.expired');
                            $date = $expired;
                        }
                    }
                    break;

                case 2:
                    $status = trans('localize.redeemed');
                    $date = Carbon::createFromFormat('Y-m-d H:i:s', $code->redeemed_at, 'UTC');
                    break;

                case 3:
                    $status = trans('localize.cancelled');
                    $date = $code->updated_at;
                    break;
            }

            $data = [
                'status' => $status,
                'date' => $date,
            ];

            return \Response::json([
                'status' => 200,
                'data' => $data
            ]);

        } catch (Exception $e) {
            return \Response::json([
                'status' => 500,
                'message' => trans('api.systemError')
            ]);
        }
    }

    public function redeemCoupon()
    {
        $type = 'coupon';
        $input = \Request::only('coupon_code', 'verification_key');
        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        //grab hash key
        $merchant = Merchant::find(trim($this->mer_id));
        $input['verification_key'] = md5($merchant->mer_id.'+'.$input['coupon_code'].'+'.md5($merchant->email.'+'.$this->app_session));

        // input validation
        $v = Validator::make($input, [
            'coupon_code' => 'required|alpha_num|min:16|max:16',
            'verification_key' => 'required|max:200',
        ])->setAttributeNames($this->niceNames);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all()),
            ], 422);
        }

        return $this->redeemCode($input, $type);
    }

    public function redeemTicket()
    {
        $type = 'ticket';
        $input = \Request::only('ticket_number', 'verification_key');
        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        //grab hash key
        $merchant = Merchant::find(trim($this->mer_id));
        $input['verification_key'] = md5($merchant->mer_id.'+'.$input['ticket_number'].'+'.md5($merchant->email.'+'.$this->app_session));

        // input validation
        $v = Validator::make($input, [
            'ticket_number' => 'required|alpha_dash',
            'verification_key' => 'required|max:200',
        ])->setAttributeNames($this->niceNames);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all()),
            ], 422);
        }

        return $this->redeemCode($input, $type);
    }

    protected function redeemCode($input, $type)
    {
        switch ($type) {
            case 'coupon':
                $lang = trans('localize.coupon_code');
                $serial_number = $input['coupon_code'];
                $code = GeneratedCodeRepo::find_coupon($serial_number);
                break;

            case 'ticket':
                $lang = trans('localize.ticket_number');
                $serial_number = $input['ticket_number'];
                $code = GeneratedCodeRepo::find_ticket($serial_number);
                break;

            default:
                return \Response::json([
                    'status' => 500,
                    'message' => trans('api.systemError')
                ]);
                break;
        }
        $merchant = Merchant::find(trim($this->mer_id));

        if(!$merchant) {
            return \Response::json([
                'status' => 422,
                'message' => trans('api.merchant_not_found'),
            ], 422);
        }

        $verification_check = md5($merchant->mer_id.'+'.$serial_number.'+'.md5($merchant->email.'+'.$this->app_session));

        if(trim($input['verification_key']) != $verification_check) {
            return \Response::json([
                'status' => 422,
                'message' => trans('api.invalid_verification_key')
            ], 422);
        }

        try {
            if(!$code) {
                return \Response::json([
                    'status' => 404,
                    'message' => $lang . trans('api.notFound')
                ]);
            }

            $order = Order::select('nm_product.pro_mr_id as mer_id','nm_order.order_status','nm_product_pricing.coupon_value')
            ->where('order_id', $code->order_id)
            ->leftJoin('nm_product', 'nm_product.pro_id', '=', 'nm_order.order_pro_id')
            ->leftJoin('nm_product_pricing', 'nm_product_pricing.id', '=', 'nm_order.order_pricing_id')
            ->first();

            if(!$order || $order->mer_id != trim($this->mer_id) || $code->merchant_id != trim($this->mer_id)) {
                return \Response::json([
                    'status' => 404,
                    'message' => $lang .' '. $serial_number . trans('api.doesNotBelongToThis') . trans('api.merchant')
                ]);
            }

            if($code->status != 1) {
                return \Response::json([
                    'status' => 403,
                    'message' => trans('api.status_not_open', ['type' => $lang])
                ]);
            }

            if(!empty($code->valid_to)) {
                $now = Carbon::now('UTC');
                $expired = Carbon::createFromFormat('Y-m-d H:i:s', $code->valid_to, 'UTC');

                if($now >= $expired) {
                    return \Response::json([
                        'status' => 403,
                        'message' => trans('api.date_expired', ['type' => $lang]),
                        'expired_date' => $expired,
                    ]);
                }
            }

            //redeem method, 1-merchant api, 0-web
            $method = 1;
            $redeem = GeneratedCodeRepo::redeem_code($type, $serial_number, $method);
            if(!$redeem) {
                return \Response::json([
                    'status' => 500,
                    'message' => trans('api.systemError')
                ]);
            }

            if($type == 'coupon') {
                return \Response::json([
                    'status' => 200,
                    'message' => trans('api.success_redeemed', ['type' => $lang]),
                    'value' => $code->value,
                ]);
            }

            return \Response::json([
                'status' => 200,
                'message' => trans('api.success_redeemed', ['type' => $lang]),
            ]);

        } catch (Exception $e) {
            return \Response::json([
                'status' => 500,
                'message' => trans('api.systemError')
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
}