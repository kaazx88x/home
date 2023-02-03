<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\TacVerificationLog;
use Carbon\Carbon;
use DB;
use Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showResetPhoneForm(Request $request, $phone = null, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'phone' => $phone]
        );
    }

    public function resetPhone(Request $request, $phone)
    {
        $this->validate($request, $this->rulesPhone(), $this->validationErrorMessages());
        $from = Carbon::now('UTC')->subHour();
        $to = Carbon::now('UTC')->addHour();

        $reset = DB::table('password_resets')->where('phone', $phone)->whereBetween('created_at', [$from, $to])->first();

        if(!$reset || !Hash::check($request->token, $reset->token))
            return back()->with('error', trans('localize.invalid_token'));

        DB::table('password_resets')->where('phone', $phone)->delete();
        $customer = Customer::where(DB::raw('CONCAT(phone_area_code, cus_phone)'), '=', $phone)->first();
        $this->resetPassword($customer, $request->password);

        return redirect('/profile');
    }

    protected function rulesPhone()
    {
        return [
            'token' => 'required',
            'password' => 'required|confirmed|min:6|password',
        ];
    }

    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6|password',
        ];
    }
}
