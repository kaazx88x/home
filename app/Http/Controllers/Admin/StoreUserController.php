<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;
use App\Repositories\StoreRepo;
use App\Repositories\StoreUserRepo;
use App\Repositories\MerchantRepo;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;

class StoreUserController extends Controller
{
    public function __construct(Mailer $mailer) {
        $this->mailer = $mailer;
    }

    public function manage($mer_id)
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
        $input['admin_country_id_list'] = $admin_country_id_list;

        $add_store_user_permission = in_array('storeusercreate', $admin_permission);
        $active_store_user_permission = in_array('storeusersetuserstatus', $admin_permission);
        $lock_store_user_permission = in_array('storeuserlockuser', $admin_permission);

        $stores = StoreRepo::get_stores_by_merchant($mer_id, $input);
        $users = StoreUserRepo::get_merchant_store_user($mer_id, $admin_country_id_list);
        $merchant = MerchantRepo::find_merchant($mer_id);
        foreach ($users as $user) {
            $user->assigned_store = StoreUserRepo::get_assigned_store_user($user->id);
        }

        return view('admin.store.user.manage', compact('users','mer_id','stores','merchant','add_store_user_permission','active_store_user_permission','lock_store_user_permission'));
    }

    public function add_submit($mer_id)
    {
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $v = \Validator::make($data, [
                'name' => 'required|max:100',
                'email' => 'required_without:password|email|max:255|unique:nm_store_users,email',
                'username' => 'required|username|min:4|max:100|unique:nm_store_users,username',
                'password' => 'required_without:email|min:6|confirmed',
            ]);
            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $token = hash_hmac('sha256', str_random(40), config('app.key'));
            $user = StoreUserRepo::create($data, $mer_id, $token);

            if(isset($data['store_list']))
                $update = StoreUserRepo::update_user_store_mapping($data['store_list'], $user->id);

            if(isset($data['email'])){

                $link = route('storeuser.activate', $token);

                $this->mailer->send('merchant.auth.emails.store_activation', array('activation_link'=>$link,'username'=>$user->username), function (Message $m) use ($user) {
                    $m->to($user->email)->subject('Account Activation');
                });

             }

            return back()->with('success', trans('localize.Successfully_create_store_user'));
        }
    }

    public function toggle_user_status($user_id, $status)
    {
        $update = StoreUserRepo::toggle_user_status($user_id,$status);
        $status = ($status == 1)? 'active' : 'inactive';
        return back()->with('success', trans('localize.Successfully_set_user_status_to') .$status);
    }

    public function edit($mer_id, $user_id)
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $reset_password_permission = Controller::checkAdminPermission($adm_id,'storeuserresetpassword');
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
        $input['admin_country_id_list'] = $admin_country_id_list;

        $user = StoreUserRepo::find_store_user($user_id);

        $store = StoreRepo::get_online_store_by_merchant_id($mer_id, $user_id);
        $store_country_id = isset($store['stor_country']) ? $store['stor_country'] : 0;
        $admin_country_id_list = array_merge($admin_country_id_list, array(0));

        $check_validation = in_array($store_country_id, $admin_country_id_list);
        if(!$check_validation){
            return redirect('admin')->with('denied', 'You are not authorized to access that page');
        }

        if(!$user)
            return back()->with('error', trans('localize.Store_user_not_found'));

        $check = StoreUserRepo::check_user_merchant($mer_id, $user_id);
        if(!$check)
            return back()->with('error', trans('localize.User_is_not_belongs_to_this_merchant'));
        $stores = StoreRepo::get_stores_by_merchant($mer_id);

        return view('admin.store.user.edit', compact('user','stores','mer_id','reset_password_permission'));
    }

    public function edit_submit()
    {
        if (\Request::isMethod('post')) {
            $data = \Request::all();
            // dd($data);
            $user_id = $data['user_id'];
            $mer_id = $data['mer_id'];
            $adm_id = \Auth::guard('admins')->user()->adm_id;
            $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
            $input['admin_country_id_list'] = $admin_country_id_list;

            $stores_full_list = StoreRepo::get_stores_by_merchant2($mer_id, $user_id);

            $stores_array_full_list = array();
            $store_exception_list = array();
            $stores_array = array();
            $final_store_list = array();

            for($row=0; $row<sizeof($stores_full_list); $row++){
                $stores_array_full_list[$row] = $stores_full_list[$row]['stor_id'];
            }

            $stores = StoreRepo::get_stores_by_merchant2($mer_id, $user_id, $input);

            for($row=0; $row<sizeof($stores); $row++){
                $stores_array[$row] = $stores[$row]['stor_id'];
            }

            for($row=0; $row<sizeof($stores_array_full_list); $row++){
                if(in_array($stores_array_full_list[$row], $stores_array) == false){
                    $store_exception_list[$row] = $stores_array_full_list[$row];
                }
            }

            $final_store_list = array_merge($store_exception_list,isset($data['store_list']) ? $data['store_list'] : array());
            $data['store_list'] = $final_store_list;

            \Validator::make($data, [
                'username' => 'required|username|min:4|unique:nm_store_users,username,'.$user_id.',id',
                'name' => 'required',
                'email' => 'nullable|email|unique:nm_store_users,email,'.$user_id.',id',
	        ],[
                'name.required' => trans('localize.Name_is_required'),
            ])->validate();

            $check = StoreUserRepo::check_user_merchant($mer_id, $user_id);
            if(!$check)
                return back()->with('error', trans('localize.User_is_not_belongs_to_this_merchant'));

            if(isset($data['store_list']))
                $update = StoreUserRepo::update_user_store_mapping($data['store_list'], $user_id);

            if(isset($data['store_list']) == false && $data['exist'] == 1)
                $remove = StoreUserRepo::remove_store_user_mapping(null, $user_id);

            StoreUserRepo::update_store_user($user_id, $data);

            return back()->with('success', trans('localize.Successfully_update_store_user'));
        }
    }

    public function reset_password()
    {
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();

            $user_id = $data['user_id'];
            $mer_id = $data['mer_id'];

           \Validator::make($data, [
                'password' => 'required|min:6|confirmed',
            ],[
                'password.required' => trans('localize.Please_fill_password_field'),
                'password.confirmed' => trans('localize.Password_confirmation_does_not_match')
            ])->validate();

            $check = StoreUserRepo::check_user_merchant($mer_id, $user_id);
            if(!$check)
                return back()->with('error', trans('localize.User_is_not_belongs_to_this_merchant'));

            try {
                $user = StoreUserRepo::reset_password($user_id, $data);

                if($user->email) {
                    $this->mailer->send('front.emails.reset_storeuser_password_by_admin', ['user'=>$user,'password'=>$data['password']], function (Message $m) use ($user) {
                        $m->to($user->email, $user->name)->subject(trans('email.admin.reset_password', ['mall_name' => trans('common.mall_name')]));
                    });
                }

                return back()->with('success', trans('localize.Successfully_reset_password'));
            } catch (Exception $e) {
                return back()->with('error', $e);
            }
        }
    }
}
