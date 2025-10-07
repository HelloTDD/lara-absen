<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Models\UserBank;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function user_bank()
    {
        return $this->belongsTo(UserBank::class, 'id', 'user_id');
    }

    public function getRoleNameAttribute()
    {
        return $this->role ? $this->role->role_name : null;
    }
    
    public function getMenuItemsAttribute()
    {
        $karyawanMenus = ['Cuti','Absensi','Gaji Bulanan','Kontrak','Surat Referensi'];

        $menus = [
            'Supervisor' => ['All'],
            'Finance'    => ['Gaji','Draft Gaji Bulanan','Gaji Bulanan','Shift','Absensi','Cuti'],
            'Scheduler'  => ['Cuti','Absensi','Shift Karyawan','Shift'],
            // kelompok ini sama seperti Karyawan
            'Karyawan'   => $karyawanMenus,
            'Staff'      => $karyawanMenus,
            'IT Support' => $karyawanMenus,
            'Programmer' => $karyawanMenus,
            'Front-end Developer' => $karyawanMenus,
        ];

        return $menus[$this->role->role_name] ?? [];
    }

    public function hasFullAccess()
    {
        $roles = [
            'Staff',
            'IT Support',
            'Programmer',
            'Front-end Developer',
            'Supervisor',
            'Finance',
            'Scheduler',
            'Karyawan',
        ];

        return in_array($this->role_name, $roles);
    }


}
