<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Http\Requests\RoleRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function index()
    {
        $role = Role::all();
        return view('role.index', compact('role'));
    }

    public function store(RoleRequest $request, RoleService $roleService)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $roleService->createRole($request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create role', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to create role: ' . $e->getMessage()]);
        }
        return redirect()->route('role.index')->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        $role = Role::find($id);
        if (!$role) {
            Log::error('Role not found', ['id' => $id]);
            return redirect()->route('role.index')->with('error', 'role not found.');
        }
        return view('role.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            Log::error('Role not found', ['id' => $id]);
            return redirect()->route('role.index')->with('error', 'Role not found.');
        }

        DB::beginTransaction();
        try {
            $role->role_name = $request->role_name;
            $role->description = $request->description;
            $role->job_description = json_encode($request->job_description, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $role->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update role', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to update role: ' . $e->getMessage()]);
        }
        return redirect()->route('role.index')->with('success', 'Role updated successfully.');
    }

    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role) {
            Log::error('Role not found', ['id' => $id]);
            return redirect()->route('role.index')->with('error', 'Role not found.');
        }

        DB::beginTransaction();
        try {
            $role->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete role', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to delete role: ' . $e->getMessage()]);
        }
        return redirect()->route('role.index')->with('success', 'Role deleted successfully.');
    }
}
