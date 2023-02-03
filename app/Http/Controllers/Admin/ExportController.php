<?php
namespace App\Http\Controllers\Admin;

use App\Repositories\VcoinLogRepo;
use App\Repositories\MerchantRepo;
use App\Repositories\OrderRepo;
use App\Repositories\OrderOfflineRepo;
use App\Repositories\FundRepo;
use App\Repositories\ReportRepo;
use App\Repositories\CountryRepo;
use File;
use Carbon\Carbon;
use Helper;

class ExportController extends Controller
{
    public function credit_customer()
    {
        $input = \Request::only('id', 'ofid', 'start', 'end', 'sort','status', 'remark', 'cus_id', 'action', 'export_as');

        $exportAs = 'csv';
        $exportAs = (!empty($input['export_as']) && in_array($input['export_as'], ['csv', 'xls', 'xlsx']))? $input['export_as'] : $exportAs;

        $logs = VcoinLogRepo::get_vcoin_log_with_input($input['cus_id'], $input);
        if($logs->isEmpty())
            return back()->with('error','No data to export');

        $header = ['#ID', 'Type', 'Debit', 'Credit', 'From', 'Wallet', 'Remarks', 'Date'];

        $contents = [];
        foreach ($logs->chunk(3000) as $file => $chunks) {

            $contents[$file][] = $header;

            foreach ($chunks as $log) {

                $contents[$file][] = [
                    'id' => (!empty($log->order_id)) ? $log->order_id : $log->offline_order_id,
                    'type' => (!empty($log->order_id)) ? 'Online Order' : 'Offline Order',
                    'debit' => ($log->debit_amount) ? number_format($log->debit_amount, 4) : '0.0000',
                    'credit' => ($log->credit_amount) ? number_format($log->credit_amount, 4) : '0.0000',
                    'from' => $log->from,
                    'svi_wallet' => $log->svi_wallet,
                    'remarks' => $log->remark,
                    'date' => Carbon::createFromTimestamp(strtotime($log->created_at))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A'),
                ];
            }
        }

        $download = Helper::export('Customer_MeiPoint_Log', $exportAs, $contents);
        return \Response::download($download)->deleteFileAfterSend(true);
    }

    public function gamepoint_customer()
    {
        $input = \Request::only('id', 'start', 'end', 'sort','status','remark', 'cus_id', 'action');

        $logs = VcoinLogRepo::gamepoint_log_with_input($input['cus_id'], $input);
        if($logs->isEmpty())
            return back()->with('error','No data to export');

        $header = ['#ID', 'Debit', 'Credit', 'Bidding ID', 'Remarks', 'Date'];
        $time = time();
        $csv_files = [];

        foreach ($logs->chunk(3000) as $key => $logs) {
            $contents = [];
            $writer = \League\Csv\Writer::createFromPath(new \SplFileObject(public_path().'/export/File_'.($key+1).'-Customer_GamePoint_Log_'.$time.'.csv', 'a+'), 'w');
            $writer->insertOne($header);
            foreach ($logs as $log) {
                $contents[] = [
                    'id' => $log->id,
                    'debit' => ($log->Debit_amount) ? $log->Debit_amount : '0.00',
                    'credit' => ($log->Credit_amount) ? $log->Credit_amount : '0.00',
                    'bidding' => ($log->bidding_id != 0) ? $log->bidding_id : '',
                    'remarks' => $log->remark,
                    'date' => Carbon::createFromTimestamp(strtotime($log->created_at))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A'),
                ];
            }
            $writer->insertAll($contents);
            $csv_files[$key] = 'File_'.($key+1).'-Customer_GamePoint_Log_'.$time;
        }
        $writer = null;

        //add files to zip
        $zip = new \ZipArchive();
        $zip_name =  public_path().'/export/Customer_GamePoint_Log_'.date('Ymd_His').".zip"; // Zip name

        $zip->open($zip_name,  \ZipArchive::CREATE);
        foreach ($csv_files as $key => $file) {
            $path = public_path().'/export/'.$file.'.csv';
            if(file_exists($path)){
                $zip->addFromString(basename($path), file_get_contents($path));
                unlink($path);
            }
        }
        $zip->close();

        $file= $zip_name;
        return \Response::download($file)->deleteFileAfterSend(true);
    }

    public function credit_merchant()
    {
        $input = \Request::only('id', 'start', 'end', 'sort', 'status', 'remark', 'mer_id', 'action', 'export_as');

        $exportAs = 'csv';
        $exportAs = (!empty($input['export_as']) && in_array($input['export_as'], ['csv', 'xls', 'xlsx']))? $input['export_as'] : $exportAs;

        $logs = MerchantRepo::get_merchant_vtoken_log($input['mer_id'], $input);
        if($logs->isEmpty())
            return back()->with('error','No data to export');

        $header = ['#ID', 'Type', 'Debit', 'Credit' ,'Remarks', 'Date'];

        $contents = [];
        foreach ($logs->chunk(3000) as $file => $chunks) {

            $contents[$file][] = $header;

            foreach ($chunks as $log) {
                $id = '';
                $type = '';

                if (!empty($log->order_id)) {
                    $id = $log->order_id;
                    $type = 'Online Order';
                } elseif (!empty($log->offline_order_id)) {
                    $id = $log->offline_order_id;
                    $type = 'Offline Order';
                } elseif (!empty($log->withdraw_id)) {
                    $id = $log->withdraw_id;
                    $type = 'Withdraw';
                }

                $contents[$file][] = [
                    'id' => $id,
                    'type' => $type,
                    'debit' => ($log->debit_amount) ? number_format($log->debit_amount, 4) : '0.0000',
                    'credit' => ($log->credit_amount) ? number_format($log->credit_amount, 4) : '0.0000',
                    'remarks' => $log->remark,
                    'date' => Carbon::createFromTimestamp(strtotime($log->created_at))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A'),
                ];
            }
        }

        $download = Helper::export('Merchant_MeiPoint_Log', $exportAs, $contents);
        return \Response::download($download)->deleteFileAfterSend(true);
    }

