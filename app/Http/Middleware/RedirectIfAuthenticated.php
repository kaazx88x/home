<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (($guard == null || $guard == 'web') && Auth::guard('web')->check()) {
            return redirect('/');
        }

        if ($guard == 'merchants' && Auth::guard('merchants')->check()) {
            return redirect('merchant');
        }

        if ($guard == 'admins' && Auth::guard('admins')->check()) {
            return redirect('admin');
        }

        return $next($request);
    }
}
