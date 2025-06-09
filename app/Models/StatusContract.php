<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusContract extends Model
{
    protected $table = 'status_contracts';
    protected $guarded = [];

    public function contracts()
    {
        return $this->belongsTo(UserContract::class,'contract_id');
    }
}
