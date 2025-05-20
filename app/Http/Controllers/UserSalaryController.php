<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserSalaryRequest;
use App\Interfaces\UserSalaryInterface;
use App\Models\User;
use App\Models\UserSalary;
use App\Models\Log;
use Illuminate\Support\Facades\Log as lgs;

class UserSalaryController extends Controller implements UserSalaryInterface
{
    
    public function index()
    {
        $users = User::all();
        $salary = UserSalary::with('user')->get();
        return view('users-salary.index', compact('users', 'salary'));
    }

    public function store(UserSalaryRequest $request)
    {
        try {
            $user = User::find($request->user_id);
            if ($user) {
                $total = $request->salary_basic + $request->salary_allowance + $request->salary_bonus + $request->salary_holiday;
                $create_salary = $user->salary()->create([
                    'salary_basic' => $request->salary_basic,
                    'salary_allowance' => $request->salary_allowance,
                    'salary_bonus' => $request->salary_bonus,
                    'salary_holiday' => $request->salary_holiday,
                    'salary_total' => $total,
                ]);
                if(!$create_salary){
                    throw new \Exception('Salary details not saved');
                }
            } 
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'create user salary',
                'controller' => 'UserSalaryController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($create_salary);
    }
}
