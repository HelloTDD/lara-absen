<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Shift;
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
        return view('user.attendance.index',compact('date', 'time', 'existing'));
    }

    public function store(AttendanceRequest $request, AttendanceService $service)
    {
        $userId = Auth::id() ?? 1;

        $action = $request->input('action');


        DB::beginTransaction();

        try {
            if ($action === 'check_in') {
                $service->checkIn($userId, $request);
            } elseif ($action === 'check_out') {
                Log::info("masuk sini");
                $service->checkOut($userId, $request);
            } else {
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
                'message' => "Gagal presensi - " . $e->getMessage(),
            ]);
        }
    }

    public function list()
    {
        if(Auth::user()->is_admin == 1){
            $data = UserAttendance::with('user', 'shift')
                ->orderBy('date', 'desc')
                ->get();
        } else {
            $data = UserAttendance::with('user', 'shift')
                ->where('user_id', Auth::id())
                ->orderBy('date', 'desc')
                ->get();
        }

        return view('user.attendance.list',compact('data'));
    }

    public function edit($id)
    {
        $absensi = UserAttendance::findOrFail($id);
        $shifts = Shift::all();
        return view('user.attendance.edit', compact('absensi', 'shifts'));
    }

    public function update(Request $request, $id)
    {
        $absensi = UserAttendance::findOrFail($id);


        $validated = $request->validate([
            'date' => 'required|date',
            'shift_id' => 'required|exists:shifts,id',
            'check_in_time' => 'nullable',
            'check_out_time' => 'nullable',
            'check_in_photo' => 'nullable|image|max:2048',
            'check_out_photo' => 'nullable|image|max:2048',
            'desc_attendance' => 'required|in:MASUK,PULANG',
        ]);

        DB::beginTransaction();
        try {
            $absensi->update($validated);

            if ($request->hasFile('check_in_photo')) {
                $checkInFile = $request->file('check_in_photo')->store('absensi', 'public');
                $absensi->check_in_photo = basename($checkInFile);

            }

            if ($request->hasFile('check_out_photo')) {
                $checkOutFile = $request->file('check_out_photo')->store('absensi', 'public');
                $absensi->check_out_photo = basename($checkOutFile);
            }


            $absensi->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating attendance: ' . $e->getMessage(), [
                'attendance_id' => $id,
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui data absensi: ' . $e->getMessage()]);
        }

        return redirect()->route('attendance.list')->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $absensi = UserAttendance::findOrFail($id);
        DB::beginTransaction();
        try {
            $absensi->delete();
            DB::commit();
            return redirect()->route('attendance.list')->with('success', 'Data absensi berhasil dihapus.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting attendance: ' . $e->getMessage(), [
                'attendance_id' => $id,
                'user_id' => Auth::id(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus data absensi: ' . $e->getMessage()]);
        }
    }

}
