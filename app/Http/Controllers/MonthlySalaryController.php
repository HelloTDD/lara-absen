<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonthlySalaryRequest;
use App\Models\MonthlySalary;
use App\Models\TypeAllowance;
use App\Models\User;
use App\Models\Log;
use App\Models\UserSalary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonthlySalaryController extends Controller
{
    /**
     * Summary of index
     * @param \App\Models\MonthlySalary $monthlySalary
     * @return \Illuminate\Contracts\View\View
     *
     * @var $data output monthly salary data
     */
    public function index(MonthlySalary $monthlySalary)
    {

        $type_allowance = TypeAllowance::all();
        $users = User::with(['salary','role'])->get();
        $raw_orm = $monthlySalary->with('user_salary.user.allowances');
        if(in_array(Auth::user()->role_name, ['Finance', 'Supervisor'])){
            $data = $raw_orm->where('status','DRAFT')->get();
        } else {
            $data = $raw_orm->where('status','PUBLISHED')->whereHas('user_salary.user',function($query){
                $query->where('id',Auth::user()->id);
            })->get();
        }
        $month = monthList();
        $year = yearlist();

        return view('user.monthly-salary.index',compact('data','users','month','year','type_allowance'));
    }

    /**
     *
     */
    public function store(MonthlySalaryRequest $req, MonthlySalary $monthlySalary)
    {
        $result = null;
        try {

            $user = UserSalary::with('user')->where('id',$req->salary_ids)->first()->user?->name;

            $result = $monthlySalary->create([
                'salary_id' => $req->salary_ids,
                'name' => $user,
                'month' => $req->month,
                'year' => $req->year,
                'status' => 'DRAFT'
            ]);
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'create draft',
                'controller' => 'MonthlySalaryController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($result);
    }

    public function publish_salary(MonthlySalary $monthlySalary)
    {
        $result = null;

        DB::beginTransaction();
        try {
            $result = $monthlySalary->where('status','DRAFT')->update([
                'status' => 'PUBLISHED'
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::create([
                'action' => 'publish salary',
                'controller' => 'MonthlySalaryController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($result);
    }
}
