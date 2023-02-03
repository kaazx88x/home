<?php
namespace App\Repositories;


use Carbon\Carbon;
use Illuminate\Database\Connection;
use App\Models\LimitAction;
use App\Models\Limit;
use App\Models\Store;
use App\Models\EmailQueue;
use App\Models\OrderOffline;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;

class LimitRepo
{
    public static function init_store_limit($store_id)
    {
        $store = Store::find($store_id);
        if($store->limit)
            return $store;

        if(!$store || $store->stor_type == 0)
            return false;

        $limit = Limit::create([]);
        $store->limit_id = $limit->id;
        $store->save();

        return Store::find($store_id);
    }

    public static function init_customer_limit($customer_id)
    {
        $customer = Customer::find($customer_id);
        if($customer->limit)
            return $customer;

        if(!$customer)
            return false;

        $limit = Limit::create([]);
        $customer->limit_id = $limit->id;
        $customer->save();

        return Customer::find($customer_id);
    }

    public static function add_action($limit_id, $data)
    {
        return LimitAction::create([
            'limit_id' => $limit_id,
            'type' => $data['type'],
            'action' => $data['action'],
            'amount' => !empty($data['amount'])? $data['amount'] : null,
            'number_transaction' => !empty($data['number_transaction'])? $data['number_transaction'] : null,
            'order' => $data['type'].'.'.$data['action'],
            'per_user' => $data['per_user'],
        ]);
    }

    public static function edit_action($action_id, $data)
    {
        $action = LimitAction::find($action_id);

        $amount = !empty($data['amount'])? $data['amount'] : null;
        $number = null;
        if($action->type > 1 && !empty($data['number_transaction'])) {
            $number = $data['number_transaction'];
        }

        $action->update([
            'amount' => $amount,
            'number_transaction' => $number,
            'per_user' => $data['per_user'],
        ]);

        return true;
    }

    public static function delete_action($action_id)
    {
        return LimitAction::where('id', $action_id)->delete();
    }

