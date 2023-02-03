<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\AdminResetPassword as AdminResetPasswordNotification;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class AdminToCountry extends Authenticatable
{
    use Notifiable;

    protected $guard = "admins";
    protected $table = 'admin_to_country';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'admin_id', 'country_id',
    ];

}