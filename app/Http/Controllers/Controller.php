<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\Admin;
use App\Models\AdminToCountry;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function adminPermissionList($adm_id)
    {
        $admin_permission = Admin::where('adm_id', '=', $adm_id)
        ->join('role_user','nm_admin.adm_id', '=', 'role_user.user_id')
        ->join('roles','roles.id','=','role_user.role_id')
        ->join('permission_role','permission_role.role_id','=','roles.id')
        ->join('permission','permission.id','=','permission_role.permission_id')
        ->get(['permission_name']);

        $admin_permission_list = array();

        if(sizeof($admin_permission) > 0) {
            for($row=0; $row<sizeof($admin_permission); $row++) {
                $admin_permission_list[$row] = $admin_permission[$row]['permission_name'];
            }
        }

        return $admin_permission_list;
    }

    public static function checkAdminPermission($adm_id, $role_string)
    {
        $permission = Admin::where('adm_id', '=', $adm_id)
        ->join('role_user','nm_admin.adm_id', '=', 'role_user.user_id')
        ->join('roles','roles.id','=','role_user.role_id')
        ->join('permission_role','permission_role.role_id','=','roles.id')
        ->join('permission','permission.id','=','permission_role.permission_id')
        ->where('permission_name','=',$role_string)
        ->value('permission_name');

        if(sizeof($permission) > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function getAdminCountryIdList($adm_id){
        $adminCountry = AdminToCountry::where('admin_id','=',$adm_id)
        ->join('nm_admin','nm_admin.adm_id','=','admin_to_country.admin_id')
        ->get(['country_id']);

        $adminCountryList = array();

        if(sizeof($adminCountry) > 0) {
            for($row=0; $row<sizeof($adminCountry); $row++) {
                $adminCountryList[$row] = $adminCountry[$row]['country_id'];
            }
        }

        return $adminCountryList;
    }
}