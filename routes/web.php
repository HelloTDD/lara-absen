<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserSalaryController;

Route::controller(UserSalaryController::class)->group(function () {
    Route::get('/user-salaries', 'index')->name('user-salaries.index');
});

Route::middleware(['is_admin'])->group(function () {
    Route::controller(UserSalaryController::class)->group(function () {
        Route::post('/user-salaries', 'store')->name('user-salaries.store');
    });
});

//dummy routes
Route::get('/home', function () {
    return 'Login';
})->name('home');

Route::get('/login', function () {
    return 'Home';
})->name('login');

Route::get('/', function () {
    return view('welcome');
});
