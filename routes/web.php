<?php

use App\Http\Controllers\AttendanceController;
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

Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::post('/attendance/save', [AttendanceController::class, 'store'])->name('attendance.store');
Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
