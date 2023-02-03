<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;
use App\Models\Admin;
use Illuminate\Support\Facades\Password;
use App\Models\Role;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use App\Repositories\AdminRepo;
use App\Repositories\CountryRepo;

class AdminUserController extends Controller
{
    public function __construct(Mailer $mailer, AdminRepo $adminrepo) {
        $this->admin = $adminrepo;
        $this->mailer = $mailer;
    }

    public function index()
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $adminusers = Admin::whereNotIn('adm_id', [$adm_id])->get();

        $admin_lock_user = Controller::checkAdminPermission($adm_id, 'adminmanageuserlock');

        return view('admin.administrator.user.list', compact('adminusers','adm_id', 'admin_lock_user'));
    }

    public function create()
    {
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();

            $v = \Validator::make($data, [
                'fname' => 'required|max:255',
                'email' => 'required|email|max:255|unique:nm_admin,email',
                'username' => 'required|username|min:4|max:100|unique:nm_admin,username',
                'phone' => 'required|numeric',
                'role' => 'required|not_in:0',
                'countries' => 'required'
            ],[
                'countries.required' => trans("localize.tick_country_msg")
            ]);


            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $admin = Admin::create([
                'adm_fname' => $data['fname'],
                'adm_lname' => $data['lname'],
                'email' => $data['email'],
                'adm_phone' => $data['phone'],
                'username' => $data['username'],
                'status' => $data['status'],
                // 'role_id' => $data['role'],
                'password' => bcrypt(str_random(8)),
            ]);

            $attach = AdminRepo::attach_admin_role_countries($admin->adm_id, $data['role'], $data['countries']);

            //send email to new admin user
            //$token = str_random(64);
            //$token = app('auth.password.tokens')->create($admin);
            $token = Password::broker('admins')->createToken($admin);
            $data = [
                'name' => $admin->adm_fname,
                'username' => $admin->username,
                'token' => $token,
            ];
            // \Mail::send('admin.emails.newadmin', ['admin' => $admin, 'token'=>$token], function ($message) use ($admin) {
            //     $message->to($admin->email);
            //     $message->subject('Welcome To Admin of '.env('SITE_NAME'));
            //   });

            $this->mailer->send('admin.emails.newadmin', $data, function (Message $m) use ($admin) {
                $m->to($admin->email, $admin->adm_fname)->subject(trans('email.admin.welcome', ['mall_name' => trans('common.mall_name')]));
            });

            return \redirect('admin/administrator/user')->with('success', trans('localize.Account_is_created') );
        }

        $roles = Role::all();
        $countries = CountryRepo::get_all_countries();

        return view('admin.administrator.user.create', compact('roles', 'countries'));
    }

    public function edit($id)
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);

        $edit_permission = false;
        $reset_permission = false;

        if(in_array('adminmanageuseredit', $admin_permission)) {
           $edit_permission = true;
        }
        else{
            if($adm_id == $id) {
                $edit_permission = true;
            }
        }

        if(in_array('adminmanageuserresetpassword', $admin_permission)) {
            $reset_permission = true;
        }
        else{
            if($adm_id == $id) {
                $reset_permission = true;
            }
        }

        $admin = Admin::find($id);
        $roles = Role::all();
        $countries = CountryRepo::get_all_countries();

        if(\Request::isMethod('post'))
        {
            $admin_id = \Helper::decrypt($id);
            $admin = Admin::find($admin_id);
            if(!$admin)
                return back()->with('error', trans('localize.invalid_request'));

            $data = \Request::all();

            $v = \Validator::make($data, [
                'fname' => 'required|max:255',
                'email' => 'required|email|max:255|unique:nm_admin,email,'.$admin->adm_id.',adm_id',
                'username' => 'required|username|min:4|max:100|unique:nm_admin,username,'.$admin->adm_id.',adm_id',
                'phone' => 'required|numeric',
                'role' => 'required|not_in:0',
                'countries' => 'required',
            ],[
                'countries.required' => trans('localize.tick_country_msg')
            ]);

            if ($v->fails())
               return back()->withInput()->withErrors($v);

            $admin->adm_fname= $data['fname'];
            $admin->adm_lname= (isset($data['lname'])? $data['lname'] : '');
            $admin->adm_phone= (isset($data['phone'])? $data['phone'] : '');
            $admin->email= $data['email'];
            $admin->username= $data['username'];
            $admin->status= $data['status'];
            // $admin->role_id = $data['role'];
            $admin->save();

            $attach = AdminRepo::attach_admin_role_countries($admin->adm_id, $data['role'], $data['countries']);

            return back()->withSuccess( trans('localize.admministrator_is_updated') );
        }

        return view('admin.administrator.user.edit',compact('admin','roles', 'countries', 'edit_permission', 'reset_permission'));
    }

    public function reset_password($admin_id)
    {
        if(\Request::isMethod('post'))
        {
            $admin_id = \Helper::decrypt($admin_id);
            $admin = Admin::find($admin_id);
            if(!$admin)
                return back()->with('error', trans('localize.invalid_request'));

            $data = \Request::all();
            $validator = \Validator::make($data, [
                'password' => 'required|min:6|confirmed',
            ],[
                'password.required' => trans('localize.Please_fill_password_field'),
                'password.confirmed' => trans('localize.Password_confirmation_does_not_match')
            ]);

            if ($validator->fails()){
                return back()->withInput()->withErrors($validator);
            }

            try {
                $admin = Admin::find($data['adm_id']);
                $admin->password = bcrypt($data['password']);
                $admin->save();

                return back()->with('success', trans('localize.Successfully_reset_password'));
            } catch (Exception $e) {
                return back()->with('error', $e);
            }
        }
    }

    public function change_admin_lock($adm_id, $type)
    {
        $admin = $this->admin->lock_admin_by_admin($adm_id,$type);
        return back()->with('status', trans('localize.Success_to') .$type.' admin');
    }
}
