<?php

namespace App\Http\Controllers\Api\V2\member;

use Auth;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
//use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;
use Illuminate\Http\Request;
use App\Providers\ActivationServiceProvider;
use App\Repositories\SmsRepo;
use App\Repositories\CustomerRepo;
use App\Models\Customer;
use App\Models\Wallet;
use App\Models\CustomerWallet;

class AuthController extends Controller
{
    protected $activationService;

    public function __construct(ActivationServiceProvider $activationService)
    {
        $this->activationService = $activationService;
    }

    // step 1 registration
    public function validate_tac()
    {
        $input = \Request::only(['areacode', 'phone', 'tac', 'lang']);

        if (isset($input['lang']))
        {
            \App::setLocale($input['lang']);
        }
        unset($input['lang']);

        $validator = Validator::make($input, [
            'areacode' => 'required',
            'phone' => 'required|numeric',
            'tac' => 'required|digits:6',
        ]);

        if ($validator->fails())
        {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        $check_tac = SmsRepo::check_tac(0, $input['tac'], 'tac_member_registration', 0, $input['areacode'].$input['phone']);
        if (!$check_tac) {
            return \Response::json([
                'status' => 403,
                'message' => trans('localize.tacNotMatch')
            ]);
        }

        return \Response::json([
            'status' => 200,
            'message' => trans('localize.proceed')
        ]);
    }

    public function validate_email_password()
    {
        $input = \Request::only(['name', 'email', 'password', 'password_confirmation', 'lang']);

        if (isset($input['lang']))
        {
            \App::setLocale($input['lang']);
        }
        unset($input['lang']);

        $validator = Validator::make($input, [
            'name' => 'required|max:255',
            'email' => 'nullable|email|max:255|unique:nm_customer,email',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails())
        {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        if (!preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])([a-zA-Z0-9]{6,})$/', $input['password'])) {
            return \Response::json([
                'status' => 403,
                'message' => trans('localize.passwordHint')
            ]);
        }

        return \Response::json([
            'status' => 200,
            'message' => trans('localize.proceed')
        ]);

    }

    //register
    public function register()
    {
        $input = \Request::only(['email', 'name', 'password', 'password_confirmation', 'securecode', 'securecode_confirmation', 'areacode', 'phone', 'tac', 'lang', 'identity_card', 'address_1', 'address_2', 'country', 'state', 'city', 'postalcode']);
        $input['name'] = trim($input['name']);
        $input['email'] = trim($input['email']);

        if (isset($input['lang']))
        {
            \App::setLocale($input['lang']);
        }
        unset($input['lang']);

        $validator = \Validator::make($input, [
            'name' => 'required|max:255',
            'email' => 'nullable|email|max:255|unique:nm_customer,email',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'securecode' => 'required|numeric|digits:6|confirmed',
            'securecode_confirmation' => 'required',
            'areacode' => 'required',
            'phone' => 'required|numeric|unique:nm_customer,cus_phone',
            'tac' => 'required|digits:6',
            'identity_card' => 'required',
            'address_1' => 'required',
            'country' => 'required|integer',
            'state' => 'required|integer',
            'postalcode' => 'required'

        ],[
            'email.unique' => trans('localize.email_exist'),
            'phone.unique' => trans('localize.phone_exist'),
            'password.confirmed' => trans('localize.matchpassword'),
            'securecode.confirmed' => trans('localize.securecode_notmatch'),
            'identity_card.required' => trans('localize.ic_error')
        ]);

        if ($validator->fails())
        {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        if (!preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])([a-zA-Z0-9]{6,})$/', $input['password'])) {
            return \Response::json([
                'status' => 403,
                'message' => trans('localize.passwordHint')
            ]);
        }

        try {
            // Check Tac Validation
            $check_tac = SmsRepo::check_tac(0, $input['tac'], 'tac_member_registration', 0, $input['areacode'].$input['phone']);
            if (!$check_tac) {
                return \Response::json([
                    'status' => 403,
                    'message' => trans('localize.tacNotMatch')
                ]);
            }

            $user = Customer::create([
                'email' => $input['email'],
                'password' => bcrypt($input['password']),
                'cus_name' => $input['name'],
                'phone_area_code' => $input['areacode'],
                'cus_phone' => $input['phone'],
                'cellphone_verified' => 1,
                'payment_secure_code' => \Hash::make($input['securecode']),
                'cus_joindate' => date('Y-m-d H:i:s'),
                'cus_logintype' => 2,
                'cus_status' => 1,
                'identity_card' => ((isset($input['identity_card'])) ? $input['identity_card'] : null),
                'cus_address1' => $input['address_1'],
                'cus_address2' => ((isset($input['address_2'])) ? $input['address_2'] : null),
                'cus_country' => $input['country'],
                'cus_state' => $input['state'],
                'cus_city_name' => ((isset($input['city'])) ? $input['city'] : null),
                'cus_postalcode' => $input['postalcode'],
            ]);

            // Set TAC as verified
            $check_tac = SmsRepo::check_tac(0, $input['tac'], 'tac_member_registration', 1);

            if ($user->email) {
                $this->activationService->sendActivationMail($user);
            }

            // create wallet
            $wallets = Wallet::get();
            foreach ($wallets as $wallet) {
                CustomerWallet::create([
                    'customer_id' => $user->cus_id,
                    'wallet_id' => $wallet->id,
                    'credit' => 0
                ]);
            }

            return \Response::json([
                'status' => 200,
                'message' => 'Member registration success.',
                'member_id' => $user->cus_id,
                'member_name' => $user->cus_name,
            ]);
        } catch(Exception $e) {
            return \Response::json([
                'status' => 500,
                'message' => trans('localize.member_registration') . trans('api.fail'),
            ], 500);
        }
    }