    public function product_orders($operation)
    {
		$adm_id = \Auth::guard('admins')->user()->adm_id;
		$admin_country_id_list = Controller::getAdminCountryIdList($adm_id);

        $mer_id = 'all';

        $input = \Request::only('id', 'name', 'status', 'sort', 'tid', 'cid', 'pid', 'mid', 'start', 'end', 'action', 'export_as', 'merchant_countries', 'customer_countries');

        $exportAs = 'csv';
        $exportAs = (!empty($input['export_as']) && in_array($input['export_as'], ['csv', 'xls', 'xlsx']))? $input['export_as'] : $exportAs;

        if (!\Auth::guard('admins')->check()) {
            return back()->with('error','Unauthorized');
        }

        switch ($operation) {
            case 'orders':
                $type = 1;
                break;

            case 'coupons':
                $type = 3;
                break;

            // case 'tickets':
            //     $type = 4;
			// 	break;

			case 'ecards':
                $type = 5;
                break;

            default:
                return back()->with('error', 'Invalid Transaction');
                break;
		}

		$input['admin_country_id_list'] = $admin_country_id_list;

        $product_orders = OrderRepo::get_orders_by_status($mer_id, $type, $input);
        $charges = OrderRepo::get_online_total_transaction($mer_id, $type, $input);

        if($product_orders->isEmpty())
        return back()->with('error','No data to export');

        $header = ['Order ID', 'Transaction ID', 'Customer', 'Merchant', 'Product', 'Option', 'Quantity', 'Amount', 'Order Credit Total', trans('localize.platform_charges'), trans('localize.gst'), 'Merchant Commission', 'Merchant Earning', 'Transaction Date', 'Status', 'Shipping Phone Number', 'Shipping Address'];

        $contents = [];
        foreach ($product_orders->chunk(3000) as $file => $chunks) {

            $contents[$file][] = $header;

            foreach ($chunks as $order) {

                switch ($order->order_status) {
                    case '1':
                        $status = 'Processing';
                        break;
                    case '2':
                        $status = 'Packaging';
                        if($order->order_type == '3');
                            $status = 'Pending';
                        break;
                    case '3':
                        $status = 'Shipped';
                        if($order->order_type == '3');
                            $status = 'Redeemed';
                        break;
                    case '4':
                        $status = 'Completed';
                        break;
                    case '5':
                        $status = 'Canceled';
						break;
					case '6':
                        $status = 'Refunded';
                        break;
                }

                $attribute_string = "";
                if($order->order_attributes != null)
                {
                    $attributes = (array)json_decode($order->order_attributes);
                    $last = count($attributes);
                    $index = 1;
                    foreach ($attributes as $attribute => $attribute_item) {
                        $attribute_string .= $attribute." : ".$attribute_item;
                        if($index < $last)
                            $attribute_string .= "\n";

                        $index++;
                    }
                }

                $content = [
                    'order_id' => $order->order_id,
                    'transaction_id' => $order->transaction_id,
                    'customer' => $order->cus_id.'-'.$order->cus_name,
                    'merchant' => $order->mer_id.'-'.$order->mer_fname,
                    'product' => $order->pro_id.'-'.$order->pro_title_en,
                    'option' => $attribute_string,
                    'quantity' => $order->order_qty,
                    'amount' => $order->currency.' '.round($order->order_vtokens * $order->currency_rate, 4),
                    'order_total' => $order->order_vtokens,
                    'platform_fee' => round($order->cus_platform_charge_rate) . '% - ' . round($order->cus_platform_charge_value, 4),
                    'service_fee' => round($order->cus_service_charge_rate) . '% - ' . round($order->cus_service_charge_value, 4),
                    'merchant_fee' => round($order->merchant_charge_percentage) . '% - ' . round($order->merchant_charge_vtoken, 4),
                    'balance' => number_format(($order->order_vtokens - $order->merchant_charge_vtoken - $order->cus_service_charge_value - $order->cus_platform_charge_value), 4),
                    'transaction_date' => Helper::UTCtoTZ($order->order_date),
                    'status' => $status,
                    'shipping_phone' => $order->ship_phone,
                    'shipping_address' => implode(', ', array_filter([$order->ship_address1, $order->ship_address2, $order->ship_postalcode,$order->ship_city_name, (!empty($order->ship_state_id) ? $order->name : $order->ci_name), $order->co_name]))
                ];

                $contents[$file][] = $content;
            }

            $contents[$file][] = [
                '', '', '', '', '', '', '', 'Subtotal',
                'order_total' => number_format($chunks->sum('order_vtokens'), 4),
                'platform_fee' => number_format($chunks->sum('cus_platform_charge_value'), 4),
                'service_fee' => number_format($chunks->sum('cus_service_charge_value'), 4),
                'merchant_fee' => number_format($chunks->sum('merchant_charge_vtoken'), 4),
                'balance' => number_format($chunks->sum('order_vtokens') - $chunks->sum('merchant_charge_vtoken') - $chunks->sum('cus_service_charge_value') - $chunks->sum('cus_platform_charge_value'), 4),
            ];

            $contents[$file][] = [
                '', '', '', '', '', '', '', 'Total',
                'order_total' => number_format($charges->total_credit, 4),
                'platform_fee' => number_format($charges->transaction_fees, 4),
                'service_fee' => number_format($charges->service_fees, 4),
                'merchant_fee' => number_format($charges->merchant_charge, 4),
                'balance' => number_format($charges->merchant_earned, 4),
            ];
        }

        $download = Helper::export('Product_Orders', $exportAs, $contents);
        return \Response::download($download)->deleteFileAfterSend(true);
    }

