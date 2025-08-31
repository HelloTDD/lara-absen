<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TDD Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    {{-- JQuery --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <style>
        #map {
            height: 200px;
        }

         @media (max-width: 425px) {

            #webcam-capture,
            #webcam-capture video {
                width: 360px !important;
                height: 380px !important;
                margin auto;
                border-radius: 33px;
            }
        }

        @media (min-width: 640px) {

            #webcam-capture,
            #webcam-capture video {
                width: 360px !important;
                height: 380px !important;
                margin auto;
                border-radius: 33px;
            }
        }

        @media (min-width: 768px) {

            #webcam-capture,
            #webcam-capture video {
                width: 360px !important;
                height: 380px !important;
                margin auto;
                border-radius: 33px;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-white to-gray-100 flex items-center justify-center font-sans">

        <div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-md">

            <audio id="notifikasi_presensi_masuk">
            <source src="{{ asset("audio/notifikasi_presensi_masuk.mp3") }}" type="audio/mpeg">
            </audio>
            <audio id="notifikasi_presensi_keluar">
                <source src="{{ asset("audio/notifikasi_presensi_keluar.mp3") }}" type="audio/mpeg">
            </audio>
            <audio id="notifikasi_presensi_gagal_radius">
                <source src="{{ asset("audio/notifikasi_presensi_gagal_radius.mp3") }}" type="audio/mpeg">
            </audio>

            <!-- Alert Section -->
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md border border-green-300">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md border border-red-300">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded-md border border-yellow-300">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h1 class="text-2xl font-bold text-center text-[#0b51b7] mb-6">Form Absensi</h1>

            <div>
            <div class="flex gap-4">
                <div class="w-1/2">
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="text" id="tanggal" name="tanggal" value="{{ $date }}" readonly
                        class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100" required/>
                </div>

                <div class="w-1/2">
                    <label for="time" class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                    <input type="text" id="time" name="time" value="{{ $time }}" readonly
                        class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100" required/>
                </div>
            </div>

            <div class="-mx-3 flex flex-wrap">
                <div class="mb-6 w-full max-w-full px-3 sm:flex-none">
                    <div class="dark:bg-slate-850 dark:shadow-dark-xl relative mt-3 mb-2 flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl">
                        <div class="flex-auto p-4">
                            <div id="map" class="mx-auto h-25 w-full"></div>
                        </div>
                    </div>
                    <div class="dark:bg-slate-850 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl">
                        <div class="flex-auto p-3">
                            <input type="text" name="lokasi" id="lokasi" class="input input-primary" hidden>
                            <div id="webcam-capture" class="mx-auto"></div>
                            <div class="flex justify-center">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <!-- Sudah absen pulang -->
            @if ($existing->check_out_time)
                <!-- Tombol Absen Masuk -->
                <div id="take-presensi" class="text-center">
                    <input type="text" name="action" id="action" value="overtime" hidden>
                    <button type="text" class="w-full bg-[#0b51b7] text-white font-semibold py-2 rounded-md hover:bg-red-600 transition duration-300">
                        LEMBUR
                    </button>
                </div>

            @elseif ($existing && $existing->check_in_time)
                <!-- Tombol Absen Pulang -->
                <div id="take-presensi" class="text-center">
                    <input type="text" name="action" id="action" value="check_out" hidden>
                    <button type="text" class="w-full bg-[#0b51b7] text-white font-semibold py-2 rounded-md hover:bg-red-600 transition duration-300">
                        Absen Pulang
                    </button>
                </div>

            @elseif ($attendanceCount >= $limitAttendance)
                <!-- total absen bulan ini -->
                <div id="take-presensi" class="text-center">
                    <input type="text" name="action" id="action" value="overtime" hidden>
                    <button type="text" class="w-full bg-[#0b51b7] text-white font-semibold py-2 rounded-md hover:bg-red-600 transition duration-300">
                        LEMBUR
                    </button>
                </div>
            @else
                <!-- Tombol Absen Masuk -->
                <div id="take-presensi" class="text-center">
                    <input type="text" name="action" id="action" value="check_in" hidden>
                    <button type="text" class="w-full bg-[#0b51b7] text-white font-semibold py-2 rounded-md hover:bg-red-600 transition duration-300">
                        Absen Masuk
                    </button>
                </div>
            @endif

            <button type="button" onclick="window.location.href='{{ url('homes') }}'" class="w-full mt-3 bg-[#0b51b7] text-white font-semibold py-2 rounded-md hover:bg-red-600 transition duration-300">Dashboard</button>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js" integrity="sha512-dQIiHSl2hr3NWKKLycPndtpbh5iaHLo6MwrXm7F0FM5e+kL2U16oE9uIwPHUl6fQBeCthiEuV/rzP3MiAB8Vfw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Webcam.set({
            width: 300,
            height: 380,
            image_format: 'jpeg',
            jpeg_quality: 90,
            force_flash: false,
            flip_horiz: false,
        });
        Webcam.attach('#webcam-capture');

        let lokasi = document.getElementById('lokasi');


        // Lokasi kantor dari config Laravel
        const kantor = {
            latitude: {{ config('officeLocation.latitude') }},
            longitude: {{ config('officeLocation.longitude') }},
            radius: {{ config('officeLocation.radius') }}
        };

        //NOTE -  Tunggu hingga browser dapatkan lokasi pengguna
        if (navigator.geolocation) {
            // console.log(navigator.geolocation);
            navigator.geolocation.getCurrentPosition(showMap, showError);
        } else {
            alert("Geolocation tidak didukung oleh browser Anda.");
        }

        function showMap(position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            lokasi.value = `${userLat}, ${userLng}`;

            const map = L.map('map').setView([userLat, userLng], 17);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            //*NOTE - Marker lokasi user
            const userMarker = L.marker([userLat, userLng]).addTo(map)
                .bindPopup("Anda berada di sini").openPopup();

            //NOTE -  Lingkaran lokasi kantor
            const kantorCircle = L.circle([kantor.latitude, kantor.longitude], {
                color: 'transparant',
                fillColor: 'transparant',
                fillOpacity: 0.5,
                radius: kantor.radius
            }).addTo(map).bindPopup("Radius kantor");

            //NOTE -  Tambahkan marker kantor (opsional)
            L.marker([kantor.latitude, kantor.longitude]).addTo(map)
                .bindPopup("Lokasi Kantor");
        }

        function showError(error) {
            alert("Gagal mendapatkan lokasi Anda.");
        }


        let notifikasi_presensi_masuk = document.getElementById('notifikasi_presensi_masuk');
        let notifikasi_presensi_keluar = document.getElementById('notifikasi_presensi_keluar');
        let notifikasi_presensi_gagal_radius = document.getElementById('notifikasi_presensi_gagal_radius');
        $("#take-presensi").click(function() {
            Webcam.snap(function(uri) {
                image = uri;
            });
            // console.log(Webcam);
            // close;
            $.ajax({
                type: "POST",
                url: "{{ route("attendance.store") }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    time : $("#time").val(),
                    tanggal: $("#tanggal").val(),
                    image: image,
                    lokasi: lokasi.value,
                    action: $("input[name='action']").val(),
                },

                cache: false,
                success: function(res) {
                    console.log(res);
                    if (res.status == 200) {
                        if (res.jenis_presensi == "check_in") {
                            notifikasi_presensi_masuk.play();
                        } else if (res.jenis_presensi == "check_out") {
                            notifikasi_presensi_keluar.play();
                        }else if (res.jenis_presensi == "overtime") {
                            notifikasi_presensi_masuk.play();
                        }
                        Swal.fire({
                            title: "Presensi",
                            text: res.message,
                            icon: "success",
                            confirmButtonText: "OK"
                        });
                         window.location.href = "{{ url('homes') }}";
                        // setTimeout("location.href='homes'", 5000);

                    } else if (res.status == 500) {
                        if (res.jenis_error == "radius") {
                            notifikasi_presensi_gagal_radius.play();
                        }
                        Swal.fire({
                            title: "Presensi",
                            text: res.message,
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                }
            });
        });

</script>
</body>
</html>
