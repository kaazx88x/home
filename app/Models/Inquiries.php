<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Inquiries extends Authenticatable
{

    protected $table = 'nm_inquiries';
    protected $fillable = ['iq_subject', 'iq_emailid', 'or_id', 'iq_message'];
    protected $primaryKey = 'iq_id';
}
