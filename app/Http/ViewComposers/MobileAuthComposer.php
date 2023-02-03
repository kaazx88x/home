<?php

namespace App\Http\ViewComposers;

use Cookie;
use Auth;
use Illuminate\View\View;
use App\Repositories\CustomerRepo;

class MobileAuthComposer
{
    public function __construct()
    {
        # code...
    }

    public function compose(View $view)
    {
        $cookies=\Request::header('cookie');
        $cookiearray = explode(';', $cookies);
        $app_session=null;
        foreach($cookiearray  as $singlecookie)
        {
            if (strpos($singlecookie, 'app_session') !== false)
            {
                $app_session = explode('=', $singlecookie)[1];
            }
        }
        $cus = null;

        if ($app_session != null) {
            $cus = CustomerRepo::check_api_key($app_session);

            if ($cus) {
                Auth::login($cus);
            }
        }

        // $view->with('laravel_session', $laravel_session);
    }
}
