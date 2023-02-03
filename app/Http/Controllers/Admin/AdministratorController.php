<?php

namespace App\Http\Controllers\Admin;

use Request;
use App\Http\Controllers\Controller;
use App\Repositories\RolePermissionRepo as RPRepo;
use Validator;

class AdministratorController extends Controller
{
    public function manage_role()
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
        $delete_permission = in_array('adminmanagedelete', $admin_permission);

        $roles = RPRepo::get_roles();
        return view('admin.administrator.role.index', compact('roles','delete_permission'));
    }

    public function add_role()
    {
        $groups = RPRepo::get_permissions_group();
        return view('admin.administrator.role.add', compact('groups'));
    }

    public function role_delete($role_id)
    {
        $role = RPRepo::delete($role_id);
        return \redirect('admin/administrator/role')->with('success',trans('localize.User_role_is_deleted'));
    }

    public function add_role_submit()
    {
        $data = Request::all();

        Validator::make($data, [
            'name' => 'required|unique:roles,name',
            'display_name' => 'required',
            'description' => 'required',
            'permissions' => 'required',
			'role_level' => 'required|integer',
        ])->validate();

        $role = RPRepo::add_role($data);
        if(!$role)
            return back()->with('error', trans('localize.Error_while_saving_data'));

        if(isset($data['permissions'])) {
            RPRepo::update_permission_role($role->id, $data['permissions']);
        }

        return redirect('admin/administrator/role/edit/'.$role->id)->with('success',  trans('localize.Successfully_created_new_role') );
    }

    public function edit_role($role_id)
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $edit_permission = Controller::checkAdminPermission($adm_id, 'adminmanageedit');

        $role = RPRepo::find_role($role_id);
        $groups = RPRepo::get_permissions_group();

        return view('admin.administrator.role.edit', compact('role', 'groups','edit_permission'));
    }

    public function edit_role_submit($role_id)
    {
        $data = Request::all();

        Validator::make($data, [
            'name' => 'required|unique:roles,name,'.$role_id.',id',
            'display_name' => 'required',
            'description' => 'required',
            'permissions' => 'required',
			'role_level' => 'required',
        ])->validate();

        $update = RPRepo::update_role($role_id, $data);
        if(!$update)
            return back()->with('error', trans('localize.Error_while_saving_data'));

        if(isset($data['permissions'])) {
            RPRepo::update_permission_role($role_id, $data['permissions']);
        }

        return back()->with('success', trans('localize.role_has_been_update') );
    }
}
