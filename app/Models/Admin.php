<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\AdminResetPassword as AdminResetPasswordNotification;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard = "admins";
    protected $table = 'nm_admin';
    protected $primaryKey = 'adm_id';

    protected $fillable = [
        'adm_fname', 'adm_lname', 'adm_phone', 'email', 'password', 'username', 'role_id', 'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role() {
        return $this->belongsToMany('App\Models\Role', 'role_user', 'user_id', 'role_id');
    }

    /**
     * This is to replace admin reset password link in the email
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token));
    }

    public function assignedCountries()
    {
        return $this->hasMany('App\Models\AdminToCountry', 'admin_id', 'adm_id');
    }
}
