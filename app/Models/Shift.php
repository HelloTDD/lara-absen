<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shift extends Model
{
    use HasFactory;
    protected $table = 'shifts';
    protected $fillable = [
        'shift_name',
        'check_in',
        'check_out',
    ];

    public function user_shift()
    {
        return $this->hasMany(UserShift::class,'shift_id');
    }
}
