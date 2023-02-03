<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PasswordController extends Controller
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

    protected $redirectTo = '/admin/login';
    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    protected $guard = 'admins';
    protected $broker = 'admins';

    public function __construct()
    {
        $this->middleware('guest:admin');
    }

//    public function showResetForm()
//    {
//        return view('admin.auth.passwords.email');
//    }

    public function showLinkRequestForm()
    {
        if (property_exists($this, 'linkRequestView')) {
            return view($this->linkRequestView);
        }

        if (view()->exists('admin.auth.passwords.email')) {
            return view('admin.auth.passwords.email');
        }

        return view('auth.password');
    }

    public function showResetForm(Request $request, $token = null)
    {
        if (is_null($token)) {
            return $this->getEmail();
        }

        $email = $request->input('email');

        if (property_exists($this, 'resetView')) {
            return view($this->resetView)->with(compact('token', 'email'));
        }

        if (view()->exists('admin.auth.passwords.reset')) {
            return view('admin.auth.passwords.reset')->with(compact('token', 'email'));
        }

        return view('auth.reset')->with(compact('token', 'email'));
    }

    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => bcrypt($password),
            'remember_token' => Str::random(60),
        ])->save();

        //Auth::guard($this->getGuard())->login($user);
    }

    protected function getResetSuccessResponse($response)
    {
        return redirect($this->redirectPath())->with('success', trans($response));
    }
}
