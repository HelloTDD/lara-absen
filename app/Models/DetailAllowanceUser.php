<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailAllowanceUser extends Model
{
    //
    protected $table = 'detail_allowance_users';
    protected $fillable = [
        'user_id',
        'type_allowance_id',
        'amount',
        'type_allowance',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function typeAllowance()
    {
        return $this->belongsTo(TypeAllowance::class, 'type_allowance_id');
    }
}
