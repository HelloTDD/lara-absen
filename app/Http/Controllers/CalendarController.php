<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\UserShift;
use App\Models\User;
use App\Models\Log;

use Illuminate\Support\Facades\Log as lg;
use App\Interfaces\CalendarInterface;
use App\Services\UserShiftService;
use App\Http\Requests\CalendarEventRequest;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller implements CalendarInterface
{
    public function index()
    {

        $user = User::all()
            ->map(function ($user) {
                return [
                    'id' => 'user_' . $user->id,
                    'title' => 'Ulang Tahun - ' . $user->name,
                    'start' =>  Carbon::parse($user->birth_date)->format('Y-m-d'),
                    'extendedProps' => [
                        'tipe' => 'user',
                    ],
                ];
            });

        $userShift = UserShift::with(['user', 'shift.attendance'])
            ->get()
            ->map(function ($userShift) {
                $cek_already_attendance = $userShift->shift?->attendance->some(function ($e) use ($userShift) {
                    return $e->user_id == $userShift->user_id && $userShift->start_date_shift == $e->date && !empty($e->check_in_time);
                });

                $color = $cek_already_attendance
                    ? ['backgroundColor' => '#8ce089', 'textColor' => '#22941e']
                    : (
                        $userShift->desc_shift == 'HOLIDAY'
                        ? []
                        : []
                    );

                return array_merge([
                    'id' => 'shift_' . $userShift->id,
                    'title' => $userShift->shift?->shift_name . ' - ' . $userShift->user->name,
                    'start' => $userShift->start_date_shift,
                    'end' => $userShift->end_date_shift,
                    'extendedProps' => [
                        'tipe' => 'shift',
                        'user' => $userShift->user->id,
                        'shift_id' => $userShift->shift?->id,
                    ],
                ], $color);
            });

        // dd($userShift);
        $calendarEvents = CalendarEvent::with('user')->get()
            ->map(function ($event) {
                return [
                    'id' => 'event_' . $event->id,
                    'title' => $event->user?->name . ":" . $event->title,
                    'start' => $event->start_date->format('Y-m-d'),
                    'end' => $event->end_date->format('Y-m-d'),
                    'extendedProps' => [
                        'tipe' => 'event',
                        'extend_data' => $event->extend_data ?? [],
                        'created_by' => $event->created_by ?? [],
                    ],
                ];
            });

        $shift = Shift::all();
        $events = array_merge($userShift->toArray(), $calendarEvents->toArray());
        $event_finals = array_merge($events, $user->toArray());
        return view('user.calendar.index', compact('event_finals', 'shift'));
    }

    public function update(CalendarEventRequest $request, CalendarEvent $calendarEvent, $id)
    {
        $result = null;
        try {
            $get_data = $calendarEvent->findOrFail($id);
            if (!$get_data->count() < 0) {
                throw new \Exception("Event Tidak Ada", 1);
            }

            $result = $get_data->update([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ]);
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'update calendar event',
                'controller' => 'CalendarController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }
        return response()->json([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Event updated successfully.' : 'Failed to update event',
        ]);
    }

    public function store(CalendarEventRequest $request, UserShiftService $service)
    {
        $result = null;
        try {
            if ($request->type == 'shift') {

                if (Carbon::parse($request->start_date)->lt(Carbon::today())) {
                    throw new \Exception("Tanggal shift tidak boleh kurang dari hari ini", 1);
                }
                //data request di susun ulang agar sesuai dengan yang diharapkan oleh service
                $title_shift = Shift::where('id', $request->data)->first()->shift_name;

                $cek_shift = UserShift::where('user_id', Auth::user()->id)
                    ->where('start_date_shift', $request->start_date)
                    ->when($request->end_date, callback: function ($query) use ($request) {
                        $query->where('end_date_shift', $request->end_date);
                    })
                    ->when($request->overtime == 'LEMBUR' || $request->overtime == 'HOLIDAY', function ($query) use ($request) {
                        $query->where('desc_shift',$request->overtime);
                    })
                    ->count();

                lg::info($cek_shift);
                // dd($request->overtime);
                if ($cek_shift == 0) {
                    $data = [
                        'user_id' => Auth::user()->id,
                        'shift_id' => $request->data,
                        'start_date_shift' => $request->start_date,
                        'end_date_shift' => $request->end_date,
                    ];

                    if (!empty($request['overtime']) || !empty($request->overtime)) {
                        $data['overtime'] = $request->overtime;
                    } else {
                        $data['holiday'] = !empty($request->holiday) ? $request->holiday : null;
                    }


                    $title = $title_shift . " - " . Auth::user()->name; //title untuk di calendar
                    $result = $service->createUserShift($data);
                } else {
                    throw new \Exception("Shift Sudah Ada", 1);
                }
            } else {
                $result = CalendarEvent::create([
                    'title' => $request->data,
                    'start_date' => Carbon::parse($request->start_date),
                    'end_date' => Carbon::parse($request->end_date),
                    'created_by' => Auth::user()->id,
                ]);
                $title = $request->data; //title untuk di calendar
                $id = 'event_' . $result->id; //title untuk di calendar
            }
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'create type calendar event',
                'controller' => 'CalendarController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);

            $title = $th->getMessage();
        }
        if (is_array($result)) {
            $id = 'shift_' . $result['id'];
            $result = $result['return'];
        }

        return response()->json([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Event created successfully.' : 'Failed to create event. Message : ' . $title,
            'data' => [
                'title' => $title,
                'id' => $id ?? NULL
            ]
        ]);
    }

    public function destroy($id)
    {
        $result = null;
        try {

            $id = str_replace('event_', '', $id);
            $data = CalendarEvent::where('id', $id)->where('created_by', Auth::user()->id);

            if ($data->count() > 0) {

                $result = $data->first();

                if (str_starts_with($id, 'user_') && $result->created_by !== Auth::user()->id) {
                    throw new \Exception('Cannot delete user birthday events.', 400);
                }

                // dd($result->created_by,str_starts_with($id, 'event_'),$id);
                if (Auth::user()->is_admin == 0 && str_starts_with($id, 'shift_')) {
                    throw new \Exception('You do not have permission to delete shift events.', 403);
                }

                $result->delete();
            } else {
                throw new \Exception('Cannot delete user this events.', 400);
            }
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'delete type calendar event',
                'controller' => 'CalendarController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return response()->json([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Event deleted successfully.' : 'Failed to delete event. Message :' . $th->getMessage(),
        ]);
    }
}