    public static function check_payment_limitation($module, $amount, $store_id = null, $customer_id = null, $product_id = null)
    {
        switch ($module) {
            case 'storeLimit':
                $store = Store::find($store_id);
                if(!$store->limit)
                    $store = self::init_store_limit($store_id);

                $limit = $store->limit;
                if($limit->blocked->isEmpty())
                    return false;

                $limits = $limit->store_blocked;
                if(!$limits->isEmpty()) {

                    $trans = json_decode(json_encode([
                        'daily' => [
                            'total' => $limit->daily,
                            'count' => $limit->daily_count,
                        ],
                        'weekly' => [
                            'total' => $limit->weekly,
                            'count' => $limit->weekly_count,
                        ],
                        'monthly' => [
                            'total' => $limit->monthly,
                            'count' => $limit->monthly_count,
                        ],
                        'yearly' => [
                            'total' => $limit->yearly,
                            'count' => $limit->yearly_count,
                        ],
                    ]));

                    $trans = json_decode(json_encode($trans));

                    $result = self::render_limit_result($amount, $trans, $limits, 'store');
                    if($result['exceed']) {
                        return $result['message'];
                    }
                }

                $limits = $limit->user_blocked;
                if($customer_id && !$limits->isEmpty()) {

                    $now = Carbon::now('UTC');
                    $orders = OrderOffline::selectRaw('CASE WHEN COUNT(*) > 0 THEN SUM(amount) ELSE 0.00 END as total, COUNT(*) as count')
                    ->where('store_id', $store_id)
                    ->where('cust_id', $customer_id)
                    ->where('status', 1)
                    ->whereRaw('YEAR(created_at) = ?', [$now->year]);

                    $yearly = $orders->first();
                    $monthly = $orders->whereRaw('MONTH(created_at) = ?', [$now->month])->first();
                    $weekly = $orders->whereRaw('WEEK(created_at, 1) = ?', [$now->weekOfYear])->first();
                    $daily = $orders->whereRaw('DATE(created_at) = ?', [$now->format("Y-m-d")])->first();

                    $trans = json_decode(json_encode([
                        'daily' => [
                            'total' => $daily->total,
                            'count' => $daily->count,
                        ],
                        'weekly' => [
                            'total' => $weekly->total,
                            'count' => $weekly->count,
                        ],
                        'monthly' => [
                            'total' => $monthly->total,
                            'count' => $monthly->count,
                        ],
                        'yearly' => [
                            'total' => $yearly->total,
                            'count' => $yearly->count,
                        ],
                    ]));

                    $result = self::render_limit_result($amount, $trans, $limits, 'member');
                    if($result['exceed']) {
                        return $result['message'];
                    }
                }

                break;

            case 'customerLimit':
                $customer = Customer::find($customer_id);
                if(!$customer->limit)
                    $customer = self::init_customer_limit($customer_id);

                $limit = $customer->limit;
                if($limit->blocked->isEmpty())
                    return false;

                $limits = $limit->blocked;
                if(!$limits->isEmpty()) {

                    $trans = json_decode(json_encode([
                        'daily' => [
                            'total' => $limit->daily,
                            'count' => $limit->daily_count,
                        ],
                        'weekly' => [
                            'total' => $limit->weekly,
                            'count' => $limit->weekly_count,
                        ],
                        'monthly' => [
                            'total' => $limit->monthly,
                            'count' => $limit->monthly_count,
                        ],
                        'yearly' => [
                            'total' => $limit->yearly,
                            'count' => $limit->yearly_count,
                        ],
                    ]));

                    $trans = json_decode(json_encode($trans));

                    $result = self::render_limit_result($amount, $trans, $limits, 'member');
                    if($result['exceed']) {
                        return $result['message'];
                    }
                }
                break;

                case 'productLimit':
                    if(!$customer_id)
                        return false;

                    if(!$product_id)
                        return false;

                    $product = Product::find($product_id);
                    if(!$product || !$product->limit_enabled)
                        return false;

                    $puchased = 0;
                    $amount = $amount? $amount : 0;
                    $now = Carbon::now('UTC');

                    $orders = Order::selectRaw("nm_product.pro_id as product_id, nm_product.limit_quantity, nm_product.limit_type, nm_order.order_qty as purchased_quantity, nm_order.order_date as date, WEEK(order_date, 1) as week_of_year")
                    ->leftJoin('nm_product', 'nm_product.pro_id', '=', 'nm_order.order_pro_id')
                    ->where('nm_order.order_pro_id', $product_id)
                    ->where('nm_order.order_cus_id', $customer_id)
                    ->whereRaw('YEAR(order_date) = ?', [$now->year])
                    ->get();

                    switch ($product->limit_type)
                    {
                        case 0:
                            $type = strtolower(trans('localize.total_limit'));
                            $puchased += $orders->sum('purchased_quantity');
                            break;

                        case 1:
                            $type = strtolower(trans('localize.daily_limit'));
                            $puchased += $orders->filter(function($order) use ($now) {
                                return (data_get($order, 'date') >= $now->startOfDay()) && (data_get($order, 'date') <= $now->endOfDay());
                            })->sum('purchased_quantity');
                            break;

                        case 2:
                            $type = strtolower(trans('localize.weekly_limit'));
                            $puchased += $orders->filter(function($order) use ($now) {
                                return data_get($order, 'week_of_year') == $now->weekOfYear;
                            })->sum('purchased_quantity');
                            break;

                        case 3:
                            $type = strtolower(trans('localize.monthly_limit'));
                            $puchased += $orders->filter(function($order) use ($now) {
                                return (data_get($order, 'date') >= $now->startOfMonth()) && (data_get($order, 'date') <= $now->endOfMonth());
                            })->sum('purchased_quantity');
                            break;

                        case 4:
                            $type = strtolower(trans('localize.yearly_limit'));
                            $puchased += $orders->filter(function($order) use ($now) {
                                return (data_get($order, 'date') >= $now->startOfYear()) && (data_get($order, 'date') <= $now->endOfYear());
                            })->sum('purchased_quantity');
                            break;
                    }

                    $amount += $puchased;
                    if($amount > $product->limit_quantity)
                    {
                        return trans('localize.transaction_limit.product.quantity', ['product_title' => $product->title, 'max_quantity' => $product->limit_quantity, 'current_quantity' => $puchased, 'type' => $type]);
                    }

                    return false;
                break;

            default:
                return 'Invalid limit module';
                break;
        }

        return false;
    }