    public function order_offline()
    {
		$adm_id = \Auth::guard('admins')->user()->adm_id;
		$admin_country_id_list = Controller::getAdminCountryIdList($adm_id);

        $mer_id = 'all';

        $input = \Request::only('id', 'cid', 'mid', 'search', 'start', 'end', 'type', 'status', 'sort', 'action', 'export_as', 'merchant_countries', 'customer_countries');

        $exportAs = 'csv';
        $exportAs = (!empty($input['export_as']) && in_array($input['export_as'], ['csv', 'xls', 'xlsx']))? $input['export_as'] : $exportAs;

        if (!\Auth::guard('admins')->check()) {
            return back()->with('error','Unauthorized');
        }

		$input['admin_country_id_list'] = $admin_country_id_list;

        $orders = OrderOfflineRepo::get_orders_offline($mer_id, $input);
        $total = OrderOfflineRepo::get_grand_total($mer_id, $input);

        if($orders->isEmpty())
            return back()->with('error','No data to export');

        $header = ['#ID', 'Invoice No.', 'Customer', 'Merchant' ,'Amount', trans('localize.credit'), trans('localize.platform_charges'), trans('localize.gst'), trans('localize.merchant_charge'), 'Merchant Earning', 'Paid Date', 'Transaction Date', 'Status'];

        $contents = [];
        foreach ($orders->chunk(3000) as $file => $chunks) {

            $contents[$file][] = $header;

            foreach ($chunks as $order) {

                switch ($order->status) {
                    case '0':
                        $status = 'Unpaid';
                        break;
                    case '1':
                        $status = 'Paid';
                        break;
                    case '2':
                        $status = 'Cancel By Member';
                        break;
                    case '3':
                        $status = 'Cancel By Merchant';
                        break;
                    case '4':
                        $status = 'Refunded';
                        break;
                }

                $contents[$file][] = [
                    'id' => $order->id,
                    'inv_no' => $order->inv_no,
                    'customer' => (!empty($order->cus_id))?$order->cus_id.'-'.$order->cus_name : 'Customer not found',
                    'merchant' => (!empty($order->mer_id))? $order->mer_id.'-'.$order->mer_fname : 'Merchant not found',
                    'amount' => $order->currency.' '.$order->amount,
                    'order_total' => $order->order_total_token,
                    'platform_fee' => round($order->merchant_platform_charge_percentage) . '% - ' . $order->merchant_platform_charge_token,
                    'service_fee' => round($order->customer_charge_percentage) . '% - ' . $order->customer_charge_token,
                    'merchant_fee' => round($order->merchant_charge_percentage) . '% - ' . $order->merchant_charge_token,
                    'balance' => number_format(($order->v_token - $order->merchant_charge_token), 4),
                    'paid_date' => Carbon::createFromTimestamp(strtotime($order->paid_date))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A'),
                    'transaction_date' => Carbon::createFromTimestamp(strtotime($order->created_at))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A'),
                    'status' => $status,
                ];
            }

            $contents[$file][] = [
                '','','','','Subtotal',
                number_format($chunks->sum('order_total_token'), 4),
                number_format($chunks->sum('merchant_platform_charge_token'), 4),
                number_format($chunks->sum('customer_charge_token'), 4),
                number_format($chunks->sum('merchant_charge_token'), 4),
                number_format($chunks->sum('v_token') - $chunks->sum('merchant_charge_token'), 4),
            ];

            $contents[$file][] = [
                '','','','','Total',
                number_format($total->v_credit, 4),
                number_format($total->merchant_platform_charge_token, 4),
                number_format($total->customer_charge_token, 4),
                number_format($total->merchant_charge_token, 4),
                number_format($total->v_credit - $total->customer_charge_token - $total->merchant_platform_charge_token - $total->merchant_charge_token, 4)
            ];
        }

        $download = Helper::export('Product_Orders', $exportAs, $contents);
        return \Response::download($download)->deleteFileAfterSend(true);
    }

    public function fund_request()
    {
		$adm_id = \Auth::guard('admins')->user()->adm_id;
		$admin_country_id_list = Controller::getAdminCountryIdList($adm_id);

        $input = \Request::only('id', 'start', 'end', 'type', 'status', 'sort', 'action', 'export_as');
		$input['admin_country_id_list'] = $admin_country_id_list;

        $exportAs = 'csv';
        $exportAs = (!empty($input['export_as']) && in_array($input['export_as'], ['csv', 'xls', 'xlsx']))? $input['export_as'] : $exportAs;

        $funds = FundRepo::get_funds_by_status($input);
        if($funds->isEmpty())
            return back()->with('error','No data to export');

        $header = ['Merchant', 'Email', 'Mei Point Withdraw Request', 'Currency', 'Exchange Rate', 'Withdraw Amount', 'Request Date', 'Transaction Date' ,'Paid Date', 'Current Mei Point Balance', 'Status'];

        $contents = [];
        foreach ($funds->chunk(3000) as $file => $chunks) {

            $contents[$file][] = $header;

            foreach ($chunks as $fund) {

                switch ($fund->wd_status) {
                    case '0':
                        $status = 'Pending';
                        break;
                    case '1':
                        $status = 'Approved';
                        break;
                    case '2':
                        $status = 'Declined';
                        break;
                    case '3':
                        $status = 'Paid';
                        break;
                }

                $contents[$file][] = [
                    'merchant' => $fund->mer_id.'-'.$fund->mer_fname.' '.$fund->mer_lname,
                    'email' => $fund->email,
                    'withdraw' => $fund->wd_submited_wd_amt,
                    'currency' => $fund->wd_currency,
                    'rate' => $fund->wd_rate,
                    'amount' => number_format($fund->wd_rate * $fund->wd_submited_wd_amt , 4),
                    'request_date' => Carbon::createFromTimestamp(strtotime($fund->created_at))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A'),
                    'transaction_date' => Carbon::createFromTimestamp(strtotime($fund->updated_at))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A'),
                    'paid_date' => ($fund->wd_status == 3 )? Carbon::createFromTimestamp(strtotime($fund->wd_date))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A') : '' ,
                    'current_credit' => $fund->mer_vtoken,
                    'status' => $status,
                ];
            }
        }

        $download = Helper::export('Fund_Request', $exportAs, $contents);
        return \Response::download($download)->deleteFileAfterSend(true);
    }

