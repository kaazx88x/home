<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Merchant\Controller;
use App\Repositories\StoreRepo;
use App\Repositories\StoreUserRepo;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;
use Hash;

class StoreUserController extends Controller
{
    public function __construct(Mailer $mailer) {
        $this->mailer = $mailer;

        if(\Auth::guard('merchants')->check()) {
            $this->mer_id = \Auth::guard('merchants')->user()->mer_id;
        }

        if(\Auth::guard('storeusers')->check()) {
            $this->mer_id = \Auth::guard('storeusers')->user()->mer_id;
        }
    }

    public function manage()
    {
        $mer_id = \Auth::guard('merchants')->user()->mer_id;
        $users = StoreUserRepo::get_merchant_store_user($mer_id);
        $stores = StoreRepo::get_stores_by_merchant($mer_id);
        foreach ($users as $user) {
            $user->assigned_store = StoreUserRepo::get_assigned_store_user($user->id);
        }

        return view('merchant.store.user.manage', compact('users','stores'));
    }

    public function add_submit()
    {
        $mer_id = \Auth::guard('merchants')->user()->mer_id;

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            // dd($data);
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
                    $m->to($user->email, $user->name)->subject('Account Activation');
                });
            }

            return back()->with('success', 'Successfully create store user');
        }
    }

    public function toggle_user_status($user_id, $status)
    {
        $update = StoreUserRepo::toggle_user_status($user_id,$status);
        $status = ($status == 1)? 'active' : 'inactive';
        return back()->with('success','Successfully set user status to '.$status);
    }

    public function change_password()
    {
        return view('merchant.store.user.change_password');
    }

    public function change_password_submit()
    {
        $user_id = \Auth::guard('storeusers')->user()->id;
        $user = StoreUserRepo::find_store_user($user_id);

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();

            \Validator::extend('old_password', function ($attribute, $value, $parameters, $validator) {
              return Hash::check($value, current($parameters));
            });

            $message = [
                'old_password.old_password' => trans('localize.old_password_validation'),
                'old_password.required' => trans('localize.oldpasswordInput'),
                'password.required' => trans('localize.password_error'),
                'old_password.min' => trans('localize.minpassword'),
                'password.confirmed' => trans('localize.matchpassword'),
            ];

            $rules = [
                'password' => 'required|min:6|confirmed',
                'old_password' => 'required|min:6|old_password:'.$user->password
            ];

            $validator = \Validator::make($data, $rules, $message);

            if ($validator->fails())
                return back()->withInput()->withErrors($validator);

            if ($data['password'] == $data['old_password'])
                return back()->withInput()->withErrors(['Old Password and New Password cannot be same. Please Try Again.']);

            $new_password = bcrypt($data['password']);
            $user->password= $new_password;
            $user->save();

            return back()->with('success',trans('localize.passwordupdated'));
        }
    }

    public function edit_profile()
    {
        $user = StoreUserRepo::find_store_user(\Auth::guard('storeusers')->user()->id);
        return view('merchant.store.user.edit_profile',compact('user'));
    }

    public function edit_profile_submit()
    {
        $user_id = \Auth::guard('storeusers')->user()->id;
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            \Validator::make($data, [
                'name' => 'required|max:100',
                'email' => 'required|email|max:255|unique:nm_store_users,email,'.$user_id,
            ])->validate();

            $update = StoreUserRepo::update_store_user($user_id,$data);

            return back()->with('success',trans('localize.profileupdated'));
        }
    }

    public function edit($user_id)
    {
        $mer_id = \Auth::guard('merchants')->user()->mer_id;

        $user = StoreUserRepo::find_store_user($user_id);
        if(!$user)
            return back()->with('error', trans('localize.Store_user_not_found'));

        $check = StoreUserRepo::check_user_merchant($mer_id, $user_id);
        if(!$check)
            return back()->with('error', trans('localize.User_is_not_belongs_to_this_merchant'));

        $stores = StoreRepo::get_stores_by_merchant($mer_id);

        return view('merchant.store.user.edit', compact('user','stores'));
    }

    public function edit_submit()
    {
        $mer_id = \Auth::guard('merchants')->user()->mer_id;

        if (\Request::isMethod('post')) {
            $data = \Request::all();

            $user_id = $data['user_id'];

            \Validator::make($data, [
                'username' => 'required|username|min:4|max:100|unique:nm_store_users,username,'.$user_id.',id',
                'name' => 'required',
                'email' => 'nullable|email|unique:nm_store_users,email,'.$user_id.',id',
	        ],[
                'name.required' => 'Name is required',
            ])->validate();

            $check = StoreUserRepo::check_user_merchant($mer_id, $user_id);
            if(!$check)
                return back()->with('error','User is not belongs to this merchant');

            if(isset($data['store_list']))
                $update = StoreUserRepo::update_user_store_mapping($data['store_list'], $user_id);

            if(isset($data['store_list']) == false && $data['exist'] == 1)
                $remove = StoreUserRepo::remove_store_user_mapping(null, $user_id);

            StoreUserRepo::update_store_user($user_id, $data);

            return back()->with('success', 'Successfully update store user');
        }
    }

    public function reset_password()
    {
        $mer_id = \Auth::guard('merchants')->user()->mer_id;
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();

            $user_id = $data['user_id'];

           \Validator::make($data, [
                'password' => 'required|min:6|confirmed',
            ],[
                'password.required' => 'Please fill password field',
                'password.confirmed' => 'Password confirmation does not match'
            ])->validate();

            $check = StoreUserRepo::check_user_merchant($mer_id, $user_id);
            if(!$check)
                return back()->with('error','User is not belongs to this merchant');

            try {
                $user = StoreUserRepo::reset_password($user_id, $data);

                if($user->email) {
                    $this->mailer->send('front.emails.reset_storeuser_password_by_admin', ['user'=>$user,'password'=>$data['password']], function (Message $m) use ($user) {
                        $m->to($user->email, $user->name)->subject(trans('email.admin.reset_password', ['mall_name' => trans('common.mall_name')]));
                    });
                }

                return back()->with('success', 'Successfully reset password');
            } catch (Exception $e) {
                return back()->with('error', $e);
            }
        }
    }
}