    public function register_tac()
    {
        $input = \Request::only(['areacode', 'phone', 'lang']);

        if (isset($input['lang']))
        {
            \App::setLocale($input['lang']);
        }
        unset($input['lang']);

        $validator = Validator::make($input, [
            'areacode' => 'required',
            'phone' => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        $check_exist = Customer::where('phone_area_code', $input['areacode'])->where('cus_phone', $input['phone'])->first();

        if ($check_exist) {
            return \Response::json([
                'status' => 403,
                'message' => trans('localize.phone_exist')
            ]);
        }

        $phone = $input['areacode'] . $input['phone'];

        $tac = SmsRepo::create_tac_log('tac_member_registration', 0, 'sms', $phone);

        return \Response::json([
            'status' => 200,
            'message' => $tac['message'],
            'expired_at' => $tac['expired_at'],
        ]);
    }

    //login
    public function login()
    {
        $input = \Request::only(['email', 'password', 'lang']);
        $input['email'] = trim($input['email']);
        $input['password'] = trim($input['password']);

        if (isset($input['lang']))
        {
            \App::setLocale($input['lang']);
        }
        unset($input['lang']);
        $niceNames = array(
            'email' => trans('api.email'),
            'password' => trans('api.password')
        );

        $validator = Validator::make($input, [
            'email' => 'required|email|max:150',
            'password' => 'required|max:50'
        ]);
        $validator->setAttributeNames($niceNames);

        if ($validator->fails())
        {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        $app_session_key = substr(str_shuffle(md5(time())), 0, 20);

        if (\Auth::attempt($input))
        {
            $user = \Auth::guard('web')->user();
            $member = Customer::leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_customer.cus_country')->where('cus_id', $user->cus_id)->first();
            if (isset($member))
            {
                if ($member->email_verified) {
                    if ((!$member->identity_card && ($member->phone_area_code != 86)) || !$member->update_flag) {
                        return \Response::json([
                            'status' => 200,
                            'message' => trans('localize.mobile_update_message')
                        ]);
                    }

                    $member->app_session = $app_session_key;
                    $member->app_session_date = date('Y-m-d H:i:s');
                    $member->api_token = str_random(60);
                    $member->save();

                    $cus_wallets = CustomerRepo::get_customer_available_wallet($member->cus_id);
                    $wallets = [];
                    foreach ($cus_wallets as $cw) {
                        $wallets[$cw->wallet->name] = $cw->credit;
                    }

                    return \Response::json([
                        'status' => 200,
                        'member_id' => $member->cus_id,
                        'member_name' => $member->cus_name,
                        'member_avatar' => ($member->cus_pic) ? env('IMAGE_DIR') . '/avatar/' . $member->cus_id . '/' . $member->cus_pic : '',
                        'member_credit_balance' => $member->v_token,
                        'member_special_wallet' => $member->special_wallet,
                        'country_code' => $member->co_code,
                        'app_session' => $member->app_session,
                        'api_token' => $member->api_token,
                        'member_wallet' => $wallets
                    ]);
                }

                return \Response::json([
                    'status' => 403,
                    'message' => trans('localize.email_not_verified')
                ]);
            }
        }

        return \Response::json([
            'status' => 403,
            'message' => trans('api.login') . trans('api.fail')
        ]);
    }

     public function phone_login()
    {
        $input = \Request::only(['areacode', 'phone', 'password', 'lang']);

        if (isset($input['lang']))
        {
            \App::setLocale($input['lang']);
        }
        unset($input['lang']);

        $validator = Validator::make($input, [
            'areacode' => 'required',
            'phone' => 'required|numeric',
            'password' => 'required|max:50'
        ]);

        if ($validator->fails())
        {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }


        $app_session_key = substr(str_shuffle(md5(time())), 0, 20);

        $member = Customer::where('phone_area_code', $input['areacode'])->where('cus_phone', $input['phone'])->first();

        if (isset($member))
        {
            if (!\Hash::check(trim($input['password']), $member->password)) {
                return \Response::json([
                    'status' => 403,
                    'message' => trans('localize.login_invalid')
                ]);
            }

            if ($member->cellphone_verified) {
                if ((!$member->identity_card && ($member->phone_area_code != 86)) || !$member->update_flag) {
                    return \Response::json([
                        'status' => 200,
                        'message' => trans('localize.mobile_update_message')
                    ]);
                }

                $member->app_session = $app_session_key;
                $member->app_session_date = date('Y-m-d H:i:s');
                $member->api_token = str_random(60);
                $member->save();

                $cus_wallets = CustomerRepo::get_customer_available_wallet($member->cus_id);
                $wallets = [];
                foreach ($cus_wallets as $cw) {
                    $wallets[$cw->wallet->name] = $cw->credit;
                }

                return \Response::json([
                    'status' => 200,
                    'member_id' => $member->cus_id,
                    'member_name' => $member->cus_name,
                    'member_avatar' => ($member->cus_pic) ? env('IMAGE_DIR') . '/avatar/' . $member->cus_id . '/' . $member->cus_pic : null,
                    'member_credit_balance' => $member->v_token,
                    'member_special_wallet' => $member->special_wallet,
                    'country_code' => $member->co_code,
                    'app_session' => $member->app_session,
                    'api_token' => $member->api_token,
                    'member_wallet' => $wallets
                ]);
            }

            return \Response::json([
                'status' => 403,
                'message' => trans('localize.phone_not_verified')
            ]);
        }

        return \Response::json([
            'status' => 403,
            'message' => trans('api.member') . trans('api.notFound')
        ]);
    }

    //logout
    public function logout()
    {
        $member = \Auth::guard('api_members')->user();
        if (isset($member)) {
            $member->api_token = null;
            $member->save();

            return \Response::json([
                'status' => 200,
                'message' => 'member logout success'
            ]);
        }

        return \Response::json([
            'status' => 403,
            'message' => 'member logout fail'
        ]);
    }

    //forget password
    public function forget_password(Request $request)
    {
        $request = \Request::only(['email']);
        $validator = Validator::make($request, [
            'email' => 'required|email'
        ]);
        $niceNames = array(
            'email' => trans('api.email')
        );
        $validator->setAttributeNames($niceNames);
        if ($validator->fails())
        {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        $broker = $this->getBroker();

        $response = Password::broker($broker)->sendResetLink($request, function (Message $message) {
            $message->subject('Your Password Reset Link');
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return \Response::json([
                    'status' => 200,
                    'message' => 'Password reset link is sent'
                ]);
            case Password::INVALID_USER:
            default:
                return \Response::json([
                'status' => 403,
                'message' => 'Invalid member'
            ]);
        }
    }

    public function getBroker()
    {
        return property_exists($this, 'broker') ? $this->broker : null;
    }
}
