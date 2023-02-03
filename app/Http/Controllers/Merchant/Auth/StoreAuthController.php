<?php

namespace App\Http\Controllers\Merchant\Auth;

use App\Models\StoreUser;
use Validator;
use App\Http\Controllers\Merchant\Controller;
// use Illuminate\Foundation\Auth\ThrottlesLogins;
// use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;

class StoreAuthController extends Controller
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
    protected $redirectTo = '/store';
    protected $guard = 'storeusers';

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
        return view('merchant.auth.store_login');
    }

    public function authenticated(Request $request, $user)
    {
        if ($user->status === 0 && $user->token === null) {
            auth($this->guard)->logout();

            return back()->with('error', 'You need to confirm your account. We have sent you an activation code, please check your email.');
        }

        return redirect()->intended($this->redirectPath());
    }

    public function activateUser($token)
    {
        if(\Request::isMethod('post')) {
            $data = \Request::all();
            //var_dump($data);exit;
            $v = Validator::make($data, [
                'email' => 'required',
                'password' => 'required|min:6|confirmed',
            ]);

            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $user = StoreUser::where('email',$data['email'])->where('token',$token)->first();

            if(!$user)
                return back()->with('error','Invalid email address');

            $user->password = bcrypt($data['password']);
            $user->token = null;
            $user->save();

            return redirect('/store/login')->with('success','Your password successfully update');
        }

        return view('merchant.auth.passwords.store_activation', ['token'=>$token]);
    }

    // public function activationSuccess()
    // {
    //     return view('merchant.activationsuccess');
    // }

    public function logout()
    {
        $this->guard()->logout();

        return redirect($this->redirectPath());
    }
}
