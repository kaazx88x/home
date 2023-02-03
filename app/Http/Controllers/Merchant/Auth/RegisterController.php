<?php

namespace App\Http\Controllers\Merchant\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Providers\MerchantActivationServiceProvider;
use App\Repositories\CountryRepo;
use App\Repositories\MerchantRepo;
use App\Repositories\StoreRepo;
use App\Repositories\S3ClientRepo;
use App\Models\Merchant;
use App\Models\AdminSetting;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/merchant';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MerchantActivationServiceProvider $activationService, CountryRepo $countryRepo, MerchantRepo $merchantRepo, StoreRepo $storeRepo)
    {
        $this->middleware('guest:merchants');

        // repo
        $this->activationService = $activationService;
        $this->countryRepo = $countryRepo;
        $this->merchantRepo = $merchantRepo;
        $this->storeRepo = $storeRepo;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|password|confirmed',
            'mer_phone' => 'required|numeric',
            'mer_fname' => 'required',
            'mer_lname' => 'required',
            'mer_address1' => 'required',
            'mer_co_id' => 'required',
            'mer_state' => 'required',
            'mer_city_name' => 'required',
            'zipcode' => 'required|numeric',
            'tel' => 'required|numeric',
            'stor_name' => 'required',
            'stor_website' => 'required',
            'stor_address1' => 'required',
            'stor_state' => 'required',
            'stor_country' => 'required',
            'stor_city_name' => 'required',
            'stor_phone' => 'required',
            'bank_acc_name' => 'required',
            'bank_acc_no' => 'required|numeric',
            'bank_country' => 'required',
            'bank_address' => 'required',
            'latitude' => 'required_if:stor_type,1',
            'longtitude' => 'required_if:stor_type,1',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $setting = AdminSetting::first();

        return Merchant::create([
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
            'bank_gst' => $data['bank_gst'],
            'bank_name' => $data['bank_name'],
            'bank_country' => $data['bank_country'],
            'bank_address' => $data['bank_address'],
            'bank_swift' => $data['bank_swift'],
            'bank_europe' => $data['bank_europe'],
            'mer_type' => $data['stor_type'],
            'mer_platform_charge' => ($data['stor_type'] == 1) ? $setting->offline_platform_charge : $setting->platform_charge,
            'mer_service_charge' => ($data['stor_type'] == 1) ? $setting->offline_service_charge : $setting->service_charge
        ]);
    }

    public function showRegistrationForm()
    {
        $country_default =  session('countryid');
        $countries = $this->countryRepo->get_all_countries();

        return view('merchant.auth.register', compact('countries','country_default'));
    }

    public function register()
    {
        $data = \Request::all();

        $v = Validator::make($data, [
            'username' => 'required|alpha_num|min:4|max:100|unique:nm_merchant,username',
            'email' => 'required|email|unique:nm_merchant,email',
            'password' => 'required|min:6|confirmed|password',

            'fname' => 'required',
            'lname' => 'required',
            'address1' => 'required',
            'tel' => 'required',
            'office_number' => 'required',
            'country' => 'required|integer',
            'state' => 'required|integer',
            'mer_city_name' => 'required',

            'stor_type' => 'required|in:0,1',
            'stor_name' => 'required',
            'stor_phone' => 'required',
            'stor_office_number' => 'required',
            'stor_address1' => 'required',
            'stor_zipcode' => 'required',
            'stor_country' => 'required|integer',
            'stor_state' => 'required|integer',
            'stor_city_name' => 'required',
            'longtitude' => 'required_if:stor_type,1',
            'latitude' => 'required_if:stor_type,1',
            'stor_category' => 'required_if:stor_type,1',

            'bank_acc_name' => 'required',
            'bank_acc_no' => 'required',
            'bank_name' => 'required',
            'bank_country' => 'required|integer',
            'bank_address' => 'required',
            'stor_img' => 'required|mimes:jpeg,jpg,png|max:1000',

            // 'guarantor_name' => 'required',
            // 'guarantor_username' => 'required',
            // 'guarantor_phone' => 'required',
            // 'guarantor_email' => 'required|email',
            // 'guarantor_bank_name' => 'required',
            // 'guarantor_acc_name' => 'required',
            // 'guarantor_bank_acc' => 'required',
        ])->validate();

        $merchant_email = $data['email'];

        try {
            $check_merchant_email = $this->merchantRepo->check_existing_merchant_email($merchant_email);

            if(!$check_merchant_email->isEmpty()) {
                throw new \Exception;
            }

            $merchant = $this->create($data);
            $inserted_merchant_id = $merchant->mer_id;

            // Upload Image
            $store_image = '';
            if (!empty($data['stor_img'])) {
                $upload_file = $data['stor_img']->getClientOriginalName();
                $file_detail = explode('.', $upload_file);
                $store_image = date('Ymd').'_'.str_random(4).'.'.$file_detail[1];
                $path = 'store/'.$inserted_merchant_id;

                if(@file_get_contents($data['stor_img']) && !S3ClientRepo::IsExisted($path, $store_image))
                    $upload = S3ClientRepo::Upload($path, $data['stor_img'], $store_image);
            }

            $merchant_store = $this->storeRepo->add_store($data, $inserted_merchant_id, $store_image);

            $merchant = $this->merchantRepo->get_merchant($inserted_merchant_id);

            $merchant_guarantor = MerchantRepo::create_update_guarantor($inserted_merchant_id, $data);
            $merchant_referrer = MerchantRepo::create_update_referrer($inserted_merchant_id, $data);

            if ($data['stor_type'] == 1) {
                if(empty(json_decode($data['stor_category']))) {
                    return redirect('/merchant/register-failed');
                }

                $cats = json_decode($data['stor_category']);
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

            $this->activationService->sendActivationMail($merchant);

            $type = 'success';
            $title = trans('localize.mer_registration_success_title');
            $msg = trans('localize.mer_registration_success_msg');

            return view('merchant.generic')->with(compact('type', 'title', 'msg', 'merchant'));
        } catch (\Exception $e) {
            $type = 'danger';
            $title = trans('localize.mer_registration_failed_title');
            $msg = trans('localize.mer_registration_failed_msg');
            logger()->error($e);

            return redirect('merchant/register')->withInput()->with('error', $msg);
            //return view('merchant.auth.register')->with(compact('type', 'title', 'msg'));
        }
    }

    public function ajaxMerchantUsernameCheck()
    {
        $input = \Request::only('username', 'merid');

        $checkusername = Merchant::where('username', '=', $input['username']);

        if (isset($input['merid']))
            $checkusername = $checkusername->where('mer_id', '!=', $input['merid']);

        return $checkusername = $checkusername->count();
    }

    public function ajaxMerchantEmailCheck()
    {
        $input = \Request::only('email', 'merid');

        $checkemail = Merchant::Where(function ($query) use ($input) {
            $query->where('email', $input['email']);
        });

        if (isset($input['merid']))
            $checkemail = $checkemail->where('mer_id', '!=', $input['merid']);

        return $checkemail = $checkemail->count();
    }

}
