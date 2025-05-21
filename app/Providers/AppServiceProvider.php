<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\User\UserSalaryController;
use App\Interfaces\UserSalaryInterface;

use App\Http\Controllers\User\UserLeaveController;
use App\Interfaces\UserLeaveInterface;
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

    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
