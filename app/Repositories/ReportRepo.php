<?php
namespace App\Repositories;

use App\Models\CustomerVTokenLog;
use App\Models\MerchantVTokenLog;
use Carbon\Carbon;

class ReportRepo
{
    public static function credit_transfer($input)
    {
        $logs = CustomerVTokenLog::select('nm_customer.cus_name', 'v_token_log.*')
        ->leftJoin('nm_customer', 'nm_customer.cus_id', 'v_token_log.cus_id')
        ->whereNull('v_token_log.order_id')
        ->whereNull('v_token_log.offline_order_id');

        if (!empty($input['start']) && !empty($input['end'])) {
            $input['start'] = Carbon::createFromFormat('d/m/Y', $input['start'])->startOfDay()->toDateTimeString();
            $input['end'] = Carbon::createFromFormat('d/m/Y', $input['end'])->endOfDay()->toDateTimeString();

            $logs->where('v_token_log.created_at', '>=', \Helper::TZtoUTC($input['start']));
            $logs->where('v_token_log.created_at', '<=', \Helper::TZtoUTC($input['end']));
        }

        if (!empty($input['cid'])) {
            $logs->where('v_token_log.cus_id', $input['cid']);
        }

        if (!empty($input['cname'])) {
            $logs->where('nm_customer.cus_name', 'LIKE', '%'.$input['cname'].'%');
        }

        if(!empty($input['countries'])) {
            $logs->whereIn('nm_customer.cus_country', $input['countries']);
        }

        if (!empty($input['sortby'])) {
            switch ($input['sortby']) {
                case 'new':
                    $logs->orderBy('v_token_log.created_at', 'desc');
                    break;
                case 'old':
                    $logs->orderBy('v_token_log.created_at', 'asc');
                    break;
                case 'cusidAsc':
                    $logs->orderBy('v_token_log.cus_id', 'asc');
                    break;
                case 'cusidDesc':
                    $logs->orderBy('v_token_log.cus_id', 'desc');
                    break;
                case 'cusnameAsc':
                    $logs->orderBy('nm_customer.cus_name', 'asc');
                    break;
                case 'cusnameDesc':
                    $logs->orderBy('nm_customer.cus_name', 'desc');
                    break;
                case 'amountAsc':
                    $logs->orderBy('v_token_log.credit_amount', 'asc');
                    break;
                case 'amountDesc':
                    $logs->orderBy('v_token_log.credit_amount', 'desc');
                    break;
                default:
                    $logs->orderBy('v_token_log.created_at', 'desc');
                    break;
            }
        } else {
            $logs->orderBy('v_token_log.created_at', 'desc');
        }

        if (!empty($input['account'])) {
            $logs->where('v_token_log.svi_wallet', $input['account']);
        } else {
            $logs->whereIn('v_token_log.svi_wallet', [1,2]);
        }

        if(!empty($input['action']) && $input['action'] == 'export')
            return $logs->get();

        return $logs->paginate(50);
    }
	
