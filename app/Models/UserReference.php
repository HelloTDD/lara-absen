<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserReference extends Model
{
    /** @use HasFactory<\Database\Factories\UserReferenceFactory> */
    use HasFactory;

    protected $table = 'user_references';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userContract()
    {
        return $this->belongsTo(UserContract::class, 'contract_id');
    }
}
