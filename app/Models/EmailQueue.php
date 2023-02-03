<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailQueue extends Model
{
    protected $table = 'nm_email_queues';
    protected $fillable = [
        'jobs', 'type', 'notifiable_id', 'notifiable_type', 'data', 'send', 'remarks'
    ];
    protected $primaryKey = 'id';
}
