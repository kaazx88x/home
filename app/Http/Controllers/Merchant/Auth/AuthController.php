<?php

namespace App\Http\Controllers\Merchant\Auth;

use App\Models\Merchant;
use Validator;
use App\Http\Controllers\Merchant\Controller;
// use Illuminate\Foundation\Auth\ThrottlesLogins;
// use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Providers\MerchantActivationServiceProvider;
use Auth;
use App\Repositories\MerchantRepo;

class AuthController extends Controller
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

    // use AuthenticatesAndRegistersUsers,
    //     ThrottlesLogins;
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/merchant';
    protected $guard = 'merchants';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(MerchantActivationServiceProvider $activationService)
    {
        $this->middleware('guest:'.$this->guard, ['except' => 'logout']);
        $this->activationService = $activationService;
    }

    protected function guard()
    {
        return Auth::guard($this->guard);
    }

    public function username()
    {
        return 'username';
    }

    public function showLoginForm()
    {
        return view('merchant.auth.login');
    }

    public function authenticated(Request $request, $merchant)
    {
        if ($merchant->mer_staus == 0) {
            $this->activationService->sendActivationMail($merchant);
            $resend_url = '/merchant/resend/activation/'.$merchant->mer_id;
            auth($this->guard)->logout();

            return back()->with('error_','Your account has not been activated. Please check your email for activation or <a href="'.$resend_url.'">click here to resend activation link</a>');
        }

        return redirect()->intended($this->redirectPath());
    }

    public function activateUser($token)
    {
        if ($merchant = $this->activationService->activateUser($token)) {
            return redirect('merchant/activation-success');
        }

        return view('auth.activation_failed');
    }

    public function activationSuccess()
    {
        return view('merchant.activationsuccess');
    }

    public function logout()
    {
        $this->guard()->logout();

        return redirect($this->redirectPath());
    }

    public function resendActivation($id, $email)
    {
        $merchant = MerchantRepo::get_merchant_by_id_and_email($id,$email);

        if(!$merchant || $merchant->mer_staus != 0)
            return back()->withError('Invalid email');

        $this->activationService->resendActivation($merchant);

        return back()->withSuccess('Activation email successfully send.');
    }

    public function resendActivationForm($id)
    {
        if (\Request::isMethod('post')) {
            $data = \Request::all();
            $v = \Validator::make($data, [
                'email' => 'required|email',
	        ],[
                'email.required' => 'Email field is required',
            ]);

            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $this->resendActivation($id, $data['email']);

        }

        return view('merchant.auth.resend_activation', compact('id'));
    }
}
