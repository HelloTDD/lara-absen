<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Log;
use App\Models\User;
use App\Models\UserLeave;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\UserLeaveInterface;
use App\Http\Requests\UserLeaveRequest;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Facades\Log as lgs;

class UserLeaveController extends Controller implements UserLeaveInterface
{
    public function index()
    {
        $users = User::all();
        $leaveStatus = leaveStatus();
        $leaves = UserLeave::with('user')
        ->when(session('user_leave_filter'), function ($query) {
            $filter = session('user_leave_filter');
            if (isset($filter['user_id']) && $filter['user_id']) {
                $query->where('user_id', $filter['user_id']);
            }
            if (isset($filter['status']) && $filter['status']) {
                $query->where('status', $filter['status']);
            }
            if (isset($filter['start_date']) && $filter['start_date']) {
                $query->whereDate('leave_date_start', '>=', Carbon::parse($filter['start_date']));
            }
            if (isset($filter['end_date']) && $filter['end_date']) {
                $query->whereDate('leave_date_end', '<=', Carbon::parse($filter['end_date']));
            }
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);
        return view('user.users-leave.index', compact('users', 'leaves', 'leaveStatus'));
    }

    public function filter(Request $request)
    {
        $user_id = $request->input('user_id');
        $status = $request->input('status');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $session = session('user_leave_filter', []);
        $session['user_id'] = $user_id;
        $session['status'] = $status;
        $session['start_date'] = $start_date;
        $session['end_date'] = $end_date;
        session(['user_leave_filter' => $session]);
        return redirect()->route('user-leave.index');
    }

    public function resetFilter()
    {
        session()->forget('user_leave_filter');
        return redirect()->route('user-leave.index');
    }

    public function index_by_user()
    {
        $user_id = Auth::user()->id;
        $users = User::all();
        $leaveStatus = leaveStatus();
        $leaves = UserLeave::where('user_id', $user_id)
        ->when(session('user_leave_filter'), function ($query) {
            $filter = session('user_leave_filter');
            if (isset($filter['user_id']) && $filter['user_id']) {
                $query->where('user_id', $filter['user_id']);
            }
            if (isset($filter['status']) && $filter['status']) {
                $query->where('status', $filter['status']);
            }
            if (isset($filter['start_date']) && $filter['start_date']) {
                $query->whereDate('leave_date_start', '>=', Carbon::parse($filter['start_date']));
            }
            if (isset($filter['end_date']) && $filter['end_date']) {
                $query->whereDate('leave_date_end', '<=', Carbon::parse($filter['end_date']));
            }
        })
        ->with('user')->paginate(10);
        return view('user.users-leave.index', compact('leaves', 'users', 'leaveStatus'));
    }

    public function print()
    {
        try{
        $leaves = UserLeave::with('user')
        ->when(session('user_leave_filter'), function ($query) {
            $filter = session('user_leave_filter');
            if (isset($filter['user_id']) && $filter['user_id']) {
                $query->where('user_id', $filter['user_id']);
            }
            if (isset($filter['status']) && $filter['status']) {
                $query->where('status', $filter['status']);
            }
            if (isset($filter['start_date']) && $filter['start_date']) {
                $query->whereDate('leave_date_start', '>=', Carbon::parse($filter['start_date']));
            }
            if (isset($filter['end_date']) && $filter['end_date']) {
                $query->whereDate('leave_date_end', '<=', Carbon::parse($filter['end_date']));
            }
        })
        ->orderBy('created_at', 'desc')
        ->get();

        $pdf = new \Dompdf\Dompdf();
            $pdf->loadHtml(view('user.users-leave.print', compact('leaves')));
            $pdf->setPaper([0, 0, 595.28, 935.43], 'portrait');
            $pdf->render();

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf');
        } catch (\Throwable $th) {
            lgs::error('Failed to preview user leave', [
                'action' => 'preview user leave',
                'controller' => 'UserleaveController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
            return back()->with('error', 'Failed to preview leave');
        }
    }

    public function export()
    {
        try {
            $leaves = UserLeave::with('user')
            ->when(session('user_leave_filter'), function ($query) {
                $filter = session('user_leave_filter');
                if (isset($filter['user_id']) && $filter['user_id']) {
                    $query->where('user_id', $filter['user_id']);
                }
                if (isset($filter['status']) && $filter['status']) {
                    $query->where('status', $filter['status']);
                }
                if (isset($filter['start_date']) && $filter['start_date']) {
                    $query->whereDate('leave_date_start', '>=', Carbon::parse($filter['start_date']));
                }
                if (isset($filter['end_date']) && $filter['end_date']) {
                    $query->whereDate('leave_date_end', '<=', Carbon::parse($filter['end_date']));
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setTitle('Daftar Cuti Karyawan');
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Nama Karyawan');
            $sheet->setCellValue('C1', 'Tanggal Cuti');
            $sheet->setCellValue('D1', 'Status');
            $sheet->setCellValue('E1', 'Cuti Terpakai');
            $sheet->setCellValue('F1', 'Cuti Tersisa');
            $sheet->getStyle('A1:F1')->getFont()->setBold(true);
            $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1:F1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row = 2;

            foreach ($leaves as $index => $leave) {

                if($leave->status == 'approved'){
                    $status = 'Approve';
                }elseif($leave->status == 'rejected'){
                   $status = 'Reject';
                }elseif($leave->status == 'pending'){
                   $status = 'Pending';
                }elseif($leave->status == 'cancel'){
                   $status = 'Cancel';
                }else{
                   $status = 'Pending';
                }

                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $leave->user->name);
                $sheet->setCellValue('C' . $row, $leave->leave_date_start ." ~ ". $leave->leave_date_end);
                $sheet->setCellValue('D' . $row, $status);
                $sheet->setCellValue('E' . $row, max(0,12 - $leave->user->leave));
                $sheet->setCellValue('F' . $row,  $leave->user->leave);
                $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A' . $row . ':F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . $row . ':F' . $row)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setSize(12);
                $row++;
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = "Daftar_Cuti_Karyawan_".date('Y-m-d').".xlsx";
            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment;filename=\"$fileName\"");
            $writer->save("php://output");
            exit();


        } catch (\Throwable $th) {
            lgs::error('Failed to export user leave', [
                'action' => 'export user leave',
                'controller' => 'UserleaveController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
            return back()->with('error', 'Failed to export leave');
        }
    }

    public function create_leave(UserLeaveRequest $request)
    {
        $create_leave = null;
        try {
            $user_id = Auth::check() ? Auth::user()->id : User::where('id', $request->user_id)->first()->id;
            if(Auth::user()->is_admin == 1){
                $user = User::where('id', $request->user_id)->first();
            }else {
                $user = User::find($user_id);
            }

            if ($user) {
                $joinDate = Carbon::parse($user->date_joined);
                $oneYearAfterJoin = $joinDate->copy()->addYear();
                $today = Carbon::now();

                if ($today->lt($oneYearAfterJoin)) {
                    return redirect()->back()->with('error', 'User must be employed for at least 1 year to take leave');
                }
            }else {
                throw new \Exception("User tersebut tidak ada", 1);
            }
            if($user->leave <= 0 || $user->leave == '0'){
                return redirect()->back()->with('error', 'User has no leave');
            }

            $create_leave = UserLeave::create([
                'user_id' => $user->id,
                'leave_date_start' => $request->start_date,
                'leave_date_end' => $request->end_date,
                'desc_leave' => filterSpecialChar($request->description),
                'status' => 'pending',
            ]);
            if (!$create_leave) {
                throw new \Exception('Leave details not saved');
            }
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'create user leave',
                'controller' => 'UserLeaveController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($create_leave);
    }
    public function update_leave(UserLeaveRequest $request, $id)
    {
        $leave = null;
        try {
            $leave = UserLeave::find($id);
            if ($leave) {
                $leave->update([
                    'leave_date_start' => $request->start_date,
                    'leave_date_end' => $request->end_date,
                    'desc_leave' => filterSpecialChar($request->description)
                ]);
            } else {
                throw new \Exception('Leave not found');
            }
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'update user leave',
                'controller' => 'UserLeaveController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($leave);
    }
    public function delete_leave($id)
    {
        $leave = null;
        try {
            $leave = UserLeave::find($id);
            if ($leave) {
                $leave->delete();
            } else {
                throw new \Exception('Leave not found');
            }
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'delete user leave',
                'controller' => 'UserLeaveController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($leave);
    }
    public function approve_leave($id)
    {
        $result = null;
        try {
            $leave = UserLeave::with('user')->find($id);

            if (!$leave) {
                throw new \Exception('Leave not found');
            }

            if($leave->user?->leave < 0 ){
                throw new \Exception('User has no leave');
            }

            if ($leave->user) {

                $joinDate = Carbon::parse($leave->user?->date_joined);
                $oneYearAfterJoin = $joinDate->copy()->addYear();
                $today = Carbon::now();

                if ($today->lt($oneYearAfterJoin)) {
                    throw new \Exception('User must be employed for at least 1 year to take leave');
                }

                $user = $leave->user;
                $user->leave = max(0, $user->leave - 1); // supaya tidak negatif
                $user->save();
            } else {
                throw new \Exception("User tersebut tidak ada", 1);
            }

            $result = $leave->update([
                'status' => 'approved'
            ]);

        } catch (\Throwable $th) {

            Log::create([
                'action' => 'approve user leave',
                'controller' => 'UserLeaveController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);

        }

        return returnProccessData($result);
    }
    public function reject_leave($id)
    {
        $result = null;
        try {
            $leave = UserLeave::with('user')->find($id);
            if ($leave) {
                $leave->user()->update([
                    'leave' => $leave->user->leave  += 1
                ]);
                $result = $leave->update(['status' => 'rejected']);
            } else {
                throw new \Exception('Leave not found');
            }
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'reject user leave',
                'controller' => 'UserLeaveController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($result);
    }

    public function cancel_request($id)
    {
        $result = null;
        try {
            $leave = UserLeave::with('user')->find($id);
            if ($leave) {
                $leave->user()->update([
                    'leave' => $leave->user->leave  += 1
                ]);
                $result = $leave->update([
                    'status' => 'canceled',
                ]);
            } else {
                throw new \Exception('Leave not found');
            }
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'canceled user leave',
                'controller' => 'UserLeaveController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($result);
    }

    public function cancel_leave($id)
    {
        $leave = null;
        try {
            $leave = UserLeave::with('user')->find($id);
            $user = User::where('id' , $leave->user_id)->first();
            if ($leave) {
                $leave->update([
                    'status' => 'rejected',
                ]);
                $user->update([
                    'leave' => $user->leave  += 1,
                ]);
            } else {
                throw new \Exception('Leave not found');
            }
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'pending user leave',
                'controller' => 'UserLeaveController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($leave);
    }
}
