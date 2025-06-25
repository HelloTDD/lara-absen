<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Shift Karyawan</title>
</head>
<body>
    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Karyawan</th>
                                    <th>Shift</th>
                                    <th>Mulai</th>
                                    <th>Selesai</th>
                                    <th>MASUK</th>
                                    <th>PULANG</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($no = 1)
                                @foreach($usershift as $item)
                                <tr>
                                    <td>{{ $no }}</td>
                                    <td>{{ $item->user?->name }}</td>
                                    <td>{{ $item->shift->shift_name }}</td>
                                    <td>{{ $item->start_date_shift }}</td>
                                    <td>{{ $item->end_date_shift }}</td>
                                    <td>{{ $item->shift->check_in }}</td>
                                    <td>{{ $item->shift->check_out }}</td>
                                </tr>
                                @php($no++)
                                @endforeach
                            </tbody>
                        </table>
                </div>
</body>
</html>
