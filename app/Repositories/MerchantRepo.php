<?php

namespace App\Repositories;
use DB;
use App\Models\Merchant;
use App\Models\Store;
use App\Models\MerchantVTokenLog;
use App\Models\AdminSetting;
use App\Models\StoreUser;
use App\Models\Admin;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use App\Models\MerchantGuarantor;
use App\Models\MerchantReferrer;
use Carbon\Carbon;

class MerchantRepo
{
    public static function create($data)
    {
        return Merchant::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'username' => $data['username'],
        'status' => $data['status'],
        'password' => bcrypt(str_random(8)),
        ]);
    }

    public static function all($input = array())
    {
        $result = Merchant::where('mer_staus','=', 1)
        ->join('nm_store','nm_store.stor_merchant_id','=','nm_merchant.mer_id')
        ->join('nm_country','nm_country.co_id','=','nm_merchant.mer_co_id')
        ->whereIn('nm_store.stor_country',isset($input['admin_country_id_list']) ? $input['admin_country_id_list'] : array())
        ->groupBy('nm_merchant.mer_id')
        ->orderBy('nm_merchant.mer_fname', 'ASC')
        ->select('nm_merchant.mer_id','nm_merchant.mer_fname','nm_country.co_name')
        ->get();

        return $result;
    }

    public static function find_merchant($id)
    {
        return Merchant::find($id);
    }

    public static function get_merchant($id)
    {
        return Merchant::where('mer_id','=',$id)->first();
    }

    public static function get_merchant_bank_info($id)
    {
        return Merchant::where('mer_id','=',$id)->leftjoin('nm_country','nm_country.co_id','=','nm_merchant.bank_country')->first();
    }

    public static function register_merchant($data)
    {
        $setting = AdminSetting::first();

        $merchant_entry = array(
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'username' => $data['username'],
            'mer_fname' => $data['fname'],
            'mer_lname' => $data['lname'],
            'mer_phone' => $data['tel'],
            'mer_office_number' => $data['office_number'],
            'mer_address1' => $data['address1'],
            'mer_address2' => $data['address2'],
            'mer_co_id' => $data['country'],
            'mer_state' => $data['state'],
            'zipcode' => $data['zipcode'],
            'mer_city_name' => $data['mer_city_name'],
            'mer_payment' => $data['email'],
            'mer_commission' => 10,
            'bank_acc_name' => $data['bank_acc_name'],
            'bank_acc_no' => $data['bank_acc_no'],
            'bank_name' => $data['bank_name'],
            'bank_country' => $data['bank_country'],
            'bank_address' => $data['bank_address'],
            'bank_swift' => $data['bank_swift'],
            'bank_europe' => $data['bank_europe'],
            'bank_gst' => $data['bank_gst'],
            'mer_type' => $data['stor_type'],
            'mer_platform_charge' => ($data['stor_type'] == 1) ? $setting->offline_platform_charge : $setting->platform_charge,
            'mer_service_charge' => ($data['stor_type'] == 1) ? $setting->offline_service_charge : $setting->service_charge
        );

        $check_insert = Merchant::create($merchant_entry);
		if($check_insert)
		{
		 	return DB::getPdo()->lastInsertId();
		} else
		{
			return 0;
		}
    }

    public static function get_merchant_vtoken($id)
    {
        return Merchant::where('mer_id', '=', $id)->pluck('mer_vtoken')->first();
    }

    public static function get_merchant_commison($id)
	{
        return Merchant::where('mer_id', '=', $id)->pluck('mer_commission')->first();
	}

    public static function get_merchant_profile_details($mer_id)
	{
        $merchant = Merchant::where('nm_merchant.mer_id', '=', $mer_id)
        ->leftJoin('nm_city', 'nm_city.ci_id', '=', 'nm_merchant.mer_ci_id')
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_merchant.mer_co_id');

        return $merchant->first();
	}

    public static function update_merchant_profile_details($mer_id,$data)
    {
        if (isset($data['type'])) {
            $merchant_details = Merchant::where('mer_id','=',$mer_id)->first();
            $merchant = Merchant::where('mer_id','=',$mer_id)->update([
                'username' => $data['username'],
                'email' => $data['email'],
                'mer_fname' => $data['fname'],
                'mer_type' => $data['type'],
                'mer_lname' => $data['lname'],
                'mer_phone' => isset($data['phone']) ? $data['phone'] : $merchant_details->mer_phone,
                'mer_office_number' => $data['office_number'],
                'mer_address1' => $data['address1'],
                'mer_address2' => $data['address2'],
                'mer_co_id' => $data['country'],
                'mer_state' => $data['state'],
                'zipcode' => $data['zipcode'],
                'mer_city_name' => $data['mer_city_name'],
                'mer_payment' => $data['email'],
                'mer_commission' => isset($data['commission']) ? $data['commission'] : $merchant_details->mer_commission,
                'bank_acc_name' => isset($data['bank_holder']) ? $data['bank_holder'] : $merchant_details->bank_acc_name,
                'bank_acc_no' => isset($data['bank_acc']) ? $data['bank_acc'] : $merchant_details->bank_acc_no,
                'bank_name' => isset($data['bank_name']) ? $data['bank_name'] : $merchant_details->bank_name,
                'bank_country' => isset($data['bank_country']) ? $data['bank_country'] : $merchant_details->bank_country,
                'bank_address' => isset($data['bank_address']) ? $data['bank_address'] : $merchant_details->bank_address,
                'bank_swift' => isset($data['bank_swift']) ? $data['bank_swift'] : $merchant_details->bank_swift,
                'bank_europe' => isset($data['bank_europe']) ? $data['bank_europe'] : $merchant_details->bank_europe,
                'bank_gst' => isset($data['bank_gst']) ? $data['bank_gst'] : $merchant_details->bank_gst,
                'mer_platform_charge' => isset($data['mer_platform_charge']) ? $data['mer_platform_charge'] : $merchant_details->mer_platform_charge,
                'mer_service_charge' => isset($data['mer_service_charge']) ? $data['mer_service_charge'] : $merchant_details->mer_service_charge,
            ]);

            // if ($merchant_details->mer_type != $date['type']) {
                $store = Store::where('stor_merchant_id', $mer_id)->update([
                    'stor_type' => $data['type']
                ]);
            // }
        } else {
            $merchant = Merchant::where('mer_id','=',$mer_id)->update([
                'mer_fname' => $data['fname'],
                'mer_lname' => $data['lname'],
                'mer_office_number' => $data['office_number'],
                'mer_address1' => $data['address1'],
                'mer_address2' => $data['address2'],
                'mer_co_id' => $data['country'],
                'mer_state' => $data['state'],
                'mer_city_name' => $data['mer_city_name'],
            ]);
        }

        return $merchant;

    }

    public static function check_existing_merchant_email($email)
    {
        return Merchant::where('email', $email)->get();
    }

    public static function get_merchant_details($type,$input)
	{
        switch ($type) {
            case 'online':
                $type = 0;
                break;
            case 'offline':
                $type = 1;
                break;
            default:
                $type = 0;
                break;
        }

        $merchants = array();
        $merchants = Merchant::select('nm_merchant.*','nm_country.co_name','nm_store.stor_id','nm_store_offline_category.*')->where('mer_type','=', $type)
                    ->LeftJoin('nm_country', 'nm_country.co_id', '=', 'nm_merchant.mer_co_id')
                    ->LeftJoin('nm_store','nm_store.stor_merchant_id','=','nm_merchant.mer_id')
                    ->LeftJoin('nm_store_offline_category','nm_store.stor_id','=','nm_store_offline_category.store_id');

        if(!empty($input['country_id_permission'])){
            $merchants->whereIn('nm_merchant.mer_co_id',$input['country_id_permission']);
        }

        if(!empty($input['id']))
        {
            $merchants->where('nm_merchant.mer_id','=',$input['id']);
        }

        if(!empty($input['name']))
        {
            $merchants->where('nm_merchant.mer_fname','LIKE', '%'.$input['name'].'%');
        }

        if (!empty($input['email']))
            $merchants->where('nm_merchant.email','LIKE', '%'.$input['email'].'%');

        if(!empty($input['country']))
        {
            $merchants->where('nm_country.co_name','LIKE', '%'.$input['country'].'%');
        }

        if(!empty($input['selected_cats']))
        {
            $merchants->where('nm_store_offline_category.offline_category_id', '=', $input['selected_cats']);
        }

        if(!empty($input['status']))
        {
            $merchants->where('nm_merchant.mer_staus','=',$input['status']);
        }

        if (!empty($input['sort'])) {
            switch ($input['sort']) {
                case 'new':
                    $merchants->orderBy('nm_merchant.created_at', 'desc');
                    break;
                case 'old':
                    $merchants->orderBy('nm_merchant.created_at', 'asc');
                    break;
            }
        }
        else {
            $merchants->orderBy('nm_merchant.mer_id', 'desc');
        }

        if(isset($input['action']))
        {
            if( $input['action'] == 'export')
            {
                return $merchants->get();
            }

            return $merchants->paginate(20);
        }


        $merchants = $merchants->paginate(20);

        foreach ($merchants as $key => $merchant) {
            $store_by_country_permission_count = Store::where('stor_merchant_id','=',$merchant->mer_id)
            ->whereIn('stor_country',$input['country_id_permission'])
            ->get()->count();

            $store = Store::where('stor_merchant_id','=',$merchant->mer_id)->get()->count();

            $users_by_country_permission_count = StoreUser::where('mer_id','=',$merchant->mer_id)
                   ->join('nm_store_user_mappings','nm_store_user_mappings.storeuser_id','=','nm_store_users.id')
                   ->join('nm_store','nm_store.stor_id','=','nm_store_user_mappings.store_id')
                   ->whereIn('stor_country',$input['country_id_permission'])
                   ->groupBy('nm_store_users.id')
                   ->get()->count();

            $users = StoreUser::where('mer_id','=',$merchant->mer_id)->get()->count();
            $updater = null;

            if ($merchant->updated_by == 1) {
                $updater = Admin::selectRaw("CONCAT(adm_fname, ' ', adm_lname) as name")->where('adm_id', $merchant->updater_id)->pluck('name')->first();
            }

            $merchants[$key]['details'] = $merchant;
            $merchants[$key]['store_count'] = $store;
            $merchants[$key]['store_by_country_permission_count'] = $store_by_country_permission_count;
            $merchants[$key]['users_by_country_permission_count'] = $users_by_country_permission_count;
            $merchants[$key]['storeuser_count'] = $users;
            $merchants[$key]['updater_name'] = $updater;
        }

        return $merchants;
	}

    public static function update_merchant_status($mer_id, $status)
    {
        $merchant = Merchant::where('mer_id','=',$mer_id)->first();

        $email_approval = false;
        if ($merchant->mer_staus == 2 )
            $email_approval = true;

        $merchant->mer_staus = $status;
        $merchant->updated_by = 1;
        $merchant->updater_id = \Auth::guard('admins')->user()->adm_id;
        $merchant->save();

        if ($email_approval) {
            \Mail::send('front.emails.merchant_approval', ['merchant'=> $merchant], function (Message $m) use ($merchant) {
                $m->to($merchant->email, $merchant->mer_fname)->subject('MeiHome Merchant Application Approval');
            });
        }

        return $merchant;
    }

    public static function update_merchant_credit($mer_id, $wd_id, $credit_debit, $new_merchant_credit, $remark)
    {
        $update_merchant = Merchant::where('mer_id', $mer_id)
            ->update(['mer_vtoken' => $new_merchant_credit]);

        $add_log = MerchantVTokenLog::create([
            'mer_id' => $mer_id,
            'debit_amount' => $credit_debit,
            'withdraw_id' => $wd_id,
            'remark' => $remark,
        ]);

        return true;
    }

    public static function get_merchant_vtoken_log($mer_id, $input)
    {
        $logs = MerchantVTokenLog::where('mer_id','=',$mer_id);

        if (!empty($input['id'])) {
            $logs->where(function($query) use ($input) {
                $query->where('order_id', '=', $input['id'])
                ->orWhere('offline_order_id', '=',$input['id'])
                ->orWhere('withdraw_id', '=', $input['id']);
            });
        }

        if (!empty($input['status'])) {
            switch ($input['status']) {
                case '1':
                    $logs->whereNotNull('order_id');
                    break;
                case '2':
                    $logs->whereNotNull('offline_order_id');
                    break;
                case '3':
                    $logs->whereNotNull('withdraw_id');
                    break;
            }
        }

        if(!empty($input['remark']))
        {
            $logs->where('remark','LIKE', '%'.$input['remark'].'%');
        }

        if (!empty($input['start']) && !empty($input['end'])) {
            $input['start'] = Carbon::createFromFormat('d/m/Y', $input['start'])->startOfDay()->toDateTimeString();
            $input['end'] = Carbon::createFromFormat('d/m/Y', $input['end'])->endOfDay()->toDateTimeString();

            $logs->where('created_at', '>=', \Helper::TZtoUTC($input['start']));
            $logs->where('created_at', '<=', \Helper::TZtoUTC($input['end']));
        }

        if (!empty($input['sort'])) {
            switch ($input['sort']) {
                case 'new':
                    $logs->orderBy('created_at', 'desc');
                    break;
                case 'old':
                    $logs->orderBy('created_at', 'asc');
                    break;
                default:
                    $logs->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $logs->orderBy('created_at', 'desc');
        }

        if (!empty($input['action']) && $input['action'] == 'export')
            return $logs->get();

        return $logs->paginate(20);;
    }

    public static function get_merchant_by_username($username)
    {
        // return Merchant::leftJoin('nm_store', 'nm_store.stor_merchant_id', '=', 'nm_merchant.mer_id')
        return Merchant::leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_merchant.mer_co_id')
        ->where('nm_merchant.username', '=', $username)
        ->first();
    }

    public static function reset_password($data)
    {
        $merchant = Merchant::find($data['mer_id']);

        $merchant->password = bcrypt($data['password']);
        $merchant->save();

        return $merchant;
    }

    public static function manage_credit($mer_id, $data)
    {
        $merchant = Merchant::find($mer_id);

        switch ($data['type']) {
            case 'credit':
                $merchant->mer_vtoken = $merchant->mer_vtoken + round($data['amount'], 4);
                MerchantVTokenLog::create([
                    'mer_id' => $mer_id,
                    'credit_amount' => round($data['amount'], 4),
                    'debit_amount' => 0,
                    'order_id' => 0,
                    'remark' => $data['remark'],
                ]);
                break;

            case 'debit':
                $merchant->mer_vtoken = $merchant->mer_vtoken - round($data['amount'], 4);
                MerchantVTokenLog::create([
                    'mer_id' => $mer_id,
                    'credit_amount' => 0,
                    'debit_amount' => round($data['amount'], 4),
                    'order_id' => 0,
                    'remark' => $data['remark'],
                ]);
                break;
        }

        $merchant->save();
    }

    public static function get_merchant_vtoken_log_by_withdraw_id($wd_id)
    {
        return MerchantVTokenLog::where('withdraw_id', $wd_id)->first();
    }

    public static function get_merchant_by_id_and_email($mer_id, $mer_email)
    {
        return Merchant::where('mer_id', $mer_id)->where('email', $mer_email)->first();
    }

    public static function create_update_guarantor($merchant_id, $data)
    {
        return MerchantGuarantor::updateOrCreate([
            'merchant_id' => $merchant_id
        ], [
            'name' => $data['guarantor_name'],
            'username' => $data['guarantor_username'],
            'phone' => $data['guarantor_phone'],
            'nationality' => $data['guarantor_nationality'],
            'email' => $data['guarantor_email'],
            'bank_name' => $data['guarantor_bank_name'],
            'bank_acc_name' => $data['guarantor_acc_name'],
            'bank_acc_no' => $data['guarantor_bank_acc']
        ]);
    }

    public static function create_update_referrer($merchant_id, $data)
    {
        return MerchantReferrer::updateOrCreate([
            'merchant_id' => $merchant_id
        ], [
            'name' => $data['referrer_name'],
            'username' => $data['referrer_username'],
            'phone' => $data['referrer_phone'],
            'nationality' => $data['referrer_nationality'],
            'email' => $data['referrer_email'],
            'bank_name' => $data['referrer_bank_name'],
            'bank_acc_name' => $data['referrer_acc_name'],
            'bank_acc_no' => $data['referrer_bank_acc']
        ]);
    }

    public static function get_online_merchants($input = array())
    {
        $result = Merchant::where('mer_staus','=', 1)
        ->where('nm_merchant.mer_type', 0)
        ->join('nm_store','nm_store.stor_merchant_id','=','nm_merchant.mer_id')
        ->join('nm_country','nm_country.co_id','=','nm_merchant.mer_co_id')
        ->whereIn('nm_store.stor_country',isset($input['admin_country_id_list']) ? $input['admin_country_id_list'] : array())
        ->groupBy('nm_merchant.mer_id')
        ->orderBy('nm_merchant.mer_fname', 'ASC')
        ->select('nm_merchant.mer_id','nm_merchant.mer_fname','nm_country.co_name')
        ->get();

        return $result;
    }
}
