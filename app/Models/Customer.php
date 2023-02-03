<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\MemberResetPassword as MemberResetPasswordNotification;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $table = 'nm_customer';
    protected $primaryKey = 'cus_id';

    protected $fillable = [
        'cus_name', 'email', 'password', 'username', 'cus_phone', 'cus_address1', 'cus_address2' , 'cus_country' , 'cus_city', 'cus_state', 'cus_city_name','payment_secure_code', 'cus_status', 'cus_postalcode', 'phone_area_code', 'cellphone_verified', 'question_1', 'answer_1', 'question_2', 'answer_2', 'question_3', 'answer_3', 'birthdate', 'gender', 'cus_joindate', 'cus_logintype', 'email_verified', 'identity_card', 'limit_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * This is to replace member reset password link in the email
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MemberResetPasswordNotification($token));
    }

    public function info()
    {
        return $this->hasOne('App\Models\CustomerInfo');
    }

    public function shipping()
    {
        return $this->hasMany('App\Models\Shipping', 'ship_cus_id', 'cus_id');
    }

    public function customer_wallets()
    {
        return $this->hasMany('App\Models\CustomerWallet', 'customer_id');
    }

    public function limit()
    {
        return $this->hasOne(Limit::class, 'id', 'limit_id');
    }

    public function country()
    {
        return $this->hasOne(Country::class, 'co_id', 'cus_country');
    }
}
