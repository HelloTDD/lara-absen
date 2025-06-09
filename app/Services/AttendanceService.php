<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\UserShift;
use Illuminate\Http\Request;
use App\Models\UserAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AttendanceService
{

    /**
     * Menghitung jarak antara dua titik koordinat menggunakan rumus Haversine.
     *
     * @param float $latKantor Latitude kantor
     * @param float $lngKantor Longitude kantor
     * @param float $latUser Latitude pengguna
     * @param float $lngUser Longitude pengguna
     * @return float Jarak dalam meter
     */

    public function validation_radius_presensi($latKantor, $lngKantor, $latUser, $lngUser)
    {
        $theta = $lngKantor - $lngUser;
        $dist = sin(deg2rad($latKantor)) * sin(deg2rad($latUser)) +  cos(deg2rad($latKantor)) * cos(deg2rad($latUser)) * cos(deg2rad($theta));
        $miles = rad2deg(acos($dist)) * 60 * 1.1515;
        return $miles * 1.609344 * 1000; //*NOTE -meter
    }

    public function checkIn($userId, Request $request)
    {

        $today = now('Asia/Jakarta')->toDateString();
        $shift = UserShift::where('user_id', $userId)
            ->whereDate('start_date_shift', '<=', $today)
            ->whereDate('end_date_shift', '>=', $today)
            ->first();
        // $shift = UserShift::where('user_id', $userId)->first();

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

        $imageData = $request->image;
        $imageName = 'checkin_' . now()->format('YmdHis') . '.jpg';
        Storage::put("public/absensi/{$imageName}", base64_decode(str_replace('data:image/jpeg;base64,', '', $imageData)));


        $lokasiUser = explode(',', $request->lokasi);
        $latUser = trim($lokasiUser[0]);
        $lngUser = trim($lokasiUser[1]);

        $config = config('officeLocation');
        $latKantor = $config['latitude'];
        $lngKantor = $config['longitude'];
        $radiusMax = $config['radius'];

        $distance = round($this->validation_radius_presensi($latKantor, $lngKantor, $latUser, $lngUser), 2);

        if ($distance > $radiusMax) {
            throw new \Exception("Jarak Anda terlalu jauh dari kantor: {$distance} meter.");
        }

        $attendance->fill([
            'shift_id' => $shift->shift_id,
            'check_in_time' => now('Asia/Jakarta')->toTimeString(),
            'latitude_in' => $latUser,
            'longitude_in' => $lngUser,
            'distance_in' => $distance,
            'check_in_photo' => $imageName,
            'desc_attendance' => 'MASUK',
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

        $imageData = $request->image;
        $imageName = 'checkout_' . now()->format('YmdHis') . '.jpg';
        Storage::put("public/absensi/{$imageName}", base64_decode(str_replace('data:image/jpeg;base64,', '', $imageData)));


        $lokasiUser = explode(',', $request->lokasi);
        $latUser = trim($lokasiUser[0]);
        $lngUser = trim($lokasiUser[1]);

        $config = config('officeLocation');
        $latKantor = $config['latitude'];
        $lngKantor = $config['longitude'];
        $radiusMax = $config['radius'];

        $distance = round($this->validation_radius_presensi($latKantor, $lngKantor, $latUser, $lngUser), 2);

        if ($distance > $radiusMax) {
            throw new \Exception("Jarak Anda terlalu jauh dari kantor: {$distance} meter.");
        }

        $attendance->update([
            'check_out_time'  => now('Asia/Jakarta')->toTimeString(),
            'latitude_out'    => $latUser,
            'longitude_out'   => $lngUser,
            'distance_out'    => $distance,
            'check_out_photo' => $imageName,
            'desc_attendance' => 'PULANG',
        ]);

        Log::info('Check-out berhasil', $attendance->toArray());
    }
}
