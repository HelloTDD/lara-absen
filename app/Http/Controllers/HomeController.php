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
        $datenow = Carbon::now();

        // Rentang 7 hari ke belakang
        $lastSevenDays = $datenow->copy()->subDays(7);
        $start_date = $lastSevenDays->format("Y-m-d");
        $end_date = $datenow->format("Y-m-d");

        // Rentang 7 hari ke depan (tidak termasuk hari ini)
        $tomorrow = Carbon::tomorrow();
        $nextSevenDays = $tomorrow->copy()->addDays(7);

        // Hitung jumlah data absensi
        $jumlah_data = UserAttendance::count();

        // Ambil absensi 7 hari terakhir
        $user_attendance = UserAttendance::with(['user'])
            ->when(Auth::user()->is_admin == 0, function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->whereBetween('date', [$start_date, $end_date])
            ->limit(5)
            ->get();

        // Ambil shift 7 hari terakhir
        $user_shift = UserShift::with(['user', 'shift'])
            ->when(Auth::user()->is_admin == 0, function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->whereBetween('start_date_shift', [$start_date, $end_date])
            ->limit(5)
            ->get();

        // 🔹 Ambil shift 7 hari ke depan (tidak termasuk hari ini)
        $next_shift = UserShift::with(['user', 'shift'])
            ->when(Auth::user()->is_admin == 0, function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->whereBetween('start_date_shift', [$tomorrow->format('Y-m-d'), $nextSevenDays->format('Y-m-d')])
            ->orderBy('start_date_shift', 'asc')
            ->first();
        $userShift = UserShift::with(['user', 'shift', 'user_attendance'])
            ->get()
            ->map(function ($userShift) {
                $cek_already_attendance = $userShift->user_attendance->some(function ($e) use ($userShift) {
                    return $e->user_id == $userShift->user_id && $userShift->start_date_shift == $e->date && !empty($e->check_in_time);
                });
                $userShift->already_attendance = $cek_already_attendance;
                return $userShift;
            });
        $today = Carbon::today()->format('Y-m-d');

        $attendance_today_count = UserAttendance::when(Auth::user()->is_admin == 0, function ($q) {
            $q->where('user_id', Auth::id());
        })
            ->whereDate('date', $today)
            ->whereNotNull('check_in_time')
            ->count();

        // Hitung total shift hari ini (untuk pembanding)
        $total_shift_today = UserShift::when(Auth::user()->is_admin == 0, function ($q) {
            $q->where('user_id', Auth::id());
        })
            ->whereDate('start_date_shift', $today)
            ->count();

        // Persentase kehadiran hari ini
        $attendance_rate_today = $total_shift_today > 0
            ? round(($attendance_today_count / $total_shift_today) * 100, 1)
            : 0;
        // Rentang tanggal bulan ini
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');

        // Ambil absensi bulan ini
        $user_attendance_month = UserAttendance::with(['user'])
            ->when(Auth::user()->is_admin == 0, function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        return view('user.home.index', compact(
            'jumlah_data',
            'user_shift',
            'user_attendance',
            'next_shift',
            'attendance_today_count',
            'total_shift_today',
            'attendance_rate_today',
            'user_attendance_month'
        ));
    }
}
