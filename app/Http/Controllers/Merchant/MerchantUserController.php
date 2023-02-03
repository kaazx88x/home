<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Merchant\Controller;
use App\Models\Merchant;
use Illuminate\Support\Facades\Password;
use DB;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use App\Providers\MerchantActivationServiceProvider;
use App\Repositories\MerchantRepo;
use Validator;
use Hash;
use App\Repositories\StoreRepo;
use App\Repositories\CityRepo;
use App\Repositories\CountryRepo;
use App\Repositories\S3ClientRepo;
use App\Repositories\StateRepo;

class MerchantUserController extends Controller
{
    public function __construct(MerchantActivationServiceProvider $activationService, MerchantRepo $merchantrepo, StoreRepo $storerepo, CityRepo $cityrepo, CountryRepo $countryrepo, StateRepo $staterepo)
    {
        $this->activationService = $activationService;
        $this->merchant = $merchantrepo;
        $this->store = $storerepo;
        $this->city = $cityrepo;
        $this->country = $countryrepo;
        $this->state = $staterepo;
    }

    public function index()
    {
        return view('merchant.merchantuser.list', ['merchantusers'=>Merchant::all()]);
    }

    public function create()
    {
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $niceNames = array(
                'name' => 'Name',
                'email' => 'Email',
                'username' => 'Username',
                );
            $v = \Validator::make($data, [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:merchants',
                'username' => 'required|username|min:4|max:100|unique:merchants',
            ]);
            $v->setAttributeNames($niceNames);
            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $merchant = $this->merchant->create($data);

            //send email to new merchant user
            //$token = str_random(64);
            //$token = app('auth.password.tokens')->create($merchant);
            $token = Password::broker('merchants')->createToken($merchant);
            \Mail::send('merchant.emails.newmerchant', ['merchant' => $merchant, 'token'=>$token], function ($message) use ($merchant) {
                $message->to($merchant->email);
                $message->subject('Welcome To Admin of '.config('app.name'));
              });

            return \redirect('merchant/merchant-user')->with('success',trans('localize.accountcreated'));
        }

        return view('merchant.merchantuser.create');
    }

    public function edit($id)
    {
        $merchant = $this->merchant->find_merchant($id);
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $niceNames = array(
                'name' => 'Name',
                'email' => 'Email',
                'username' => 'Username',
                );
            $v = \Validator::make($data, [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:merchants,email,'.$merchant->id,
                'username' => 'required|username|min:4|max:100|unique:merchants,username,'.$merchant->id,
            ]);
            $v->setAttributeNames($niceNames);
            if ($v->fails())
               return back()->withInput()->withErrors($v);

            $merchant->name= $data['name'];
            $merchant->email= $data['email'];
            $merchant->username= $data['username'];
            $merchant->status= $data['status'];
            $merchant->save();
            return view('merchant.merchantuser.edit')->withMerchant($merchant)->withSuccess(trans('localize.userupdated'));
        }
        return view('merchant.merchantuser.edit')->withMerchant($merchant);
    }

    public function get_merchant_profile()
    {
        $mer_id = \Auth::guard('merchants')->user()->mer_id;
        $merchant = $this->merchant->get_merchant($mer_id);
    	$country = $this->country->get_all_countries();
        $state = $this->state->get_states();

        return view('merchant.profile.view', compact('merchant','country','state'));
    }

    public function edit_merchant_profile()
    {
        $mer_id = \Auth::guard('merchants')->user()->mer_id;
        $merchant = $this->merchant->get_merchant($mer_id);
        $country_details = $this->country->get_all_countries();

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            //var_dump($data);exit;
            $v = \Validator::make($data,
            [
                'fname' => 'required',
                'lname' => 'required',
                'office_number' => 'required',
                'address1' => 'required',
                'country' => 'required',
                'state' => 'required',
                'mer_city_name' => 'required',
            ],[
                'fname.required' => trans('localize.fnamerequired'),
                'lname.required' => trans('localize.lnamerequired'),
                'email.required' => trans('localize.emailrequired'),
                'phone.required' => trans('localize.phonerequired'),
                'office_number.required' => trans('localize.office_number_required'),
                'address1.required' => trans('localize.address1required'),
                'country.required' => trans('localize.countryrequired'),
                'state.required' => trans('localize.staterequired'),
                'mer_city_name.required' => trans('localize.cityrequired'),
                'bank_holder.required' => trans('localize.bankholderrequired'),
                'bank_acc.required' => trans('localize.bankaccrequired'),
                'bank_name.required' => trans('localize.banknamerequired'),
                'bank_country.required' => trans('localize.bankcountryrequired'),
                'bank_address.required' => trans('localize.bankaddrequired'),
                'username.required' => trans('localize.username_error'),
                'username.unique' => trans('localize.username_exist'),
                'username.alpha_num' => trans('localize.username_invalid_character'),
            ]);

            if ($v->fails())
                return back()->withInput()->withErrors($v);
            $data['commission'] = $merchant->mer_commission;
            $merchant = $this->merchant->update_merchant_profile_details($mer_id, $data);
            \Cookie::queue(\Cookie::forget('update_username'));

            return redirect('merchant/profile/edit')->with('status',trans('localize.profileupdated'));
        }

        return view('merchant.profile.edit', compact('merchant','country_details'));
    }

    public function update_password()
    {
        $mer_id = \Auth::guard('merchants')->user()->mer_id;
        $merchant = $this->merchant->get_merchant($mer_id);

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
                'old_password' => 'required|min:6|old_password:'.$merchant->password
            ];

            $validator = Validator::make($data, $rules, $message);

            if ($validator->fails())
                return back()->withInput()->withErrors($validator);

            if ($data['password'] == $data['old_password'])
                return back()->withInput()->withErrors(['Old Password and New Password cannot be same. Please Try Again.']);

            $new_password = bcrypt($data['password']);
            $merchant->password= $new_password;
            $merchant->update_password = 0;
            $merchant->save();
            return view('merchant.profile.change_password')->with('success',trans('localize.passwordupdated'));
        }
        return view('merchant.profile.change_password');
    }

    public function merchant_credit_log()
    {
        $mer_id = \Auth::guard('merchants')->user()->mer_id;
        $input = \Request::only('id', 'start', 'end', 'status', 'sort');
        $status_list = array(
            '' => trans('localize.all'),
            '1' => trans('localize.order'),
            '2' => trans('localize.order_offline'),
            '3' => trans('localize.withdraw'),
        );

        $logs = $this->merchant->get_merchant_vtoken_log($mer_id, $input);
        return view('merchant.profile.credit_log', compact('input','status_list','logs'));
    }
}
