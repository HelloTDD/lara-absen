<?php

namespace App\Models;

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
}
