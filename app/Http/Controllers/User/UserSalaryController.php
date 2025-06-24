<?php

namespace App\Http\Controllers\User;

use App\Models\Log;
use App\Models\User;
use App\Models\UserSalary;
use Illuminate\Http\Request;
use App\Models\TypeAllowance;
use App\Http\Controllers\Controller;
use App\Interfaces\UserSalaryInterface;
use App\Http\Requests\UserSalaryRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log as lgs;

class UserSalaryController extends Controller implements UserSalaryInterface
{

    public function index()
    {
        $type_allowance = TypeAllowance::all();
        $users = User::all();
        $salary = UserSalary::with(['user.allowances'])
                            ->when(Auth::check() && Auth::user()->is_admin == 0, function($query){
                                $query->where('user_id', Auth::user()->id);
                            })
                            ->get();

        return view('user.users-salary.index', compact('users', 'salary','type_allowance'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $create_salary = null;
        try {
            $user = User::find($request->user_id);

            $salary = $user->salary()->where('user_id', $user->id)->first();
            if($salary)
            {
                throw new \Exception("Salary Data is Exists", 1);
            }

            if ($user) {
                $total_allowance = 0;
                foreach ($request->salary_allowance as $allowance) {
                    $total_allowance += $request->allowances[$allowance];

                    $user->allowances()->syncWithoutDetaching([
                        $allowance => ['amount' => $request->allowances[$allowance]]
                    ]);
                }

                $total = $request->salary_basic + $total_allowance + $request->salary_bonus + $request->salary_holiday;
                $create_salary = $user->salary()->create([
                    'salary_basic' => $request->salary_basic,
                    'salary_allowance' => $total_allowance,
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

    public function update(UserSalaryRequest $request, $id)
    {
        $update_salary = null;
        try {
            $user = User::find($request->user_id);

            if ($user) {
                $salary = $user->salary()->where('id', $id)->first();
                $total_allowance = 0;

                // Hitung dan update allowance
                foreach ($request->salary_allowance as $allowance) {
                    $amount = $request->allowances[$allowance];
                    $total_allowance += $amount;

                    $user->allowances()->syncWithoutDetaching([
                        $allowance => ['amount' => $amount]
                    ]);
                }

                $total = $request->salary_basic + $total_allowance + $request->salary_bonus + $request->salary_holiday;

                if ($salary) {
                    // Update jika salary sudah ada
                    $update_salary = $salary->update([
                        'salary_basic' => $request->salary_basic,
                        'salary_allowance' => $total_allowance,
                        'salary_bonus' => $request->salary_bonus,
                        'salary_holiday' => $request->salary_holiday,
                        'salary_total' => $total
                    ]);
                    if (!$update_salary) {
                        throw new \Exception('Salary details not updated');
                    }
                } else {
                    // Create jika salary belum ada
                    $update_salary = $user->salary()->create([
                        'user_id' => $user->id,
                        'salary_basic' => $request->salary_basic,
                        'salary_allowance' => $total_allowance,
                        'salary_bonus' => $request->salary_bonus,
                        'salary_holiday' => $request->salary_holiday,
                        'salary_total' => $total
                    ]);
                    if (!$update_salary) {
                        throw new \Exception('Salary details not created');
                    }
                }
            }
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'update user salary',
                'controller' => 'UserSalaryController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($update_salary);

    }

    public function destroy($id)
    {
        $delete_salary = null;
        try {
            $user_salary = UserSalary::find($id);
            if ($user_salary) {
                $delete_salary = $user_salary->delete();
                if(!$delete_salary){
                    throw new \Exception('Salary details not deleted');
                }
            }
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'delete user salary',
                'controller' => 'UserSalaryController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($delete_salary);
    }
}
