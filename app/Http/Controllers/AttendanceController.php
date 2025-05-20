<?php

namespace App\Http\Controllers;

use App\Models\UserAttendance;
use App\Models\UserShift;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function index()
    {
         // $user = Auth()->user();
        $user = [
            'id' => 1,
        ];
        $date = now()->format('Y-m-d');
        $time = Carbon::now('Asia/Jakarta')->format('H:i:s');

        $existing = UserAttendance::where('user_id', $user['id'])
                    ->where('date', now()->format('Y-m-d'))
                    ->where('check_in_time', '!=', null)
                    ->first();
        if (empty($existing)) {
            $existing = new UserAttendance();
            $existing->check_in_time = null;
            $existing->check_out_time = null;
        }
        return view('attendance.index',compact('date', 'time', 'existing'));
    }

    public function store(Request $request)
    {
        $user = [
            'id' => 1,
        ];

        $action = $request->input('action');

        $request->validate([
            'tanggal' => 'required|date_format:Y-m-d',
            'time'    => 'required|date_format:H:i:s',
        ]);

        $shift = UserShift::where('user_id', $user['id'])->first();

        if (!$shift) {
            return back()->with('error', 'Shift tidak ditemukan.');
        }

        $today = now('Asia/Jakarta')->format('Y-m-d');

        $existing = UserAttendance::where('user_id', $user['id'])
            ->where('date', $today)
            ->where('check_in_time', '!=', null)
            ->first();

        DB::beginTransaction();

        try {
            if ($action === 'check_in') {
                if ($existing && $existing->check_in_time) {
                    return back()->with('error', 'Anda sudah melakukan absen masuk.');
                }

                UserAttendance::create([
                    'user_id'        => $user['id'],
                    'shift_id'       => $shift->shift_id,
                    'date'           => $today,
                    'check_in_time'  => now('Asia/Jakarta')->format('H:i:s'),
                    'latitude_in'    => $request->latitude_in ?? null,
                    'longitude_in'   => $request->longitude_in ?? null,
                    'distance_in'    => $request->distance_in ?? null,
                    'check_in_photo' => $request->check_in_photo ?? null,
                    'desc_attendance'=> 'Absen MASUK',
                ]);

                Log::info('Absen MASUK berhasil disimpan');
                DB::commit();
                return back()->with('success', 'Absen MASUK berhasil.');
            }

            if ($action === 'check_out') {
                if (!$existing || !$existing->check_in_time) {
                    return back()->with('error', 'Anda belum melakukan absen MASUK.');
                }

                if ($existing->check_out_time) {
                    return back()->with('error', 'Anda sudah melakukan absen PULANG.');
                }

                $existing->update([
                    'check_out_time'  => now('Asia/Jakarta')->format('H:i:s'),
                    'latitude_out'    => $request->latitude_out ?? null,
                    'longitude_out'   => $request->longitude_out ?? null,
                    'distance_out'    => $request->distance_out ?? null,
                    'check_out_photo' => $request->check_out_photo ?? null,
                    'desc_attendance'=> 'COMPLETED',
                ]);

                Log::info('Absen PULANG berhasil disimpan', $existing->toArray());
                DB::commit();
                return back()->with('success', 'Absen PULANG berhasil.');
            }

            return back()->with('error', 'Aksi tidak valid.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal Absen', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'raw'   => $e->getRawSql(),
                'request' => $request->all(),
            ]);

            return back()->with('error', 'Terjadi kesalahan saat absen. Silakan coba lagi.');
        }
    }

    public function list()
    {
        $data = UserAttendance::with('user', 'shift')
                ->where('user_id', 1)
                ->orderBy('date', 'desc')
                ->get();
        // dd($data);
        return view('attendance.list',compact('data'));
    }

}
