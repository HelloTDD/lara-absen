<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserShift extends Model
{
    use HasFactory;
    // protected $table = 'user_shifts';
    // protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'shift_id',
        'start_date_shift',
        'end_date_shift',
    ];

}
