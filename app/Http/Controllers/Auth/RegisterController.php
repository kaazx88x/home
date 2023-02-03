<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Providers\ActivationServiceProvider;
use App\Models\Customer;
use App\Models\SecurityQuestion;
use App\Repositories\CountryRepo;
use App\Repositories\SmsRepo;
use App\Models\Wallet;
use App\Models\CustomerWallet;
use Auth;

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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ActivationServiceProvider $activationService)
    {
        $this->middleware('guest');
        $this->activationService = $activationService;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @param  array  $niceNames
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data, array $niceNames, array $customMessages)
    {
        return Validator::make($data, [
            'cus_email' => 'nullable|email|max:255|unique:nm_customer,email',
            'cus_password' => 'required|min:6|confirmed|password',
            'cus_password_confirmation' => 'required',
            'cus_name' => 'required|max:255',
            'securecode' => 'required|numeric|digits:6|confirmed',
            'securecode_confirmation' => 'required',
            'areacode' => 'required',
            'phone' => 'required|numeric',
            'tac' => 'required|digits:6',
            'identity_card' => 'required',
            'address1' => 'required',
            'zipcode' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
        ], $customMessages, $niceNames);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return Customer::create([
            'email' => $data['cus_email'],
            'password' => bcrypt($data['cus_password']),
            'cus_name' => $data['cus_name'],
            'phone_area_code' => $data['areacode'],
            'cus_phone' => $data['phone'],
            'cellphone_verified' => 1,
            'payment_secure_code' => \Hash::make($data['securecode']),
            'cus_joindate' => date('Y-m-d H:i:s'),
            'cus_logintype' => 2,
            'cus_status' => 1,
            'identity_card' => $data['identity_card'],
            'cus_address1' => $data['address1'],
            'cus_address2' => $data['address2'],
            'cus_country' => $data['country'],
            'cus_state' => $data['state'],
            'cus_city_name' => $data['city'],
            'cus_postalcode' => $data['zipcode'],
        ]);
    }

    public function register()
    {
        $data = request()->all();

        $niceNames = [
            'cus_email' => trans('localize.email'),
            'cus_password' => trans('localize.password'),
            'cus_password_confirmation' => trans('localize.confirmPassword'),
            'cus_name' => trans('localize.name'),
            'securecode' => trans('localize.code'),
            'securecode_confirmation' => trans('localize.confirmcode'),
            'areacode' => trans('localize.areacode'),
            'phone' => trans('localize.cellphone'),
            'tac' => trans('localize.smsverificationcode'),
            'identity_card' => trans('localize.ic_number'),
        ];

        $customMessages = [];

        $validator = $this->validator($data, $niceNames, $customMessages);

        // Check Validation
        if ($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        // Check Tac Validation
        $check_tac = SmsRepo::check_tac(0, $data['tac'], 'tac_member_registration', 0);
        if (!$check_tac) {
            return back()->withInput()->with('warning', trans('localize.tacNotMatch'));
        }

        $user = $this->create($data);

        // create wallet
        $wallets = Wallet::get();
        foreach ($wallets as $wallet) {
            CustomerWallet::create([
                'customer_id' => $user->cus_id,
                'wallet_id' => $wallet->id,
                'credit' => 0
            ]);
        }


        // Set TAC as verified
        $check_tac = SmsRepo::check_tac(0, $data['tac'], 'tac_member_registration', 1);

        if ($data['cus_email']) {
            $this->activationService->sendActivationMail($user);
        }

        Auth::login($user);
        return redirect('profile')->with('status', trans('localize.member.registration_success'));
    }

    public function showRegistrationForm()
    {
        $countries = CountryRepo::get_all_countries();

        return view('auth.register', compact('countries'));
    }
}
