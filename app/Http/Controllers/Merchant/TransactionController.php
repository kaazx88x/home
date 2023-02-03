<?php
namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Merchant\Controller;
use App\Repositories\OrderRepo;
use App\Repositories\OrderOfflineRepo;
use App\Repositories\StoreRepo;

class TransactionController extends Controller
{
    private $mer_id;
    private $route;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if(\Auth::guard('merchants')->check()) {
                $this->mer_id = \Auth::guard('merchants')->user()->mer_id;
                $this->route = 'merchant';
            }

            if(\Auth::guard('storeusers')->check()) {
                $this->mer_id = \Auth::guard('storeusers')->user()->mer_id;
                $this->route = 'store';
            }

            return $next($request);
        });

    }

    public function product_transaction($operation)
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $input = \Request::only('id', 'name', 'status', 'sort','start','end');
        $status = $input['status'];

        $status_list = [
            ''  => trans('localize.all'),
            '2' => trans('localize.pending'),
            '4' => trans('localize.completed'),
            '5' => trans('localize.cancelled'),
            '6' => trans('localize.refunded'),
        ];

        $type_list = [
            1 => ['name' => 'orders', 'lang' => trans('localize.Online_Orders')],
            3 => ['name' => 'coupons', 'lang' => trans('localize.coupon_orders')],
            4 => ['name' => 'tickets', 'lang' => trans('localize.ticket_orders')],
            5 => ['name' => 'ecards', 'lang' => trans('localize.e-card.orders')],
        ];

        switch ($operation) {
            case 'orders':
                $status_list = [
                    ''  => trans('localize.all'),
                    '1' => trans('localize.processing'),
                    '2' => trans('localize.packaging'),
                    '3' => trans('localize.shipped'),
                    '4' => trans('localize.completed'),
                    '5' => trans('localize.cancelled'),
                    '6' => trans('localize.refunded'),
                ];
                $type = 1;
                break;

            // case 'coupons':
            //     $type = 3;
            //     break;

            // case 'tickets':
            //     $type = 4;
            //     break;

            case 'ecards':
                $type = 5;
                break;

            default:
                return back()->with('error', 'Invalid Transaction');
                break;
        }

        $orders = OrderRepo::get_orders_by_status($mer_id, $type, $input);
        $charges = OrderRepo::get_online_total_transaction($mer_id, $type, $input);

        return view('merchant.transaction.product', compact('orders', 'status_list', 'input', 'mer_id','route', 'charges','status', 'type', 'type_list'));
    }

    public function order_offline()
    {
        $mer_id = $this->mer_id;
        $route = $this->route;

        $input = \Request::only('id', 'start', 'end', 'type', 'status', 'sort', 'store','tax_inv_no');
        $status = $input['status'];
        $status_list = array(
            ''  => trans('localize.all'),
            '0' => trans('localize.unpaid'),
            '1' => trans('localize.paid'),
            '2' => trans('localize.cancel_by_member'),
            '3' => trans('localize.cancel_by_merchant'),
        );

        $store = StoreRepo::get_offline_store_by_merchant_id($mer_id);
        $store_list = array('' => trans('localize.all_store'));

        foreach($store as $key => $stor){
            $store_list[$stor->stor_id] = $stor->stor_name;
        }


        $orders = OrderOfflineRepo::get_orders_offline($mer_id, $input);
        $total = OrderOfflineRepo::get_grand_total($mer_id, $input);

        return view('merchant.transaction.order_offline', compact('orders', 'status', 'status_list', 'input', 'mer_id', 'store_list', 'route', 'total'));
    }

    public function update_batch_transaction($mer_id, $operation)
    {
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();

            if(!isset($data['order_id']))
                return back()->withError('Nothing to be updated');

            OrderRepo::update_batch_status_transaction($operation, $data['order_id']);

            return back()->withSuccess(trans('localize.recordupdated'));
        }
    }

    //completing self pickup item only
    public function completing_merchant_order($order_id)
    {
        $mer_id = $this->mer_id;
        $order = OrderRepo::get_order_by_id($order_id);
        if(!$order || $order->mer_id != $mer_id || $order->order_status != 3 || $order->product_shipping_fees_type != 3) {
            return back()->with('error', trans('localize.invalid_request'));
        }

        $update = OrderRepo::completing_merchant_order($order_id);
        if($update)
            return back()->with('success', trans('localize.recordupdated'));

        return back()->with('error', trans('localize.internal_server_error.title'));
    }
}