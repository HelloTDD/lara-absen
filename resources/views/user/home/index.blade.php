@extends('layouts.app')
@section('page-title', 'Overview')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row d-flex justify-content-center">
                                <div class="col-9">
                                    <p class=" mb-0 fw-semibold">Jumlah Kehadiran Bulan Ini</p>
                                    <h3 class="my-1 font-20 fw-bold">{{ $user_attendance_month->count() }}</h3>

                                    <p class="mb-0 text-muted">Periode
                                        {{ \Carbon\Carbon::now()->startOfMonth()->format('d M') }} -
                                        {{ \Carbon\Carbon::now()->endOfMonth()->format('d M') }}</p>
                                    <button class="btn btn-outline-primary btn-sm rounded-pill ms-auto" type="button"
                                        onclick="location.href='{{ route('attendance.index') }}'">
                                        Absen
                                    </button>       
                                </div>
                                <div class="col-3 align-self-center">
                                    <div
                                        class="d-flex justify-content-center align-items-center thumb-md bg-light-alt rounded-circle mx-auto">
                                        <i class="ti ti-calendar-event font-24 align-self-center text-muted"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row d-flex justify-content-center">
                                <div class="col-9">
                                    <p class=" mb-0 fw-semibold">Next Shift (7 Hari ke Depan)</p>

                                    @if ($next_shift)
                                        @php
                                            $shiftName = strtolower($next_shift->shift->shift_name);

                                            // Tentukan jam shift berdasarkan nama
                                            $shiftTime = match ($shiftName) {
                                                'pagi' => ['08:30', '17:00'],
                                                'malam' => ['19:00', '04:00'],
                                                default => [
                                                    \Carbon\Carbon::parse($next_shift->start_date_shift)->format('H:i'),
                                                    \Carbon\Carbon::parse($next_shift->end_date_shift)->format('H:i'),
                                                ],
                                            };
                                        @endphp

                                        <h3 class="my-1 font-20 fw-bold">
                                            {{ \Carbon\Carbon::parse($next_shift->start_date_shift)->format('d M Y') }}
                                        </h3>

                                        <p class="mb-0 text-truncate text-muted">
                                            <span class="text-success"><i class="mdi mdi-account-clock"></i></span>
                                            {{ ucfirst($next_shift->shift->shift_name) }}
                                            ({{ $shiftTime[0] }} - {{ $shiftTime[1] }})
                                        </p>
                                    @else
                                        <h3 class="my-1 font-20 fw-bold">—</h3>
                                        <p class="mb-0 text-truncate text-muted text-danger">
                                            Tidak ada shift dalam 7 hari ke depan
                                        </p>
                                    @endif
                                </div>


                                <div class="col-3 align-self-center">
                                    <div
                                        class="d-flex justify-content-center align-items-center thumb-md bg-light-alt rounded-circle mx-auto">
                                        <i class="ti ti-clock font-24 align-self-center text-muted"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row d-flex justify-content-center">
                                <div class="col-9">
                                    <p class=" mb-0 fw-semibold">Absensi Hari Ini</p>
                                    <h3 class="my-1 font-20 fw-bold">
                                        {{ $attendance_today_count }}/{{ $total_shift_today }}
                                    </h3>
                                    <p class="mb-0 text-truncate text-muted">
                                        @if ($attendance_rate_today > 0)
                                            <span class="text-success">
                                                <i class="mdi mdi-trending-up"></i>
                                                {{ $attendance_rate_today }}%
                                            </span> Sudah Absen
                                        @else
                                            <span class="text-danger">
                                                <i class="mdi mdi-timer-off"></i> Belum Ada Absen
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-3 align-self-center">
                                    <div
                                        class="d-flex justify-content-center align-items-center thumb-md bg-light-alt rounded-circle mx-auto">
                                        <i class="ti ti-activity font-24 align-self-center text-muted"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row d-flex justify-content-center">
                                <div class="col-9">
                                    <p class=" mb-0 fw-semibold">Jumlah Kehadiran 7 Hari Terakhir</p>
                                    <h3 class="my-1 font-20 fw-bold">{{ $user_attendance->count() }}</h3>

                                </div>
                                <div class="col-3 align-self-center">
                                    <div
                                        class="d-flex justify-content-center align-items-center thumb-md bg-light-alt rounded-circle mx-auto">
                                        <i class="ti ti-users font-24 align-self-center text-muted"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row d-flex justify-content-center">
                                <div class="col-9">
                                    <p class=" mb-0 fw-semibold">Goal Completions</p>
                                    <h3 class="my-1 font-20 fw-bold">85000</h3>
                                    <p class="mb-0 text-truncate text-muted"><span class="text-success"><i
                                                class="mdi mdi-trending-up"></i>10.5%</span> Completions
                                        Weekly</p>
                                </div>
                                <div class="col-3 align-self-center">
                                    <div
                                        class="d-flex justify-content-center align-items-center thumb-md bg-light-alt rounded-circle mx-auto">
                                        <i class="ti ti-confetti font-24 align-self-center text-muted"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>

        </div>

    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title align-items-center">User Shift</h5>

                                @if (in_array(Auth::user()->role_name, ['Finance', 'Scheduler', 'Supervisor']))
                                    <div>
                                        <button class="btn btn-outline-primary btn-sm rounded-pill ms-auto" type="button"
                                            onclick="location.href='{{ route('user-shift.index') }}'">
                                            See More →
                                        </button>

                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive browser_users">
                        <table class="table mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-top-0">ID</th>
                                    <th class="border-top-0">User Name</th>
                                    <th class="border-top-0">Shift Name</th>
                                    <th class="border-top-0">Start Time</th>
                                    <th class="border-top-0">End Time</th>
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
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">User Attendance</h4>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-primary btn-sm rounded-pill ms-auto" type="button"
                                onclick="location.href='{{ route('attendance.list') }}'">
                                Lihat Detail Absensi →
                            </button>
                        </div>

                    </div>
                </div>
                <div class="card-bodyp-0">
                    <div class="p-3" data-simplebar>
                        <div class="activity">
                            @if ($user_attendance->count() > 0)
                                @foreach ($user_attendance as $attendance)
                                    <div class="activity-info">
                                        <div class="icon-info-activity">
                                            {{-- Ganti ikon sesuai kondisi (pagi/siang/malam, misalnya) --}}
                                            @if ($attendance->check_out_time)
                                                <i class="mdi mdi-sleep"></i>
                                            @else
                                                <i class="mdi mdi-weather-sunny"></i>
                                            @endif
                                        </div>

                                        <div class="activity-info-text">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <p class="text-muted mb-0 font-13 w-100">
                                                    <span>{{ $attendance->user->name }}</span>
                                                    telah melakukan
                                                    <strong>{{ $attendance->check_out_time ? 'Check-out' : 'Check-in' }}</strong>
                                                    pada tanggal
                                                    <a href="javascript:void(0)">{{ $attendance->date }}</a>
                                                    {{ $attendance->check_out_time ? 'pada pukul ' . $attendance->check_out_time : 'pada pukul ' . $attendance->check_in_time }}
                                                </p>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted">Belum ada aktivitas absensi.</div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
