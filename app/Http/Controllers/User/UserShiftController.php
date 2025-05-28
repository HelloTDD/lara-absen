<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Shift;
use App\Services\UserShiftService;
use App\Models\UserShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserShiftRequest;

class UserShiftController extends Controller
{
    public function index()
    {
        // Logic to display user shifts
        $users = User::all();
        $shift = Shift::all();
        $usershift = UserShift::all();
        return view('user-shift.index', compact('users', 'shift', 'usershift'));
    }

    public function store(UserShiftRequest $request, UserShiftService $service)
    {
        DB::beginTransaction();
        try {
            $service->createShift($request);
            DB::commit();
        } catch (\Exception $e) {
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
            return redirect()->route('user-shift.index')->with('error', 'Shift not found.');
        }
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'start_date_shift' => 'required|date',
            'end_date_shift' => 'required|date|after_or_equal:start_date_shift',
        ]);
        DB::beginTransaction();
        try {
            $userShift->user_id = $request->user_id;
            $userShift->shift_id = $request->shift_id;
            $userShift->start_date_shift = $request->start_date_shift;
            $userShift->end_date_shift = $request->end_date_shift;
            $userShift->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user shift', [
                'error' => $e->getMessage(),
                'id' => $id,
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to update shift: ' . $e->getMessage()]);
        }
        return redirect()->route('user-shift.index')->with('success', 'Shift updated successfully.');
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
