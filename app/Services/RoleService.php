<?php
namespace App\Services;

use Carbon\Carbon;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RoleService
{
    /**
     * Create a new role.
     *  
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\Role
     */
    public function createRole($request)
    {
        DB::beginTransaction();
        try{
            Role::create([
                'role_name' => $request->role_name,
                'description' => $request->description,
                'job_description' => $request->job_description,
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