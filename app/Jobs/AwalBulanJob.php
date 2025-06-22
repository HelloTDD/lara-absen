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
                        'salary_id' => $data->id,
                        'name' => $data->user?->name,
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
