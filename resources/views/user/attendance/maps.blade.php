@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">TDD</a>
                        </li>
                        <li class="breadcrumb-item active">
                            Maps
                        </li>
                    </ol>
                </div>
                <h4 class="page-title">Maps</h4>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Lokasi Anda & Lokasi Perusahaan</h4>
        </div>
        <div class="card-body">
            <div id="currentLoct" class="rounded-3" style="height: 60vh; z-index: 1;"></div>
            <p class="mt-2 text-muted" id="userLocationText">Mendeteksi lokasi Anda...</p>
        </div>
    </div>
@endsection

@push('header')
    {{-- Leaflet CSS --}}
    <link href="{{ asset('/assets/libs/leaflet/leaflet.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
    {{-- Leaflet JS --}}
    <script src="{{ asset('/assets/libs/leaflet/leaflet.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 🏢 Koordinat perusahaan (pusat tampilan peta)
            const companyCoords = [-7.575179, 110.895617];

            // 🗺️ Inisialisasi peta, fokus di perusahaan
            const map = L.map('currentLoct').setView(companyCoords, 17);

            // Base map
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                maxZoom: 20,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            }).addTo(map);

            // 🏢 Marker dan area perusahaan
            const companyMarker = L.marker(companyCoords).addTo(map)
                .bindTooltip("<b>Perusahaan</b><br>Area kantor pusat", { permanent: true, direction: "top" })
                .openTooltip();

            const companyCircle = L.circle(companyCoords, {
                radius: 100,
                color: "#f03",
                opacity: 0.7,
                fillOpacity: 0.1,
            }).addTo(map);

            // 👣 Marker user (akan diperbarui setelah lokasi ditemukan)
            let userMarker = null;
            let userCircle = null;

            // Cek dukungan geolocation
            if (navigator.geolocation) {
                navigator.geolocation.watchPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        const accuracy = position.coords.accuracy;

                        // Tambah / update marker user
                        if (!userMarker) {
                            userMarker = L.marker([lat, lng], { icon: blueIcon() }).addTo(map)
                                .bindTooltip("<b>Anda di sini!</b>", { permanent: true, direction: "bottom" })
                                .openTooltip();
                        } else {
                            userMarker.setLatLng([lat, lng]);
                        }

                        // Tambah / update lingkaran akurasi
                        if (userCircle) map.removeLayer(userCircle);
                        userCircle = L.circle([lat, lng], {
                            radius: accuracy,
                            color: "#007bff",
                            opacity: 0.5,
                            fillOpacity: 0.15,
                        }).addTo(map);

                        // Update teks koordinat
                        document.getElementById('userLocationText').innerHTML =
                            `Lokasi Anda: <b>${lat.toFixed(5)}, ${lng.toFixed(5)}</b> (akurasi ±${accuracy.toFixed(0)} m)`;
                    },
                    function(error) {
                        document.getElementById('userLocationText').innerHTML =
                            `<span class="text-danger">Tidak dapat mendeteksi lokasi (${error.message})</span>`;
                    },
                    { enableHighAccuracy: true }
                );
            } else {
                document.getElementById('userLocationText').innerHTML =
                    "<span class='text-danger'>Browser tidak mendukung geolocation.</span>";
            }

            // 🟦 Icon biru untuk lokasi user
            function blueIcon() {
                return L.icon({
                    iconUrl: "https://cdn-icons-png.flaticon.com/512/64/64113.png",
                    iconSize: [28, 28],
                    iconAnchor: [14, 28],
                    popupAnchor: [0, -28],
                });
            }
        });
    </script>
@endpush
