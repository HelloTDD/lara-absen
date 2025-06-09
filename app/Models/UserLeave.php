<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserLeave extends Model
{
    use HasFactory;
    protected $table = 'user_leaves';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}