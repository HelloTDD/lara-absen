@extends('layouts.app')
@section('page-title', 'Absensi Harian')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="row">
                        <div class="col-4 col-lg-3">Tanggal Shift</div>
                        <div class="col-8 col-lg-9">: {{ $date }}</div>
                    </div>
                    <div class="row">
                        <div class="col-4 col-lg-3">Shift</div>
                        <div class="col-8 col-lg-9">: {{ $checkShift?->shift_name ?? 'Tidak ditemukan' }} -
                            {{ $checkShift?->check_in ?? 'Tidak ditemukan' }} -
                            {{ $checkShift?->check_out ?? 'Tidak ditemukan' }}</div>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="attr--absen">
                        <div>
                            <span id="today" class="btn btn-primary"></span>
                        </div>
                        <div>

                            <a href="{{ 'attendance/maps' }}" class="btn btn-success">
                                Lihat Lokasi Saya
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 ">
            <div class="card" id="absen-masuk">
                <div class="card-header">
                    <h4 class="card-title">Absen</h4>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="web--cam text-center my-3">
                            <div id="camera" class="camera-container mx-auto rounded-4 overflow-hidden shadow-lg">
                                <div id="loading-camera" class="loading-camera">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="animate-spin h-8 w-8 text-white"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4">
                                        </circle>
                                        <path class="opacity-75" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="4" d="M4 12a8 8 0 018-8v8z"></path>
                                    </svg>
                                    <p class="text-white text-sm mt-2">Menyiapkan kamera...</p>
                                </div>
                                <video id="video" autoplay playsinline></video>
                                <canvas id="canvas" class="d-none"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body w-50 text-center mx-auto">
                    @if ($existing->check_out_time)
                        <button id="btnLembur" type="button" class="btn btn-danger btn-lg">Lembur</button>
                    @elseif ($existing && $existing->check_in_time)
                        <button id="btnPulang" type="button" class="btn btn-warning btn-lg">Pulang</button>
                    @elseif ($attendanceCount >= $limitAttendance)
                        <button type="button" class="btn btn-secondary btn-lg">Lembur</button>
                    @else
                        <button id="btnMasuk" type="button" class="btn btn-primary btn-lg">Masuk</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalLocation" tabindex="-1" role="dialog" aria-labelledby="modalLocation"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header--tdd">
                    <h6 class="modal-title m-0" id="modalLocation">Lokasi Saya</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="V_Simple" class="" style="height: 400px"></div>
                    <!-- <div id="V_Simple" class="bg-secondary rounded-3" style="height: 400px;"></div> -->

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-de-secondary btn-sm" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/attendance.js') }}"></script>

@endsection

@push('header')
    {{-- Leaflet CSS --}}
    <link href="{{ asset('/assets/libs/leaflet/leaflet.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style-custom.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/attendance.css') }}" rel="stylesheet" type="text/css" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

@endpush

@push('scripts')
    <script>
        window.attendanceConfig = {
            routeStore: "{{ route('attendance.store') }}",
            redirectHome: "{{ url('homes') }}",
            office: {
                latitude: {{ config('officeLocation.latitude') }},
                longitude: {{ config('officeLocation.longitude') }},
                radius: {{ config('officeLocation.radius') }}
            }
        };
    </script>
    {{-- Leaflet JS --}}
    <script src="{{ asset('assets/js/custom/tdd.timer.clock.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom/tdd.app.mode.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

    <!-- Baru file JS kamu -->

    <script>
        var map = L.map("V_Simple").setView([-7.575179429449874, 110.8956172421839], 15);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        var marker = L.marker([-7.576394411230164, 110.89619972878383])
            .addTo(map)
            .bindTooltip("<b>Lokasi Anda!</b>", {
                permanent: true
            })
            .openTooltip();

        var circle = L.circle([-7.575179429449874, 110.8956172421839], {
                radius: 100,
                color: "#f03",
                opacity: 0.7,
                fillOpacity: 0.5,
            })
            .addTo(map)
            .bindTooltip("Area sekitar perusahaan", {
                permanent: true,
                direction: "top"
            })
            .openTooltip();
    </script>
@endpush
