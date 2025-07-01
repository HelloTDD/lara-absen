<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Shift;
use App\Models\UserShift;
use Illuminate\Http\Request;
use App\Services\UserShiftService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserShiftRequest;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class UserShiftController extends Controller
{
    public function index()
    {
        // Logic to display user shifts
        $users = User::all();
        $shift = Shift::all();
        $usershift = UserShift::with(['user', 'shift'])
            ->when(session('user_shift_filter.user_id'), function ($query) {
                return $query->where('user_id', session('user_shift_filter.user_id'));
            })
            ->when(session('user_shift_filter.shift_id'), function ($query) {
                return $query->where('shift_id', session('user_shift_filter.shift_id'));
            })
            ->when(session('user_shift_filter.start_date_shift'), function ($query) {
                return $query->where('start_date_shift', '>=', session('user_shift_filter.start_date_shift'));
            })
            ->when(session('user_shift_filter.end_date_shift'), function ($query) {
                return $query->where('end_date_shift', '<=', session('user_shift_filter.end_date_shift'));
            })
            ->orderBy('start_date_shift', 'desc')
            ->get();
        return view('user.user-shift.index', compact('users', 'shift', 'usershift'));
    }

    /**
     * filter show data user shift
     *
     */
    public function filter(Request $request)
    {
        $user_id = $request->input('user_id');
        $shift_id = $request->input('shift_id');
        $start_date_shift = $request->input('start_date_shift');
        $end_date_shift = $request->input('end_date_shift');

        $session = session('user_shift_filter', []);
        $session['user_id'] = $user_id;
        $session['shift_id'] = $shift_id;
        $session['start_date_shift'] = $start_date_shift;
        $session['end_date_shift'] = $end_date_shift;
        session(['user_shift_filter' => $session]);
        return redirect()->route('user-shift.index');
    }

    public function resetFilter()
    {
        session()->forget('user_shift_filter');
        return redirect()->route('user-shift.index');
    }

    public function print()
    {
        try {
        $users = User::all();
        $shift = Shift::all();
        $usershift = UserShift::with(['user', 'shift'])
            ->when(session('user_shift_filter.user_id'), function ($query) {
                return $query->where('user_id', session('user_shift_filter.user_id'));
            })
            ->when(session('user_shift_filter.shift_id'), function ($query) {
                return $query->where('shift_id', session('user_shift_filter.shift_id'));
            })
            ->when(session('user_shift_filter.start_date_shift'), function ($query) {
                return $query->where('start_date_shift', '>=', session('user_shift_filter.start_date_shift'));
            })
            ->when(session('user_shift_filter.end_date_shift'), function ($query) {
                return $query->where('end_date_shift', '<=', session('user_shift_filter.end_date_shift'));
            })
            ->orderBy('start_date_shift', 'desc')
            ->get();

            $pdf = new \Dompdf\Dompdf();
            $pdf->loadHtml(view('user.user-shift.print', compact('usershift')));
            $pdf->setPaper([0, 0, 595.28, 935.43], 'portrait');
            $pdf->render();

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf');
        } catch (\Throwable $th) {
            Log::error('Failed to preview user Shift', [
                'action' => 'preview user Shift',
                'controller' => 'UserShiftController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
            return back()->with('error', 'Failed to preview Shift');
        }
        return view('user.user-shift.print', compact('users', 'shift', 'usershift'));
    }

    public function export()
    {
        try {
        $users = User::all();
        $shift = Shift::all();
        $usershift = UserShift::with(['user', 'shift'])
            ->when(session('user_shift_filter.user_id'), function ($query) {
                return $query->where('user_id', session('user_shift_filter.user_id'));
            })
            ->when(session('user_shift_filter.shift_id'), function ($query) {
                return $query->where('shift_id', session('user_shift_filter.shift_id'));
            })
            ->when(session('user_shift_filter.start_date_shift'), function ($query) {
                return $query->where('start_date_shift', '>=', session('user_shift_filter.start_date_shift'));
            })
            ->when(session('user_shift_filter.end_date_shift'), function ($query) {
                return $query->where('end_date_shift', '<=', session('user_shift_filter.end_date_shift'));
            })
            ->orderBy('start_date_shift', 'desc')
            ->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setTitle('Daftar Shift Karyawan');
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Nama Karyawan');
            $sheet->setCellValue('C1', 'Shift');
            $sheet->setCellValue('D1', 'Tanggal Mulai Shift');
            $sheet->setCellValue('E1', 'Tanggal Selesai Shift');
            $sheet->setCellValue('F1', 'Masuk');
            $sheet->setCellValue('G1', 'Pulang');
            $sheet->getStyle('A1:G1')->getFont()->setBold(true);
            $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1:G1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row = 2;

            foreach ($usershift as $index => $shift) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $shift->user->name);
                $sheet->setCellValue('C' . $row, $shift->shift->shift_name);
                $sheet->setCellValue('D' . $row, $shift->start_date_shift);
                $sheet->setCellValue('E' . $row, $shift->end_date_shift);
                $sheet->setCellValue('F' . $row, $shift->shift->check_in);
                $sheet->setCellValue('G' . $row, $shift->shift->check_out);
                $sheet->getStyle('A' . $row . ':G' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . $row . ':G' . $row)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setSize(12);
                $row++;
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = "Daftar_Shift_Karyawan_".date('Y-m-d').".xlsx";
            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment;filename=\"$fileName\"");
            $writer->save("php://output");
            exit();

        } catch (\Throwable $th) {
            Log::error('Failed to Export user Shift', [
                'action' => 'Export user Shift',
                'controller' => 'UserShiftController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
            return back()->with('error', 'Failed to Export Shift');
        }

    }

    public function store(UserShiftRequest $request, UserShiftService $service)
    {
        $cek_shift = UserShift::where('shift_id',$request->shift_id)
                                ->where('user_id',$request->user_id)
                                ->where('start_date_shift',$request->start_date_shift)
                                ->first();
        if($cek_shift){
            // throw new \Exception("Jadwal Shift Sudah ada", 1);
            return redirect()->route('user-shift.index')->with('error', 'Shift Already exist.');
        }

        DB::beginTransaction();
        try {
                $service->createUserShift($request);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to create user shift', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to create shift: ' . $e->getMessage()]);
        }
        return redirect()->route('user-shift.index')->with('success', 'Shift created successfully.');
    }

    public function update(Request $request, $id)
    {

        $userShift = UserShift::find($id);

        if (!$userShift) {

            if($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shift Not Found'
                ]);
            }

            return redirect()->route('user-shift.index')->with('error', 'Shift not found.');

        }

        $cek_shift = UserShift::where('user_id',$request->user_id)
        ->where('start_date_shift',$request->start_date_shift)
        ->first();
        if($cek_shift){
            // throw new \Exception("Jadwal Shift Sudah ada", 1);
             return redirect()->back()->with('error', 'Shift Already exist.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'start_date_shift' => 'required|date',
            'end_date_shift' => 'required|date|after_or_equal:start_date_shift',
        ]);

        DB::beginTransaction();
        try {
            $userShift->update([
                'user_id' => $request->user_id,
                'shift_id' => $request->shift_id,
                'start_date_shift' => $request->start_date_shift,
                'end_date_shift' => $request->end_date_shift
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user shift', [
                'error' => $e->getMessage(),
                'id' => $id,
            ]);

            if($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update shift: ' . $e->getMessage()
                ]);
            }
            return redirect()->back()->withErrors(['error' => 'Failed to update shift: ' . $e->getMessage()]);
        }

        //masih bisa disederhanakan gunakan helper yang pernah dibuat agar tidak redundan dan kode re-useable
        if($request->ajax())
        {
            return response()->json([
                'success' => true,
                'message' => 'berhasil mengganti shift'
            ]);
        } else {
            return redirect()->route('user-shift.index')->with('success', 'Shift updated successfully.');
        }
    }

    public function destroy($id)
    {
        $userShift = UserShift::find($id);
        if (!$userShift) {
            return redirect()->route('user-shift.index')->with('error', 'Shift not found.');
        }
        DB::beginTransaction();
        try {
            $userShift->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete user shift', [
                'error' => $e->getMessage(),
                'id' => $id,
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to delete shift: ' . $e->getMessage()]);
        }
        return redirect()->route('user-shift.index')->with('success', 'Shift deleted successfully.');
    }
}
