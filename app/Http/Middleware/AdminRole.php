<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, ...$role)
	{
        $guard = 'admins';

		if (!Auth::guard($guard)->check()) {
			return redirect('admin/login');
		}

        if(Auth::guard($guard)->user()->isSuperuser()) {
            return $next($request);
        } else if(in_array("operation", $role) && Auth::guard($guard)->user()->isOperation()) {
            return $next($request);
        } else if(in_array("customerService", $role) && Auth::guard($guard)->user()->isCustomerService()) {
            return $next($request);
        }

        return redirect('admin')->with('denied', 'You are not authorized to access that page');
	}
}