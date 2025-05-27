<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 p-6 font-sans">

    <div class="max-w-6xl mx-auto">
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
            <table class="w-full table-auto border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="py-2 px-4 border">#</th>
                        <th class="py-2 px-4 border">Nama</th>
                        <th class="py-2 px-4 border">Tanggal</th>
                        <th class="py-2 px-4 border">Shift</th>
                        <th class="py-2 px-4 border">Jam Masuk</th>
                        <th class="py-2 px-4 border">Foto Masuk</th>
                        <th class="py-2 px-4 border">Jam Pulang</th>
                        <th class="py-2 px-4 border">Foto Pulang</th>
                        <th class="py-2 px-4 border">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $index => $item)
                        <tr class="text-center border-t hover:bg-gray-50">
                            <td class="py-2 px-4">{{ $index + 1 }}</td>
                            <td class="py-2 px-4">{{ $item->user->name ?? '-' }}</td>
                            <td class="py-2 px-4">{{ \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y') }}</td>
                            <td class="py-2 px-4">{{ $item->shift->shift_name ?? '-' }}</td>
                            <td class="py-2 px-4 text-green-600 font-semibold">
                                {{ $item->check_in_time ?? '-' }}
                            </td>
                            <td class="py-2 px-4">
                                @if ($item->check_in_photo)
                                    <img src="{{ asset('storage/absensi/' . $item->check_in_photo) }}" class="h-12 mx-auto rounded shadow">
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-2 px-4 text-red-600 font-semibold">
                                {{ $item->check_out_time ?? '-' }}
                            </td>
                            <td class="py-2 px-4">
                                @if ($item->check_out_photo)
                                    <img src="{{ asset('storage/absensi/' . $item->check_out_photo) }}" class="h-12 mx-auto rounded shadow">
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-2 px-4">
                                @if ($item->status == 'invalid')
                                    <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full">Diluar Radius</span>
                                @else
                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Valid</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-4 text-center text-gray-500">Belum ada data absensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
