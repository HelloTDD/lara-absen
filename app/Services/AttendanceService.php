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

        $checkShift = Shift::where(function ($q) use ($checkInTime) {
            $q->where(function ($q1) use ($checkInTime) {
                $q1->where('check_in', '<=', $checkInTime)
                    ->where('check_out', '>=', $checkInTime);
            })->orWhere(function ($q2) use ($checkInTime) {
                $q2->whereColumn('check_in', '>', 'check_out')
                    ->where(function ($q3) use ($checkInTime) {
                        $q3->where('check_in', '<=', $checkInTime)
                            ->orWhere('check_out', '>=', $checkInTime);
                    });
            });
        })->first();

        $shift = UserShift::with(['shift', 'user_attendance'])->where('user_id', $userId)
            ->whereHas('shift',callback: function ($query) use ($checkShift){
                $query->where('shift_id',$checkShift->id); // bagian ini masih bug
            })
            ->whereDate('start_date_shift', '<=', $today)
            ->whereDate('end_date_shift', '>=', $today)
            ->first();

        
        Log::info("[1]:",[isset($shift)]);
        
        $descAttendance = 'MASUK';
        
        
        // Cegah absen dua kali
        $checkInTimeNull = UserAttendance::whereDate('date', $today)
                                ->whereNull('check_in_time')
                                ->exists();
        if(!$shift || $shift->desc_shift == 'LEMBUR' || !$checkInTimeNull){

            if(!empty($shift->desc_shift) && $shift->desc_shift == 'LEMBUR'){
                $descAttendance = 'LEMBUR MASUK';
            }
            
            if (!$shift) {
                $descAttendance = 'LEMBUR MASUK';

                $shift = UserShift::create([
                    'user_id' => $userId,
                    'shift_id' => $checkShift->id,
                    'desc_shift' => 'LEMBUR',
                    'start_date_shift' => $today,
                    'end_date_shift' => $today,
                ]);
            }
        }

        if ($shift->desc_shift === 'HOLIDAY') {
            $descAttendance = 'LEMBUR MASUK';
        }

         // Cegah absen dua kali
        $alreadyCheckedIn = UserAttendance::where('user_shift_id', $shift->id)
            ->whereDate('date', $today)
            ->whereNotNull('check_in_time')
            ->exists();

        if ($alreadyCheckedIn) {
            throw new \Exception('Sudah absen MASUK.');
        }

        // Validasi lokasi dan gambar
        if (!$request->has('image') || !$request->has('lokasi')) {
            throw new \Exception('Data lokasi atau foto tidak lengkap.');
        }
        /**
         * Point Penting ----- selesai
         */
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

        // Log::info('Check-in berhasil', $attendance->toArray());
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
