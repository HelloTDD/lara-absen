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

        // Cari shift yang aktif pada jam sekarang, exclude "Overtime"

        if ($checkInTime >= '04:00:00' && $checkInTime < '08:30:00') {
            $checkShift = Shift::where('shift_name', 'Pagi')->first();
        } else {
           $checkShift = Shift::where(function ($q1) use ($checkInTime) {
                $q1->where('check_in', '<=', $checkInTime)
                    ->where('check_out', '>=', $checkInTime);
            })->orWhere(function ($q2) use ($checkInTime) {
                $q2->whereColumn('check_in', '>', 'check_out')
                    ->where(function ($q3) use ($checkInTime) {
                        $q3->where('check_in', '<=', $checkInTime)
                            ->orWhere('check_out', '>=', $checkInTime);
                    });
            })
            ->first();
        }

        Log::info("Shift : ", [$checkShift?->id ?? 'Tidak ditemukan']);

        // Cari shift user yang aktif hari ini
        $shift = UserShift::with(['shift', 'user_attendance'])
            ->where('user_id', $userId)
            ->whereDate('start_date_shift', '<=', $today)
            ->whereDate('end_date_shift', '>=', $today)
            ->first();

        $descAttendance = 'MASUK';

        // Cegah absen dua kali pada shift yang sama
        $alreadyCheckedIn = UserAttendance::with(['user_shift'])
            ->where('user_id', $userId)
            ->whereHas('user_shift', function ($q) use ($checkShift) {
                $q->where('shift_id', $checkShift?->id);
            })
            ->whereDate('date', $today)
            ->whereNotNull('check_in_time')
            ->exists();

        Log::info("Already Absen : ", [$alreadyCheckedIn]);

        if ($alreadyCheckedIn) {
            throw new \Exception('Sudah absen MASUK.');
        }

        // Validasi lokasi & gambar
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
        //$distance  = 0;
        $distance = round($this->validation_radius_presensi($latKantor, $lngKantor, $latUser, $lngUser), 2);

        // if ($distance > $radiusMax) {
        //     throw new \Exception("Jarak Anda terlalu jauh dari kantor: {$distance} meter.");
        // }

        // --- Penentuan desc_attendance ---
        if (!$shift) {
            // Jika belum punya shift hari ini, selalu buat dengan status MASUK
            $descShift = 'MASUK';
            Log::info("kondisi shift if MASUK (user belum punya shift, default MASUK)");

            $shift = UserShift::create([
                'user_id' => $userId,
                'shift_id' => $checkShift?->id,
                'desc_shift' => $descShift,
                'start_date_shift' => $today,
                'end_date_shift' => $today,
            ]);

            Log::info("Shift baru dibuat dengan desc_shift: {$shift->desc_shift}, waktu: {$checkInTime}, shift_in: {$checkShift?->check_in}");
        } else {
            switch ($shift->desc_shift) {
                case 'LEMBUR':
                    $descAttendance = 'LEMBUR';
                    Log::info("kondisi shift lembur if ke 1");
                    break;

                case 'HOLIDAY':
                    $descAttendance = 'HOLIDAY';
                    Log::info("kondisi shift lembur if ke 2");
                    break;

                case 'MASUK':
                default:
                    if ($checkShift && !self::isWithinShiftTime($checkInTime, $checkShift->check_in, $checkShift->check_out)) {
                        $sudahAbsenShiftLain = UserAttendance::where('user_id', $userId)
                            ->whereDate('date', $today)
                            ->where('desc_attendance', 'MASUK')
                            ->exists();

                        if ($sudahAbsenShiftLain) {
                            $descAttendance = 'LEMBUR';
                            Log::info("kondisi shift lembur if ke 3");
                        } else {
                            $descAttendance = 'ABSEN DILUAR JAM KERJA';
                            Log::info("kondisi shift lembur if ke 4");
                        }
                    } else {
                        $descAttendance = 'MASUK';
                        Log::info("kondisi shift lembur if ke 5");
                    }
                    break;
            }
        }

        // Simpan data absensi
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

    protected static function isWithinShiftTime($now, $start, $end)
    {
        if ($start <= $end) {
            // Shift normal, contoh: 08:00 - 16:00
            return $now >= $start && $now <= $end;
        } else {
            // Shift malam, contoh: 19:00 - 04:00
            return $now >= $start || $now <= $end;
        }
    }

    public function checkOut($userId, Request $request)
    {
        $now = now('Asia/Jakarta');
        //test time
        // $now = \Carbon\Carbon::parse('2025-07-03 04:00:00', 'Asia/Jakarta');
        // Jika masih dini hari, anggap shift kemarin
        $targetDate = $this->resolveShiftDateForCheckout($now);
        // Cari absensi hari target yang belum check_out (apapun jenisnya)
        $attendance = UserAttendance::where('user_id', $userId)
            ->where('date', $targetDate)
            ->whereNull('check_out_time')
            ->orderByDesc('id')
            ->first();

        if (!$attendance) {
            throw new \Exception('Belum absen MASUK atau LEMBUR hari ini. '.$targetDate);
        }

        $descAttendance = match ($attendance->desc_attendance) {
            'LEMBUR' => 'LEMBUR',
            'ABSEN DILUAR JAM KERJA' => 'ABSEN PULANG DILUAR JAM KERJA',
            default => 'MASUK',
        };

        if (!$attendance->check_in_time) {
            throw new \Exception('Belum absen MASUK.');
        }

        if ($attendance->check_out_time) {
            throw new \Exception('Sudah absen PULANG.');
        }

        if (!$request->has('image') || !$request->has('lokasi')) {
            throw new \Exception('Data lokasi atau foto tidak lengkap.');
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
        //$distance = 0;
        $distance = round($this->validation_radius_presensi($latKantor, $lngKantor, $latUser, $lngUser), 2);

        // if ($distance > $radiusMax) {
        //     throw new \Exception("Jarak Anda terlalu jauh dari kantor: {$distance} meter.");
        // }

        $attendance->update([
            'check_out_time'  => $now->toTimeString(),
            'latitude_out'    => $latUser,
            'longitude_out'   => $lngUser,
            'distance_out'    => $distance,
            'check_out_photo' => $imageName,
            'desc_attendance' => $descAttendance,
        ]);

        Log::info('Check-out berhasil', $attendance->toArray());
    }

    private function resolveShiftDateForCheckout(\Carbon\Carbon $now): string
    {
        $lateShift = Shift::whereColumn('check_in', '>', 'check_out')
            ->orderBy('check_out', 'desc')
            ->first();

        $maxNightOutTime = $lateShift?->check_out ?? '05:00:00';
        $timeNow = $now->toTimeString();

        return $timeNow <= $maxNightOutTime
            ? $now->copy()->subDay()->toDateString()
            : $now->toDateString();
    }

    public function overTime($userId, Request $request)
    {
        $now = now('Asia/Jakarta');
        $today = $now->toDateString();
        $checkInTime = $now->toTimeString();
        Log::info("Time : ", [$checkInTime]);

        // Cari shift yang aktif pada jam sekarang, exclude "Overtime"
        $checkShift = Shift::where(function ($q1) use ($checkInTime) {
            $q1->where('check_in', '<=', $checkInTime)
                ->where('check_out', '>=', $checkInTime);
        })->orWhere(function ($q2) use ($checkInTime) {
            $q2->whereColumn('check_in', '>', 'check_out')
                ->where(function ($q3) use ($checkInTime) {
                    $q3->where('check_in', '<=', $checkInTime)
                        ->orWhere('check_out', '>=', $checkInTime);
                });
        })
        ->first();

        Log::info("Shift : ", [$checkShift?->id ?? 'Tidak ditemukan']);

        // Cari shift user yang aktif hari ini
        $shift = UserShift::with(['shift', 'user_attendance'])
            ->where('user_id', $userId)
            ->whereDate('start_date_shift', '<=', $today)
            ->whereDate('end_date_shift', '>=', $today)
            ->first();

        // Validasi lokasi & gambar
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

         if (!$shift) {
            // Jika belum punya shift hari ini, selalu buat dengan status MASUK
            $descShift = 'Overtime';
            Log::info("kondisi shift if MASUK (user belum punya shift, default MASUK)");

            $shift = UserShift::create([
                'user_id' => $userId,
                'shift_id' => $checkShift?->id,
                'desc_shift' => $descShift,
                'start_date_shift' => $today,
                'end_date_shift' => $today,
            ]);

            Log::info("Shift baru dibuat dengan desc_shift: {$shift->desc_shift}, waktu: {$checkInTime}, shift_in: {$checkShift?->check_in}");
        }

        // Simpan data absensi
        UserAttendance::create([
            'user_id' => $userId,
            'user_shift_id' => $shift->id,
            'date' => $today,
            'check_in_time' => $checkInTime,
            'latitude_in' => $latUser,
            'longitude_in' => $lngUser,
            'distance_in' => $distance,
            'check_in_photo' => $imageName,
            'desc_attendance' => 'LEMBUR',
        ]);
    }

}
