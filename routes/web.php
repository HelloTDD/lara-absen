<?php

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\User\UserLeaveController;
use App\Http\Controllers\User\UserShiftController;
use App\Http\Controllers\User\UserSalaryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AllowanceController;
use App\Http\Controllers\User\UserContractController;
use App\Http\Controllers\User\UserEmployeeController;
use App\Http\Controllers\CalendarController;

URL::forceScheme('https');

Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
Route::post('/calendar', [CalendarController::class, 'store'])->name('calendar.store');
Route::delete('/calendar/delete/{id}', [CalendarController::class, 'destroy'])->name('calendar.delete');

route::controller(AuthController::class)->group(function(){
    Route::get('/login','index')->name('login');
    Route::post('/ceklogin','login')->name('login.ceklogin');
    Route::get('/logout','logout')->name('login.logout');
});

Route::middleware('auth')->group(function(){
    Route::middleware(['is_admin'])->group(function () {
        Route::controller(UserSalaryController::class)->group(function () {
            Route::get('/user-salaries', 'index')->name('user-salaries.index');
            Route::post('/user-salaries', 'store')->name('user-salaries.store');
            Route::put('/user-salaries/update/{id}', 'update')->name('user-salaries.update');
            Route::get('/user-salaries/delete/{id}', 'destroy')->name('user-salaries.delete');
        });
        Route::controller(UserContractController::class)->group(function(){
            Route::post('/user-contract','store')->name('user-contract.store');
            Route::get('/user-contract/update/{status}/{id}','status_update')->name('user-contract.status');
            Route::put('/user-contract/update/{id}','update')->name('user-contract.update');
            Route::get('/user-contract/delete/{id}','delete')->name('user-contract.delete');
        });
        Route::controller(UserLeaveController::class)->group(function(){
            Route::get('/user-leave', 'index')->name('user-leave.index');
            Route::put('/user-leave/update/{id}', 'update_leave')->name('user-leave.update');
            Route::get('/user-leave/approve/{id}', 'approve_leave')->name('user-leave.approve');
            Route::get('/user-leave/reject/{id}', 'reject_leave')->name('user-leave.reject');
        });

        Route::prefix('user-shift')->controller(UserShiftController::class)->name('user-shift.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/save', 'store')->name('store');
            Route::put('/update/{id}', 'update')->name('update');
            Route::get('/shift/{id}/delete', 'destroy')->name('delete');
        });

         Route::prefix('shift')->controller(ShiftController::class)->name('shift.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/save', 'store')->name('store');
            Route::put('/update/{id}', 'update')->name('update');
            Route::get('/shift/{id}/delete', 'destroy')->name('delete');
        });

        Route::controller(AllowanceController::class)->group(function () {
            Route::post('/allowance', 'store')->name('allowance.store');
            Route::put('/allowance/update/{id}', 'update')->name('allowance.update');
            Route::get('/allowance/delete/{id}', 'destroy')->name('allowance.delete');
        });

    });

    Route::controller(ProfileController::class)->group(function(){
        Route::get('/profile','index')->name('profile.index');
        Route::put('/profile/update','update')->name('profile.update');
        Route::put('/change-password/update','changePassword')->name('profile.change.password');
        Route::get('/slip-gaji','downloadSalarySlip')->name('profile.slip.gaji');
    });

    Route::prefix('role')->controller(RoleController::class)->name('role.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/save', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::put('/update/{id}', 'update')->name('update');
        Route::get('/role/{id}/delete', 'destroy')->name('delete');
    });

    Route::prefix('user-employee')->controller(UserEmployeeController::class)->name('user-employee.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/save', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::put('/update/{id}', 'update')->name('update');
        Route::get('/user-employee/{id}/delete', 'destroy')->name('delete');
    });

    Route::controller(UserContractController::class)->group(function(){
        Route::get('/user-contract',action: 'index')->name('user-contract.index');
        Route::get('/user-contract/unduh-kontrak/{id}','download')->name('user-contract.download');
    });

    Route::controller(UserLeaveController::class)->group(function(){
        Route::get('/user-leave/user', 'index_by_user')->name('user-leave.user');
        Route::post('/user-leave', 'create_leave')->name('user-leave.store');
        Route::delete('/user-leave/delete/{id}','delete_leave')->name('user-leave.delete');
    });

    Route::prefix('attendance')->controller(AttendanceController::class)->name('attendance.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/save', 'store')->name('store');
        Route::get('/list', 'list')->name('list');
        Route::get('/absensi/{id}/edit', 'edit')->name('edit');
        Route::put('/absensi/{id}', 'update')->name('update');
        Route::delete('/absensi/{id}/delete', 'destroy')->name('destroy');

    });

});



// //dummy routes
// Route::get('/home', function () {
//     return 'Login';
// })->name('home');

// Route::get('/login', function () {
//     return 'Home';
// })->name('login');

Route::get('/homes',function () {
    return view('user.home.index');
})->name('homes');

Route::get('/', function () {
    return redirect()->route('attendance.index');
});

