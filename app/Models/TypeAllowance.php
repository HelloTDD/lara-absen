<?php

namespace App\Models;

use App\Models\MonthlySalary;
use Illuminate\Database\Eloquent\Model;

class TypeAllowance extends Model
{
    protected $table = 'type_allowances';

    protected $fillable = [
        'name_allowance',
    ];

    public function detailAllowanceUsers()
    {
        return $this->hasMany(DetailAllowanceUser::class, 'type_allowance_id');
    }

    public function monthly_salaries()
    {
        return $this->belongsToMany(MonthlySalary::class, 'monthly_salary_allowance', 'type_allowance_id', 'monthly_salary_id')
            ->withPivot('amount')
            ->withTimestamps();
    }
}
