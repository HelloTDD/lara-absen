<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserContract extends Model
{
    protected $table = 'user_contracts';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function status_contract()
    {
        return $this->hasMany(StatusContract::class,'contract_id');
    }
}
