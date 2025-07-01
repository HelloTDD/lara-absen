<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Cuti Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .card-body {
            margin: 20px;
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
                                    <th>Status</th>
                                    <th>Cuti Terpakai</th>
                                    <th>Sisa Cuti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($no = 1)
                                @foreach($leaves as $item)

                                <tr>
                                    <td>{{ $no }}</td>
                                    <td>{{ $item->user?->name }}</td>
                                    <td>{{ $item->leave_date_start }} ~ {{ $item->leave_date_end }}</td>
                                    <td>
                                        @if ($item->status == 'approved')
                                            <span class="badge rounded-4 bg-success fs-6 m-1">Approve</span>
                                        @elseif ($item->status == 'rejected')
                                            <span class="badge rounded-4 bg-danger fs-6 m-1">Reject</span>
                                        @elseif ($item->status == 'pending')
                                            <span class="badge rounded-4 bg-warning fs-6 m-1">Pending</span>
                                        @elseif ($item->status == 'cancel')
                                            <span class="badge rounded-4 bg-secondary fs-6 m-1">Cancel</span>
                                        @else
                                            <span class="badge rounded-4 bg-warning fs-6 m-1">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ max(0,12 - $item->user->leave)  }}</td>
                                    <td>{{ $item->user->leave  }}</td>
                                </tr>
                                @php($no++)
                                @endforeach
                            </tbody>
                        </table>
                </div>
</body>
</html>