    public function credit_transfer_report()
    {
        $input = \Request::only('cid', 'cname', 'start', 'end', 'account', 'sortby', 'countries', 'action', 'export_as');

        $exportAs = 'csv';
        $exportAs = (!empty($input['export_as']) && in_array($input['export_as'], ['csv', 'xls', 'xlsx']))? $input['export_as'] : $exportAs;

        $logs = collect([]);
        if ($input['start'] && $input['end']) {
            $logs = ReportRepo::credit_transfer($input);
        } else {
            return back()->with('error','Please select date start and date end');
        }

        if($logs->isEmpty())
            return back()->with('error','No data to export');

        $header = ['Date', 'Customer Detail', 'Amount', 'Account', 'Remarks'];

        $contents = [];
        foreach ($logs->chunk(3000) as $file => $chunks) {

            $contents[$file][] = $header;

            foreach ($chunks as $log) {

                $contents[$file][] = [
                    'date' => \Helper::UTCtoTZ($log->created_at),
                    'detail' => $log->cus_id . ' - ' . $log->cus_name,
                    'amount' => number_format($log->credit_amount, 4),
                    'account' => ($log->svi_wallet == 1)? 'Royal2u' : 'Early2u',
                    'remarks' => $log->from,
                ];
            }
        }

        $download = Helper::export('Credit_Transfer_Report', $exportAs, $contents);
        return \Response::download($download)->deleteFileAfterSend(true);
    }

