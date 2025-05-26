<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TDD Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map {
            height: 500px;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-white to-gray-100 flex items-center justify-center font-sans">

    <div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-md">
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
        <h1 class="text-2xl font-bold text-center text-[#FF2D20] mb-6">Form Absensi</h1>

        <form action="{{ route('attendance.store') }}" method="POST" class="space-y-5">
            @csrf
        <div>
        <div class="-mx-3 flex flex-wrap">
            <div class="mb-6 w-full max-w-full px-3 sm:flex-none">
                <div class="dark:bg-slate-850 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl">
                    <div class="flex-auto p-4">
                        <input type="text" name="lokasi" id="lokasi" class="input input-primary" hidden>
                        <div id="webcam-capture" class="mx-auto"></div>
                        <div class="flex justify-center">
                        </div>
                    </div>
                </div>
                <div class="dark:bg-slate-850 dark:shadow-dark-xl relative mt-3 flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl">
                    <div class="flex-auto p-4">
                        <div id="map" class="mx-auto h-80 w-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
            <!-- Tanggal Otomatis -->
            <div>
                <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="text" id="tanggal" name="tanggal" value="{{ $date }}" readonly
                       class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100" required/>
            </div>

            <div>
                <label for="time" class="block text-sm font-medium text-gray-700 mb-1">time</label>
                <input type="text" id="time" name="time" value="{{ $time }}" readonly
                       class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100" required/>
            </div>

            <!-- Sudah absen pulang -->
            @if ($existing && $existing->check_out_time)
                <div class="text-center">
                    <button type="button"
                            class="w-full bg-[#B2C6D5] text-white font-semibold py-2 rounded-md hover:bg-red-600 transition duration-300"
                            disabled>
                        Anda Sudah Absen Hari Ini
                    </button>
                </div>

            @elseif ($existing && $existing->check_in_time)
                <!-- Tombol Absen Pulang -->
                <div class="text-center">
                    <button type="submit" name="action" value="check_out"
                            class="w-full bg-[#FF2D20] text-white font-semibold py-2 rounded-md hover:bg-red-600 transition duration-300">
                        Absen Pulang
                    </button>
                </div>

            @else
                <!-- Tombol Absen Masuk -->
                <div class="text-center">
                    <button type="submit" name="action" value="check_in"
                            class="w-full bg-[#FF2D20] text-white font-semibold py-2 rounded-md hover:bg-red-600 transition duration-300">
                        Absen Masuk
                    </button>
                </div>
            @endif

        </form>
    </div>


    <script>
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
            color: 'red',
            fillColor: '#f03',
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
</script>
</body>
</html>
