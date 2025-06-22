<?php

namespace App\Providers;

use Carbon\Carbon;

use App\Interfaces\AuthInterface;
use Illuminate\Support\Facades\App;
use Illuminate\Pagination\Paginator;

use App\Interfaces\UserLeaveInterface;
use App\Interfaces\UserSalaryInterface;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\UserContractInterface;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserLeaveController;
use App\Http\Controllers\User\UserSalaryController;
use App\Http\Controllers\User\UserContractController;

use App\Http\Controllers\CalendarController;
use App\Interfaces\CalendarInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserSalaryInterface::class, function ($app) {
            return new UserSalaryController();
        });

        $this->app->bind(UserLeaveInterface::class, function ($app) {
            return new UserLeaveController();
        });

        $this->app->bind(AuthInterface::class, function ($app){
            return new AuthController();
        });

        $this->app->bind(UserContractInterface::class, function($app){
            return new UserContractController();
        });

        $this->app->bind(CalendarInterface::class, function($app){
            return new CalendarController();
        });

    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        App::setLocale('id');
        Carbon::setLocale('id');
    }
}