	public function sale_by_transaction_date_report()
    {
		$adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
		$admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
        $export_permission = in_array('exportsalesreport',$admin_permission);

		$input = \Request::only('report_type','status','report_view', 'start_date', 'end_date','end_date_month','start_date_month','start_date_year','end_date_year','transaction_type','sortby','merchant_online_countries','store_online_countries','merchant_offline_countries','store_offline_countries','export_as');

		$exportAs = 'csv';
        $exportAs = (!empty($input['export_as']) && in_array($input['export_as'], ['csv', 'xls', 'xlsx']))? $input['export_as'] : $exportAs;

		$logs = collect([]);

		$all_merchant_online_countries = CountryRepo::get_countries($admin_country_id_list);
		$all_store_online_countries = CountryRepo::get_countries($admin_country_id_list);
		$all_merchant_offline_countries = CountryRepo::get_countries($admin_country_id_list);
		$all_store_offline_countries = CountryRepo::get_countries($admin_country_id_list);

		$all_merchant_online_countries_id[] = NULL;
		$all_store_online_countries_id[] = NULL;
		$all_merchant_offline_countries_id[] = NULL;
		$all_store_offline_countries_id[] = NULL;

		foreach ($all_merchant_online_countries as $all_country){
			$all_merchant_online_countries_id[] = $all_country->co_id;
		}
		$all_store_online_countries_id = $all_merchant_online_countries_id;

		foreach ($all_merchant_offline_countries as $all_country){
			$all_merchant_offline_countries_id[] = $all_country->co_id;
		}
		$all_store_offline_countries_id = $all_merchant_offline_countries_id;

		if(isset($input['transaction_type']) && $input['transaction_type'] == 0){
			$input['all_merchant_countries'] = $all_merchant_online_countries_id;
			$input['all_store_countries'] = $all_store_online_countries_id;
		}elseif(isset($input['transaction_type']) && $input['transaction_type'] == 1){
			$input['all_merchant_countries'] = $all_merchant_offline_countries_id;
			$input['all_store_countries'] = $all_store_offline_countries_id;
		}

		if(isset($input['report_type']) && $input['report_type'] == 0 ){
			if(isset($input['report_view']) && $input['report_view'] == 0){
			// Daily Report
				if(isset($input['start_date']) && isset($input['end_date'])){
					$logs = ReportRepo::sale_summary($input,'daily');
				}

			}elseif(isset($input['report_view']) && $input['report_view']== 1){
			// Monthly Report
				if(isset($input['start_date_month']) && isset($input['end_date_month'])){
					$logs = ReportRepo::sale_summary($input,'monthly');
				}

			}elseif(isset($input['report_view']) && $input['report_view']== 2){
			// Yearly Report
				if(isset($input['start_date_year']) && isset($input['end_date_year'])){
					$logs = ReportRepo::sale_summary($input,'yearly');
				}
			}

			if($logs == NULL){
				return back()->with('error','No data to export');
			}


			$total_order = 0;
			$total_product = 0;
			$total_sales = 0;
			$total_platform_charge = 0;
			$total_customer_charge = 0;
			$total_merchant_charge = 0;
			$total_merchant_commission = 0;
			$total_merchant_earning = 0;

			$pre_date = '';
			$pre_merchant_country = 0;
			$pre_merchant = 0;
			$pre_store = 0;
			$pre_year = 0;
			$pre_month = 0;

			if(isset($input['transaction_type']) && $input['transaction_type'] == 0){ //Online
				$header = ['Date', 'Country', 'Merchant', 'Stores', 'Total Order','Total Product','Total Sale (Mei Point)','Platform Charges (Mei Point)','Customer Charge (Mei Point)','Merchant Charge (Mei Point)','Merchant Commission (Mei Point)','Earning (Mei Point)'];
			}elseif(isset($input['transaction_type']) && $input['transaction_type'] == 1){  //Offline
				$header = ['Date', 'Country', 'Merchant', 'Stores', 'Total Order','Total Sale (Mei Point)','Platform Charges (Mei Point)','Customer Charge (Mei Point)','Merchant Charge (Mei Point)','Merchant Commission (Mei Point)','Earning (Mei Point)'];
			}

			$contents = [];
			$contents[0][0] = $header;
			$index_row = 0;

			foreach ($logs as $index => $data) {
				$group_level_write1 = 0;
				$group_level_write2 = 0;
				$group_level_write3 = 0;
				$group_level_write4 = 0;


				if(isset($input['report_view']) && $input['report_view'] == 0 ){
					if($pre_date <> $data->l1_transaction_date){
						$group_level_write1 = 1;
						$group_level_write2 = 1;
						$group_level_write3 = 1;
						$group_level_write4 = 1;
					}

				}else if(isset($input['report_view']) && $input['report_view'] == 1 ){
					if($pre_year <> $data->l1_year || $pre_month <> $data->l1_month){
						$group_level_write1 = 1;
						$group_level_write2 = 1;
						$group_level_write3 = 1;
						$group_level_write4 = 1;
					}

				}else if(isset($input['report_view']) && $input['report_view'] == 2 ){
					if($pre_year <> $data->l1_year ){
						$group_level_write1 = 1;
						$group_level_write2 = 1;
						$group_level_write3 = 1;
						$group_level_write4 = 1;
					}
				}

				$total_order = $total_order + $data->l4_total_order;
				$total_product = $total_product + $data->l4_total_product;
				$total_sales = $total_sales + round( $data->l4_sales_amount , 4);
				$total_platform_charge = $total_platform_charge + round( $data->l4_platform_charge , 4);
				$total_customer_charge = $total_customer_charge + round( $data->l4_customer_charge , 4);
				$total_merchant_charge = $total_merchant_charge + round( $data->l4_merchant_commission , 4);
				$total_merchant_commission = $total_merchant_commission + round( $data->l4_merchant_earning , 4);
				$total_merchant_earning = $total_merchant_earning + round( $data->l4_earning , 4);
				$date_display = '';

				if($pre_merchant_country <> $data->l2_merchant_country_id){
					$group_level_write2 = 1;
					$group_level_write3 = 1;
					$group_level_write4 = 1;
				}
				if($pre_merchant <> $data->l3_merchant_id){
					$group_level_write3 = 1;
					$group_level_write4 = 1;
				}
				if($pre_store <> $data->l4_store_id){
					$group_level_write4 = 1;
				}

				if(isset($input['report_view']) && $input['report_view'] == 0 ){
					$date_display = $data->l1_transaction_date;
				}else if(isset($input['report_view']) && $input['report_view'] == 1 ){
					$date_display = $data->l1_year .'-'. $data->l1_month;
				}else if(isset($input['report_view']) && $input['report_view'] == 2 ){
					$date_display = $data->l1_year;
				}

				if(isset($input['transaction_type']) && $input['transaction_type'] == 0){ //Online
					if($group_level_write1 == 1) {
						$index_row = $index_row + 1;

						$contents[0][$index_row] = [
							'Date' => $date_display ,
							'Country' => '' ,
							'Merchant' => '' ,
							'Stores' => '' ,
							'Total_Order' => $data->l1_total_order ,
							'Total_Product' => $data->l1_total_product,
							'Total_Sale' =>  $data->l1_sales_amount,
							'Platform_Charges' => $data->l1_platform_charge ,
							'Customer_Charge' => $data->l1_customer_charge ,
							'Merchant_Commission' => $data->l1_merchant_commission ,
							'Merchant_Earning' => $data->l1_merchant_earning,
							'Earning' => $data->l1_earning
						];
					}

					if($group_level_write2 == 1) {
						$index_row = $index_row + 1;

						$contents[0][$index_row] = [
							'Date' => '' ,
							'Country' => $data->l2_merchant_country ,
							'Merchant' => '' ,
							'Stores' => '' ,
							'Total_Order' => $data->l2_total_order ,
							'Total_Product' => $data->l2_total_product,
							'Total_Sale' =>  $data->l2_sales_amount,
							'Platform_Charges' => $data->l2_platform_charge ,
							'Customer_Charge' => $data->l2_customer_charge ,
							'Merchant_Commission' => $data->l2_merchant_commission ,
							'Merchant_Earning' => $data->l2_merchant_earning,
							'Earning' => $data->l2_earning
						];
					}

					if($group_level_write3 == 1) {
						$index_row = $index_row + 1;
						$contents[0][$index_row] = [
							'Date' => '' ,
							'Country' => '' ,
							'Merchant' => $data->l3_merchant_name1 .' '. $data->l3_merchant_name2  .'(id = '. $data->l3_merchant_id .')',
							'Stores' => '' ,
							'Total_Order' => $data->l3_total_order ,
							'Total_Product' => $data->l3_total_product,
							'Total_Sale' =>  $data->l3_sales_amount,
							'Platform_Charges' => $data->l3_platform_charge ,
							'Customer_Charge' => $data->l3_customer_charge ,
							'Merchant_Commission' => $data->l3_merchant_commission ,
							'Merchant_Earning' => $data->l3_merchant_earning,
							'Earning' => $data->l3_earning
						];
					}

					if($group_level_write4 == 1) {
						$index_row = $index_row + 1;
						$contents[0][$index_row] = [
							'Date' => '' ,
							'Country' => '' ,
							'Merchant' =>'' ,
							'Stores' => $data->l4_stor_name .' ('.$data->l4_store_country .') (id = '. $data->l4_store_id .')',
							'Total_Order' => $data->l4_total_order ,
							'Total_Product' => $data->l4_total_product,
							'Total_Sale' =>  $data->l4_sales_amount,
							'Platform_Charges' => $data->l4_platform_charge ,
							'Customer_Charge' => $data->l4_customer_charge ,
							'Merchant_Commission' => $data->l4_merchant_commission ,
							'Merchant_Earning' => $data->l4_merchant_earning,
							'Earning' => $data->l4_earning
						];
					}

				}else if(isset($input['transaction_type']) && $input['transaction_type'] == 1){ //Offline
					if($group_level_write1 == 1) {
						$index_row = $index_row + 1;

						$contents[0][$index_row] = [
							'Date' => $date_display ,
							'Country' => '' ,
							'Merchant' => '' ,
							'Stores' => '' ,
							'Total_Order' => $data->l1_total_order ,
							'Total_Sale' =>  $data->l1_sales_amount,
							'Platform_Charges' => $data->l1_platform_charge ,
							'Customer_Charge' => $data->l1_customer_charge ,
							'Merchant_Commission' => $data->l1_merchant_commission ,
							'Merchant_Earning' => $data->l1_merchant_earning,
							'Earning' => $data->l1_earning
						];
					}

					if($group_level_write2 == 1) {
						$index_row = $index_row + 1;

						$contents[0][$index_row] = [
							'Date' => '' ,
							'Country' => $data->l2_merchant_country ,
							'Merchant' => '' ,
							'Stores' => '' ,
							'Total_Order' => $data->l2_total_order ,
							'Total_Sale' =>  $data->l2_sales_amount,
							'Platform_Charges' => $data->l2_platform_charge ,
							'Customer_Charge' => $data->l2_customer_charge ,
							'Merchant_Commission' => $data->l2_merchant_commission ,
							'Merchant_Earning' => $data->l2_merchant_earning,
							'Earning' => $data->l2_earning
						];
					}

					if($group_level_write3 == 1) {
						$index_row = $index_row + 1;
						$contents[0][$index_row] = [
							'Date' => '' ,
							'Country' => '' ,
							'Merchant' => $data->l3_merchant_name1 .' '. $data->l3_merchant_name2 ,
							'Stores' => '' ,
							'Total_Order' => $data->l3_total_order ,
							'Total_Sale' =>  $data->l3_sales_amount,
							'Platform_Charges' => $data->l3_platform_charge ,
							'Customer_Charge' => $data->l3_customer_charge ,
							'Merchant_Commission' => $data->l3_merchant_commission ,
							'Merchant_Earning' => $data->l3_merchant_earning,
							'Earning' => $data->l3_earning
						];
					}

					if($group_level_write4 == 1) {
						$index_row = $index_row + 1;
						$contents[0][$index_row] = [
							'Date' => '' ,
							'Country' => '' ,
							'Merchant' =>'' ,
							'Stores' => $data->l4_stor_name .' ('.$data->l4_store_country .')',
							'Total_Order' => $data->l4_total_order ,
							'Total_Sale' =>  $data->l4_sales_amount,
							'Platform_Charges' => $data->l4_platform_charge ,
							'Customer_Charge' => $data->l4_customer_charge ,
							'Merchant_Commission' => $data->l4_merchant_commission ,
							'Merchant_Earning' => $data->l4_merchant_earning,
							'Earning' => $data->l4_earning
						];
					}
				}


				if(isset($input['report_view']) && $input['report_view'] == 0 ){
					$pre_date = $data->l1_transaction_date ;

				}else if(isset($input['report_view']) && $input['report_view'] == 1 ){
					$pre_year = $data->l1_year ;
					$pre_month = $data->l1_month ;

				}else if(isset($input['report_view']) && $input['report_view'] == 2 ){
					$pre_year = $data->l1_year ;
				}

				$pre_merchant_country = $data->l2_merchant_country_id ;
				$pre_merchant = $data->l3_merchant_id ;
				$pre_store = $data->l4_store_id ;
			}

			if(isset($input['transaction_type']) && $input['transaction_type'] == 0){ //Online
				$index_row = $index_row + 1;
				$contents[0][$index_row] = [
					'Date' => '' ,
					'Country' => '' ,
					'Merchant' =>'' ,
					'Stores' => 'Grand Total',
					'Total_Order' => $total_order ,
					'Total_Product' => $total_product,
					'Total_Sale' =>  $total_sales,
					'Platform_Charges' => $total_platform_charge ,
					'Customer_Charge' => $total_customer_charge ,
					'Merchant_Charge' => $total_merchant_charge,
					'Merchant_Commission' => $total_merchant_commission ,
					'Earning' => $total_merchant_earning,
				];

			}else if(isset($input['transaction_type']) && $input['transaction_type'] == 1){ //Offline
				$index_row = $index_row + 1;
				$contents[0][$index_row] = [
					'Date' => '' ,
					'Country' => '' ,
					'Merchant' =>'' ,
					'Stores' => 'Grand Total',
					'Total_Order' => $total_order ,
					'Total_Sale' =>  $total_sales,
					'Platform_Charges' => $total_platform_charge ,
					'Customer_Charge' => $total_customer_charge ,
					'Merchant_Charge' => $total_merchant_charge,
					'Merchant_Commission' => $total_merchant_commission ,
					'Earning' => $total_merchant_earning,
				];
			}

			$download = Helper::export('Sales_Report_By_Transaction', $exportAs, $contents);
			return \Response::download($download)->deleteFileAfterSend(true);

		}
	}

