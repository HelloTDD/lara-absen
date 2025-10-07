<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MonthlySalary extends Model
{
    protected $table = 'monthly_salaries';
    protected $guarded = [];

    // public function user_salary()
    // {
    //     return $this->belongsTo(UserSalary::class,'salary_id');
    // }

    // relasi langsung ke user_id baru
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function allowances()
    {
        return $this->hasManyThrough(
            TypeAllowance::class,         // model tujuan
            DetailAllowanceUser::class,   // pivot table model
            'user_id',                    // foreign key di detail_allowance_users
            'id',                         // foreign key di type_allowances
            'user_id',                    // local key di monthly_salaries
            'type_allowance_id'           // local key di detail_allowance_users
        )->withPivot('amount')->withTimestamps();
    }


}
