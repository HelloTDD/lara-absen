<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use App\Services\ShiftService;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ShiftRequest;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Logic to display a list of shifts
        $shift = Shift::all();
        return view('user.shift.index', compact('shift'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShiftRequest $request, ShiftService $shiftService)
    {
        DB::beginTransaction();
        try {
            $shiftService->createShift($request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create shift', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to create shift: ' . $e->getMessage()]);
        }
        return redirect()->route('shift.index')->with('success', 'Shift created successfully.');
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $shift = Shift::find($id);
        if (!$shift) {
            Log::error('Shift not found', ['id' => $id]);
            return redirect()->route('shift.index')->with('error', 'Shift not found.');
        }
        // $request->validate([
        //     'shift_name' => 'required|string|max:255',
        //     'check_in' => 'required|date_format:H:i',
        //     'check_out' => 'required|date_format:H:i',
        // ]);
        // dd($request->all());
        DB::beginTransaction();
        try {
            $shift->shift_name = $request->shift_name;
            $shift->check_in = $request->check_in;
            $shift->check_out = $request->check_out;
            $shift->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update shift', [
                'error' => $e->getMessage(),
                'id' => $id,
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to update shift: ' . $e->getMessage()]);
        }
        return redirect()->route('shift.index')->with('success', 'Shift updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $userShift = Shift::find($id);
        if (!$userShift) {
            return redirect()->route('shift.index')->with('error', 'Shift not found.');
        }
        DB::beginTransaction();
        try {
            $userShift->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete shift', [
                'error' => $e->getMessage(),
                'id' => $id,
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to delete shift: ' . $e->getMessage()]);
        }
        return redirect()->route('shift.index')->with('success', 'Shift deleted successfully.');
    }
}
