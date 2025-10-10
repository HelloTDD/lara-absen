<?php

use App\Models\UserShift;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\AllowanceController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\MonthlySalaryController;
use App\Http\Controllers\User\UserLeaveController;
use App\Http\Controllers\User\UserShiftController;
use App\Http\Controllers\User\UserSalaryController;
use App\Http\Controllers\User\UserContractController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\User\UserEmployeeController;
use App\Http\Controllers\User\UserReferenceController;


Route::get('/version-lara', function () {
    return view('welcome');
});

Route::controller(CalendarController::class)->group(function () {
    Route::get('/calendar', 'index')->name('calendar.index');
    Route::post('/calendar', 'store')->name('calendar.store');
    Route::put('/calendar/update/{id}', 'update')->name('calendar.update');
    Route::delete('/calendar/delete/{id}', 'destroy')->name('calendar.delete');
});

route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'index')->name('login');
    Route::post('/ceklogin', 'login')->name('login.ceklogin');
    Route::get('/logout', 'logout')->name('login.logout');
});

Route::middleware('auth')->group(function () {

    /**
     * ==============================
     * SUPERVISOR (akses penuh)
     * ==============================
     */
    Route::middleware(['checkRole:Supervisor'])->group(function () {
        // Salary
        Route::controller(UserSalaryController::class)->group(function () {
            Route::get('/user-salaries', 'index')->name('user-salaries.index');
            Route::post('/user-salaries', 'store')->name('user-salaries.store');
            Route::put('/user-salaries/update/{id}', 'update')->name('user-salaries.update');
            Route::get('/user-salaries/delete/{id}', 'destroy')->name('user-salaries.delete');
        });

        // Contract
        Route::controller(UserContractController::class)->group(function () {
            Route::post('/user-contract', 'store')->name('user-contract.store');
            Route::get('/user-contract/update/{status}/{id}', 'status_update')->name('user-contract.status');
            Route::put('/user-contract/update/{id}', 'update')->name('user-contract.update');
            Route::get('/user-contract/delete/{id}', 'delete')->name('user-contract.delete');
        });

        // Leave (Supervisor mengelola cuti)
        Route::controller(UserLeaveController::class)->group(function () {
            Route::get('/user-leave', 'index')->name('user-leave.index');
            Route::post('/filter', 'filter')->name('user-leave.filter');
            Route::get('/reset', 'resetFilter')->name('user-leave.reset');
            Route::put('/user-leave/update/{id}', 'update_leave')->name('user-leave.update');
            Route::get('/user-leave/approve/{id}', 'approve_leave')->name('user-leave.approve');
            Route::get('/user-leave/reject/{id}', 'reject_leave')->name('user-leave.reject');
            Route::get('/user-leave/canceled/{id}', 'cancel_leave')->name('user-leave.cancel');
            Route::delete('/user-leave/delete/{id}', 'delete_leave')->name('user-leave.delete');
            Route::get('/print', 'print')->name('user-leave.print');
            Route::get('/export', 'export')->name('user-leave.export');
        });

        // Reference
        Route::controller(UserReferenceController::class)->group(function () {
            Route::post('/user-references', 'store')->name('user-references.store');
            Route::put('/user-references/update/{id}', 'update')->name('user-references.update');
            Route::get('/user-references/delete/{id}', 'destroy')->name('user-references.delete');
        });

        // Shift & UserShift
        Route::prefix('user-shift')->middleware('checkRole:Supervisor,Scheduler')->controller(UserShiftController::class)->name('user-shift.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/save', 'store')->name('store');
            Route::post('/filter', 'filter')->name('filter');
            Route::get('/reset', 'resetFilter')->name('reset');
            Route::put('/update/{id}', 'update')->name('update');
            Route::get('/shift/{id}/delete', 'destroy')->name('delete');
            Route::delete('/shift/{id}/delete', 'destroy')->name('delete-shift');
            Route::get('/print', 'print')->name('print');
            Route::get('/export', 'export')->name('export');
        });

        Route::prefix('shift')->middleware('checkRole:Supervisor,Scheduler')->controller(ShiftController::class)->name('shift.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/save', 'store')->name('store');
            Route::put('/update/{id}', 'update')->name('update');
            Route::get('/shift/{id}/delete', 'destroy')->name('delete');
        });

        // Allowance
        Route::controller(AllowanceController::class)->group(function () {
            Route::post('/allowance', 'store')->name('allowance.store');
            Route::put('/allowance/update/{id}', 'update')->name('allowance.update');
            Route::get('/allowance/delete/{id}', 'destroy')->name('allowance.delete');
        });

        // Config
        Route::controller(ConfigController::class)->group(function () {
            Route::get('/config', 'index')->name('config.index');
            Route::put('/config/update', 'update')->name('config.update');
        });

        // Role Management
        Route::prefix('role')->controller(RoleController::class)->name('role.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/save', 'store')->name('store');
            Route::get('/edit/{id}', 'edit')->name('edit');
            Route::put('/update/{id}', 'update')->name('update');
            Route::get('/role/{id}/delete', 'destroy')->name('delete');
        });

        // Employee Management
        Route::prefix('user-employee')->controller(UserEmployeeController::class)->name('user-employee.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/save', 'store')->name('store');
            Route::get('/edit/{id}', 'edit')->name('edit');
            Route::put('/update/{id}', 'update')->name('update');
            Route::get('/user-employee/{id}/delete', 'destroy')->name('delete');
            Route::get('/user-employee/{id}/show', 'show')->name('show');
        });
    });

    /**
     * ==============================
     * FINANCE (akses Salary + Contract + Report)
     * ==============================
     */
    Route::middleware(['checkRole:Supervisor,Finance'])->group(function () {
        // Salary
        Route::controller(UserSalaryController::class)->group(function () {
            Route::get('/user-salaries', 'index')->name('user-salaries.index');
            Route::post('/user-salaries', 'store')->name('user-salaries.store');
            Route::put('/user-salaries/update/{id}', 'update')->name('user-salaries.update');
            Route::get('/user-salaries/delete/{id}', 'destroy')->name('user-salaries.delete');
        });
        // Monthly Salary report
        Route::controller(MonthlySalaryController::class)->group(function () {
            Route::get('/monthly-salary', 'index')->name('finance.monthly.salary.index');
            Route::post('/monthly-salary', 'store')->name('finance.monthly.salary.store');
            Route::get('/monthly-salary/draft', 'draft')->name('finance.monthly.salary.draft');
            Route::put('/monthly-salary/publish', 'publish_salary')->name('finance.monthly.salary.publish');
            Route::put('/monthly-salary/{id}', 'update')->name('finance.monthly.salary.update');
            Route::delete('/monthly-salary/{id}', 'destroy')->name('finance.monthly.salary.destroy');
        });
    });

    /**
     * ==============================
     * SCHEDULER (akses Shift + UserShift)
     * ==============================
     */
    Route::middleware(['checkRole:Supervisor,Scheduler'])->group(function () {
        Route::prefix('user-shift')->controller(UserShiftController::class)->name('scheduler.user-shift.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/save', 'store')->name('store');
        });

        Route::prefix('shift')->controller(ShiftController::class)->name('scheduler.shift.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/save', 'store')->name('store');
        });
    });

    /**
     * ==============================
     * ROUTE BISA DIAKSES SEMUA ROLE
     * ==============================
     */
    Route::prefix('user-shift')->controller(UserShiftController::class)->name('user-shift.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/save', 'store')->name('store');
        Route::post('/filter', 'filter')->name('filter');
        Route::get('/reset', 'resetFilter')->name('reset');
        Route::put('/update/{id}', 'update')->name('update');
        Route::get('/shift/{id}/delete', 'destroy')->name('delete');
        Route::delete('/shift/{id}/delete', 'destroy')->name('delete-shift');
        Route::get('/print', 'print')->name('print');
        Route::get('/export', 'export')->name('export');
    });
    Route::prefix('shift')->controller(ShiftController::class)->name('shift.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/save', 'store')->name('store');
        Route::put('/update/{id}', 'update')->name('update');
        Route::get('/shift/{id}/delete', 'destroy')->name('delete');
    });
    Route::get('/homes', [HomeController::class, 'index'])->name('homes');
    Route::controller(MonthlySalaryController::class)->group(function () {
        Route::get('/monthly-salary', 'index')->name('monthly.salary.index');
    });
    Route::controller(UserReferenceController::class)->group(function () {
        Route::get('/user-references', 'index')->name('user-references.index');
        Route::get('/user-references/unduh-references/{id}', 'download')->name('user-references.download');
        Route::get('/user-references/{id}/preview', 'preview')->name('user-references.preview');
    });

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile.index');
        Route::put('/profile/update', 'update')->name('profile.update');
        Route::put('/change-password/update', 'changePassword')->name('profile.change.password');
        Route::put('/user-bank/update', 'updateBank')->name('profile.update.bank');
        Route::post('/slip-gaji', 'downloadSalarySlip')->name('profile.slip.gaji');
    });

    Route::controller(UserContractController::class)->group(function () {
        Route::get('/user-contract', 'index')->name('user-contract.index');
        Route::get('/user-contract/unduh-kontrak/{id}', 'download')->name('user-contract.download');
    });

    Route::controller(UserLeaveController::class)->group(function () {
        Route::get('/user-leave/user', 'index_by_user')->name('user-leave.user');
        Route::post('/user-leave', 'create_leave')->name('user-leave.store');
        Route::get('/user-leave/canceled-request/{id}', 'cancel_request')->name('user-leave.cancel-request');
        Route::delete('/user-leave/delete/{id}', 'delete_leave')->name('user-leave.delete');
    });

    Route::prefix('attendance')->controller(AttendanceController::class)->name('attendance.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/save', 'store')->name('store');
        Route::get('/list', 'list')->name('list');
        Route::post('/filter', 'filter')->name('filter');
        Route::get('/reset', 'resetFilter')->name('reset');
        Route::get('/absensi/{id}/edit', 'edit')->name('edit');
        Route::put('/absensi/{id}', 'update')->name('update');
        Route::delete('/absensi/{id}/delete', 'destroy')->name('destroy');
        Route::get('/print', 'print')->name('print');
        Route::get('/export', 'export')->name('export');
        Route::get('/maps', 'maps')->name('maps');
    });

    Route::put('/ajax/update-shift/{id}', [UserShiftController::class, 'update'])->name('user-shift.ajax.update');
    Route::delete('/user-shift/shift/{id}/delete', [UserShiftController::class, 'destroy'])->name('user-shift.delete-shift');
});

Route::get('/', function () {
    return redirect()->route('attendance.index');
});
