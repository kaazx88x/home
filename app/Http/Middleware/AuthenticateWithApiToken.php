<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticateWithApiToken
{

    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request $request
    * @param  \Closure $next
    * @return mixed
    */
    public function handle($request, Closure $next, ...$guard)
    {

        if ((in_array("api_members", $guard)) && Auth::guard('api_members')->check()) {
            return $next($request);
        }

        if (in_array("api_merchants", $guard) && Auth::guard('api_merchants')->check()) {
            return $next($request);
        }

        if (in_array("api_storeusers", $guard) && Auth::guard('api_storeusers')->check()) {
            return $next($request);
        }

        return response('Unauthorized.', 401);
    }
}