@extends('layouts.app')
@section('page-title', 'Overview')
@section('content')
    <div class="row">
        <div class="col-lg-8 row">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title align-items-center">User Shift</h5>

                        @if (in_array(Auth::user()->role_name, ['Finance', 'Scheduler', 'Supervisor']))
                            <div>
                                <button class="btn btn-primary btn-sm ms-auto" type="button"
                                    onclick="location.href='{{ route('user-shift.index') }}'">See More</button>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped bg-primary rounded-1">
                        <thead>
                            <tr>
                                <th class="text-light">ID</th>
                                <th class="text-light">User Name</th>
                                <th class="text-light">Shift Name</th>
                                <th class="text-light">Start Time</th>
                                <th class="text-light">End Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($user_shift->count() > 0)
                                @foreach ($user_shift as $shift)
                                    <tr>
                                        <td>{{ $shift->id }}</td>
                                        <td>{{ $shift->user->name }}</td>
                                        <td>{{ $shift->shift->shift_name }}</td>
                                        <td>{{ $shift->start_date_shift }}</td>
                                        <td>{{ $shift->end_date_shift }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center">No Data Available</td>
                                </tr>
                            @endif
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title align-items-center">User Attendance</h5>
                        <div>
                            <button class="btn btn-primary btn-sm ms-auto" type="button"
                                onclick="location.href='{{ route('attendance.list') }}'">See More</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped bg-primary rounded-1">
                        <thead>
                            <tr>
                                <th class="text-light">ID</th>
                                <th class="text-light">User Name</th>
                                <th class="text-light">Date</th>
                                <th class="text-light">Check In</th>
                                <th class="text-light">Check Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($user_attendance->count() > 0)
                                @foreach ($user_attendance as $attendance)
                                    <tr>
                                        <td>{{ $attendance->id }}</td>
                                        <td>{{ $attendance->user->name }}</td>
                                        <td>{{ $attendance->date }}</td>
                                        <td>{{ $attendance->check_in }}</td>
                                        <td>{{ $attendance->check_out }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center">No Data Available</td>
                                </tr>
                            @endif
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
