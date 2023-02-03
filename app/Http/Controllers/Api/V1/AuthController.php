<?php

namespace App\Http\Controllers\Api\V1;

use Validator;
use App\Http\Controllers\Controller;
// use Illuminate\Foundation\Auth\ThrottlesLogins;
// use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Merchant;
use App\Models\Customer;
use App;
use App\Repositories\MerchantRepo;
use App\Repositories\StoreRepo;
use App\Repositories\StoreUserRepo;

class AuthController extends Controller
{
    // use AuthenticatesAndRegistersUsers,
    //     ThrottlesLogins;
    use AuthenticatesUsers;

    public function merchantLogin()
    {
        $input = \Request::only(['username', 'password', 'lang', 'type']);

        // trim username
        $input['username'] = trim($input['username']);
        $input['password'] = trim($input['password']);
        $input['type'] = (isset($input['type'])) ? trim($input['type']) : 1;

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
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
            $intErrors = $validator->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];
            // foreach ($intErrors as $item => $arrMessages) {
            //     foreach ($arrMessages as $key => $value) {
            //         array_push($errors['message'], $value);
            //     }
            // }

            return $errors;
        }

        // added merchant type as additional criterion for login (mer_type = 1 for online merchant)
        // $input['mer_type'] = 1;
        $merchant = null;

        if($input['type'] == 1) {
            unset($input['type']);
            if (\Auth::guard('merchants')->attempt($input))
            {
                $merchant = MerchantRepo::get_merchant_by_username($input['username']);

                if ($merchant)
                {
                    $stores = StoreRepo::api_get_stores($merchant->mer_id);
                    return \Response::json([
                        'status' => 1,
                        'merchant_id' => $merchant->mer_id,
                        'merchant_name' => $this->getMerchantFullName($merchant),
                        'merchant_currency' => $merchant->co_curcode,
                        'merchant_vcoin_balance' => $merchant->mer_vtoken,
                        'stores' => $stores,
                    ]);
                }
            }
        } else if($input['type'] == 2) {
            unset($input['type']);
            if (\Auth::guard('storeusers')->attempt($input))
            {
                $merchant = StoreUserRepo::get_merchant_by_storeuser_username($input['username']);

                if ($merchant)
                {
                    $stores = StoreRepo::api_get_storeusers_stores($merchant->storeuser_id, $merchant->mer_id);
                    return \Response::json([
                        'status' => 1,
                        'merchant_id' => $merchant->mer_id,
                        'merchant_name' => $this->getMerchantFullName($merchant),
                        'merchant_currency' => $merchant->co_curcode,
                        'merchant_vcoin_balance' => $merchant->mer_vtoken,
                        'stores' => $stores,
                    ]);
                }
            }
        }

        return \Response::json([
            'status' => 0,
            'message' => trans('api.login') . trans('api.fail')
        ]);
    }

//    public function merchantLogout()
//    {
//        $merchant = \Auth::guard('api:merchants')->user();
//        if (isset($merchant)) {
////            $merchant->api_token = null;
////            $merchant->save();
//
//            return \Response::json([
//                'status' => 1,
//                'message' => 'merchant logout success'
//            ]);
//        }
//    }

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

    public function memberLogin()
    {
        $input = \Request::only(['email', 'password', 'lang']);

        // trim email
        $input['email'] = trim($input['email']);
        $input['password'] = trim($input['password']);

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $niceNames = array(
            'email' => trans('api.email'),
            'password' => trans('api.password')
        );

        $validator = Validator::make($input, [
            'email' => 'required|email|max:150',
            'password' => 'required|max:50'
        ]);
        $validator->setAttributeNames($niceNames);

        if ($validator->fails()) {
            $intErrors = $validator->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        $app_session_key = substr(str_shuffle(md5(time())), 0, 20);

        if(\Auth::attempt($input)) {
             $member = Customer::leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_customer.cus_country')->where('email', $input['email'])->first();
             if (isset($member)) {
                 $member->app_session = $app_session_key;
                 $member->app_session_date = date('Y-m-d H:i:s');
                 $member->save();

                 return \Response::json([
                    'status' => 1,
                    'member_id' => $member->cus_id,
                    'member_name' => $member->cus_name,
                    'member_vcoin_balance' => $member->v_token,
                    'country_code' => $member->co_code,
                    'app_session' => $member->app_session,
                ]);
             }
        }
//        else
//        {
//            $member = Customer::oldPassword($input)->first();
//
//            if (isset($member)) {
//                $member->password = \Hash::make($input['password']);
//                $member->app_session = $app_session_key;
//                $member->app_session_date = date('Y-m-d H:i:s');
//                $member->save();
//
//                return \Response::json([
//                    'status' => 1,
//                    'member_id' => $member->cus_id,
//                    'member_name' => $member->cus_name,
//                    'member_vcoin_balance' => $member->v_token,
//                    'country_code' => $member->co_code,
//                    'app_session' => $member->app_session,
//                ]);
//            }
//        }

        return \Response::json([
            'status' => 0,
            'message' => trans('api.login') . trans('api.fail')
        ]);
    }

//    public function memberLogout()
//    {
//        $member = \Auth::guard('api:members')->user();
//        if (isset($member)) {
//            $member->api_token = null;
//            $member->save();
//
//            return \Response::json([
//                'status' => 1,
//                'message' => 'member logout success'
//            ]);
//        }
//
//        return \Response::json([
//            'status' => 0,
//            'message' => 'member logout fail'
//        ]);
//    }

}