<?php

namespace App\Http\Middleware;

use App;
use Closure;
use Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
	{
//        if (Session::has('lang')) {
//            if (Request::is('admin*')) {
//                Session::forget('lang');
//                App::setLocale('en');
//            } else {
//                App::setLocale(Session::get('lang'));
//            }
//        } else {
//            if (Request::is('merchant*')) {
//                App::setLocale('cn');
//            } elseif (Request::is('admin*')) {
//                Session::forget('lang');
//                App::setLocale('en');
//            } else {
//                App::setLocale(Config::get('app.locale'));
//            }
//        }

		App::setLocale(Session::has('lang') ? Session::get('lang') : Config::get('app.locale'));
                
               
        
		return $next($request);
	}
}