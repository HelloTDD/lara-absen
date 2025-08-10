<?php
namespace App\Services;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserEmployeeService
{
    /**
     * Create a new role.
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\Role
     */
    public function createEmployee($request)
    {
        if($request->has('is_admin') && $request->is_admin == 'on'){
            $request->merge(['is_admin' => 1]);
        }else{
            $request->merge(['is_admin' => 0]);
        }
        DB::beginTransaction();
        try{
            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone' => $request->phone,
                'birth_date' => Carbon::parse($request->birth_date)->format('Y-m-d'),
                'gender' => $request->gender,
                'address' => $request->address,
                'leave' => $request->leave ?? 12,
                'is_admin' => $request->is_admin,
                'role_id' => $request->role_id,
                'date_joined' => $request->date_joined ? Carbon::parse($request->date_joined)->format('Y-m-d') : null,
                'date_leave' => null,
            ]);
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create role', [
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to create role: ' . $e->getMessage());
        }
    }

}
