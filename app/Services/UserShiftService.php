<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\UserShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserShiftService
{
    public function createShift(Request $request)
    {
        DB::beginTransaction();
        try {
            $userShift = new UserShift();
            $userShift->user_id = $request->user_id;
            $userShift->shift_id = $request->shift_id;
            $userShift->start_date_shift = Carbon::parse($request->start_date_shift);
            $userShift->end_date_shift = Carbon::parse($request->end_date_shift);
            $userShift->save();
            DB::commit();
            Log::info('Check-in berhasil', $userShift->toArray());

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create user shift', [
                'error' => $e->getMessage(),
            ]);
            Log::info('Check-in berhasil', $userShift->toArray());
        }
    }
}
