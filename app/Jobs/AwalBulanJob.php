<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\MonthlySalary;
use App\Models\UserSalary;
use Carbon\Carbon;
use App\Models\Log;
use Illuminate\Support\Facades\Log as lg;

class AwalBulanJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(MonthlySalary $monthlySalary): void
    {
        lg::error("start");
        $now = Carbon::now();

        // if ($now->isStartOfMonth()) {
        if (true) {
            $year = $now->year;
            $month = $now->month;

            try {

                $exists_draft = $monthlySalary->where('year',$year)->where('month',$month)->count();
                lg::info("Exists : ".$exists_draft);
                if($exists_draft > 0){
                    lg::info("Im Here");
                    throw new \Exception("Data Draft Sudah Ada", 1);
                }
                
                $get_data = UserSalary::with('user')->get();
                foreach ($get_data as $data) {
                    $monthlySalary->create([
                        'user_id'          => $data->user?->id,   // 🔹 tambahkan ini
                        'name'             => $data->user?->name,
                        'salary_basic'     => $data->salary_basic ?? 0,
                        'salary_allowance' => $data->salary_allowance ?? 0,
                        'salary_bonus'     => $data->salary_bonus ?? 0,
                        'salary_holiday'   => $data->salary_holiday ?? 0,
                        'salary_total'     => $data->salary_total ?? 0,

                        'year' => $year,
                        'month' => $month,
                        'status' => 'DRAFT'
                    ]);
                }

            } catch (\Throwable $th) {
                Log::create([
                    'action' => 'create draft monthly salary',
                    'controller' => '[Job]AwalBulanJob',
                    'error_code' => $th->getCode(),
                    'description' => $th->getMessage(),
                ]);
                
            }
        }
    }
}