    protected static function render_limit_result($amount, $trans, $limits, $userType)
    {
        $exceed = false;
        $limitType = '';
        $blockBy = '';
        $blockValue = 0;
        foreach ($limits as $limit) {

            switch ($limit->type) {
                case 1:
                    //single transaction
                    $limitType = 'single';
                    if($amount > $limit->amount) {
                        $exceed = true;
                        $blockBy = 'amount';
                        $blockValue = $limit->amount;
                    }
                    break;

                case 2:
                    //daily transaction
                    $limitType = 'daily';
                    if( !empty($limit->amount) && ($amount + $trans->daily->total) > $limit->amount) {
                        $exceed = true;
                        $blockBy = 'amount';
                        $blockValue = $limit->amount;
                    }

                    if( !empty($limit->number_transaction) && ($trans->daily->count + 1) > $limit->number_transaction) {
                        $exceed = true;
                        $blockBy = 'number';
                        $blockValue = $limit->number_transaction;
                    }
                    break;

                case 3:
                    //weekly transaction
                    $limitType = 'weekly';
                    if( !empty($limit->amount) && ($amount + $trans->weekly->total) > $limit->amount) {
                        $exceed = true;
                        $blockBy = 'amount';
                        $blockValue = $limit->amount;
                    }

                    if( !empty($limit->number_transaction) && ($trans->weekly->count + 1) > $limit->number_transaction) {
                        $exceed = true;
                        $blockBy = 'number';
                        $blockValue = $limit->number_transaction;
                    }
                    break;

                case 4:
                    //monthly transaction
                    $limitType = 'monthly';
                    if( !empty($limit->amount) && ($amount + $trans->monthly->total) > $limit->amount) {
                        $exceed = true;
                        $blockBy = 'amount';
                        $blockValue = $limit->amount;
                    }

                    if( !empty($limit->number_transaction) && ($trans->monthly->count + 1) > $limit->number_transaction) {
                        $exceed = true;
                        $blockBy = 'number';
                        $blockValue = $limit->number_transaction;
                    }
                    break;

                case 5:
                    //yearly transaction
                    $limitType = 'yearly';
                    if( !empty($limit->amount) && ($amount + $trans->yearly->total) > $limit->amount) {
                        $exceed = true;
                        $blockBy = 'amount';
                        $blockValue = $limit->amount;
                    }

                    if( !empty($limit->number_transaction) && ($trans->yearly->count + 1) > $limit->number_transaction) {
                        $exceed = true;
                        $blockBy = 'number';
                        $blockValue = $limit->number_transaction;
                    }
                    break;
            }
        }

        $message = '';
        if($exceed) {
            if($blockBy == 'amount') {
                $message = trans('localize.transaction_limit.amount', ['user' => $userType, 'limit_type' => $limitType, 'value' => $blockValue]);
            } else {
                $message = trans('localize.transaction_limit.number', ['user' => $userType, 'limit_type' => $limitType, 'value' => $blockValue]);
            }
        }

        return [
            'exceed' => $exceed,
            'message' => $message
        ];
    }

