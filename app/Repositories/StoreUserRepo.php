<?php

namespace App\Repositories;
use DB;
use App\Models\Store;
use App\Models\StoreUser;
use App\Models\StoreUserMapping;
use Auth;

class StoreUserRepo
{
    public static function get_merchant_store_user($mer_id, $admin_country_id_list = null)
    {
        $result = StoreUser::where('mer_id', $mer_id)
        ->join('nm_store_user_mappings','nm_store_user_mappings.storeuser_id','=','nm_store_users.id')
        ->join('nm_store','nm_store.stor_id','=','nm_store_user_mappings.store_id');

        if($admin_country_id_list != NULL){
            $result = $result->whereIn('stor_country',$admin_country_id_list);
        }

        $result = $result->groupBy('nm_store_users.id')->paginate(20);

        return $result;
    }

    public static function create($data, $mer_id, $token)
    {
        return StoreUser::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => isset($data['email']) ? $data['email'] : NULL ,
                'username' => $data['username'],
                'token' => isset($data['password']) ? NULL : $token,
                'mer_id' => $mer_id,
                'password' => isset($data['password']) ? bcrypt($data['password']) : bcrypt(str_random(6)),
                'status' => isset($data['email']) ? 0 : 1,
            ]);
    }

    public static function get_store_users_list($store_id, $mer_id)
    {
        try {
            $users = StoreUser::where('mer_id','=',$mer_id)->get();
            $assigned_users = StoreUserMapping::where('store_id',$store_id)->pluck('storeuser_id')->toArray();
            foreach ($users as $user) {
                $user->assigned = (in_array($user->id, $assigned_users))? 1 : 0;
            }

            return $users;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function update_store_user_mapping($user_id, $store_id)
    {
        try {
            foreach ($user_id as $id) {
                StoreUserMapping::firstOrCreate([
                'store_id' => $store_id,
                'storeuser_id' => $id
                ]);
            }

            StoreUserMapping::where('store_id','=',$store_id)->whereNotIn('storeuser_id',$user_id)->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function remove_store_user_mapping($store_id = null, $user_id = null)
    {
        try {
            if($user_id) {
                StoreUserMapping::where('storeuser_id','=', $user_id)->delete();
            } elseif($store_id) {
                StoreUserMapping::where('store_id','=', $store_id)->delete();
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function get_assigned_store_user($user_id)
    {
        return StoreUserMapping::select('nm_store_user_mappings.*','nm_store.*')->where('storeuser_id','=',$user_id)->leftJoin('nm_store','nm_store.stor_id','=','nm_store_user_mappings.store_id')->get();
    }

    public static function toggle_user_status($user_id, $status)
    {
        return StoreUser::where('id', $user_id)->update([
            'status' => $status,
        ]);
    }

    public static function find_store_user($user_id)
    {
        return StoreUser::where('id',$user_id)->first();
    }

    public static function update_store_user($user_id, $data)
    {
        return StoreUser::where('id',$user_id)->update([
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);
    }

    public static function get_merchant_by_storeuser_username($username)
    {
        return StoreUser::select('nm_store_users.id as storeuser_id','nm_merchant.*','nm_country.*')
        ->where('nm_store_users.username','=', $username)
        ->leftJoin('nm_merchant','nm_merchant.mer_id','=','nm_store_users.mer_id')
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_merchant.mer_co_id')
        ->first();
    }

    public static function check_user_merchant($mer_id, $user_id)
    {
        return StoreUser::where('id', $user_id)->where('mer_id',$mer_id)->first();
    }

    public static function reset_password($user_id, $data)
    {
        $user = StoreUser::find($user_id);

        $user->password = bcrypt($data['password']);
        $user->save();

        return $user;
    }


    public static function update_user_store_mapping($store_id, $user_id)
    {
        try {
            foreach ($store_id as $id) {
                StoreUserMapping::firstOrCreate([
                'storeuser_id' => $user_id,
                'store_id' => $id
                ]);
            }

            StoreUserMapping::where('storeuser_id','=',$user_id)->whereNotIn('store_id',$store_id)->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function get_storeuser_by($username)
    {
        return StoreUser::where('username', $username)->first();
    }
}
