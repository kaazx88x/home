<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;
use App\Repositories\ReportRepo;
use App\Repositories\CountryRepo;

class ReportController extends Controller
{
    public function credit_transfer()
    {
		$adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
		$admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
        $export_permission = in_array('exportcredittransfer',$admin_permission);
		
        $data = \Request::only('cid', 'cname', 'start', 'end', 'account', 'sortby', 'countries');

        $logs=[];

        if ($data['start'] && $data['end']) {
            $logs = ReportRepo::credit_transfer($data);
        }

        $countries = CountryRepo::get_countries($admin_country_id_list);
        return view('admin.report.credit_transfer', compact('logs', 'data', 'countries','export_permission'));
    }
	
	public function sales(){
		
		$adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
		$admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
        $export_permission = in_array('exportsalesreport',$admin_permission);
		
		
		$input = \Request::only('report_type','status','report_view', 'start_date', 'end_date','end_date_month','start_date_month','start_date_year','end_date_year','transaction_type','sortby','merchant_online_countries','store_online_countries','merchant_offline_countries','store_offline_countries');
		
		$orders = NULL;
		
		$status_list = array(
            '1'  => trans('localize.success_order'),
            '2' => trans('localize.cancelled'),
            '3' => trans('localize.refunded')
        );
		
		$report_type =  array('By Transaction Date');
		$report_view = array('Daily', 'Monthly', 'Yearly');
		$transaction_type = array('Online', 'Offline');
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
		
		if(isset($input['report_type']) && $input['report_type'] <> NULL){
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
						$orders = ReportRepo::sale_summary($input,'daily'); 
					}
					
				}elseif(isset($input['report_view']) && $input['report_view']== 1){  
				// Monthly Report
					if(isset($input['start_date_month']) && isset($input['end_date_month'])){
						$orders = ReportRepo::sale_summary($input,'monthly'); 
					}
					
				}elseif(isset($input['report_view']) && $input['report_view']== 2){  
				// Yearly Report
					if(isset($input['start_date_year']) && isset($input['end_date_year'])){
						$orders = ReportRepo::sale_summary($input,'yearly'); 
					}
				}
			}
		}
		
		return view('admin.report.sales', compact('export_permission','orders','input','status_list','report_type', 'report_view','transaction_type','all_merchant_online_countries','all_store_online_countries','all_merchant_offline_countries','all_store_offline_countries'));
	}
	
	public function credit_log(){
		$adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
		$admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
        $export_permission = in_array('exportmicreditlog',$admin_permission);
		
		$logs=[];
		
		$input = \Request::only('user_type','user_transaction_type','merchant_transaction_type','start','end','country','sort_by','userid', 'username');
		$user_type = array('User', 'Merchant');
		$user_transaction_type = array('Credit Top Up', 'Online Transaction', 'Offline Transaction','Transfer From Admin');
		$merchant_transaction_type = array('Withdraw', 'Online Transaction', 'Offline Transaction','Transfer From Admin');
		
		$country = CountryRepo::get_countries($admin_country_id_list); 
		$country_list = NULL;
		
		foreach ($country as $all_country){
			$country_list[] = $all_country->co_id;
		}
		
		$input['all_merchant_countries'] = $country_list;
		$input['all_merchant_countries'] = array_merge($input['all_merchant_countries'], array(0));
		
		
		if(isset($input['user_type']) && $input['user_type'] <> NULL){
			if(isset($input['user_transaction_type']) && isset($input['user_transaction_type'])){
				if(isset($input['start']) && isset($input['end'])){
					$logs = ReportRepo::credit_log($input); 
				}
			}
		}
		
		return view('admin.report.credit_log', compact('export_permission','logs','input', 'user_type', 'user_transaction_type','merchant_transaction_type', 'country','sort_by','userid','username','start','end'));
	}
	
	public function credit_summary(){
		$adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
		$admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
		$export_permission = in_array('exportmicreditsummary',$admin_permission);
		
		$logs=[];
		
		$input = \Request::only('user_type','report_view','start_date','end_date','start_date_month','end_date_month','start_date_year','end_date_year','country','sort_by','user_transaction_type','merchant_transaction_type');
		$user_type = array('User', 'Merchant');
		$report_view = array('Daily', 'Monthly', 'Yearly');
		$user_transaction_type = array('Credit Top Up', 'Online Transaction', 'Offline Transaction','Transfer From Admin');
		$merchant_transaction_type = array('Withdraw', 'Online Transaction', 'Offline Transaction','Transfer From Admin');
		
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
		}
		
		return view('admin.report.credit_summary', compact('export_permission','logs','input','user_type','report_view','start_date','end_date','start_date_month','end_date_month','start_date_year','end_date_year','country','sort_by','user_transaction_type','merchant_transaction_type'));
	}
}

