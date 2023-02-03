<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotStoreUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'storeusers')
	{
		if (!Auth::guard($guard)->check()) {
			return redirect('/store/login');
		}

        if (Auth::guard($guard)->check() && Auth::guard('merchants')->check() ) {
			Auth::guard('merchants')->logout();
		}

		return $next($request);
	}
}