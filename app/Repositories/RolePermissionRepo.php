<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\Permission;
use App\Models\PermissionRole;
use App;

class RolePermissionRepo
{
    public static function get_roles()
    {
        return Role::all();
    }

    public static function find_role($id)
    {
        return Role::find($id);
    }

    public static function get_permissions_group()
    {
        return Permission::orderBy('display_sorting')->get()->groupBy('permission_group');
    }

    public static function add_role($data)
    {
        try {

            return Role::create([
                'name' => $data['name'],
                'display_name' => $data['display_name'],
                'description' => $data['description'],
				'role_level' => $data['role_level'],
            ]);

        } catch(Exception $e) {
            return false;
        }
    }

    public static function update_role($id, $data)
    {
        try {

            return Role::find($id)->update([
                'name' => $data['name'],
                'display_name' => $data['display_name'],
                'description' => $data['description'],
				'role_level' => $data['role_level'],
            ]);

        } catch(Exception $e) {
            return false;
        }
    }

    public static function update_permission_role($role_id, $permission_id)
    {
        try {
            $role = Role::find($role_id);
            $role->permissions()->sync($permission_id);

        } catch(Exception $e) {
            return false;
        }
    }

    public static function delete($id)
    {
        $role = Role::find($id);
        $role->delete();

        return $role;
    }
}
