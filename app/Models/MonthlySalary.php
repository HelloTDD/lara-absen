<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlySalary extends Model
{
    protected $table = 'monthly_salaries';
    
    protected $guarded = [];

    public function user_salary()
    {
        return $this->belongsTo(UserSalary::class,'salary_id');
    }
}
