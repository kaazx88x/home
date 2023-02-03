<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Limit extends Model
{
    protected $table = 'limits';
    protected $fillable = ['daily', 'daily_count', 'weekly', 'weekly_count', 'monthly', 'monthly_count', 'yearly', 'yearly_count'];
    protected $primaryKey = 'id';

    public function actions()
    {
        return $this->hasMany(LimitAction::class, 'limit_id')->orderBy('order');
    }

    public function blocked()
    {
        return $this->actions()->where('action', 2);
    }

    public function alerts()
    {
        return $this->actions()->where('action', 1);
    }

    public function store_blocked()
    {
        return $this->blocked()->where('per_user', 0);
    }

    public function user_blocked()
    {
        return $this->blocked()->where('per_user', 1);
    }

    //this limit belongs to either customer or store
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id', 'limit_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'id', 'limit_id');
    }
}
