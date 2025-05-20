<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::post('/attendance/save', [AttendanceController::class, 'store'])->name('attendance.store');
Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
