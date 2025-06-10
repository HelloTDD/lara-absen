<?php

namespace App\Http\Controllers\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\UserEmployeeService;
use App\Http\Requests\UserEmployeeRequest;

class UserEmployeeController extends Controller
{
     public function index()
    {
        $users = User::with('role')
            ->orderBy('created_at', 'desc')
            ->get();
        $roles = Role::all();
        return view('user-employee.index', compact('users','roles'));
    }

    public function store(UserEmployeeRequest $request, UserEmployeeService $userEmployeeService)
    {
        $cekUsername = User::where('username', $request->username)->first();
        if($cekUsername){
            Log::error('Username already exists', ['username' => $request->username]);
            return redirect()->route('user-employee.index')->with('error' , 'Username already exists.');
        }
        $cekEmail = User::where('email', $request->email)->first();
        if($cekEmail){
            Log::error('Email already exists', ['email' => $request->email]);
            return redirect()->route('user-employee.index')->with('error' , 'email name already exists.');
        }

        DB::beginTransaction();
        try {
            $userEmployeeService->createEmployee($request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create user employee', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
        return redirect()->route('user-employee.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::find($id);
        if (!$user) {
            Log::error('User not found', ['id' => $id]);
            return redirect()->route('user-employee.index')->with('error', 'User not found.');
        }
        $roles = Role::all();
        return view('user-employee.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            Log::error('User not found', ['id' => $id]);
            return redirect()->route('user-employee.index')->with('error', 'User not found.');
        }

        $cekEmail = User::where('email', $request->email)->where('id','!=',$id)->first();
        if($cekEmail){
            Log::error('Email already exists', ['email' => $request->email]);
            return redirect()->route('user-employee.index')->with('error' , 'email name already exists.');
        }

        DB::beginTransaction();
        try {
            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = $request->has('password') ? bcrypt($request->password) : $user->password;
            $user->phone = $request->phone;
            $user->birth_date = $request->birth_date;
            $user->gender = $request->gender;
            $user->address = $request->address;
            $user->leave = $request->leave;
            $user->role_id = $request->role_id;
            $user->date_joined = $request->date_joined ? $request->date_joined : null;
            $user->date_leave = $request->date_leave ? $request->date_leave : null;
            $user->is_admin = $request->has('is_admin') ? 1 : 0;
            $user->save();
            DB::commit();
            Log::info('User updated successfully', $user->toArray());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()]);
        }
        return redirect()->route('user-employee.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            Log::error('User not found', ['id' => $id]);
            return redirect()->route('user-employee.index')->with('error', 'User not found.');
        }

        DB::beginTransaction();
        try {
            $user->delete();
            DB::commit();
            Log::info('User deleted successfully', ['id' => $id]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete user', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to delete user: ' . $e->getMessage()]);
        }
        return redirect()->route('user-employee.index')->with('success', 'User deleted successfully.');
    }
}
