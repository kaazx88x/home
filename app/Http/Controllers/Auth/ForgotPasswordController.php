<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Auth\Passwords\DatabaseTokenRepository as TokenRepo;
use Illuminate\Support\Facades\Password;
use App\Repositories\CountryRepo;
use App\Repositories\SmsRepo;
use Illuminate\Http\Request;
use App\Models\Customer;
use Carbon\Carbon;
use DB;
use Hash;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm()
    {
        $countries = CountryRepo::get_all_countries();
        return view('auth.passwords.email', compact('countries'));
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);
        $customer = Customer::where('email', $request->email)->first();
        if($customer && !$customer->email_verified || !$customer->cus_status)
            return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans('localize.only_active_account_and_verified_email_reset')]);

        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }

    public function sendResetLinkPhone(Request $request)
    {
        $this->validatePhone($request);
        $phone = $request->areacode.$request->phone;

        $customer = Customer::where('phone_area_code', $request->areacode)
        ->where('cus_phone', $request->phone)
        ->first();

        if($customer && !$customer->cellphone_verified || !$customer->cus_status)
            return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans('localize.only_active_account_and_verified_phone_reset')]);

        // Check Tac Validation
        $check_tac = SmsRepo::check_tac(0, $request->tac, 'tac_member_password_reset', 1, $phone);
        if (!$check_tac) {
            return back()->withInput()->with('error', trans('localize.tacNotMatch'));
        }

        $reset_token = strtolower(str_random(64));
        DB::table('password_resets')->where('phone', $phone)->delete();
        DB::table('password_resets')->insert([
            'phone' => $phone,
            'token' => Hash::make($reset_token),
            'created_at' => Carbon::now('UTC'),
        ]);

        return redirect('password/phone/reset/'.$phone.'/'.$reset_token);
    }

    public function validatePhone($request)
    {
        $this->validate($request, [
            'areacode' => 'required',
            'phone' => 'required|numeric',
            'tac' => 'required|digits:6',
        ]);
    }
}
