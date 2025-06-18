<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    protected $table = 'calendar_events';
    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'extend_data',
        'created_by'
    ];

    protected $casts = [
        'extend_data' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
}
