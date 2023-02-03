<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;
use App\Repositories\OrderRepo;
use App\Repositories\StoreRepo;
use App\Repositories\TransactionRepo;
use App\Repositories\FundRepo;
use App\Repositories\MerchantRepo;
use App\Repositories\OrderOfflineRepo;
use App\Repositories\ProductRepo;
use App\Repositories\ProductPricingRepo;
use App\Repositories\CountryRepo;
use App\Repositories\GeneratedCodeRepo;
use App\Repositories\LimitRepo;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function __construct(Mailer $mailer) {
        $this->mailer = $mailer;
    }

    public function product_orders($operation)
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
        $export_permission = in_array('transactiononlineorderslistexport', $admin_permission);
        $accept_order_permission = in_array('transactiononlineacceptorder', $admin_permission);
        $update_shipping_info_permission = in_array('transactiononlineupdateshippinginfo', $admin_permission);
        $cancel_order_permission = in_array('transactiononlinecancelorder', $admin_permission);
        $order_refund_permission = in_array('transactiononlineorderrefund', $admin_permission);
        $redeem_coupons_permission = in_array('transactionredeemcoupons', $admin_permission);
        $cancel_coupons_permission = in_array('transactioncancelcoupons', $admin_permission);
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
        $accept_multiple_order_permission = in_array('transactiononlineacceptmultipleorder', $admin_permission);

		if(in_array('transactiononlineorderslist', $admin_permission) == false){
		return redirect('admin')->with('denied', trans('localize.You_are_not_authorized_to_access_that_page'));
		}

        $mer_id = 'all';

        $input = \Request::only('id', 'name', 'status', 'sort', 'tid', 'cid', 'pid', 'mid', 'start', 'end', 'merchant_countries', 'customer_countries', 'sid', 'oid');

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

            case 'coupons':
                $type = 3;
                break;

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

        $countries = CountryRepo::get_countries($admin_country_id_list);
        $orders = OrderRepo::get_orders_by_status($mer_id, $type, $input);
        // dd($orders->first()->toArray());
        $charges = OrderRepo::get_online_total_transaction($mer_id, $type, $input);

		return view('admin.transaction.product.order', compact('orders', 'status_list','input','charges', 'countries', 'type', 'type_list','cancel_coupons_permission','redeem_coupons_permission','order_refund_permission','cancel_order_permission','update_shipping_info_permission', 'accept_order_permission','export_permission','accept_multiple_order_permission'));

    }

    public function product_cod()
    {
        $input = (isset($data['search'])) ? $data['search'] : '';
        $status = (isset($_GET['s'])) ? $_GET['s'] : '';
        $status_list = array(
            ''  => trans('localize.all'),
            '1' => trans('localize.Success'),
            '2' => trans('localize.completed'),
            '3' => trans('localize.Hold'),
            '4' => trans('localize.Failed'),
        );

        $coddetails = TransactionRepo::get_product_orders_cod($status,$input);
        return view('admin.transaction.product.cod', compact('coddetails','status_list','status'));
    }

    public function deal_orders()
    {
        $mer_id = 'all';
        $status = (isset($_GET['s'])) ? $_GET['s'] : '';
        $status_list = array(
            ''  => trans('localize.all'),
            '1' => trans('localize.Success'),
            '2' => trans('localize.completed'),
            '3' => trans('localize.Hold'),
            '4' => trans('localize.Failed'),
        );

        $input = (isset($data['search'])) ? $data['search'] : '';
        $deals = OrderRepo::get_all_deal_orders_by_status($status, $mer_id, $input);

		return view('admin.transaction.deal.order', compact('deals','status_list','status'));
    }

    public function deal_cod()
    {
        $status = (isset($_GET['s'])) ? $_GET['s'] : '';
        $status_list = array(
            ''  => trans('localize.all'),
            '1' => trans('localize.Success'),
            '2' => trans('localize.completed'),
            '3' => trans('localize.Hold'),
            '4' => trans('localize.Failed'),
        );
        $input = (isset($data['search'])) ? $data['search'] : '';

        $coddetails = TransactionRepo::get_deal_orders_cod($status,$input);

		return view('admin.transaction.deal.cod', compact('coddetails','status_list','status'));
    }


    public function update_order_cod()
	{
        $orderid = $_GET['order_id'];
		$status = $_GET['status'];

		$updaters = TransactionRepo::update_cod_status($status, $orderid);
		if($updaters)
		{
			echo trans('localize.Success');
		}
	}

    public function fund_request()
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
        $export_permission = in_array('transactionfundrequestlistexport', $admin_permission);
        $fund_paid = in_array('transactionfundrequesfundpaid', $admin_permission);
        $fund_approval_permission = in_array('transactionfundrequesapproval', $admin_permission);

        $input = \Request::only('id', 'start', 'end', 'type', 'status', 'sort','countries','name','email');
        $status= $input['status'];
        $status_list = array(
            ''  => trans('localize.all'),
            '0' => trans('localize.pending'),
            '1' => trans('localize.approved'),
            '2' => trans('localize.declined'),
            '3' => trans('localize.paid'),
        );

		$input['admin_country_id_list'] = $admin_country_id_list;

        $funds = FundRepo::get_funds_by_status($input);
        $countries = CountryRepo::get_countries($admin_country_id_list);
        return view('admin.transaction.fund.manage', compact('fund_approval_permission','fund_paid','export_permission','funds','status_list','input','status','countries'));
    }

    public function update_fund_withdraw_status($wd_id, $status)
    {
        $fund = FundRepo::get_fund($wd_id);
        if ($fund->wd_status == 2)
            return back()->with('error', trans('localize.Fund_Withdraw_status_already_been_declined'));

         if ($fund->wd_status == 3)
            return back()->with('error', trans('localize.Fund_Withdraw_status_already_been_paid'));

        // Double check just in case
        $check_merchant_log = MerchantRepo::get_merchant_vtoken_log_by_withdraw_id($wd_id);
        if (!empty($check_merchant_log))
            return back()->with('error', trans('localize.Fund_already_withdrawed'));

        $update_fund = FundRepo::update_fund_status($wd_id, $status);

        if($update_fund)
        {
            if($status == 3) {
                $fund = FundRepo::get_fund($wd_id);
                $mer_id = $fund->wd_mer_id;

                $merchant = MerchantRepo::get_merchant($mer_id);

                $credit_debit = $fund->wd_submited_wd_amt + $fund->wd_admin_comm_amt;
                $current_merchant_credit = $merchant->mer_vtoken;

                $new_merchant_credit = $current_merchant_credit - $credit_debit;

                $remark = "Withdrawal";
                $log_credit = MerchantRepo::update_merchant_credit($mer_id, $wd_id, $credit_debit, $new_merchant_credit, $remark);

                $data = [
                    'bank_acc_name' => $merchant->bank_acc_name,
                    'bank_acc_no' => $merchant->bank_acc_no,
                    'bank_name' => $merchant->bank_name,
                    'receipt_no' => $fund->wd_id,
                    'payment_date' => date('d F Y'),
                    'withdraw_by' => $merchant->mer_fname . ' ' . $merchant->mer_lname,
                    'withdraw_date' => \Helper::UTCtoTZ($fund->created_at),
                    'approve_by' => 'MeiHome Administrator',
                    'credit' => $fund->wd_submited_wd_amt,
                    'currency' => $fund->wd_rate,
                    'currency_code' => $fund->wd_currency,
                    'amount' =>number_format($fund->wd_submited_wd_amt * $fund->wd_rate, 2)
                ];

                $this->mailer->send('front.emails.withdraw_request_slip', $data, function (Message $m) use ($merchant) {
                    $m->to('operation@meihome.asia', 'MeiHome Operation')->subject('MeiHome Official Payment Slip');
                });
            }
            return back()->with('success', trans('localize.Successfully_update_fund_withdraw'));
        }
        return back()->with('error', trans('localize.Failed_to_update_fund_withdraw'));
    }

    public function order_offline()
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
        $export_permission = in_array('transactionofflineorderslistexport', $admin_permission);
        $order_refund_permission = in_array('transactionofflineorderrefund', $admin_permission);
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);

		if(in_array('transactionofflineorderslist', $admin_permission) == false){
		return redirect('admin')->with('denied', trans('localize.You_are_not_authorized_to_access_that_page'));
		}

        $mer_id = 'all'; // For Admin | calling from the same repo as merchant site.
        $input = \Request::only('id', 'cid', 'mid', 'search', 'start', 'end', 'type', 'status', 'sort', 'merchant_countries', 'customer_countries', 'sid','tax_inv_no');
        $status= $input['status'];
        $status_list = array(
            ''  => trans('localize.all'),
            '0' => trans('localize.unpaid'),
            '1' => trans('localize.paid'),
            '2' => trans('localize.cancelbymember'),
            '3' => trans('localize.cancelbymerchant'),
            '4' => trans('localize.refunded'),
        );

        $countries = CountryRepo::get_countries($admin_country_id_list);
        $orders = OrderOfflineRepo::get_orders_offline($mer_id, $input);
        $total = OrderOfflineRepo::get_grand_total($mer_id, $input);



        return view('admin.transaction.order.offline', compact('orders', 'status', 'status_list', 'input', 'total', 'countries', 'order_refund_permission', 'export_permission'));
    }

    public function refund_online_order($order_id, $operation)
    {
        $order = OrderRepo::get_order_by_id($order_id);
        if(!$order || $order->order_type == 5)
            return back()->with('error', trans('localize.invalid_operation'));

        $order_status = $order->order_status;

        if($order_status == 4) {
            // old : order_vtokens - merchant_charge_token
            $merchant_amount = ROUND(($order->order_vtokens - $order->merchant_charge_vtoken), 4);

            if ($order->cus_platform_charge_value > 0 && $order->cus_service_charge_value > 0) {
                // new : ((product_original_price * order_qty) / currency_rate) - merchant_charge_token
                $merchant_amount = ROUND(((($order->product_original_price * $order->order_qty) / $order->currency_rate) - $order->merchant_charge_vtoken), 4);
            }

            if ($order->mer_vtoken < $merchant_amount)
                return redirect('admin/transaction/product/orders')->with('error', trans('localize.Error_while_refund_this_orders'));
        }

        $results = \DB::select("CALL refund_online_order($order_id)");

        if ($results) {
            // update pricing quantity
            ProductPricingRepo::refund_pricing_quantity($order_id, $operation, $order_status);

            // deduct limit transaction
            LimitRepo::deduct_limit_transactions('online', $order_id);

            //cancel coupon
            if(in_array($order->order_type, [3,4,5]))
                $cancel = GeneratedCodeRepo::cancel_code_by_order($order_id);

            return back()->with('success', trans('localize.Successfully') . ' ' . $operation . ' ' . trans('localize.order') . '!');
        } else {
            return back()->with('error', trans('localize.Error_while_update_orders'));
        }
    }

    public function refund_offline_order($order_id)
    {
        $order = OrderOfflineRepo::get_order_offline_by_id($order_id);

        $merchant_amount = ROUND(($order->v_token - $order->merchant_charge_token), 4);
        if ($order->mer_vtoken < $merchant_amount)
            return redirect('admin/transaction/offline')->with('error', trans('localize.Error_while_refund_this_orders'));

        $results = \DB::select("CALL refund_offline_order($order_id,'')");

        if ($results) {
            
            LimitRepo::deduct_limit_transactions('offline', $order_id);

            return redirect('admin/transaction/offline')->with('success', trans('localize.Successfully_refund_order'));
        } else {
            return redirect('admin/transaction/offline')->with('error', trans('localize.Error_while_refund_this_order'));
        }
    }

    public function update_batch_transaction($operation)
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

    public function cancel_code($type, $order_id, $serial_number)
    {
        switch ($type) {
            case 'coupon':

                $coupon = GeneratedCodeRepo::find_coupon($serial_number);
                if(!$coupon || $coupon->order_id != $order_id || $coupon->status == 3)
                    return back()->with('error', trans('localize.Invalid_coupon_code') . '!');

                $cancel = GeneratedCodeRepo::cancel_code($type, $serial_number);
                if($cancel)
                    return back()->with('success', trans('localize.Coupon_has_been_canceled') . '!');

                break;

            case 'ticket':

                $ticket = GeneratedCodeRepo::find_ticket($serial_number);
                if(!$ticket || $ticket->order_id != $order_id || $ticket->status == 3)
                    return back()->with('error', trans('localize.Invalid_coupon_code') . '!');

                $cancel = GeneratedCodeRepo::cancel_code($type, $serial_number);
                if($cancel)
                    return back()->with('success', trans('localize.Ticket_has_been_canceled') . '!');

                break;
        }

        return back()->with('error', 'Internal server error!');
    }

    public function completing_merchant_order($order_id)
    {
        $order = OrderRepo::get_order_by_id($order_id);
        if(!$order || $order->order_status != 3 || $order->product_shipping_fees_type != 3) {
            return 0;
        }

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            \Validator::make($data, [
                'remarks' => 'required',
            ])->validate();

            $update = OrderRepo::completing_merchant_order($order_id, $data['remarks']);
            if($update)
                return back()->with('success', trans('localize.recordupdated'));

            return back()->with('error', trans('localize.internal_server_error.title'));
        }

        return view('modals.product_received', compact('order'))->render();
    }
}