	public static function sale_summary($input, $report_type){	
		$online_offline_flag = 0;
		$order_status = 0;
		$merchant_countries = 0;
		$story_countries = 0;
		$sorting_by = '';
		$orders = NULL;
		
		if(isset($input['transaction_type']) && $input['transaction_type'] == 0){ // online
			$online_offline_flag = 1;
			if(isset($input['merchant_online_countries']) && $input['merchant_online_countries'] <> NULL){
				$merchant_countries = $input['merchant_online_countries'];
			}else{
				$merchant_countries = isset($input['all_merchant_countries']) ? $input['all_merchant_countries'] : 0;
			}
			if(isset($input['store_online_countries']) && $input['store_online_countries'] <> NULL){
				$story_countries = $input['store_online_countries'];
			}else{
				$story_countries = isset($input['all_store_countries']) ? $input['all_store_countries']  : 0;
			}
			
		}elseif(isset($input['transaction_type']) && $input['transaction_type'] == 1){ // offline
			$online_offline_flag = 2;
			if(isset($input['merchant_offline_countries']) && $input['merchant_offline_countries'] <> NULL){
				$merchant_countries = $input['merchant_offline_countries'];
			}else{
				$merchant_countries = isset($input['all_merchant_countries']) ? $input['all_merchant_countries'] : 0;
			}
			if(isset($input['store_offline_countries']) && $input['store_offline_countries'] <> NULL){
				$story_countries = $input['store_offline_countries'];
			}else{
				$story_countries = isset($input['all_store_countries']) ? $input['all_store_countries']  : 0;
			}
		}
		
		$merchant_countries = (implode(",", $merchant_countries));
		$story_countries = (implode(",", $story_countries));
		
		if(isset($input['status']) && $input['status'] <> NULL ){
			$order_status = $input['status'];
		}
		
		if(isset($input['sortby']) && $input['sortby'] <> NULL){
			$sorting_by = $input['sortby'];
		}
		
		if($report_type == 'daily'){
			if($online_offline_flag > 0 && (isset($input['start_date']) && isset($input['end_date']))){
			
				$orders = \DB::select("CALL sp_get_daily_sale_report_by_date( 
							'". $online_offline_flag ."',
							'". $order_status ."', 
							'". date('Y-m-d', strtotime($input['start_date'])) ."',
							'". date('Y-m-d', strtotime($input['end_date'])) ."', 
							'". $merchant_countries ."', 
							'". $story_countries ."', 
							'". $sorting_by ."')");
			}
			
		}else if($report_type == 'monthly'){
			if($online_offline_flag > 0 && (isset($input['start_date_month']) && isset($input['end_date_month']))){
				$from_period = explode("-",$input['start_date_month']);
				$to_period = explode("-",$input['end_date_month']);
				
				$from_month = $from_period[0];
				$from_year = $from_period[1];
				$to_month = $to_period[0];
				$to_year = $to_period[1];
				
				$orders = \DB::select("CALL sp_get_monthly_sale_report_by_date( 
							'". $online_offline_flag ."',
							'". $from_month ."',
							'". $from_year ."', 
							'". $to_month ."',
							'". $to_year ."', 
							'". $order_status ."', 
							'". $merchant_countries ."', 
							'". $story_countries ."', 
							'". $sorting_by ."')");
			}
			
		}else if($report_type == 'yearly'){ 
			if($online_offline_flag > 0 && (isset($input['start_date_year']) && isset($input['end_date_year']))){
				$from_period = $input['start_date_year'];
				$to_period = $input['end_date_year'];
				
				$orders = \DB::select("CALL sp_get_yearly_sale_report_by_date( 
							'". $online_offline_flag ."',
							'". $from_period ."', 
							'". $to_period ."', 
							'". $order_status ."', 
							'". $merchant_countries ."', 
							'". $story_countries ."', 
							'". $sorting_by ."')");
			}
		}
		
		return $orders;
    }
	
