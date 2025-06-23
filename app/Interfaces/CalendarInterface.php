<?php

namespace App\Interfaces;

use App\Http\Requests\CalendarEventRequest;
use App\Services\UserShiftService;
use App\Models\CalendarEvent;

interface CalendarInterface
{
    public function index();
    public function store(CalendarEventRequest $request, UserShiftService $service);
    public function update(CalendarEventRequest $request, CalendarEvent $calendarEvent,$id);
    public function destroy($id);
}
