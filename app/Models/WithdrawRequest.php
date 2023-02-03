<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class WithdrawRequest extends Authenticatable
{

    protected $table = 'nm_withdraw_request';
    protected $primaryKey = 'wd_id';

     protected $fillable = ['wd_mer_id', 'wd_total_wd_amt', 'wd_submited_wd_amt', 'wd_balance_after', 'wd_admin_comm_amt', 'wd_currency', 'wd_rate', 'wd_status', 'wd_statement'];
}
