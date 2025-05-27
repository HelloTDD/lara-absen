<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\UserShift;
use Illuminate\Http\Request;
use App\Models\UserAttendance;
use Illuminate\Support\Facades\DB;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AttendanceRequest;

class AttendanceController extends Controller
{
    public function index()
    {
        $user_id = Auth::id() ?? 1;
        $date = now()->format('Y-m-d');
        $time = Carbon::now('Asia/Jakarta')->format('H:i:s');

        $existing = UserAttendance::where('user_id', $user_id)
                    ->where('date', now()->format('Y-m-d'))
                    ->where('check_in_time', '!=', null)
                    ->first();
                    // dd($existing);
        if (empty($existing)) {
            $existing = new UserAttendance();
            $existing->check_in_time = null;
            $existing->check_out_time = null;
        }
        return view('attendance.index',compact('date', 'time', 'existing'));
    }

    public function store(AttendanceRequest $request, AttendanceService $service)
    {
        // $userId = Auth::id() ?? 1;
        // dd($request->all());
        $userId = 1;

        $action = $request->input('action');


        DB::beginTransaction();

        try {
            if ($action === 'check_in') {
                $service->checkIn($userId, $request);
            } elseif ($action === 'check_out') {
                $service->checkOut($userId, $request);
            } else {
                // return back()->with('error', 'Aksi tidak valid.');
                return response()->json([
                    'status' => 400,
                    'success' => false,
                    'message' => "Aksi tidak valid",
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => 200,
                // 'data' => $data,
                'success' => true,
                'message' => "Berhasil presensi",
                'jenis_presensi' => $action,
            ]);
            // return back()->with('success', 'Absen berhasil.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error during attendance processing: ' . $e->getMessage(), [
                'user_id' => $userId,
                'action' => $action,
                'request_data' => $request->all(),
            ]);
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => "Gagal presensi",
            ]);
            // return back()->with('error', $e->getMessage());
        }
    }

    public function list()
    {
        $data = UserAttendance::with('user', 'shift')
                ->where('user_id',  Auth::id() ?? 1)
                ->orderBy('date', 'desc')
                ->get();
        return view('attendance.list',compact('data'));
    }

}
