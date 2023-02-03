<?php

namespace App\Http\Middleware;
use App\Http\Controllers\Admin\Controller;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class Permission
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

        $role_string = isset($role[0])? $role[0] : '';

        $adm_id = Auth::guard($guard)->user()->adm_id;

        $admin_permission = Controller::checkAdminPermission($adm_id, $role_string);

        if($admin_permission){
            return $next($request);
        }

        if($role_string == ""){
            return $next($request);
        }

        return redirect('admin')->with('denied', trans('localize.You_are_not_authorized_to_access_that_page'));
    }
}