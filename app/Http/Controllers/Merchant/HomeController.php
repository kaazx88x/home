<?php namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Merchant\Controller;
use App\Repositories\StoreUserRepo;

class HomeController extends Controller
{
    public function index()
    {
        if(\Auth::guard('merchants')->check()) {
            return view('merchant.dashboard');
        }

        if(\Auth::guard('storeusers')->check()) {
            $user_id = \Auth::guard('storeusers')->user()->id;
            $storeuser = StoreUserRepo::find_store_user($user_id);
            $stores = StoreUserRepo::get_assigned_store_user($user_id);
            // dd($stores);

            return view('merchant.dashboard',compact('storeuser','stores'));
        }
    }

    public function setlocale()
    {
        $data = \Request::all();
        Session::forget('lang');
        Session::put('lang', $data['lang']);
        return 'success';
    }
}