	public function credit_log_report(){
		$adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
		$admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
        $export_permission = in_array('exportmicreditlog',$admin_permission);

		$input = \Request::only('user_type','user_transaction_type','merchant_transaction_type','start','end','country','sort_by','userid', 'username','export_as');

		$exportAs = 'csv';
        $exportAs = (!empty($input['export_as']) && in_array($input['export_as'], ['csv', 'xls', 'xlsx']))? $input['export_as'] : $exportAs;

		$logs = collect([]);
		$contents = [];


		$country = CountryRepo::get_countries($admin_country_id_list);
		$country_list = NULL;

		foreach ($country as $all_country){
			$country_list[] = $all_country->co_id;
		}

		$input['all_countries'] = $country_list;
		$input['all_countries'] = array_merge($input['all_countries'], array(0));

		if(isset($input['user_type']) && $input['user_type'] <> NULL){
			if(isset($input['user_transaction_type']) && isset($input['user_transaction_type'])){
				if(isset($input['start']) && isset($input['end'])){
					$logs = ReportRepo::credit_log($input);
				}
			}

			if($logs == NULL){
				return back()->with('error','No data to export');
			}

			$header = ['Date', 'Country', 'ID', 'Username', 'Credit','Debit','Remarks'];

			$contents[0][0] = $header;
			$index_row = 0;

			$total_credit_amount = 0;
			$total_debit_amount = 0;

			foreach ($logs as $index => $data) {
				$index_row = $index_row + 1;
				$total_credit_amount = $total_credit_amount + $data->credit_amount;
				$total_debit_amount = $total_debit_amount + $data->debit_amount;

				if(isset($input['user_type']) && $input['user_type'] == 0){ //user

					$contents[0][$index_row] = [
						'Date' => date_format($data->created_at,"d/m/Y") ,
						'Country' => ($data->co_name <> '') ? $data->co_name : "No Country" ,
						'Id' => $data->cus_id ,
						'Username' => $data->cus_name ,
						'Credit' => $data->credit_amount ,
						'Debit' => $data->debit_amount,
						'Remarks' =>  $data->remark
					];
				}elseif(isset($input['user_type']) && $input['user_type'] == 1){ //merchant

					$contents[0][$index_row] = [
						'Date' => date_format($data->created_at,"d/m/Y") ,
						'Country' => ($data->co_name <> '') ? $data->co_name : "No Country" ,
						'Id' => $data->mer_id ,
						'Username' => $data->username ,
						'Credit' => $data->credit_amount ,
						'Debit' => $data->debit_amount,
						'Remarks' =>  $data->remark
					];
				}
			}
				$index_row = $index_row + 1;

				$contents[0][$index_row] = [
						'Date' => '',
						'Country' => '' ,
						'Id' => '' ,
						'Username' => 'Total' ,
						'Credit' => $total_credit_amount ,
						'Debit' => $total_debit_amount,
						'Remarks' => ''
					];

				$download = Helper::export('Credit_Log_Report', $exportAs, $contents);
				return \Response::download($download)->deleteFileAfterSend(true);

		}
	}

