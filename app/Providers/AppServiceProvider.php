<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;

use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\User\UserSalaryController;
use App\Interfaces\UserSalaryInterface;

use App\Http\Controllers\User\UserLeaveController;
use App\Interfaces\UserLeaveInterface;

use App\Http\Controllers\Auth\AuthController;
use App\Interfaces\AuthInterface;

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

    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
    }
}
