<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\UserShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Log as LogModel;
use Illuminate\Support\Facades\Storage;

class UserShiftService
{
    /**
     * Create a new user shift.
     */
    public function createUserShift($request)
    {
        $id = null;
        DB::beginTransaction();
        try {
            $data = is_array($request) ? $request : (array) $request;
            $userShift = new UserShift();
            $userShift->user_id = $data['user_id'];
            $userShift->shift_id = $data['shift_id'];
            $userShift->start_date_shift = Carbon::parse($data['start_date_shift']);
            $userShift->end_date_shift = Carbon::parse($data['end_date_shift']);

            if($userShift->save()){
                $return = true;
                $id = $userShift->id;
                DB::commit();
                Log::info('Check-in berhasil', $userShift->toArray());
            } else {
                throw new \Exception("Failed to save shift data", 1);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Gagal membuat user shift', [
                'action' => 'create type calendar event',
                'controller' => 'CalendarController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
            $return = false;
        }

        return [
            'return' => $return,
            'id' => $id
        ];
    }
}
