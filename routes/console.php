<?php

use App\Jobs\AwalBulanJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::job(new AwalBulanJob())
// ->dailyAt('00:01')
// ->when(fn()=> Carbon::now()->isStartOfMonth());
->when(fn()=> true); // untuk testing