	public static function credit_log($input){
		$logs = NULL;
		
		if(isset($input['user_type']) && $input['user_type'] == 0){ 
		//USER 
		
			$logs = CustomerVTokenLog::select(
				'v_token_log.created_at',
				'nm_country.co_name',
				'nm_customer.cus_id',
				'nm_customer.cus_name',
				'v_token_log.credit_amount',
				'v_token_log.debit_amount',
				'v_token_log.remark'
			)
				
				->leftJoin('nm_customer', 'nm_customer.cus_id', 'v_token_log.cus_id')
				->leftJoin('nm_country', 'nm_customer.cus_country', 'nm_country.co_id');
				
			if (isset($input['start']) && !empty($input['start']) && isset($input['end']) && !empty($input['end'])) {
				$input['start'] = Carbon::createFromFormat('d/m/Y', $input['start'])->startOfDay()->toDateTimeString();
				$input['end'] = Carbon::createFromFormat('d/m/Y', $input['end'])->endOfDay()->toDateTimeString();
			
				$logs->where('v_token_log.created_at', '>=', \Helper::TZtoUTC($input['start']));
				$logs->where('v_token_log.created_at', '<=', \Helper::TZtoUTC($input['end']));
			}			
			
			if (isset($input['userid']) && !empty($input['userid'])) {
				$logs->where('v_token_log.cus_id', $input['userid']);
			}
            
			if (isset($input['username']) && !empty($input['username'])) {
				$logs->where('nm_customer.cus_name', 'LIKE', '%'.$input['username'].'%');
			}
            
			if(isset($input['country']) && !empty($input['country'])) {
				$logs->whereIn('nm_customer.cus_country', $input['country']);
			}
            
			if (isset($input['sort_by']) && !empty($input['sort_by'])) {
				switch ($input['sort_by']) {
					case 'trans_date_asc':
						$logs->orderBy('v_token_log.created_at', 'asc');
						break;
					case 'trans_date_desc':
						$logs->orderBy('v_token_log.created_at', 'desc');
						break;
					case 'username_asc':
						$logs->orderBy('nm_customer.username', 'asc');
						break;
					case 'username_desc':
						$logs->orderBy('nm_customer.username', 'desc');
						break;
					case 'user_id_asc':
						$logs->orderBy('v_token_log.cus_id', 'asc');
						break;
					case 'user_id_desc':
						$logs->orderBy('v_token_log.cus_id', 'desc');
						break;
					case 'credit_asc':
						$logs->orderBy('v_token_log.credit_amount', 'asc');
						break;
					case 'credit_desc':
						$logs->orderBy('v_token_log.credit_amount', 'desc');
						break;
					case 'debit_asc':
						$logs->orderBy('v_token_log.debit_amount', 'asc');
						break;
					case 'debit_desc':
						$logs->orderBy('v_token_log.debit_amount', 'desc');
						break;	
					default:
						$logs->orderBy('v_token_log.created_at', 'desc');
						break;
				}
			} else {
				$logs->orderBy('v_token_log.created_at', 'desc');
			}
		
			if(isset($input['user_transaction_type']) && $input['user_transaction_type'] == 0){ 
			//1. Credit Top Up
				$logs->whereNull('v_token_log.order_id');
				$logs->whereNull('v_token_log.offline_order_id');
				$logs->whereNotNull('v_token_log.from');
				$logs->whereNotNull('v_token_log.svi_wallet');
				
			}else if(isset($input['user_transaction_type']) && $input['user_transaction_type'] == 1){
			//2. Online Transaction
				$logs->whereNotNull('v_token_log.order_id');
				$logs->whereNull('v_token_log.offline_order_id');
				$logs->where('v_token_log.order_id', '>', 0);
				
			}else if(isset($input['user_transaction_type']) && $input['user_transaction_type'] == 2){
			//3. Offline Transaction
				$logs->whereNotNull('v_token_log.offline_order_id');
				$logs->where('v_token_log.offline_order_id', '>', 0);
				$logs->whereNull('v_token_log.order_id');
			
			}else if(isset($input['user_transaction_type']) && $input['user_transaction_type'] == 3){
			//4. Transfer From Admin
				$logs->where(function($query){
					$query->where('v_token_log.offline_order_id', '=',0)->orWhere('v_token_log.offline_order_id','=', NULL);
				});
				$logs->whereNull('v_token_log.from');
				$logs->whereNull('v_token_log.svi_wallet');
				$logs->where(function($query){
					$query->where('v_token_log.order_id', '=',0)->orWhere('v_token_log.order_id','=', NULL);
				});
				
			}
			
		}else if(isset($input['user_type']) && $input['user_type'] == 1){ 
		//MERCHANT
			
			$logs = MerchantVTokenLog::select(
				'merchant_vtoken_log.created_at',
				'nm_country.co_name',
				'nm_merchant.mer_id',
				'nm_merchant.username',
				'merchant_vtoken_log.credit_amount',
				'merchant_vtoken_log.debit_amount',
				'merchant_vtoken_log.remark'
			)
			
				->leftJoin('nm_merchant', 'nm_merchant.mer_id', 'merchant_vtoken_log.mer_id')
				->leftJoin('nm_country', 'nm_merchant.mer_co_id', 'nm_country.co_id');
		
			if (isset($input['start']) && !empty($input['start']) && isset($input['end']) && !empty($input['end'])) {
				$input['start'] = Carbon::createFromFormat('d/m/Y', $input['start'])->startOfDay()->toDateTimeString();
				$input['end'] = Carbon::createFromFormat('d/m/Y', $input['end'])->endOfDay()->toDateTimeString();
			
				$logs->where('merchant_vtoken_log.created_at', '>=', \Helper::TZtoUTC($input['start']));
				$logs->where('merchant_vtoken_log.created_at', '<=', \Helper::TZtoUTC($input['end']));
			}			
			
			if (isset($input['userid']) && !empty($input['userid'])) {
				$logs->where('merchant_vtoken_log.mer_id', $input['userid']);
			}
            
			if (isset($input['username']) && !empty($input['username'])) {
				$logs->where('nm_merchant.username', 'LIKE', '%'.$input['username'].'%');
			}
            
			if(isset($input['country']) && !empty($input['country'])) {
				$logs->whereIn('nm_merchant.mer_co_id', $input['country']);
			}
            
			if (isset($input['sort_by']) && !empty($input['sort_by'])) {
				switch ($input['sort_by']) {
					case 'trans_date_asc':
						$logs->orderBy('merchant_vtoken_log.created_at', 'asc');
						break;
					case 'trans_date_desc':
						$logs->orderBy('merchant_vtoken_log.created_at', 'desc');
						break;
					case 'username_asc':
						$logs->orderBy('nm_merchant.username', 'asc');
						break;
					case 'username_desc':
						$logs->orderBy('nm_merchant.username', 'desc');
						break;
					case 'user_id_asc':
						$logs->orderBy('merchant_vtoken_log.mer_id', 'asc');
						break;
					case 'user_id_desc':
						$logs->orderBy('merchant_vtoken_log.mer_id', 'desc');
						break;
					case 'credit_asc':
						$logs->orderBy('merchant_vtoken_log.credit_amount', 'asc');
						break;
					case 'credit_desc':
						$logs->orderBy('merchant_vtoken_log.credit_amount', 'desc');
						break;
					case 'debit_asc':
						$logs->orderBy('merchant_vtoken_log.debit_amount', 'asc');
						break;
					case 'debit_desc':
						$logs->orderBy('merchant_vtoken_log.debit_amount', 'desc');
						break;	
					default:
						$logs->orderBy('merchant_vtoken_log.created_at', 'desc');
						break;
				}
			} else {
				$logs->orderBy('merchant_vtoken_log.created_at', 'desc');
			}
			
			if(isset($input['merchant_transaction_type']) && $input['merchant_transaction_type'] == 0){ 
			//1. Withdraw
				$logs->whereNull('merchant_vtoken_log.offline_order_id');
				$logs->whereNull('merchant_vtoken_log.order_id');
				$logs->whereNotNull('merchant_vtoken_log.withdraw_id');
				
			}else if(isset($input['merchant_transaction_type']) && $input['merchant_transaction_type'] == 1){
			//2. Online Transaction
				$logs->whereNotNull('merchant_vtoken_log.order_id');
				$logs->where('merchant_vtoken_log.order_id', '>', 0);
				$logs->whereNull('merchant_vtoken_log.offline_order_id');
				$logs->whereNull('merchant_vtoken_log.withdraw_id');
				
			}else if(isset($input['merchant_transaction_type']) && $input['merchant_transaction_type'] == 2){
			//3. Offline Transaction
				$logs->whereNotNull('merchant_vtoken_log.offline_order_id');
				$logs->where('merchant_vtoken_log.offline_order_id', '>', 0);
				$logs->whereNull('merchant_vtoken_log.order_id');
				$logs->whereNull('merchant_vtoken_log.withdraw_id');
			
			}else if(isset($input['merchant_transaction_type']) && $input['merchant_transaction_type'] == 3){
			//4. Transfer From Admin
				$logs->whereNull('merchant_vtoken_log.offline_order_id');
				$logs->whereNull('merchant_vtoken_log.withdraw_id');
				$logs->where(function($query){
					$query->where('merchant_vtoken_log.order_id', '=',0)->orWhere('merchant_vtoken_log.order_id','=', NULL);
				});
			}
		}
		//var_dump($logs->toSql());
		
		return $logs->get(); 
	}
	
