<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\User\UserSalaryController;
use App\Http\Controllers\User\UserLeaveController;

Route::controller(UserSalaryController::class)->group(function () {
    Route::get('/user-salaries', 'index')->name('user-salaries.index');
});

Route::middleware(['is_admin'])->group(function () {
    Route::controller(UserSalaryController::class)->group(function () {
        Route::post('/user-salaries', 'store')->name('user-salaries.store');
    });
});


Route::controller(UserLeaveController::class)->group(function(){
    Route::get('/user-leave', 'index')->name('user-leave.index');
    Route::get('/user-leave/user', 'index_by_user')->name('user-leave.user');
    Route::post('/user-leave', 'create_leave')->name('user-leave.store');
    Route::put('/user-leave/{id}', 'update_leave')->name('user-leave.update');
    Route::delete('/user-leave/delete/{id}','delete_leave')->name('user-leave.delete');
    Route::get('/user-leave/approve/{id}', 'approve_leave')->name('user-leave.approve');
    Route::get('/user-leave/reject/{id}', 'reject_leave')->name('user-leave.reject');
});

Route::prefix('attendance')->controller(AttendanceController::class)->name('attendance.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/save', 'store')->name('store');
    Route::get('/list', 'list')->name('list');
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

