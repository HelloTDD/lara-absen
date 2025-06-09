<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ShiftService
{
    public function createShift(Request $request)
    {
        DB::beginTransaction();
        try {
            $shift = new Shift();
            $shift->shift_name = $request->shift_name;
            $shift->check_in = $request->check_in;
            $shift->check_out = $request->check_out;
            $shift->save();
            DB::commit();
            Log::info(' shift disimpan', $shift->toArray());

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create  shift', [
                'error' => $e->getMessage(),
            ]);
            Log::info('Shift berhasil disimpan', $shift->toArray());
        }
    }
}
