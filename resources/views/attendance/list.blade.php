<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 p-6 font-sans">

    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Daftar Absensi</h1>

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

        <div class="bg-white shadow-md rounded-lg overflow-x-auto">
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="py-2 px-4 border">#</th>
                        <th class="py-2 px-4 border">Nama</th>
                        <th class="py-2 px-4 border">Tanggal</th>
                        <th class="py-2 px-4 border">Shift</th>
                        <th class="py-2 px-4 border">Jam Masuk</th>
                        <th class="py-2 px-4 border">Jam Pulang</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $index => $item)
                        <tr class="text-center border-t">
                            <td class="py-2 px-4">{{ $index + 1 }}</td>
                            <td class="py-2 px-4">{{ $item->user->name ?? '-' }}</td>
                            <td class="py-2 px-4">{{ $item->date }}</td>
                            <td class="py-2 px-4">{{ $item->shift->shift_name ?? '-' }}</td>
                            <td class="py-2 px-4">{{ $item->check_in_time ?? '-' }}</td>
                            <td class="py-2 px-4">{{ $item->check_out_time ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">Belum ada data absensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
