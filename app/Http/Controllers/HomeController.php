<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAttendance;
use App\Models\UserShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // $role =  Auth::user()->role_id;
        // dd($role);
        $datenow = Carbon::now();
        $lastSevenDays = $datenow->copy()->subDays(7);

        $start_date = $lastSevenDays->format("Y-m-d");
        $end_date = $datenow->format("Y-m-d");

        $jumlah_data = UserAttendance::count();
        $user_attendance = UserAttendance::with(['user'])
            ->when(Auth::user()->is_admin == 0, function($q){
                $q->where('user_id', Auth::id());
            })
            ->whereBetween('date', [$start_date, $end_date])->limit(5)->get();
        // dd($user_attendance);
        $user_shift = UserShift::with(['user','shift'])
            ->when(Auth::user()->is_admin == 0, function($q){
                $q->where('user_id', Auth::id());
            })
            ->whereBetween('start_date_shift', [$start_date,$end_date])->limit(5)->get();

        return view('user.home.index', compact('jumlah_data','user_shift','user_attendance'));
    }
}