	public function credit_summary_report(){
		$adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
		$admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
		$export_permission = in_array('exportmicreditsummary',$admin_permission);

		$input = \Request::only('user_type','report_view','start_date','end_date','start_date_month','end_date_month','start_date_year','end_date_year','country','sort_by','user_transaction_type','merchant_transaction_type','export_as');

		$exportAs = 'csv';
        $exportAs = (!empty($input['export_as']) && in_array($input['export_as'], ['csv', 'xls', 'xlsx']))? $input['export_as'] : $exportAs;

		$logs = collect([]);
		$contents = [];


		$country = CountryRepo::get_countries($admin_country_id_list);
		$country_list = NULL;

		foreach ($country as $all_country){
			$country_list[] = $all_country->co_id;
		}

		$input['all_countries'] = $country_list;
		$input['all_countries'] = array_merge($input['all_countries'], array(0));

		if(isset($input['user_type']) && $input['user_type'] <> NULL){
			if((isset($input['user_transaction_type']) && $input['user_transaction_type'] <> NULL) &&
				(isset($input['merchant_transaction_type']) && $input['merchant_transaction_type'] <> NULL)){
					if(isset($input['report_view']) && $input['report_view'] == 0 ){
					//Daily
						if(isset($input['start_date']) && $input['start_date'] <> NULL && isset($input['end_date']) && $input['end_date']){
							$logs = ReportRepo::credit_summary($input,'daily');
						}
					}elseif(isset($input['report_view']) && $input['report_view'] == 1 ){
					//Monthly
						if(isset($input['start_date_month']) && $input['start_date_month'] <> NULL && isset($input['end_date_month']) && $input['end_date_month']){
							$logs = ReportRepo::credit_summary($input,'monthly');
						}

					}elseif(isset($input['report_view']) && $input['report_view'] == 2 ){
					//Yearly
						if(isset($input['start_date_year']) && $input['start_date_year'] <> NULL && isset($input['end_date_year']) && $input['end_date_year']){
							$logs = ReportRepo::credit_summary($input,'yearly');
						}
					}
			}

			if($logs == NULL){
				return back()->with('error','No data to export');
			}

			$header = [ 'Date', 'Country', 'Credit', 'Debit'];

			$contents[0][0] = $header;
			$index_row = 0;
			$total_credit_amount = 0;
			$total_debit_amount = 0;
			$previous_date = '';
			$previous_country = '';
			$previous_year = '';
			$previous_month = '';

			foreach ($logs as $key => $log){
				$group_level_write1 = 0;
				$group_level_write2 = 0;

				$total_credit_amount = $total_credit_amount + $log->l2_credit_amount;
				$total_debit_amount = $total_debit_amount + $log->l2_debit_amount;

				if(isset($input['report_view']) && $input['report_view'] == 0 ){
					if($previous_date <> $log->l1_date){
						$group_level_write1 = 1;
						$group_level_write2 = 1;
					}

				}else if(isset($input['report_view']) && $input['report_view'] == 1 ){
					if($previous_year <> $log->l1_year || $previous_month <> $log->l1_month){
						$group_level_write1 = 1;
						$group_level_write2 = 1;
					}

				}else if(isset($input['report_view']) && $input['report_view'] == 2 ){
					if($previous_year <> $log->l1_year ){
						$group_level_write1 = 1;
						$group_level_write2 = 1;
					}
				}


				if($previous_country <> $log->l2_country_id){
					$group_level_write2 = 1;
				}

				if($group_level_write1 == 1){
					$index_row = $index_row + 1;

					if(isset($input['report_view']) && $input['report_view'] == 0 ){
						$contents[0][$index_row] = [
							'Date' => $log->l1_date ,
							'Country' => '',
							'Credit' => $log->l1_credit_amount ,
							'Debit' => $log->l1_debit_amount
						];
					}else if(isset($input['report_view']) && $input['report_view'] == 1 ){
						$contents[0][$index_row] = [
							'Date' => $log->l1_year ."-". $log->l1_month ,
							'Country' => '',
							'Credit' => $log->l1_credit_amount ,
							'Debit' => $log->l1_debit_amount
						];
					}else if(isset($input['report_view']) && $input['report_view'] == 2 ){
						$contents[0][$index_row] = [
							'Date' => $log->l1_year ,
							'Country' => '',
							'Credit' => $log->l1_credit_amount ,
							'Debit' => $log->l1_debit_amount
						];
					}
				}

				if($group_level_write2 == 1){
					$index_row = $index_row + 1;
					$contents[0][$index_row] = [
						'Date' => '',
						'Country' => ($log->l2_country_name <> '' ? $log->l2_country_name : 'No Country'),
						'Credit' => $log->l2_credit_amount ,
						'Debit' => $log->l2_debit_amount
					];
				}

				if(isset($input['report_view']) && $input['report_view'] == 0 ){
					$previous_date = $log->l1_date;
					$previous_country = $log->l2_country_id;
				}else if(isset($input['report_view']) && $input['report_view'] == 1 ){
					$previous_year = $log->l1_year;
					$previous_month = $log->l1_month;
					$previous_country = $log->l2_country_id;
				}else if(isset($input['report_view']) && $input['report_view'] == 2 ){
					$previous_year = $log->l1_year;
					$previous_country = $log->l2_country_id;
				}

			}
			$index_row = $index_row + 1;
			$contents[0][$index_row] = [
						'Date' => '',
						'Country' => "Total",
						'Credit' => $total_credit_amount ,
						'Debit' => $total_debit_amount
					];


			$download = Helper::export('Credit_Summary_Report', $exportAs, $contents);
			return \Response::download($download)->deleteFileAfterSend(true);

		}
	}

