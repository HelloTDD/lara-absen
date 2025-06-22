@extends('layouts.app')
@section('page-title', 'Absensi')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="justify-content-between d-flex">
                    <div>
                        <h3 class="card-title">Daftar Absensi</h3>
                    </div>
                    <div>
                        <!-- Tempat untuk tombol atau aksi lainnya -->
                            <a href="{{ route('attendance.index') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Absensi
                            </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr class="bg-gray-200 text-gray-700">
                                <th>#</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Shift</th>
                                <th>Jam Masuk</th>
                                <th>Foto Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Foto Pulang</th>
                                <th>Status</th>
                                <th>Keterangan Terlambat</th>
                                <th>Aksi</th>
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
                                    <td class="py-2 px-4 text-red-600 font-semibold">
                                        {{ $item->check_out_time ?? '-' }}
                                    </td>
                                    <td class="py-2 px-4">
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
                                    <td class="py-2 px-4">
                                        @if ($item->desc_attendance == 'PULANG')
                                            <span class="badge rounded-4 text-white bg-danger">PULANG</span>
                                        @else
                                            <span class="badge rounded-4 text-white bg-success">MASUK</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-4">
                                        @if ($item->late_reason)
                                            <span class="badge rounded-4 bg-{{ isset($item->late_reason) ? 'danger' : 'success' }} text-white">
                                                {{ isset($item->late_reason) ? $item->late_reason : 'Tepat Waktu' }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="py-2 px-4">
                                        @if(Auth::user()->is_admin == 1)
                                            <a href="{{ route('attendance.edit', $item->id) }}" class="btn btn-warning mb-2" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('attendance.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-500"> - </span>
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
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk gambar -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-body text-center p-0">
        <img src="" id="modalImage" class="img-fluid w-100 rounded">
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
    function openModal(imageSrc) {
        const modalImg = document.getElementById('modalImage');
        modalImg.src = imageSrc;
        const myModal = new bootstrap.Modal(document.getElementById('imageModal'));
        myModal.show();
    }
</script>
@endpush
