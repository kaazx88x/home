<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Providers\ActivationServiceProvider;
use Auth;
use App\Models\Customer;
use App\Repositories\CountryRepo;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected $activationService;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ActivationServiceProvider $activationService)
    {
        $this->middleware('guest', ['except' => 'logout']);
        $this->activationService = $activationService;
    }

    public function authenticated(Request $request, $user)
    {
        if ($request->type == 'phone') {
            if (!$user->cellphone_verified) {
                auth()->logout();
                return back()
                ->withInput($request->only($this->username(), 'remember', 'type'))
                ->with('error_', trans('localize.phone_not_verified'));
            }
        }

        if ($request->type == 'email') {
            if (!$user->email_verified) {
                $resend_url = '/resend/activation/'.$user->cus_id;
                auth()->logout();
                return back()
                ->withInput($request->only($this->username(), 'remember', 'type'))
                ->with('error_', trans('localize.email_not_verified') . ' ' . trans('localize.check_email_verify') . ' <a href="' . $resend_url . '">' . trans('localize.resend_activation') . '</a>');
            }
        }

        if (!$user->cus_status) {
            auth()->logout();
            return back()->with('error_',  trans('localize.account_not_active'));
        }

        if (!$user->info) {
            return redirect('/profile');
        }
        return redirect()->intended($this->redirectPath());
    }

    // public function activateUser($token)
    // {
    //     if ($user = $this->activationService->activateUser($token)) {
    //         if (!$user) {
    //             abort(500);
    //         }

    //         return view('auth.activationsuccess');
    //     }
    // }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        return redirect($this->redirectPath());
    }

    public function resendActivation($cus_id, $email)
    {
        $customer = Customer::where('cus_id', $cus_id)->where('email', $email)->first();

        if(!$customer || $customer->email_verified != 0)
            return back()->withError('Invalid email');

        $this->activationService->resendActivation($customer);

        return back()->withSuccess(trans('localize.merchant.resend_activation_email.msg'));
    }

    public function resendActivationForm($id)
    {
        if (\Request::isMethod('post')) {
            $data = \Request::all();
            $v = \Validator::make($data, [
                'email' => 'required|email',
	        ],[
                'email.required' => trans('localize.emailrequired'),
            ])->validate();


            $this->resendActivation($id, $data['email']);
        }

        return view('auth.resend_activation', compact('id'));
    }

    protected function credentials(Request $request)
    {
        if ($request->has('phone_area_code')) {
            $request->merge(['cus_phone' => request('login')]);
            return $request->only('phone_area_code', 'cus_phone', 'password');
        } else {
            $request->merge(['email' => request('login')]);
            return $request->only('email', 'password');
        }
    }

    public function username()
    {
        return 'login';
    }

    public function showLoginForm()
    {
        $countries = CountryRepo::get_all_countries();

        return view('auth.login', compact('countries'));
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember', 'type'))
            ->withErrors($errors);
    }
}