	public static function credit_summary($input, $report_type){
		$transaction_type = '';
		$country_list = NULL;
		$data = NULL;
		$user_type_input = '';
		
		if(isset($input['country']) && $input['country'] <> NULL){
			$country_list = $input['country'];
		}else{
			$country_list = isset($input['all_countries']) ?  $input['all_countries'] : 0;
		}
		
		$country_list = (implode(",", $country_list));
		
		if(isset($input['user_type']) && $input['user_type'] == 0){ //User
			$user_type_input = 'user';
			if(isset($input['user_transaction_type'])){
				if($input['user_transaction_type'] == 0){
					$transaction_type = 1;
				}elseif($input['user_transaction_type'] == 1){
					$transaction_type = 2;
				}elseif($input['user_transaction_type'] == 2){
					$transaction_type = 3;
				}elseif($input['user_transaction_type'] == 3){
					$transaction_type = 4;
				}
			}
		}elseif(isset($input['user_type']) && $input['user_type'] == 1){ //Merchant
			$user_type_input = 'merchant';
			if(isset($input['merchant_transaction_type'])){
				if($input['merchant_transaction_type'] == 0){
					$transaction_type = 1;
				}elseif($input['merchant_transaction_type'] == 1){
					$transaction_type = 2;
				}elseif($input['merchant_transaction_type'] == 2){
					$transaction_type = 3;
				}elseif($input['merchant_transaction_type'] == 3){
					$transaction_type = 4;
				}
			}
		}
		
		if($report_type == 'daily'){
		//Daily	
			$data = \DB::select("CALL sp_get_daily_credit_report(
						'". $user_type_input ."',
						'". $transaction_type ."', 
						'". date('Y-m-d', strtotime($input['start_date'])) ."', 
						'". date('Y-m-d', strtotime($input['end_date'])) ."', 
						'". $country_list ."', 
						'". $input['sort_by']."')");
						
		}elseif($report_type == 'monthly'){
		//Monthly	
			$from_period = explode("-",$input['start_date_month']);
			$to_period = explode("-",$input['end_date_month']);
			
			$from_month = $from_period[0];
			$from_year = $from_period[1];
			$to_month = $to_period[0];
			$to_year = $to_period[1];
			
			$data = \DB::select("CALL sp_get_monthly_credit_report(
						'". $user_type_input ."',
						'". $transaction_type ."', 
						'". $from_month ."', 
						'". $from_year ."',
						'". $to_month ."',
						'". $to_year ."',
						'". $country_list ."', 
						'". $input['sort_by']."')");
						
		}elseif($report_type == 'yearly'){
		//Yearly
			$from_period = explode("-",$input['start_date_month']);
			$to_period = explode("-",$input['end_date_month']);
			
			$from_year = $from_period[1];
			$to_year = $to_period[1];
			
			$data = \DB::select("CALL sp_get_yearly_credit_report(
						'". $user_type_input ."',
						'". $transaction_type ."',
						'". $from_year ."',
						'". $to_year ."',
						'". $country_list ."', 
						'". $input['sort_by']."')");
			
		}
		
		return $data;
	}
}