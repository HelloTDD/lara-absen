<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Absensi Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .card-body {
            margin: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Karyawan</th>
                                    <th>Tanggal</th>
                                    <th>Shift</th>
                                    <th>Jam Masuk</th>
                                    <th>Foto Masuk</th>
                                    <th>Jam Pulang</th>
                                    <th>Foto Pulang</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse ($data as $index => $item)
                               @php
                                    $item->late_reason = null;

                                    if ($item->check_in_time && $item->shift) {
                                        $shiftStart = \Carbon\Carbon::parse($item->date . ' ' . $item->shift->check_in, 'Asia/Jakarta');
                                        $checkInTime = \Carbon\Carbon::parse($item->date . ' ' . $item->check_in_time, 'Asia/Jakarta');

                                        if ($checkInTime->gt($shiftStart)) {
                                            $lateMinutes = $shiftStart->diffInMinutes($checkInTime);
                                            $hours = floor($lateMinutes / 60);
                                            $minutes = $lateMinutes % 60;

                                            if ($hours > 0 && $minutes > 0) {
                                                $item->late_reason = "Terlambat {$hours} jam {$minutes} menit";
                                            } elseif ($hours > 0) {
                                                $item->late_reason = "Terlambat {$hours} jam";
                                            } else {
                                                $item->late_reason = "Terlambat {$minutes} menit";
                                            }
                                        } else {
                                            $item->late_reason = null;
                                        }
                                    }
                                @endphp

                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->user->name ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y') }}</td>
                                    <td>{{ $item->shift->shift_name ?? '-' }}</td>
                                    <td>
                                        {{ $item->check_in_time ?? '-' }}
                                    </td>
                                    <td>
                                        @if ($item->check_in_photo)
                                            <img
                                                src="{{ asset('storage/absensi/' . $item->check_in_photo) }}"
                                                onclick="openModal(this.src)"
                                                style="max-width: 100%; height: auto; cursor: pointer;"
                                                class="img-fluid rounded shadow"
                                            >
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        {{ $item->check_out_time ?? '-' }}
                                    </td>
                                    <td>
                                        @if ($item->check_out_photo)
                                            <img
                                                src="{{ asset('storage/absensi/' . $item->check_out_photo) }}"
                                                onclick="openModal(this.src)"
                                                style="max-width: 100%; height: auto; cursor: pointer;"
                                                class="img-fluid rounded shadow"
                                            >
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->desc_attendance == 'PULANG')
                                            <span class="badge rounded-4 text-white bg-danger">PULANG</span>
                                        @else
                                            <span class="badge rounded-4 text-white bg-success">MASUK</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->late_reason)
                                            <span class="badge rounded-4 bg-{{ isset($item->late_reason) ? 'danger' : 'success' }} text-white">
                                                {{ isset($item->late_reason) ? $item->late_reason : 'Tepat Waktu' }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="py-4 text-center text-gray-500">Belum ada data absensi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        </table>
                </div>
</body>
</html>
