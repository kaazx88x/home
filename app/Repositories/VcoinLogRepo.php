<?php

namespace App\Repositories;
use DB;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\VcoinLog;
use App\Models\Shipping;
use App\Models\Inquiries;
use Carbon\Carbon;


class VcoinLogRepo
{
    public static function get_vcoin_log($id)
    {
        return $vcoin = VcoinLog::where('v_token_log.cus_id', '=', $id)
        ->Leftjoin('nm_order', 'nm_order.order_id', '=', 'v_token_log.order_id')
        ->Leftjoin('nm_product', 'nm_product.pro_id', '=', 'nm_order.order_pro_id')
        ->orderBy('v_token_log.created_at', 'DESC')
        ->paginate(50);
    }

    public static function get_customer_detail($id)
    {
        return $customer = Customer::where('nm_customer.cus_id', '=', $id)
        ->first();
    }

    public static function get_vcoin_log_with_input($id, $input)
    {
        $vcoin = VcoinLog::select('*', 'v_token_log.created_at')
        ->with('wallet')
        ->where('v_token_log.cus_id', '=', $id)
        ->Leftjoin('nm_order', 'nm_order.order_id', '=', 'v_token_log.order_id')
        ->Leftjoin('nm_product', 'nm_product.pro_id', '=', 'nm_order.order_pro_id');

        if (!empty($input['id'])) {
            $vcoin->where('v_token_log.order_id', '=', $input['id']);
        }

        if (!empty($input['ofid'])) {
            $vcoin->where('v_token_log.offline_order_id', '=', $input['ofid']);
        }

        if(!empty($input['remark'])) {
            $vcoin->where('v_token_log.remark','LIKE', '%'.$input['remark'].'%');
        }

        if(!empty($input['wallet'])) {
            $vcoin->where('v_token_log.wallet_id', $input['wallet']);
        }

        if (!empty($input['start']) && isset($input['end'])) {
            $input['start'] = Carbon::createFromFormat('d/m/Y', $input['start'])->startOfDay()->toDateTimeString();
            $input['end'] = Carbon::createFromFormat('d/m/Y', $input['end'])->endOfDay()->toDateTimeString();

            $vcoin->where('v_token_log.created_at', '>=', \Helper::TZtoUTC($input['start']));
            $vcoin->where('v_token_log.created_at', '<=', \Helper::TZtoUTC($input['end']));
        }

        if (!empty($input['status'])) {
            switch ($input['status']) {
                case '1':
                    $vcoin->whereNotNull('v_token_log.order_id');
                    break;
                case '2':
                    $vcoin->whereNotNull('v_token_log.offline_order_id');
                    break;
            }
        }

        if (!empty($input['sort'])) {
            switch ($input['sort']) {
                case 'new':
                    $vcoin->orderBy('v_token_log.created_at', 'desc');
                    break;
                case 'old':
                    $vcoin->orderBy('v_token_log.created_at', 'asc');
                    break;
                default:
                    $vcoin->orderBy('v_token_log.created_at', 'desc');
                    break;
            }
        } else {
            $vcoin->orderBy('v_token_log.created_at', 'desc');
        }

        if (!empty($input['action']) && $input['action'] == 'export')
            return $vcoin->get();

        return $vcoin->paginate(20);
    }
}
