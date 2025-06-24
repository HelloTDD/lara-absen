<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\UserShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Log as lg;
use Illuminate\Support\Facades\Storage;

class UserShiftService
{
    public function createUserShift($request)
    {
        DB::beginTransaction();
        try {
            $userShift = new UserShift();
            $userShift->user_id = is_array($request) ? $request['user_id'] : $request->user_id;
            $userShift->shift_id = is_array($request) ? $request['shift_id'] : $request->shift_id;
            $userShift->start_date_shift = Carbon::parse(is_array($request) ? $request['start_date_shift'] : $request->start_date_shift);
            $userShift->end_date_shift = Carbon::parse(is_array($request) ? $request['end_date_shift'] : $request->end_date_shift);
            
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
            lg::create([
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
