<?php

namespace App\Models;

use App\Models\User;
use App\Models\Shift;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserShift extends Model
{
    use HasFactory;
    protected $table = 'user_shifts';
    // protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'shift_id',
        'desc_shift',
        'start_date_shift',
        'end_date_shift',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
