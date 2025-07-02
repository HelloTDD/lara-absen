<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Shift;
use App\Models\UserShift;
use Illuminate\Http\Request;
use App\Models\UserAttendance;
use Illuminate\Support\Facades\DB;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AttendanceRequest;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AttendanceController extends Controller
{
    public function index()
    {
        $user_id = Auth::id() ?? 1;
        $date = now()->format('Y-m-d');
        $time = Carbon::now('Asia/Jakarta')->format('H:i:s');

        // test night time
        // $date = '2025-07-03';
        // $time = '04:00:00';
        // $date = Carbon::parse($date, 'Asia/Jakarta')->format('Y-m-d');
        // $time = Carbon::parse($time, 'Asia/Jakarta')->format('H:i:s');
        $existing = UserAttendance::where('user_id', $user_id)
                    ->where('date', now()->format('Y-m-d'))
                    ->where('check_in_time', '!=', null)
                    ->orderBy('created_at', 'desc')
                    ->first();
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
                Log::info("check_out");
                $service->checkOut($userId, $request);
            }elseif ($action === 'overtime') {
                Log::info("overtime");
                $service->overTime($userId, $request);
            }else {
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
                'service' => $service,
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
        $users = User::all();
        $shift = Shift::all();
        if(Auth::user()->is_admin == 1){
            $data = UserAttendance::with('user', 'user_shift', 'user_shift.shift')
                ->when(session('attendance_filter.user_id'), function ($query) {
                    return $query->where('user_id', session('attendance_filter.user_id'));
                })
                ->when(session('attendance_filter.shift_id'), function ($query) {
                    return $query->where('shift_id', session('attendance_filter.shift_id'));
                })
                ->when(session('attendance_filter.start_date_shift'), function ($query) {
                    return $query->where('date', '>=',session('attendance_filter.start_date_shift'));
                })
                ->when(session('attendance_filter.end_date_shift'), function ($query) {
                    return $query->where('date', '<=' , session('attendance_filter.end_date_shift'));
                })
                ->orderBy('date', 'desc')
                ->get();
        } else {
            $data = UserAttendance::with('user', 'user_shift', 'user_shift.shift')
                ->where('user_id', Auth::id())
                ->when(session('attendance_filter.user_id'), function ($query) {
                    return $query->where('user_id', session('attendance_filter.user_id'));
                })
                ->when(session('attendance_filter.shift_id'), function ($query) {
                    return $query->where('shift_id', session('attendance_filter.shift_id'));
                })
                ->when(session('attendance_filter.start_date_shift'), function ($query) {
                    return $query->where('date', '>=',session('attendance_filter.start_date_shift'));
                })
                ->when(session('attendance_filter.end_date_shift'), function ($query) {
                    return $query->where('date', '<=' , session('attendance_filter.end_date_shift'));
                })
                ->orderBy('date', 'desc')
                ->get();
        }

        return view('user.attendance.list',compact('data', 'users', 'shift'));
    }

    public function filter(Request $request)
    {
       $user_id = $request->input('user_id');
       $shift_id = $request->input('shift_id');
       $start_date_shift = $request->input('start_date_shift');
       $end_date_shift = $request->input('end_date_shift');

       $session = session('attendance_filter', []);
       $session['user_id'] = $user_id;
       $session['shift_id'] = $shift_id;
       $session['start_date_shift'] = $start_date_shift;
       $session['end_date_shift'] = $end_date_shift;
       session(['attendance_filter' => $session]);

       return redirect()->route('attendance.list');
    }

    public function resetFilter()
    {
        session()->forget('attendance_filter');
        return redirect()->route('attendance.list');
    }

    public function print()
    {
        try{
            $users = User::all();
            $shift = Shift::all();
            if(Auth::user()->is_admin == 1){
                $data = UserAttendance::with('user', 'shift')
                    ->when(session('attendance_filter.user_id'), function ($query) {
                        return $query->where('user_id', session('attendance_filter.user_id'));
                    })
                    ->when(session('attendance_filter.shift_id'), function ($query) {
                        return $query->where('shift_id', session('attendance_filter.shift_id'));
                    })
                    ->when(session('attendance_filter.start_date_shift'), function ($query) {
                        return $query->where('date', '>=',session('attendance_filter.start_date_shift'));
                    })
                    ->when(session('attendance_filter.end_date_shift'), function ($query) {
                        return $query->where('date', '<=' , session('attendance_filter.end_date_shift'));
                    })
                    ->orderBy('date', 'desc')
                    ->get();
            } else {
                $data = UserAttendance::with('user', 'shift')
                    ->where('user_id', Auth::id())
                    ->when(session('attendance_filter.user_id'), function ($query) {
                        return $query->where('user_id', session('attendance_filter.user_id'));
                    })
                    ->when(session('attendance_filter.shift_id'), function ($query) {
                        return $query->where('shift_id', session('attendance_filter.shift_id'));
                    })
                    ->when(session('attendance_filter.start_date_shift'), function ($query) {
                        return $query->where('date', '>=',session('attendance_filter.start_date_shift'));
                    })
                    ->when(session('attendance_filter.end_date_shift'), function ($query) {
                        return $query->where('date', '<=' , session('attendance_filter.end_date_shift'));
                    })
                    ->orderBy('date', 'desc')
                    ->get();
            }

            $pdf = new \Dompdf\Dompdf();
            $pdf->loadHtml(view('user.attendance.print', compact('data')));
            $pdf->setPaper([0, 0, 595.28, 935.43], 'portrait');
            $pdf->render();

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf');
        }catch (\Throwable $th) {
            Log::error('Failed to preview user Attendance', [
                'action' => 'preview user Attendance',
                'controller' => 'UserAttendanceController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
            return back()->with('error', 'Failed to preview Attendance');
        }
    }

    public function export()
    {
        try {
            $users = User::all();
            $shift = Shift::all();
            if(Auth::user()->is_admin == 1){
                $data = UserAttendance::with('user', 'shift')
                    ->when(session('attendance_filter.user_id'), function ($query) {
                        return $query->where('user_id', session('attendance_filter.user_id'));
                    })
                    ->when(session('attendance_filter.shift_id'), function ($query) {
                        return $query->where('shift_id', session('attendance_filter.shift_id'));
                    })
                    ->when(session('attendance_filter.start_date_shift'), function ($query) {
                        return $query->where('date', '>=',session('attendance_filter.start_date_shift'));
                    })
                    ->when(session('attendance_filter.end_date_shift'), function ($query) {
                        return $query->where('date', '<=' , session('attendance_filter.end_date_shift'));
                    })
                    ->orderBy('date', 'desc')
                    ->get();
            } else {
                $data = UserAttendance::with('user', 'shift')
                    ->where('user_id', Auth::id())
                    ->when(session('attendance_filter.user_id'), function ($query) {
                        return $query->where('user_id', session('attendance_filter.user_id'));
                    })
                    ->when(session('attendance_filter.shift_id'), function ($query) {
                        return $query->where('shift_id', session('attendance_filter.shift_id'));
                    })
                    ->when(session('attendance_filter.start_date_shift'), function ($query) {
                        return $query->where('date', '>=',session('attendance_filter.start_date_shift'));
                    })
                    ->when(session('attendance_filter.end_date_shift'), function ($query) {
                        return $query->where('date', '<=' , session('attendance_filter.end_date_shift'));
                    })
                    ->orderBy('date', 'desc')
                    ->get();
            }

            $spreadsheet =  new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setTitle('Daftar Absensi Karyawan');
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Nama Karyawan');
            $sheet->setCellValue('C1', 'Tanggal');
            $sheet->setCellValue('D1', 'Shift');
            $sheet->setCellValue('E1', 'Jam Masuk');
            $sheet->setCellValue('F1', 'Foto Masuk');
            $sheet->setCellValue('G1', 'Jam Pulang');
            $sheet->setCellValue('H1', 'Foto Pulang');
            $sheet->setCellValue('I1', 'Status');
            $sheet->setCellValue('J1', 'Keterangan');
            $sheet->getStyle('A1:J1')->getFont()->setBold(true);
            $sheet->getStyle('A1:J1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1:J1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row = 2;

            foreach ($data as $index => $item) {
                $item->late_reason = null;
                if ($item->check_in_time && $item->shift) {
                    $shiftStart = \Carbon\Carbon::parse($item->date . ' ' . $item->shift->check_in, 'Asia/Jakarta');
                    $checkInTime = \Carbon\Carbon::parse($item->date . ' ' . $item->check_in_time, 'Asia/Jakarta');

                    if ($checkInTime->gt($shiftStart)) {
                        $lateMinutes = $shiftStart->diffInMinutes($checkInTime);
                        $hours = floor($lateMinutes / 60);
                        $minutes = $lateMinutes % 60;

                        if ($hours > 0 && $minutes > 0) {
                            $item->late_reason = "Terlambat {$hours} jam {$minutes} menit";
                            } elseif ($hours > 0) {
                                $item->late_reason = "Terlambat {$hours} jam";
                            } else {
                                $item->late_reason = "Terlambat {$minutes} menit";
                            }
                        } else {
                            $item->late_reason = null;
                        }
                    }

                    if($item->check_in_photo){
                        $checkin_foto = asset('storage/absensi/' . $item->check_in_photo);
                    }else{
                        $checkin_foto = '-';
                    }

                    if($item->check_out_photo){
                        $checkout_foto = asset('storage/absensi/' . $item->check_out_photo);
                    }else{
                        $checkout_foto = '-';
                    }

                    if($item->desc_attendance == 'PULANG'){
                        $desc = 'PULANG';
                    } else {
                        $desc = 'MASUK';
                    }

                    if($item->late_reason){
                        isset($item->late_reason) ? $item->late_reason : 'Tepat Waktu';
                    }else{
                        '-';
                    }

                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $item->user->name ?? '-');
                $sheet->setCellValue('C' . $row, \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y'));
                $sheet->setCellValue('D' . $row, $item->shift->shift_name ?? '-' );
                $sheet->setCellValue('E' . $row, $item->check_in_time ?? '-');
                $sheet->setCellValue('F' . $row, $checkin_foto);
                $sheet->setCellValue('G' . $row, $item->check_out_time ?? '-');
                $sheet->setCellValue('H' . $row, $checkout_foto);
                $sheet->setCellValue('I' . $row, $desc);
                $sheet->setCellValue('J' . $row, $item->late_reason ?? '-');
                $sheet->getStyle('A' . $row . ':J' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A' . $row . ':J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . $row . ':J' . $row)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A' . $row . ':J' . $row)->getFont()->setSize(12);
                $row++;
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = "Daftar_Absensi_".date('Y-m-d').".xlsx";
            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment;filename=\"$fileName\"");
            $writer->save("php://output");
            exit();

            $filename = 'absensi_' . now()->format('Ymd_His') . '.xlsx';
        } catch (\Throwable $th) {
            Log::error('Failed to export user Attendance', [
                'action' => 'export user Attendance',
                'controller' => 'UserAttendanceController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
            return back()->with('error', 'Failed to export Attendance');
        }
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
