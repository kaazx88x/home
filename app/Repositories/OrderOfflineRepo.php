<?php

namespace App\Repositories;
use App\Models\OrderOffline;
use App\Models\StoreUserMapping;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderOfflineRepo
{

    public static function get_orders_offline($mer_id, $input)
    {
        $orders = OrderOffline::select('order_offline.*', 'nm_customer.cus_id', 'nm_customer.cus_name', 'nm_merchant.mer_fname', 'nm_merchant.mer_id', 'nm_store.stor_name', 'wallets.name_en as wallet_name');

        if(\Auth::guard('storeusers')->check()) {
            $assigned_stores = StoreUserMapping::where('storeuser_id','=',\Auth::guard('storeusers')->user()->id)->pluck('store_id')->toArray();
            $orders->whereIn('store_id', $assigned_stores);
        }

        if($mer_id != 'all')
            $orders->where('order_offline.mer_id', '=', $mer_id);

        if (!empty($input['mid']))
            $orders->where('order_offline.mer_id', '=', $input['mid']);

        $orders->leftJoin('nm_customer', 'nm_customer.cus_id', '=', 'order_offline.cust_id');
        $orders->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'order_offline.mer_id');
        $orders->leftJoin('nm_store', 'nm_store.stor_id', '=','order_offline.store_id');
        $orders->leftJoin('wallets', 'wallets.id', '=','order_offline.wallet_id');
        $orders->groupBy('order_offline.id');

        if (!empty($input['sid']))
            $orders->where('nm_store.stor_id', '=', $input['sid']);


        if(!empty($input['merchant_countries'])) {
            $orders->where(function($query) use ($input){
                $query->whereIn('nm_merchant.mer_co_id', $input['merchant_countries']);
            });
        }

        if(!empty($input['customer_countries'])) {
            $orders->where(function($query) use ($input){
                $query->whereIn('nm_customer.cus_country', $input['customer_countries']);
            });
        }

        if (!empty($input['id'])) {
            $orders->where('order_offline.inv_no', 'LIKE', '%'.$input['id'].'%');
        }

        if (!empty($input['store']))
            $orders->where('order_offline.store_id', '=', $input['store']);

        if (!empty($input['tax_inv_no']))
            $orders->where('order_offline.tax_inv_no', 'LIKE', '%'.$input['tax_inv_no'].'%');

        if (!empty($input['cid']))
            $orders->where('nm_customer.cus_id', '=', $input['cid']);

        if (!empty($input['search'])) {
            $search = '%'.$input['search'].'%';
            $orders->where(function($q) use ($search) {
                $q->whereRaw('nm_merchant.mer_fname LIKE ? or nm_customer.cus_name LIKE ? or nm_store.stor_name LIKE ?', [$search, $search, $search]);
            });
        }

        if (!empty($input['start']) && !empty($input['end'])) {
            $range_type = ($input['type'] != '') ? $input['type'] : 'created_at';
            $input['start'] = Carbon::createFromFormat('d/m/Y', $input['start'])->startOfDay()->toDateTimeString();
            $input['end'] = Carbon::createFromFormat('d/m/Y', $input['end'])->endOfDay()->toDateTimeString();

            $orders->where('order_offline.'.$range_type, '>=', \Helper::TZtoUTC($input['start']));
            $orders->where('order_offline.'.$range_type, '<=', \Helper::TZtoUTC($input['end']));
        }

        if (!empty($input['status']) || strlen($input['status']))
            $orders->where('order_offline.status', '=', $input['status']);

        if (!empty($input['sort'])) {
            switch ($input['sort']) {
                case 'new':
                    $orders->orderBy('order_offline.id', 'desc');
                    break;
                case 'old':
                    $orders->orderBy('order_offline.id', 'asc');
                    break;
                default:
                    $orders->orderBy('order_offline.id', 'desc');
                    break;
            }
        } else {
            $orders->orderBy('order_offline.id', 'desc');
        }

        if (!empty($input['action']) && $input['action'] == 'export')
            return $orders->get();

        return $orders->paginate(50);
    }

    public static function get_order_offline_details($id)
    {
        $order = OrderOffline::select('order_offline.*', 'nm_customer.cus_id', 'nm_customer.cus_name', 'nm_customer.email', 'nm_customer.cus_phone', 'nm_merchant.mer_fname', 'nm_merchant.mer_lname')
        ->addSelect(DB::raw('concat_ws(" ", nm_merchant.mer_fname, nm_merchant.mer_lname) as merchant_name, nm_merchant.mer_address1, nm_merchant.mer_address2, nm_merchant.zipcode, nm_merchant.mer_city_name as mer_city, nm_state.name as mer_state, nm_country.co_name as mer_country, nm_merchant.bank_gst as mer_bank_gst, nm_merchant.mer_phone as mer_phone'))
        ->where('order_offline.id', '=', $id)
        ->leftJoin('nm_customer', 'nm_customer.cus_id', '=', 'order_offline.cust_id')
        ->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'order_offline.mer_id')
        ->leftJoin('nm_state', 'nm_state.id', '=', 'nm_merchant.mer_state')
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_merchant.mer_co_id');

        if (\Auth::guard('merchants')->check()) {
            $mer_id = \Auth::guard('merchants')->user()->mer_id;
            $order->where('order_offline.mer_id', '=', $mer_id);
        }

        return $order->first();
    }

    public static function get_grand_total($mer_id = null, $input = null)
    {
        $total =  OrderOffline::select(DB::raw("sum(order_offline.merchant_charge_token) as merchant_charge_token"));

        if ($mer_id == 'all') {
            $total->addSelect(DB::raw("sum(order_offline.merchant_platform_charge_token) as merchant_platform_charge_token, sum(order_offline.customer_charge_token) as customer_charge_token, sum(order_offline.order_total_token) as v_credit"));
        } else {
            $total->addSelect(DB::raw("sum(order_offline.v_token) as v_credit"));
            $total->where('order_offline.mer_id', '=', $mer_id);
        }

        if(\Auth::guard('storeusers')->check()) {
            $assigned_stores = StoreUserMapping::where('storeuser_id','=',\Auth::guard('storeusers')->user()->id)->pluck('store_id')->toArray();
            $total->whereIn('order_offline.store_id', $assigned_stores);
        }

        if (!empty($input['mid']))
            $total->where('order_offline.mer_id', '=', $input['mid']);

        $total->leftJoin('nm_customer', 'nm_customer.cus_id', '=', 'order_offline.cust_id');
        $total->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'order_offline.mer_id');
        $total->leftJoin('nm_store', 'nm_store.stor_id', '=','order_offline.store_id');

        if(isset($input['admin_country_id_list'])) {
            $total->whereIn('nm_store.stor_country', $input['admin_country_id_list']);
        }

        if(!empty($input['merchant_countries'])) {
            $total->where(function($query) use ($input){
                $query->whereIn('nm_merchant.mer_co_id', $input['merchant_countries']);
            });
        }

        if(!empty($input['customer_countries'])) {
            $total->where(function($query) use ($input){
                $query->whereIn('nm_customer.cus_country', $input['customer_countries']);
            });
        }

        if (!empty($input['id'])) {
            $total->where(function($query) use ($input) {
                $query->where('order_offline.inv_no', '=', $input['id'])
                ->orwhere('order_offline.id', '=', $input['id']);
            });
        }

        if (!empty($input['store']))
            $total->where('order_offline.store_id', '=', $input['store']);

        if (!empty($input['tax_inv_no']))
            $total->where('order_offline.tax_inv_no', 'LIKE', '%'.$input['tax_inv_no'].'%');

        if (!empty($input['cid']))
            $total->where('nm_customer.cus_id', '=', $input['cid']);

        if (!empty($input['search'])) {
            $search = '%'.$input['search'].'%';
            $total->where(function($q) use ($search) {
                $q->whereRaw('nm_merchant.mer_fname LIKE ? or nm_customer.cus_name LIKE ? or nm_store.stor_name LIKE ?', [$search, $search, $search]);
            });
        }

        if (!empty($input['start']) && !empty($input['end'])) {
            $range_type = ($input['type'] != '') ? $input['type'] : 'created_at';
            $input['start'] = Carbon::createFromFormat('d/m/Y', $input['start'])->startOfDay()->toDateTimeString();
            $input['end'] = Carbon::createFromFormat('d/m/Y', $input['end'])->endOfDay()->toDateTimeString();

            $total->where('order_offline.'.$range_type, '>=', \Helper::TZtoUTC($input['start']));
            $total->where('order_offline.'.$range_type, '<=', \Helper::TZtoUTC($input['end']));
        }

        if (!empty($input['status']) || strlen($input['status']))
            $total->where('order_offline.status', '=', $input['status']);

        return $total->first();
    }

    public static function get_order_offline_by_id($id)
    {
        return $order = OrderOffline::leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'order_offline.mer_id')
        ->where('order_offline.id', $id)
        ->first();
    }

    public static function generate_tax_inv()
    {

        $month_now = Carbon::now('UTC')->format('m');
        $year_now = Carbon::now('UTC')->format('y');

        $tax_inv_latest = OrderOffline::select('created_at','tax_inv_no')->orderBy('tax_inv_no','desc')->first();

        $num = 1;
        if(!empty($tax_inv_latest)){

            $latest_inv_year = date("y",strtotime($tax_inv_latest->created_at));
            $latest_inv_month = date("m",strtotime($tax_inv_latest->created_at));
            $latest_count_num = substr($tax_inv_latest->tax_inv_no,4,8);

            if($latest_inv_year != $year_now || $latest_inv_month != $month_now){
                $inv_tax_no = $year_now.$month_now.str_pad($num, 4, '0', STR_PAD_LEFT);
            }else{
                $count_num = $latest_count_num+1;
                $inv_tax_no= $year_now.$month_now.str_pad($count_num, 4, '0', STR_PAD_LEFT);
            }
        }else{
            $inv_tax_no = $year_now.$month_now.str_pad($num, 4, '0', STR_PAD_LEFT);
        }

        $check = self::check_inv($inv_tax_no);

        return $check;
    }

    public static function check_inv($inv_tax_no)
    {

        $order_tax_inv_exist = OrderOffline::where('tax_inv_no','=',$inv_tax_no)->exists();

        if($order_tax_inv_exist == true){

            self::generate_tax_inv();
        }
        else{
            return $inv_tax_no;
        }
    }
}