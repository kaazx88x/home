<?php
namespace App\Http\ViewComposers;

use Cookie;
use Auth;
use Illuminate\View\View;
use App\Models\Admin;
use App\Http\Controllers\Admin\Controller;

class AdminComposer
{
    /**
     * Create a movie composer.
     *
     * @return void
     */
    // public function __construct()
    // {

    // }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $adm_id = (\Auth::guard('admins'))? \Auth::guard('admins')->user()->adm_id : 0;
        $admin = Admin::where('adm_id','=', $adm_id)->first();
        // $admin_role = Admin::where('adm_id','=', $adm_id)->leftJoin('nm_admin_role','nm_admin_role.id','=','nm_admin.role_id')->value('name');

        $admin_permission = array();
        $admin_permission = Controller::adminPermissionList($adm_id);

        $view->with(compact('admin', 'admin_permission'));
    }
}
