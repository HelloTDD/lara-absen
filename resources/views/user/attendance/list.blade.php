@extends('layouts.app')
@section('page-title', 'Absensi')
@section('content')
<div class="row">
    <div class="col-lg-12">
    @if(Auth()->user()->is_admin)
        <div class="card">
            <div class="card-header">
                    <form action="{{ route('attendance.filter') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="user_id">User</label>
                                        <select class="form-control" name="user_id" >
                                            <option value="">Pilih User</option>
                                            {{-- Check if there are users available --}}
                                            @if (count($users) == 0)
                                                <option value="">No users available</option>
                                            @else
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="mb-3">
                                        <label for="user_id">Shift</label>
                                        <select class="form-control" name="shift_id" >
                                            <option value="">Pilih Shift</option>
                                            {{-- Check if there are shifts available --}}
                                            @if (count($shift) == 0)
                                                <option value="">No shift available</option>
                                            @else
                                                @foreach($shift as $row)
                                                    <option value="{{ $row->id }}">{{ $row->shift_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="start_date_shift">Tanggal Mulai</label>
                                        <input class="form-control" type="date" name="start_date_shift">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="end_date_shift">Tanggal Selesai</label>
                                        <input class="form-control" type="date" name="end_date_shift">
                                    </div>
                                </div>
                            </div>
                        <button type="submit" class="btn btn-success">Filter</button>
                        <a href="{{ route('attendance.reset') }}" class="btn btn-secondary">Reset</a>
                        <a href="{{ route('attendance.print') }}" class="btn btn-primary">Print</a>
                        <a href="{{ route('attendance.export') }}" class="btn btn-primary">Export</a>
                    </form>
            </div>
        </div>
        @endif


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
                    <table class="table table-bordered table-striped" id="datatable">
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
                                    <td class="py-2 px-4">{{ $item->user_shift->shift->shift_name ?? '-' }}</td>
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
                                        @if ($item->desc_attendance == 'MASUK')
                                            <span class="badge rounded-4 text-white bg-success">MASUK</span>
                                        @elseif ($item->desc_attendance == 'LEMBUR')
                                            <span class="badge rounded-4 text-white bg-danger">LEMBUR</span>
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
                                        @if(Auth::user()->hasFullAccess())
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
