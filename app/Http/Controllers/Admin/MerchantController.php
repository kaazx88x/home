<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;

use App\Repositories\MerchantRepo;
use App\Repositories\CountryRepo;
use App\Repositories\StoreRepo;
use App\Providers\MerchantActivationServiceProvider;
use App\Repositories\CityRepo;
use Validator;
use App\Repositories\StateRepo;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use App\Repositories\S3ClientRepo;
use App\Models\Admin;
use App\Repositories\InvoiceRepo;
use App\Repositories\OfflineCategoryRepo;

class MerchantController extends Controller
{
    public function __construct(MerchantRepo $merchantrepo, CountryRepo $countryrepo, StoreRepo $storerepo, MerchantActivationServiceProvider $activationService, CityRepo $cityrepo, StateRepo $staterepo,Mailer $mailer,OfflineCategoryRepo $offlinecategoryrepo) {

        $this->merchant = $merchantrepo;
        $this->country = $countryrepo;
        $this->store = $storerepo;
        $this->activationService = $activationService;
        $this->city = $cityrepo;
        $this->state = $staterepo;
        $this->mailer = $mailer;
        $this->offlinecategory = $offlinecategoryrepo;
    }

    public function add_merchant()
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);

        $country_details = $this->country->get_countries($admin_country_id_list);
        $all_country_details = $this->country->get_all_countries();

        return view('admin.merchant.add', compact('merchants','country_details','all_country_details'));
    }

    public function add_merchant_submit()
    {
         if (\Request::isMethod('post')) {
            $data = \Request::all();

            $v = Validator::make($data, [
                'username' => 'required|username|min:4|max:100|unique:nm_merchant,username',
                'fname' => 'required',
                'lname' => 'required',
                'email' => 'required|email|unique:nm_merchant,email',
                'tel' => 'required',
                'office_number' => 'required',
                'country' => 'required',
                'state' => 'required',
                'mer_city_name' => 'required',

                'stor_type' => 'required',
                'stor_name' => 'required',
                'stor_phone' => 'required',
                'stor_office_number' => 'required',
                'stor_address1' => 'required',
                'stor_zipcode' => 'required',
                'stor_country' => 'required',
                'stor_state' => 'required',
                'stor_city_name' => 'required',
                'longtitude' => 'required_if:stor_type,1',
                'latitude' => 'required_if:stor_type,1',

                'bank_acc_name' => 'required',
                'bank_acc_no' => 'required',
                'bank_name' => 'required',
                'bank_country' => 'required',
                'bank_address' => 'required',
                'stor_img' => 'required|mimes:jpeg,jpg,png|max:1000',

                // 'guarantor_name' => 'required',
                // 'guarantor_username' => 'required',
                // 'guarantor_phone' => 'required',
                // 'guarantor_email' => 'required',
                // 'guarantor_bank_name' => 'required',
                // 'guarantor_acc_name' => 'required',
                // 'guarantor_bank_acc' => 'required',

	        ],[
                'username.required' => trans('localize.Merchant_Username_field_is_required'),
                'fname.required' => trans('localize.Merchant_First_Name_field_is_required'),
                'lname.required' => trans('localize.Merchant_Last_Name_field_is_required'),
                'email.required' => trans('localize.Merchant_Email_field_is_required'),
                'office_number.required' => trans('localize.Merchant_Office_Number_field_is_required'),
                'tel.required' => trans('localize.Merchant_Phone_Number_field_is_required'),
                'country.required' => trans('localize.Merchant_Country_selection_is_required'),
                'state.required' => trans('localize.Merchant_State_selection_is_required'),
                'mer_city_name.required' => trans('localize.Merchant_City_field_is_required'),

                'stor_type.required' => trans('localize.Merchant_Store_Type_field_is_required'),
                'stor_name.required' => trans('localize.Merchant_Store_Name_field_is_required'),
                'stor_phone.required' => trans('localize.Merchant_Store_Phone_Number_field_is_required'),
                'stor_office_number.required' => trans('localize.Merchant_Store_Office_Number_field_is_required'),
                'stor_address1.required' => trans('localize.Merchant_Store_Address_field_is_required'),
                'stor_country.required' => trans('localize.Merchant_Store_Country_field_is_required'),
                'stor_state.required' => trans('localize.Merchant_Store_State_field_is_required'),
                'stor_city_name.required' => trans('localize.Merchant_Store_City_field_is_required'),
                'longtitude.required' => trans('localize.Store_Longtitude_field_is_required'),
                'latitude.required' => trans('localize.Store_Latitude_field_is_required'),
                'commission.required' => trans('localize.Commision_field_is_required'),

                'bank_acc_name.required' => trans('localize.Bank_Holder_Name_field_is_required'),
                'bank_acc_no.required' => trans('localize.Bank_Account_Number_field_is_required'),
                'bank_name.required' => trans('localize.Bank_Name_field_is_required'),
                'bank_country.required' => trans('localize.Bank_Country_selection_is_required'),
                'bank_address.required' => trans('localize.Bank_Address_field_is_required'),
            ])->validate();

            if ($data['stor_type'] == 1) {
                if(empty(json_decode($data['stor_category']))) {
                    return back()->withInput()->withErrors(trans('localize.Please_select_store_category'));
                }

                $cats = json_decode($data['stor_category']);
            }

            $data['password'] = str_random(6);

            try {
                $mer_id = $this->merchant->register_merchant($data);

                //insert store image
                $store_image = '';
                if (!empty($data['stor_img'])) {
                    $upload_file = $data['stor_img']->getClientOriginalName();
                    $file_detail = explode('.', $upload_file);
                    $store_image = date('Ymd').'_'.str_random(4).'.'.$file_detail[1];
                    $path = 'store/'.$mer_id;
                        if(@file_get_contents($data['stor_img']) && !S3ClientRepo::IsExisted($path, $store_image))
                            $upload = S3ClientRepo::Upload($path, $data['stor_img'], $store_image);
                }

                $data['by_admin'] = true;
                $merchant_store = $this->store->add_store($data, $mer_id, $store_image);

                if($data['stor_type'] == 1) {
                    $store_id = $merchant_store->stor_id;
                    foreach ($cats as $key => $cat) {
                        if ($cat) {
                            $store_category = array(
                                'store_id' => $store_id,
                                'offline_category_id' => $cat,
                            );

                            try {
                                StoreRepo::insert_offline_category_details($store_category);
                            } catch (\Exception $e) {
                                return back()->withInput()->withErrors(trans('localize.failupdatestore'));
                            }
                        }
                    }
                }

                $merchant_guarantor = MerchantRepo::create_update_guarantor($mer_id, $data);
                $merchant_referrer = MerchantRepo::create_update_referrer($mer_id, $data);

                $merchant = $this->merchant->get_merchant($mer_id);

                $this->activationService->sendActivationMail_by_admin($merchant,$data['password']);

                return redirect('/admin/merchant/edit/'.$mer_id)->with('success', trans('localize.Successfully_add_new_merchant_account'));

            } catch (Exception $e) {
                    return back()->withInput()->withErrors(trans('localize.Unable_to_add_store'));
            }

         }
    }

    public function manage_merchant($type = null)
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);


		if(in_array('merchantonlinelist', $admin_permission) == false){
		return redirect('admin')->with('denied', trans('localize.You_are_not_authorized_to_access_that_page'));
		}


        $edit_permission = in_array('merchantonlineedit',$admin_permission);
        $block_merchant_permission = in_array('merchantonlineblockmerchant',$admin_permission);
        $lock_merchant_permission = in_array('merchantonlinelockmerchant',$admin_permission);
        $manage_store = in_array('storelist',$admin_permission);
        $manage_store_user = in_array('storeuserlist',$admin_permission);
        $export_permission = in_array('merchantlistingexport',$admin_permission);

       $input = \Request::only('id','name', 'email','status','sort','country','selected_cats');
        $request = $input;
        $status_list = array(
            ''  => trans('localize.all'),
            '1' => trans('localize.Active'),
            '0' => trans('localize.Inactive'),
        );
        $offline_category_name = "";
        if(!empty($input['selected_cats'])){
            $offline_category_name = $this->offlinecategory->get_detail_by_id($input['selected_cats']);
        }

        $request['country_id_permission'] = $admin_country_id_list;
        $merchants = $this->merchant->get_merchant_details($type, $request);

        return view('admin.merchant.manage', compact('merchants','input','status_list','type','manage_store_user','manage_store','edit_permission','block_merchant_permission','lock_merchant_permission', 'export_permission','offline_category_name'));
    }

    public function edit_merchant($mer_id)
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
        $country_list_array = Controller::getAdminCountryIdList($adm_id);
        $all_country_details = $this->country->get_all_countries();
        $online_merchant_permission = in_array('merchantonlinelist', $admin_permission);
        $edit_merchant_type_permission = in_array('merchantonlineeditmerchanttype', $admin_permission);
        $edit_merchant_username_permission = in_array('merchantonlineeditmerchantusername', $admin_permission);
        $edit_merchant_email_permission = in_array('merchantonlineeditmerchantemail', $admin_permission);
        $edit_merchant_phone_permission = in_array('merchantonlineeditmerchantphone', $admin_permission);
        $edit_merchant_bank_info_permission = in_array('merchantonlineeditmerchantbankinfo', $admin_permission);
        $hard_reset_password = in_array('merchantonlinehardresetpassword', $admin_permission);
        $soft_reset_password = in_array('merchantonlinesoftresetpassword', $admin_permission);
        $mi_credit_report_permission = in_array('merchantonlinemicreditreport', $admin_permission);
        $mi_credit_manage_permission = in_array('merchantonlinemanagemicredit', $admin_permission);
        $resend_activation_email_permission = in_array('merchantonlineresentactivationemail', $admin_permission);
        $online_order_report = in_array('transactiononlineorderslist', $admin_permission);
        $offline_order_report = in_array('transactionofflineorderslist', $admin_permission);
        $manage_store = in_array('storelist', $admin_permission);
        $edit_merchant_commission = in_array('merchantonlineeditcommission', $admin_permission);
        $edit_merchant_platform_charge = in_array('merchantonlineeditmerchantplatformcharge', $admin_permission);
        $edit_merchant_service_charge = in_array('merchantonlineeditmerchantservicecharge', $admin_permission);

        $merchant = $this->merchant->get_merchant($mer_id);

        $merchant_country_id = isset($merchant['mer_co_id']) ? $merchant['mer_co_id'] : 0;
        $admin_country_id_list = array_merge($admin_country_id_list, array(0));

        $check_validation = in_array($merchant_country_id, $admin_country_id_list);
        if(!$check_validation){
            return redirect('admin')->with('denied', 'You are not authorized to access that page');
        }

        if(!$merchant)
            return back()->with('error', trans('localize.invalid_request'));

        $country_details = $this->country->get_countries($country_list_array);

        return view('admin.merchant.edit', compact(
            'merchant',
            'country_details',
            'mi_credit_manage_permission',
            'mi_credit_report_permission',
            'soft_reset_password',
            'hard_reset_password',
            'edit_merchant_bank_info_permission',
            'edit_merchant_phone_permission',
            'edit_merchant_email_permission',
            'edit_merchant_username_permission',
            'online_merchant_permission',
            'edit_merchant_type_permission',
            'resend_activation_email_permission',
            'online_order_report',
            'offline_order_report',
            'edit_merchant_platform_charge',
            'edit_merchant_commission',
            'edit_merchant_service_charge',
            'manage_store',
            'all_country_details'
        ));
    }

    public function edit_merchant_submit()
    {
        if (\Request::isMethod('post')) {
            $admin = \Auth::guard('admins')->user()->adm_id;
            // $admin_role = Admin::where('adm_id','=', $admin)->leftJoin('nm_admin_role','nm_admin_role.id','=','nm_admin.role_id')->value('name');

            $data = \Request::all();

            $mer_id = $data['mer_id'];
            $merchant = MerchantRepo::get_merchant($mer_id);
            $admin_permission = Controller::adminPermissionList($admin);
            $edit_merchant_type_permission = in_array('merchantonlineeditmerchanttype', $admin_permission);
            $edit_merchant_username_permission = in_array('merchantonlineeditmerchantusername', $admin_permission);
            $edit_merchant_email_permission = in_array('merchantonlineeditmerchantemail', $admin_permission);
            $edit_merchant_phone_permission = in_array('merchantonlineeditmerchantphone', $admin_permission);
            $edit_merchant_bank_info_permission = in_array('merchantonlineeditmerchantbankinfo', $admin_permission);
            $edit_merchant_guarantor = in_array('merchantupdateguarantor', $admin_permission);
            $edit_merchant_referrer = in_array('merchantupdatereferrer', $admin_permission);

            //Permission update fields
            if(!$edit_merchant_type_permission){
                $data['type'] = isset($merchant->mer_type) ? $merchant->mer_type : 0  ;
            }
            if(!$edit_merchant_username_permission){
                $data['username'] = isset($merchant->username) ? $merchant->username : ''  ;
            }
            if(!$edit_merchant_email_permission){
                $data['email'] = isset($merchant->email) ? $merchant->email : ''  ;
            }
            if(!$edit_merchant_phone_permission){
                $data['phone'] = isset($merchant->mer_phone) ? $merchant->mer_phone : ''  ;
            }
            if(!$edit_merchant_bank_info_permission){
                $data['bank_acc_name'] = isset($merchant->bank_acc_name) ? $merchant->bank_acc_name : ''  ;
                $data['bank_acc_no'] = isset($merchant->bank_acc_no) ? $merchant->bank_acc_no : ''  ;
                $data['bank_name'] = isset($merchant->bank_name) ? $merchant->bank_name : ''  ;
                $data['bank_country'] = isset($merchant->bank_country) ? $merchant->bank_country : ''  ;
                $data['bank_address'] = isset($merchant->bank_address) ? $merchant->bank_address : ''  ;
                $data['bank_swift'] = isset($merchant->bank_swift) ? $merchant->bank_swift : ''  ;
                $data['bank_europe'] = isset($merchant->bank_europe) ? $merchant->bank_europe : ''  ;
            }

            $form_input = [
                'fname' => 'required',
                'lname' => 'required',
                'office_number' => 'required',
                'country' => 'required',
                'state' => 'required',
                'mer_city_name' => 'required',
            ];

            if($edit_merchant_username_permission){
                $form_input = $form_input + ['username' => 'required'];
            }
            if($edit_merchant_email_permission){
                $form_input = $form_input + ['email' => 'required'];
            }
            if($edit_merchant_phone_permission){
                $form_input = $form_input + ['phone' => 'required'];
            }
            if($edit_merchant_bank_info_permission){
                 $extra_form_input = [
                    'bank_acc_name' => 'required',
                    'bank_acc_no' => 'required',
                    'bank_name' => 'required',
                    'bank_country' => 'required',
                    'bank_address' => 'required',
                ];
                $form_input = array_merge($form_input, $extra_form_input);

                $data['bank_holder'] = $data['bank_acc_name'];
                $data['bank_acc'] = $data['bank_acc_no'];
            }

            $v = Validator::make($data, $form_input, [
                'fname.required' => trans('localize.Merchant_First_Name_field_is_required'),
                'lname.required' => trans('localize.Merchant_Last_Name_field_is_required'),
                'email.required' => trans('localize.Merchant_Email_field_is_required'),
                'phone.required' => trans('localize.Merchant_Phone_Number_field_is_required'),
                'office_number.required' => trans('localize.Merchant_Office_Number_field_is_required'),
                'country.required' => trans('localize.Merchant_Country_selection_is_required'),
                'mer_city_name.required' => trans('localize.Merchant_City_is_required'),

                'bank_acc_name.required' => trans('localize.Bank_Holder_Name_field_is_required'),
                'bank_acc_no.required' => trans('localize.Bank_Account_Number_field_is_required'),
                'bank_name.required' => trans('localize.Bank_Name_field_is_required'),
                'bank_country.required' => trans('localize.Bank_Country_selection_is_required'),
                'bank_address.required' => trans('localize.Bank_Address_field_is_required'),
            ]);

            if ($v->fails())
               return back()->withInput()->withErrors($v);

            try {
                $edit_merchant = $this->merchant->update_merchant_profile_details($mer_id, $data);

                if($edit_merchant_guarantor) {
                    $edit_guarantor = MerchantRepo::create_update_guarantor($mer_id, $data);
                }

                if($edit_merchant_referrer) {
                    $edit_refferer = MerchantRepo::create_update_referrer($mer_id, $data);
                }

                return back()->with('success', trans('localize.Successfully_update_merchant_account'));

            } catch (Exception $e) {
                    return back()->withInput()->withErrors(trans('localize.Unable_to_edit_merchant'));
            }
        }
    }

    public function view_merchant($mer_id)
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
        $edit_permission = in_array('merchantonlineedit',$admin_permission);
        $mi_credit_report_permission = in_array('merchantonlinemicreditreport', $admin_permission);

        $merchant = $this->merchant->get_merchant($mer_id);

        $merchant_country_id = isset($merchant['mer_co_id']) ? $merchant['mer_co_id'] : 0;
        $admin_country_id_list = array_merge($admin_country_id_list, array(0));

        $check_validation = in_array($merchant_country_id, $admin_country_id_list);
        if(!$check_validation){
            return redirect('admin')->with('denied', 'You are not authorized to access that page');
        }

        $country_details = $this->country->get_all_countries();
        $state_details = $this->state->get_states();

        return view('admin.merchant.view', compact('merchant','country_details','state_details','edit_permission','mi_credit_report_permission'));
    }

    public function update_merchant_status($mer_id, $status)
    {
        $this->merchant->update_merchant_status($mer_id, $status);

        if ($status == 1) {
            return back()->with('success', trans('localize.Successfully_set_merchant_status_to_Active'));
        }else if ($status == 0) {
            $this->store->inactive_merchant_store($mer_id);
            return back()->with('success', trans('localize.Succesfully_block_merchant'));
        }
    }

    public function merchant_credit_log($mer_id)
    {
        $input = \Request::only('id', 'start', 'end', 'status', 'sort','remark');
        $status_list = array(
            '' => trans('localize.all'),
            '1' => trans('localize.order'),
            '2' => trans('localize.order_offline'),
            '3' => trans('localize.withdraw'),
        );

        $logs = $this->merchant->get_merchant_vtoken_log($mer_id, $input);
        $merchant = $this->merchant->get_merchant($mer_id);

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::checkAdminPermission($adm_id,'merchantonlinemicreditreportexport');

        return view('admin.merchant.credit_log', compact('input','status_list','logs','merchant','admin_permission'));
    }

    public function reset_password()
    {
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $validator = \Validator::make($data, [
                'password' => 'required|min:6|confirmed',
            ],[
                'password.required' => trans('localize.Please_fill_password_field'),
                'password.confirmed' => trans('localize.Password_confirmation_does_not_match')
            ]);

            if ($validator->fails()){
                return back()->withInput()->withErrors($validator);
            }

            try {
                $merchant = $this->merchant->reset_password($data);

                $this->mailer->send('front.emails.reset_merchant_password_by_admin', ['merchant'=>$merchant,'password'=>$data['password']], function (Message $m) use ($merchant) {
                    $m->to($merchant->email, $merchant->mer_fname)->subject(trans('email.admin.reset_password_merchant', ['mall_name' => trans('common.mall_name')]));
                });

                return back()->with('success', trans('localize.Successfully_reset_password'));
            } catch (Exception $e) {
                return back()->with('error', $e);
            }
        }
    }

    public function manage_credit($mer_id)
    {
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $validator = \Validator::make($data, [
                'amount' => 'required',
                'remark' => 'required',
            ],[
                'amount.required' => trans('localize.Please_fill_Mei_Credit_amount_field'),
                'remark.required' => trans('localize.Please_fill_remarks_field')
            ]);

            if ($validator->fails()){
                return back()->withInput()->withErrors($validator);
            }

            if($data['type'] == 'debit')
            {
                $merchant = $this->merchant->get_merchant($mer_id);
                $mer_vc = round($merchant->mer_vtoken, 4);
                if(round($data['amount'],4) > $mer_vc)
                    return back()->with('error', trans('localize.Insufficient') . ' Mei Point' . trans('localize.to') . trans('localize.debit'));
            }

            try {
                $merchant = $this->merchant->manage_credit($mer_id, $data);
                $message = trans('localize.Successfully') .  trans('localize.credit') .round($data['amount'],4).' Mei Point ' . trans('localize.to') . trans('localize.Merchant');
                if($data['type'] == 'debit')
                    $message = trans('localize.Successfully') .  trans('localize.debit') .round($data['amount'],4).' Mei Point ' . trans('localize.from') . trans('localize.Merchant');

                return back()->with('success', $message);

            } catch (Exception $e) {
                return back()->with('error', $e);
            }
        }
    }

    public function soft_reset_password($mer_id)
    {
        try {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $data = [
                'mer_id' => $mer_id,
                'password' => substr(str_shuffle($chars), 0, 8)
            ];

            $merchant = $this->merchant->reset_password($data);

            $this->mailer->send('front.emails.reset_merchant_password_by_admin', ['merchant'=>$merchant,'password'=>$data['password']], function (Message $m) use ($merchant) {
                $m->to($merchant->email, $merchant->mer_fname)->cc('operation@meihome.asia', 'MeiHome Operation')->subject(trans('email.admin.reset_password_merchant', ['mall_name' => trans('common.mall_name')]));
            });

            return back()->with('success', trans('localize.Successfully_reset_password'));
        } catch (Exception $e) {
            return back()->with('error', $e);
        }
    }

    public function tax_listing($merchant_id)
    {
        $input = \Request::only('start', 'end', 'type', 'invoice_no');
        $merchant = $this->merchant->get_merchant($merchant_id);
        $invoices = InvoiceRepo::get_invoices($merchant_id, $input);

        return view('admin.merchant.tax_listing', compact('input', 'merchant', 'invoices'));
    }

    public function tax_create($merchant_id)
    {
        $input = \Request::only('start', 'end', 'type');
        $merchant = $this->merchant->get_merchant($merchant_id);

        $invoices = [];
        if(!empty($input['type'])) {
            $invoices = InvoiceRepo::get_invoice_items($merchant_id, $input);
        }

        return view('admin.merchant.tax_create', compact('input', 'merchant', 'invoices'));
    }

    public function tax_save($merchant_id)
    {
        if(\Request::isMethod('post')) {
            $input = \Request::all();

            $invoices = [];
            if(!empty($input['type'])) {
                $invoices = InvoiceRepo::get_invoice_items($merchant_id, $input);
            }

            $save_invoices = InvoiceRepo::create_invoice_items($merchant_id, $input, $invoices);

            if ($save_invoices) {
                return redirect('admin/merchant/tax/view/' . $merchant_id . '/' . $save_invoices->id);
            }

            return back()->with('error', trans('localize.Failed'));
        }
    }

    public function tax_view($merchant_id, $invoice_id)
    {
        $input = \Request::only('action');

        $invoices = InvoiceRepo::get_invoice_items_details($merchant_id, $invoice_id);

        if (!$input['action']) {
            return view('admin.merchant.tax_view', compact('invoices'));
        }
        else {
            return view('admin.merchant.tax_print', compact('invoices'));
        }
    }
}