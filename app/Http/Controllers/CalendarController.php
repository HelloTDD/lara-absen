<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\UserShift;
use App\Models\User;
use App\Models\Log;

use App\Http\Requests\CalendarEventRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        
        $user = User::all()
        ->map(function ($user) {
                return [
                    'id' => 'user_'.$user->id,
                    'title' => 'Ulang Tahun - '.$user->name,
                    'start' =>  Carbon::parse($user->birth_date)->format('Y-m-d'),
                    'extendedProps' => [
                        'tipe' => 'user',
                    ],
                ];
            });
        $userShift = UserShift::with(['user', 'shift'])
            ->get()
            ->map(function ($userShift) {
                return [
                    'id' => 'shift_'.$userShift->id,
                    'title' => $userShift->shift?->shift_name .' - '.$userShift->user->name,
                    'start' => $userShift->shift?->start_date_shift?->format('Y-m-d'),
                    'end' => $userShift->shift?->end_date_shift?->format('Y-m-d'),
                    'extendedProps' => [
                        'tipe' => 'shift',
                        'user' => $userShift->user->id,
                    ],
                ];
            });
        
        $calendarEvents = CalendarEvent::all()
            ->map(function ($event) {
                return [
                    'id' => 'event_'.$event->id,
                    'title' => $event->title,
                    'start' => $event->start_date->format('Y-m-d'),
                    'end' => $event->end_date->format('Y-m-d'),
                    'extendedProps' => [
                        'tipe' => 'event',
                        'extend_data' => $event->extend_data ?? [],
                        'created_by' => $event->created_by ?? [],
                    ],
                ];
            });

            // dd($userShift->toArray());
        $events = array_merge($userShift->toArray(), $calendarEvents->toArray());
        $event_finals = array_merge($events, $user->toArray());
        return view('user.calendar.index', compact('event_finals'));
    }

    public function store(CalendarEventRequest $request)
    {
        $result = null;
        try {
            $result = CalendarEvent::create([
                'title' => $request->title,
                'start_date' => Carbon::parse($request->start_date),
                'end_date' => Carbon::parse($request->end_date),
                'created_by' => Auth::user()->id,
            ]);
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'create type calendar event',
                'controller' => 'CalendarController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return response()->json([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Event created successfully.' : 'Failed to create event.',
        ]);
    }

    public function destroy($id)
    {
        $result = null;
        try {
            
            if(str_starts_with($id, 'user_')) {
                throw new \Exception('Cannot delete user birthday events.', 400);
            }

            if(Auth::user()->is_admin == 0 && str_starts_with($id, 'shift_')) {
                throw new \Exception('You do not have permission to delete shift events.', 403);
            }

            $id = str_replace('event_', '', $id);
            $result = CalendarEvent::findOrFail($id);
            $result->delete();
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
            'message' => $result ? 'Event deleted successfully.' : 'Failed to delete event.',
        ]);
    }
}
