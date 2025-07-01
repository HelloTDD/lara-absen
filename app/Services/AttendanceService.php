<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Shift;
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
        $now = now('Asia/Jakarta');
        $today = $now->toDateString();
        $checkInTime = $now->toTimeString();
        Log::info("Time : ", [$checkInTime]);
        
        // Cari shift yang aktif pada jam sekarang
        $checkShift = Shift::where(function ($q1) use ($checkInTime) {
            $q1->where('check_in', '<=', $checkInTime)
            ->where('check_out', '>=', $checkInTime);
        })->orWhere(function ($q2) use ($checkInTime) {
            $q2->whereColumn('check_in', '>', 'check_out')
            ->where(function ($q3) use ($checkInTime) {
                $q3->where('check_in', '<=', $checkInTime)
                ->orWhere('check_out', '>=', $checkInTime);
            });
        })->first();
        
        Log::info("Shift : ", [$checkShift->id]);
        
        // Cari user shift yang aktif hari ini
        $shift = UserShift::with(['shift', 'user_attendance'])
                ->where('user_id', $userId)
                ->whereDate('start_date_shift', '<=', $today)
                ->whereDate('end_date_shift', '>=', $today)
                ->first();
        
        $descAttendance = 'MASUK';
        
        // Cegah absen dua kali
        $alreadyCheckedIn = UserAttendance::with(['user_shift'])->where('user_id', $userId)
        ->whereHas('user_shift',function($q) use ($checkShift){
            $q->where('shift_id',$checkShift->id);
        })
        ->whereDate('date', $today)
        ->whereNotNull('check_in_time')
        ->exists();
        
        Log::info("Already Absen : ", [$alreadyCheckedIn]);

        if ($alreadyCheckedIn) {
            throw new \Exception('Sudah absen MASUK.');
        }

        // Validasi lokasi dan gambar
        if (!$request->has('image') || !$request->has('lokasi')) {
            throw new \Exception('Data lokasi atau foto tidak lengkap.');
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

        // --- Penentuan desc_attendance sesuai scenario ---
        if (!$shift) {
            $descAttendance = 'LEMBUR MASUK';
            if ($checkShift && ($checkInTime < $checkShift->check_in || $checkInTime > $checkShift->check_out)) {
                $descShift = null;
            } else {
                $descShift = 'LEMBUR';
            }

            $shift = UserShift::create([
                'user_id' => $userId,
                'shift_id' => $checkShift ? $checkShift->id : null,
                'desc_shift' => $descShift,
                'start_date_shift' => $today,
                'end_date_shift' => $today,
            ]);
        } else {
            if ($shift->desc_shift === 'LEMBUR') {
                $descAttendance = 'LEMBUR MASUK';
            } elseif ($shift->desc_shift === 'HOLIDAY') {
                $descAttendance = 'LEMBUR MASUK';
            } elseif (empty($shift->desc_shift)) {
                if ($checkShift && ($checkInTime < $checkShift->check_in || $checkInTime > $checkShift->check_out)) {
                    $sudahAbsenShiftLain = UserAttendance::where('user_id', $userId)
                        ->whereDate('date', $today)
                        ->where('desc_attendance','MASUK')
                        ->exists();
                    if ($sudahAbsenShiftLain) {
                        $descAttendance = 'LEMBUR MASUK';
                    } else {
                        $descAttendance = 'ABSEN DILUAR JAM KERJA';
                    }
                } else {
                    $descAttendance = 'MASUK';
                }
            } else {
                $descAttendance = 'LEMBUR MASUK';
            }
        }

        // Simpan absensi
        UserAttendance::create([
            'user_id' => $userId,
            'user_shift_id' => $shift->id,
            'date' => $today,
            'check_in_time' => $checkInTime,
            'latitude_in' => $latUser,
            'longitude_in' => $lngUser,
            'distance_in' => $distance,
            'check_in_photo' => $imageName,
            'desc_attendance' => $descAttendance,
        ]);
    }

    public function checkOut($userId, Request $request)
    {
        $today = now('Asia/Jakarta')->toDateString();
        $attendance = UserAttendance::with('shift')->where('user_id', $userId)
            ->where('date', $today)
            ->first();

        $descAttendance = 'PULANG';

        if ($attendance->desc_attendance === 'LEMBUR MASUK') {
            $descAttendance = 'LEMBUR PULANG';
        }

        $time_now = Carbon::today()->format("H:i:s");
        if ($attendance->shift?->check_in > $time_now) {
            $descAttendance = 'ABSEN PULANG DILUAR JAM KERJA';
        }

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
            'desc_attendance' => $descAttendance,
        ]);

        Log::info('Check-out berhasil', $attendance->toArray());
    }
}
