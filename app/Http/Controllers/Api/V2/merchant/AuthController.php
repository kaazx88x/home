<?php

namespace App\Http\Controllers\Api\V2\merchant;

use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Controller;
use App\Repositories\StoreUserRepo;
use App\Repositories\MerchantRepo;
use App\Repositories\StoreRepo;
use Validator;
use Auth;

class AuthController extends Controller
{
    public function login()
    {
        $input = \Request::only(['username', 'password', 'lang', 'type']);

        $input['username'] = trim($input['username']);
        $input['password'] = trim($input['password']);
        $input['type'] = (isset($input['type'])) ? trim($input['type']) : 1;

        if (isset($input['lang'])) {
            \App::setLocale($input['lang']);
        }
        unset($input['lang']);

        $niceNames = array(
            'username' => trans('api.username'),
            'password' => trans('api.password')
        );

        $validator = Validator::make($input, [
            'username' => 'required|alpha_num|max:150',
            'password' => 'required|max:50'
        ]);
        $validator->setAttributeNames($niceNames);

        if ($validator->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ], 422);
        }

        $app_session_key = substr(str_shuffle(md5(time())), 0, 20);

        $attemptLogin = [
            'username' => $input['username'],
            'password' => $input['password'],
        ];

        //merchant login
        if ($input['type'] == 1 && \Auth::guard('merchants')->attempt($attemptLogin)) {

            $merchant = MerchantRepo::get_merchant_by_username($input['username']);
            if ($merchant) {

                $merchant->app_session = $app_session_key;
                $merchant->app_session_date = date('Y-m-d H:i:s');
                $merchant->api_token = str_random(60);
                $merchant->save();

                $stores = StoreRepo::api_get_stores($merchant->mer_id);
                return \Response::json([
                    'status' => 200,
                    'merchant_id' => $merchant->mer_id,
                    'merchant_name' => $this->getMerchantFullName($merchant),
                    'merchant_email' => $merchant->email,
                    'merchant_currency' => $merchant->co_curcode,
                    'merchant_credit_balance' => $merchant->mer_vtoken,
                    'stores' => $stores,
                    'app_session' => $merchant->app_session,
                    'api_token' => $merchant->api_token,
                ]);
            }
        } else if($input['type'] == 2 && \Auth::guard('storeusers')->attempt($attemptLogin)) {

            $storeuser = StoreUserRepo::get_storeuser_by($input['username']);
            $merchant = StoreUserRepo::get_merchant_by_storeuser_username($input['username']);

            if ($merchant) {

                $storeuser->app_session = $app_session_key;
                $storeuser->app_session_date = date('Y-m-d H:i:s');
                $storeuser->api_token = str_random(60);
                $storeuser->save();

                $stores = StoreRepo::api_get_storeusers_stores($merchant->storeuser_id, $merchant->mer_id);
                return \Response::json([
                    'status' => 200,
                    'merchant_id' => $merchant->mer_id,
                    'merchant_name' => $this->getMerchantFullName($merchant),
                    'merchant_email' => $merchant->email,
                    'merchant_currency' => $merchant->co_curcode,
                    'merchant_credit_balance' => $merchant->mer_vtoken,
                    'stores' => $stores,
                    'app_session' => $storeuser->app_session,
                    'api_token' => $storeuser->api_token,
                ]);
            }
        }

        return \Response::json([
            'status' => 403,
            'message' => trans('api.login') . trans('api.fail')
        ], 403);
    }

    public function logout()
    {
        if(\Auth::guard('api_merchants')->check()) {
            $user = \Auth::guard('api_merchants')->user();
        } else if (\Auth::guard('api_storeusers')->check()) {
            $user = \Auth::guard('api_storeusers')->user();
        }

        if (isset($user)) {
            $user->api_token = null;
            $user->save();

            return \Response::json([
                'status' => 200,
                'message' => 'logout success'
            ]);
        }

        return \Response::json([
            'status' => 403,
            'message' => 'logout fail'
        ], 403);
    }

    private function getMerchantFullName($merchant) {
        $merchantName = null;
        if (isset($merchant->mer_fname)) {
            $merchantName = trim($merchant->mer_fname);
        }
        if (isset($merchant->mer_lname)) {
            $merchantName .= ' ' . trim($merchant->mer_lname);
        }

        return $merchantName;
    }
}