    public static function update_limit_transaction($orderType, $reference_id)
    {
        try {

            $amountValue = 0;
            $creditValue = 0;
            switch ($orderType) {
                case 'offline':
                    $order = OrderOffline::find($reference_id);
                    if(!$order)
                        return;

                    $store = $order->store? $order->store : null;
                    $customer = $order->customer? $order->customer : null;
                    $amountValue = $order->amount;
                    $creditValue = round($amountValue / $order->currency_rate, 4);
                    break;

                case 'online':
                    $order = Order::where('transaction_id', $reference_id)->get();
                    if($order->isEmpty())
                        return;

                    //store limit checking for online store is not enabled
                    $store = null;
                    $customer = $order->first()->customer? $order->first()->customer : null;
                    $amountValue = $order->sum('total_product_price') + $order->sum('total_product_shipping_fees');
                    $creditValue = round($amountValue / $order->first()->currency_rate, 4);
                    break;

                default:
                    return;
                    break;
            }

            //reference id is based on module
            if($store) {
                $module = 'Store';
                if(!$store->limit) {
                    $store = self::init_store_limit($store->stor_id);
                }

                if($store->country) {
                    $currency_rate = $orderType == 'offline'? $store->country->co_offline_rate : $store->country->co_rate;
                    $amountValue = round($creditValue * $currency_rate, 2);
                }

                self::update_transaction($store->limit_id, $amountValue);
                self::send_limit_alert($module, null, $store->limit_id, $amountValue);
            }

            if($customer) {
                $module = 'Customer';
                if(!$customer->limit) {
                    $customer = self::init_customer_limit($customer->cus_id);
                }

                if($customer->country) {
                    $currency_rate = $orderType == 'offline'? $customer->country->co_offline_rate : $customer->country->co_rate;
                    $amountValue = round($creditValue * $currency_rate, 2);
                }

                self::update_transaction($customer->limit_id, $amountValue);

                if($customer->email_verified)
                    self::send_limit_alert($module, null, $customer->limit_id, $amountValue);
            }

            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    protected static function update_transaction($limit_id, $amount)
    {
        $limit = Limit::find($limit_id);
        if(!$limit)
            return false;

        $limit->daily += $amount;
        $limit->daily_count += 1;
        $limit->weekly += $amount;
        $limit->weekly_count += 1;
        $limit->monthly += $amount;
        $limit->monthly_count += 1;
        $limit->yearly += $amount;
        $limit->yearly_count += 1;
        $limit->save();
    }

    public static function send_limit_alert($module, $limit = null, $limit_id = null, $amount)
    {
        if(!$limit && !$limit_id)
            return false;

        if(!$limit && $limit_id)
            $limit = Limit::find($limit_id);

        if(!$limit)
            return false;

        $alerts = $limit->alerts;
        if($alerts->isEmpty())
            return true;

        $notifiable_id = null;
        $notifiable_type = $module;
        switch ($module) {
            case 'Store':
                $store = $limit->store;
                if(!$store)
                    return false;

                $notifiable_id = $store->stor_id;
                break;

            case 'Customer':
                $customer = $limit->customer;
                if(!$customer)
                    return false;

                $notifiable_id = $customer->cus_id;
                break;

            default:
                return false;
                break;
        }

        $notify = self::render_limit_alert($limit, $amount, $alerts);
        if(!$notify)
            return false;

        EmailQueue::create([
            'jobs' => 'LimitAlert',
            'type' => $notify['notify_type'],
            'notifiable_id' => $notifiable_id,
            'notifiable_type' => $notifiable_type,
            'data' => json_encode($notify),
            'send' => 0,
            'remarks' => null,
        ]);

    }

    public static function render_limit_alert($limit, $amount, $alerts)
    {
        foreach ($alerts as $alert) {

            switch ($alert->type) {
                case 1:
                    if($amount >= $alert->amount)
                    {
                        return [
                            'notify_type' => 'SingleLimitNotify',
                            'action_id' => $alert->id,
                            'block_type' => 'amount',
                            'limit_type' => 'single',
                            'current' => null,
                            'alert_when' => $alert->amount,
                        ];
                    }
                    break;

                case 2:
                    if(!empty($alert->amount) && $limit->daily >= $alert->amount)
                    {
                        return [
                            'notify_type' => 'DailyLimitNotify',
                            'action_id' => $alert->id,
                            'block_type' => 'amount',
                            'limit_type' => 'daily',
                            'current' => $limit->daily,
                            'alert_when' => $alert->amount,
                        ];
                    }

                    if(!empty($alert->number_transaction) && $limit->daily_count >= $alert->number_transaction)
                    {
                        return [
                            'notify_type' => 'DailyLimitNumberNotify',
                            'action_id' => $alert->id,
                            'block_type' => 'number',
                            'limit_type' => 'daily',
                            'current' => $limit->daily_count,
                            'alert_when' => $alert->number_transaction,
                        ];
                    }
                    break;

                case 3:
                    if(!empty($alert->amount) && $limit->weekly >= $alert->amount)
                    {
                        return [
                            'notify_type' => 'WeeklyLimitNotify',
                            'action_id' => $alert->id,
                            'block_type' => 'amount',
                            'limit_type' => 'weekly',
                            'current' => $limit->weekly,
                            'alert_when' => $alert->amount,
                        ];
                    }

                    if(!empty($alert->number_transaction) && $limit->weekly_count >= $alert->number_transaction)
                    {
                        return [
                            'notify_type' => 'WeeklyLimitNumberNotify',
                            'action_id' => $alert->id,
                            'block_type' => 'number',
                            'limit_type' => 'weekly',
                            'current' => $limit->weekly_count,
                            'alert_when' => $alert->number_transaction,
                        ];
                    }
                    break;

                case 4:
                    if(!empty($alert->amount) && $limit->monthly >= $alert->amount)
                    {
                        return [
                            'subject' => '',
                            'notify_type' => 'MonthlyLimitNotify',
                            'action_id' => $alert->id,
                            'block_type' => 'amount',
                            'limit_type' => 'monthly',
                            'current' => $limit->monthly,
                            'alert_when' => $alert->amount,
                        ];
                    }

                    if(!empty($alert->number_transaction) && $limit->monthly_count >= $alert->number_transaction)
                    {
                        return [
                            'notify_type' => 'MonthlyLimitNumberNotify',
                            'action_id' => $alert->id,
                            'block_type' => 'number',
                            'limit_type' => 'monthly',
                            'current' => $limit->monthly_count,
                            'alert_when' => $alert->number_transaction,
                        ];
                    }
                    break;

                case 5:
                    if(!empty($alert->amount) && $limit->yearly >= $alert->amount)
                    {
                        return [
                            'notify_type' => 'YearlyLimitNotify',
                            'action_id' => $alert->id,
                            'block_type' => 'amount',
                            'limit_type' => 'yearly',
                            'current' => $limit->yearly,
                            'alert_when' => $alert->amount,
                        ];
                    }

                    if(!empty($alert->number_transaction) && $limit->yearly_count >= $alert->number_transaction)
                    {
                        return [
                            'notify_type' => 'YearlyLimitNumberNotify',
                            'action_id' => $alert->id,
                            'block_type' => 'number',
                            'limit_type' => 'yearly',
                            'current' => $limit->yearly_count,
                            'alert_when' => $alert->number_transaction,
                        ];
                    }
                    break;
            }
        }

        return false;

    }

    public static function deduct_limit_transactions($orderType, $reference_id)
    {
        try {

            $amountValue = 0;
            $creditValue = 0;
            switch ($orderType) {
                case 'offline':
                    $order = OrderOffline::find($reference_id);
                    if(!$order)
                        return;

                    $orderDate = strtotime($order->created_at);
                    $store = $order->store? $order->store : null;
                    $customer = $order->customer? $order->customer : null;
                    $amountValue = $order->amount;
                    $creditValue = round($amountValue / $order->currency_rate, 4);
                    break;

                case 'online':
                    $order = Order::find($reference_id);
                    if(!$order)
                        return;

                    //store limit checking for online store is not enabled
                    $store = null;
                    $orderDate = strtotime($order->created_at);
                    $customer = $order->customer? $order->customer : null;
                    $amountValue = round($order->total_product_price + $order->total_product_shipping_fees, 2);
                    $creditValue = round($amountValue / $order->currency_rate, 4);
                    break;

                default:
                    return;
                    break;
            }

            $now = Carbon::now('UTC');
            $year = $now->year;
            $month = $now->month;
            $week = $now->weekOfYear;
            $day = $now->dayOfYear;

            $orderDate = Carbon::createFromTimestamp($orderDate);
            $oYear = $orderDate->year;
            $oMonth = $orderDate->month;
            $oWeek = $orderDate->weekOfYear;
            $oDay = $orderDate->dayOfYear;

            //only do deduction when transaction is within current year, else the transaction is reset yearly by cron jobs
            if($year == $oYear) {

                if($store && $orderType == 'offline') {
                    if(!$store->limit) {
                        $store = self::init_store_limit($store->stor_id);
                    }

                    $trans = $store->limit;
                    if($store->country) {
                        $currency_rate = $orderType == 'offline'? $store->country->co_offline_rate : $store->country->co_rate;
                        $amountValue = round($creditValue * $currency_rate, 2);
                    }

                    $trans->yearly = max($trans->yearly - $amountValue, 0.00);
                    $trans->yearly_count = max($trans->yearly_count - 1, 0);

                    if($month == $oMonth) {
                        $trans->monthly = max($trans->monthly - $amountValue, 0.00);
                        $trans->monthly_count = max($trans->monthly_count - 1, 0);
                    }

                    if($week == $oWeek) {
                        $trans->weekly = max($trans->weekly - $amountValue, 0.00);
                        $trans->weekly_count = max($trans->weekly_count - 1, 0);
                    }

                    if($day == $oDay) {
                        $trans->daily = max($trans->daily - $amountValue, 0.00);
                        $trans->daily_count = max($trans->daily_count - 1, 0);
                    }

                    $trans->save();
                }

                if($customer) {

                    if(!$customer->limit) {
                        $customer = self::init_store_limit($customer->cus_id);
                    }

                    $trans = $customer->limit;
                    if($customer->country) {
                        $currency_rate = $orderType == 'offline'? $customer->country->co_offline_rate : $customer->country->co_rate;
                        $amountValue = round($creditValue * $currency_rate, 2);
                    }

                    $trans->yearly = max($trans->yearly - $amountValue, 0.00);
                    $trans->yearly_count = max($trans->yearly_count - 1, 0);

                    if($month == $oMonth) {
                        $trans->monthly = max($trans->monthly - $amountValue, 0.00);
                        $trans->monthly_count = max($trans->monthly_count - 1, 0);
                    }

                    if($week == $oWeek) {
                        $trans->weekly = max($trans->weekly - $amountValue, 0.00);
                        $trans->weekly_count = max($trans->weekly_count - 1, 0);
                    }

                    if($day == $oDay) {
                        $trans->daily = max($trans->daily - $amountValue, 0.00);
                        $trans->daily_count = max($trans->daily_count - 1, 0);
                    }

                    $trans->save();
                }
            }

            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    public static function find_min_max_blocked($limit_id, $type, $min = true, $byAmount = false)
    {
        $blocked = LimitAction::where('limit_id', $limit_id);

        if($min) {
            $blocked->where('type', '<', $type)->orderBy('type', 'desc');
        } else {
            $blocked->where('type', '>', $type)->orderBy('type', 'asc');
        }

        if($byAmount) {
            $blocked->where('amount', '>', 0);
        } else {
            $blocked->where('number_transaction', '>', 0);
        }

        $blocked = $blocked->first();

        return $blocked;
    }

    public static function getProductLimitTypes()
    {
        return json_decode(json_encode([
            '0' => trans('localize.total_limit'),
            '1' => trans('localize.daily_limit'),
            '2' => trans('localize.weekly_limit'),
            '3' => trans('localize.monthly_limit'),
            // '4' => trans('localize.yearly_limit'),
        ]));
    }
}
