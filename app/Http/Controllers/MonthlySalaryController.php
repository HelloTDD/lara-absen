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
        $data = $monthlySalary->with(['user.allowances'])->where('status','PUBLISHED')->whereHas('user',function($query){
                $query->where('id',Auth::user()->id);
            })->get();;

        $month = monthList();
        $year = yearlist();

        return view('user.monthly-salary.index',compact('data','users','month','year','type_allowance'));
    }

    public function draft(MonthlySalary $monthlySalary)
    {

        $type_allowance = TypeAllowance::all();
        $users = User::with(['salary','role'])->get();
        $data = $monthlySalary->with(['user.allowances'])->where('status','DRAFT')->get();

        $month = monthList();
        $year = yearlist();

        return view('user.monthly-salary.draft',compact('data','users','month','year','type_allowance'));
    }

    /**
     *
     */
    public function store(MonthlySalaryRequest $req, MonthlySalary $monthlySalary)
    {
        $result = null;
        try {
            $salary = UserSalary::with('user')->findOrFail($req->salary_ids);

            // Cek apakah sudah ada draft untuk user + bulan + tahun
            $exists = MonthlySalary::where('user_id', $salary->user_id)
                ->where('month', $req->month)
                ->where('year', $req->year)
                ->exists();

            if ($exists) {
                throw new \Exception("Draft salary untuk user {$salary->user?->name} bulan {$req->month}-{$req->year} sudah ada.");
            }

            $result = $monthlySalary->create([
                'user_id'          => $salary->user_id,   // 🔹 tambahkan ini
                'name'             => $salary->user?->name,
                'salary_basic'     => $salary->salary_basic ?? 0,
                'salary_allowance' => $salary->salary_allowance ?? 0,
                'salary_bonus'     => $salary->salary_bonus ?? 0,
                'salary_holiday'   => $salary->salary_holiday ?? 0,
                'salary_total'     => $salary->salary_total ?? 0,
                'month'            => $req->month,
                'year'             => $req->year,
                'status'           => 'DRAFT'
            ]);

        } catch (\Throwable $th) {
            Log::create([
                'action'      => 'create draft',
                'controller'  => 'MonthlySalaryController',
                'error_code'  => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($result);
    }

    public function update(MonthlySalaryRequest $req, $id)
    {
        $result = null;

        DB::beginTransaction();
        try {
            $salary = MonthlySalary::findOrFail($id);

            // Hitung allowance total
            $allowanceTotal = 0;
            if ($req->has('allowances')) {
                foreach ($req->allowances as $amount) {
                    $allowanceTotal += (int) $amount;
                }
            }

            // Update data salary
            $salary->update([
                'user_id'          => $req->user_id,
                'salary_basic'     => $req->salary_basic ?? 0,
                'salary_allowance' => $allowanceTotal,
                'salary_bonus'     => $req->salary_bonus ?? 0,
                'salary_holiday'   => $req->salary_holiday ?? 0,
                'salary_total'     => ($req->salary_basic ?? 0) + $allowanceTotal + ($req->salary_bonus ?? 0) + ($req->salary_holiday ?? 0),
            ]);

            // ❌ Jangan sync pivot karena kita pakai allowances bawaan user
            // Jadi cukup update angka total saja

            DB::commit();
            $result = $salary;
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::create([
                'action' => 'update monthly salary',
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

    public function destroy($id)
    {
        $salary = MonthlySalary::findOrFail($id);
        $salary->delete();

        return redirect()->route('monthly.salary.index')->with('success', 'Monthly salary berhasil dihapus.');
    }

}
