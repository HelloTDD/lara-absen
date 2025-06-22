<?php

namespace App\Models;

use App\Models\User;
use App\Models\Shift;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAttendance extends Model
{

    use HasFactory, SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'user_attendances';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'shift_id',
        'date',
        'check_in_time',
        'latitude_in',
        'longitude_in',
        'distance_in',
        'check_in_photo',
        'check_out_time',
        'latitude_out',
        'longitude_out',
        'distance_out',
        'check_out_photo',
        'desc_attendance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class,'shift_id','id');
    }
}
