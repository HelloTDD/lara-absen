<?php

namespace App\Interfaces;

use App\Http\Requests\CalendarEventRequest;
use App\Services\UserShiftService;

interface CalendarInterface
{
    public function index();
    public function store(CalendarEventRequest $request, UserShiftService $service);
    public function destroy($id);
}
