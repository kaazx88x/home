<?php

namespace App\Repositories;
use DB;
use App\Models\Admin;
use App\Models\Role;
use App\Models\AdminToCountry;
use App\Models\RoleUser;

class AdminRepo
{
    //For counting the of fail login failed
    public static function add_attempt_fail($username)
    {
        $admin = Admin::where('username', $username)->first();

        if ($admin) {
            $admin->attempt_fail += 1;
            $admin->save();
        }
    }

    //For locking the account
    public static function lock_account($username)
    {
        $admin = Admin::where('username', $username)->first();

        if ($admin) {
            if($admin->attempt_fail >= 5){
                $admin->account_locked = 1;
                $admin->save();
            }
        }
    }

    //For unlocking the account
    public static function unlock_account($username)
    {
        $admin = Admin::where('username', $username)->first();

        if ($admin) {
            $admin->attempt_fail = 0;
            $admin->account_locked = 0;
            $admin->save();
        }
    }

    public static function check_superadmin()
    {
        if(\Auth::guard('admins')->user()->role_id == 1){
            return true;
        }

        return false;
    }

    public static function lock_admin_by_admin($adm_id,$type)
    {
        $admin = Admin::findorfail($adm_id);

        switch ($type) {
            case 'lock':
                $admin->account_locked = 1;
                break;

            case 'unlock':
                $admin->account_locked = 0;
                $admin->attempt_fail = 0;
                break;

            default:
                break;
        }
        $admin->save();

        return $admin;
    }

    public static function attach_admin_role_countries($admin_id, $role_id = null, $countries = null)
    {
        try {
            //countries must be array type
            if(!is_null($admin_id) && !is_null($countries)) {
                AdminToCountry::where('admin_id', $admin_id)->delete();

                foreach ($countries as $country_id) {
                    AdminToCountry::create([
                    'admin_id' => $admin_id,
                    'country_id' => $country_id,
                    ]);
                }
            }

            if(!is_null($admin_id) && !is_null($role_id)) {
                //attach user role
                RoleUser::where('user_id', $admin_id)->delete();
                $role = Role::find($role_id)->users()->attach($admin_id);
            }

            return true;

        } catch(Exception $e) {
            return false;
        }
    }
}