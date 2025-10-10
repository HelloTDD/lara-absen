document.addEventListener("DOMContentLoaded", function () {
    // console.log("Attendance.js loaded ✅");

    if (typeof Webcam === "undefined") {
        console.error("⚠️ Webcam.js belum siap");
        return;
    }

    Webcam.set({
        width: 280,
        height: 280,
        image_format: "jpeg",
        jpeg_quality: 90,
    });

    Webcam.attach("#camera");

    Webcam.on("live", function () {
        const loading = document.getElementById("loading-camera");
        if (loading) loading.style.display = "none";
    });

    Webcam.on("error", function (err) {
        console.error("Webcam error:", err);
        Swal.fire("Kamera Gagal", "Pastikan izin kamera diberikan.", "error");
    });

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                window.currentLocation = {
                    latitude: pos.coords.latitude,
                    longitude: pos.coords.longitude,
                };
                // console.log("Lokasi didapat:", window.currentLocation);
            },
            (err) => {
                console.error("Gagal mendapatkan lokasi:", err);
                Swal.fire("Akses Lokasi Ditolak", "Aktifkan izin lokasi untuk presensi.", "error");
            }
        );
    } else {
        console.error("Geolocation tidak didukung browser ini.");
        Swal.fire("Error", "Browser tidak mendukung lokasi GPS.", "error");
    }

    // --- Tombol Presensi ---
    const btnMasuk = document.getElementById("btnMasuk");
    const btnPulang = document.getElementById("btnPulang");
    const btnLembur = document.getElementById("btnLembur");

    function kirimPresensi(action) {
        if (!window.currentLocation) {
            Swal.fire("Lokasi Tidak Ditemukan", "Mohon aktifkan GPS dan coba lagi.", "error");
            return;
        }

        let lokasi = `${window.currentLocation.latitude},${window.currentLocation.longitude}`;

        Webcam.snap(function (image) {
            Swal.fire({
                title: "Mengirim presensi...",
                text: "Mohon tunggu sebentar",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });

            $.ajax({
                type: "POST",
                url: "/attendance/save",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    image: image,
                    lokasi: lokasi,
                    action: action,
                },
                success: function (res) {
                    Swal.close();

                    if (res.status === 200) {
                        let pesan = "Berhasil absen";
                        if (res.jenis_presensi === "check_in") pesan = "Berhasil absen masuk";
                        if (res.jenis_presensi === "check_out") pesan = "Berhasil absen pulang";
                        if (res.jenis_presensi === "overtime") pesan = "Berhasil absen lembur";

                        Swal.fire({
                            icon: "success",
                            title: "Sukses!",
                            text: pesan,
                            timer: 2000,
                            showConfirmButton: false,
                        }).then(() => {
                            window.location.href = "/homes";
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal!",
                            text: res.message || "Gagal melakukan presensi.",
                        });
                    }
                },
                error: function (xhr) {
                    Swal.close();
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Terjadi kesalahan saat mengirim presensi.",
                    });
                    console.error(xhr.responseText);
                },
            });
        });
    }


    if (btnMasuk) btnMasuk.addEventListener("click", () => kirimPresensi("check_in"));
    if (btnPulang) btnPulang.addEventListener("click", () => kirimPresensi("check_out"));
    if (btnLembur) btnLembur.addEventListener("click", () => kirimPresensi("overtime"));

    // console.log("Attendance.js ready 🟢");
});
