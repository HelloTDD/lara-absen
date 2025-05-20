<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TDD Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

</body>
</html>
