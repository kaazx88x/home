<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\MerchantResetPassword as MerchantResetPasswordNotification;

class Merchant extends Authenticatable
{
    use Notifiable;

    protected $guard = "merchants";
    protected $table = 'nm_merchant';
    protected $primaryKey = 'mer_id';

    protected $fillable = [
        'mer_type', 'mer_lname', 'mer_fname', 'email', 'username', 'password', 'mer_phone', 'mer_address1', 'mer_address2', 'mer_ci_id', 'mer_co_id', 'mer_payment', 'mer_commission', 'mer_vtoken', 'mer_staus', 'bank_acc_name', 'bank_acc_no', 'bank_name', 'bank_country', 'bank_address', 'bank_swift', 'bank_europe', 'mer_state', 'mer_city_name', 'mer_platform_charge', 'mer_service_charge', 'app_session', 'app_session_date', 'api_token', 'mer_office_number', 'mer_referrer', 'mer_referrer_phone', 'zipcode', 'bank_gst'
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
     * This is to replace merchant reset password link in the email
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MerchantResetPasswordNotification($token));
    }

    public function guarantor()
    {
        return $this->hasOne(MerchantGuarantor::class, 'merchant_id', 'mer_id');
    }

    public function referrer()
    {
        return $this->hasOne(MerchantReferrer::class, 'merchant_id', 'mer_id');
    }

    public function merchantName()
    {
        $fname = $this->{'mer_fname'};
        $lname = $this->{'mer_lname'};

        if(preg_match("/\p{Han}+/u", $fname) && preg_match("/\p{Han}+/u", $lname))
            return $fname.$lname;

        return $fname . ' ' . $lname;
    }

    public function country()
    {
        return $this->hasOne(Country::class, 'co_id', 'mer_co_id');
    }

    public function state()
    {
        return $this->hasOne(State::class, 'id', 'mer_state');
    }

    public function country_bank()
    {
        return $this->hasOne(Country::class, 'co_id', 'bank_country');
    }
}
