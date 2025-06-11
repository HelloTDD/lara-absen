<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
class User extends Authenticatable
{
    use HasFactory, Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];
    protected $guarded = [];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function salary()
    {
        return $this->hasOne(UserSalary::class,'user_id');
    }

    public function leaves()
    {
        return $this->hasMany(UserLeave::class,'user_id');
    }

    public function contracts()
    {
        return $this->hasMany(UserContract::class,'user_id');
    }

    public function allowances()
    {
        return $this->belongsToMany(TypeAllowance::class, 'detail_allowance_users', 'user_id', 'type_allowance_id')
                    ->withPivot('amount')
                    ->withTimestamps();
    }
}
