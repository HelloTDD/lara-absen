<?php

namespace App\Services;

use App\Models\UserAttendance;
use App\Models\UserShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceService
{
    public function checkIn($userId, Request $request)
    {
        $today = now('Asia/Jakarta')->toDateString();
        $shift = UserShift::where('user_id', $userId)->first();

        if (!$shift) {
            throw new \Exception('Shift tidak ditemukan.');
        }

        $attendance = UserAttendance::firstOrNew([
            'user_id' => $userId,
            'date' => $today
        ]);

        if ($attendance->check_in_time) {
            throw new \Exception('Sudah absen MASUK.');
        }

        $attendance->fill([
            'shift_id'        => $shift->shift_id,
            'check_in_time'   => now('Asia/Jakarta')->toTimeString(),
            'latitude_in'     => $request->latitude_in,
            'longitude_in'    => $request->longitude_in,
            'distance_in'     => $request->distance_in,
            'check_in_photo'  => $request->check_in_photo,
            'desc_attendance' => 'Absen MASUK',
        ])->save();

        Log::info('Check-in berhasil', $attendance->toArray());
    }

    public function checkOut($userId, Request $request)
    {
        $today = now('Asia/Jakarta')->toDateString();
        $attendance = UserAttendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in_time) {
            throw new \Exception('Belum absen MASUK.');
        }

        if ($attendance->check_out_time) {
            throw new \Exception('Sudah absen PULANG.');
        }

        $attendance->update([
            'check_out_time'  => now('Asia/Jakarta')->toTimeString(),
            'latitude_out'    => $request->latitude_out,
            'longitude_out'   => $request->longitude_out,
            'distance_out'    => $request->distance_out,
            'check_out_photo' => $request->check_out_photo,
            'desc_attendance' => 'COMPLETED',
        ]);

        Log::info('Check-out berhasil', $attendance->toArray());
    }
}