	public function merchant_listing($type = null)
	{
		$adm_id = \Auth::guard('admins')->user()->adm_id;
		$admin_permission = Controller::adminPermissionList($adm_id);
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);

		$input = \Request::only('id', 'name', 'email', 'status', 'sort', 'country', 'export_as', 'action');

		$exportAs = 'csv';
		$exportAs = (!empty($input['export_as']) && in_array($input['export_as'], ['csv', 'xls', 'xlsx']))? $input['export_as'] : $exportAs;

        $input['country_id_permission'] = $admin_country_id_list;
        $merchants = MerchantRepo::get_merchant_details($type, $input);

		if($merchants->isEmpty())
            return back()->with('error','No data to export');

        $header = ['#ID', 'Merchant Type', 'Status', 'First Name', 'Last Name', 'Username', 'Email', 'Phone', 'Office Number', 'Address', 'Country', 'Commission (%)', 'MEI Admin Fees Rate (%)', 'MeiHome GST Rate (%)', trans('localize.credit'), 'Account Holder Name', 'Account Number', 'Name Of Bank', 'Address Of Bank', 'Bank Country', 'Bank Swift Code / BIC / ABA', 'If Europe Bank / IBAN', 'GST Registration No.', 'Referrer Name', 'Referrer Mei User ID', 'Referrer Nationality', 'Referrer Mobile No.', 'Referrer Email', 'Referrer Bank Name', 'Referrer Account Name', 'Referrer Bank Account', 'Guarantor Name', 'Guarantor Mei User ID', 'Guarantor Nationality', 'Guarantor Mobile No.', 'Guarantor Email', 'Guarantor Bank Name', 'Guarantor Account Name', 'Guarantor Bank Account', 'Register Date', 'Updated By'];

        $contents = [];
        foreach ($merchants->chunk(3000) as $file => $chunks) {

            $contents[$file][] = $header;

            foreach ($chunks as $merchant) {

                switch ($merchant->mer_staus) {
                    case '1':
                        $status = 'Merchant In Use';
                        break;
                    case '2':
                        $status = 'Pending Approval';
                        break;
					default:
						$status = 'Merchant Blocked';
						break;
				}

				switch ($merchant->mer_type) {
                    case '0':
                        $type = 'Online';
						break;

					default:
						$type = 'Offline';
						break;
				}

				$updater = '';
				if($merchant->updater_id)
				{
					if($merchant->mer_staus == 1)
					{
						$updater .= 'Activated By : ';
					} else {
						$updater .= 'Blocked By : ';
					}

					$updater .= $merchant->updater_name . ' at '. \Helper::UTCtoTZ($merchant->updated_at);
				}

                $contents[$file][] = [
                    $merchant->mer_id,
                    $type,
                    $status,
                    $merchant->mer_fname,
                    $merchant->mer_lname,
                    $merchant->username,
                    $merchant->email,
                    $merchant->mer_phone,
					$merchant->mer_office_number,
					ucwords(implode(', ', array_map('trim', array_filter([$merchant->mer_address1, $merchant->mer_address2, $merchant->mer_city_name, $merchant->zipcode, $merchant->state? $merchant->state->name : '', $merchant->country? $merchant->country->co_name : ''])))),
					$merchant->country? $merchant->country->co_name : '',
					number_format($merchant->mer_commission, 2),
					number_format($merchant->mer_platform_charge, 2),
					number_format($merchant->mer_service_charge, 2),
					number_format($merchant->mer_vtoken? $merchant->mer_vtoken : 0, 4),
					$merchant->bank_acc_name,
					$merchant->bank_acc_no,
					$merchant->bank_name,
					$merchant->bank_address,
					$merchant->country_bank? $merchant->country_bank->co_name : '',
					$merchant->bank_swift,
					$merchant->bank_europe,
					$merchant->bank_gst,
					$merchant->referrer ? $merchant->referrer->name : '',
					$merchant->referrer ? $merchant->referrer->username : '',
					$merchant->referrer ? $merchant->referrer->nationality : '',
					$merchant->referrer ? $merchant->referrer->phone : '',
					$merchant->referrer ? $merchant->referrer->email : '',
					$merchant->referrer ? $merchant->referrer->bank_name : '',
					$merchant->referrer ? $merchant->referrer->bank_acc_name : '',
					$merchant->referrer ? $merchant->referrer->bank_acc_no : '',
					$merchant->guarantor? $merchant->guarantor->name : '',
					$merchant->guarantor? $merchant->guarantor->username : '',
					$merchant->guarantor? $merchant->guarantor->nationality : '',
					$merchant->guarantor? $merchant->guarantor->phone : '',
					$merchant->guarantor? $merchant->guarantor->email : '',
					$merchant->guarantor? $merchant->guarantor->bank_name : '',
					$merchant->guarantor? $merchant->guarantor->bank_acc_name : '',
					$merchant->guarantor? $merchant->guarantor->bank_acc_no : '',
					\Helper::UTCtoTZ($merchant->created_at),
					$updater,
                ];
            }
        }

        $download = Helper::export('Merchant_Listing', $exportAs, $contents);
        return \Response::download($download)->deleteFileAfterSend(true);
	}
}