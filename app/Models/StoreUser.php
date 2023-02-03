<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\StoreUserResetPassword as StoreUserResetPasswordNotification;

class StoreUser extends Authenticatable
{
    use Notifiable;

    protected $guard = "storeusers";
    protected $table = 'nm_store_users';
    protected $guarded = ['created_at', 'updated_at'];
    protected $primaryKey = 'id';

    /**
     * This is to replace merchant reset password link in the email
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new StoreUserResetPasswordNotification($token));
    }

    public function assigned_stores()
    {
        return $this->hasMany(StoreUserMapping::class, 'storeuser_id', 'id');
    }
}