@extends('layouts.app')
@section('page-title', 'Absensi')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            @if (Auth()->user()->is_admin)
                <div class="card">
                    <div class="card-header">
                        <form action="{{ route('attendance.filter') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="user_id">User</label>
                                        <select class="form-control" name="user_id">
                                            <option value="">Pilih User</option>
                                            {{-- Check if there are users available --}}
                                            @if (count($users) == 0)
                                                <option value="">No users available</option>
                                            @else
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="mb-3">
                                        <label for="user_id">Shift</label>
                                        <select class="form-control" name="shift_id">
                                            <option value="">Pilih Shift</option>
                                            {{-- Check if there are shifts available --}}
                                            @if (count($shift) == 0)
                                                <option value="">No shift available</option>
                                            @else
                                                @foreach ($shift as $row)
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
                        <table class="table table-striped table-hover" id="datatable_1">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center">No.</th>
                                    <th class="text-center">Nama Pegawai</th>
                                    <th class="text-center">Shift</th>
                                    <th class="text-center" data-type="date" data-format="YYYY/MM/DD">Tanggal</th>
                                    <th class="text-center">Jam Masuk</th>
                                    <th class="text-center">Jam Keluar</th>
                                    <th class="text-center">Foto Masuk</th>
                                    <th class="text-center">Foto Keluar</th>
                                    <th class="text-center">Keterangan Masuk</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $index => $item)
                                    @php
                                        // Hitung keterlambatan
                                        $item->late_reason = null;
                                        if ($item->check_in_time && $item->shift) {
                                            $shiftStart = \Carbon\Carbon::parse(
                                                $item->date . ' ' . $item->shift->check_in,
                                                'Asia/Jakarta',
                                            );
                                            $checkInTime = \Carbon\Carbon::parse(
                                                $item->date . ' ' . $item->check_in_time,
                                                'Asia/Jakarta',
                                            );

                                            if ($checkInTime->gt($shiftStart)) {
                                                $lateMinutes = $shiftStart->diffInMinutes($checkInTime);
                                                $hours = floor($lateMinutes / 60);
                                                $minutes = $lateMinutes % 60;

                                                if ($hours > 0 && $minutes > 0) {
                                                    $item->late_reason = "{$hours} Jam {$minutes} Menit";
                                                } elseif ($hours > 0) {
                                                    $item->late_reason = "{$hours} Jam";
                                                } else {
                                                    $item->late_reason = "{$minutes} Menit";
                                                }
                                            }
                                        }
                                    @endphp

                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center">{{ $item->user->name ?? '-' }}</td>

                                        <td class="text-center">
                                            {{ $item->user_shift->shift->shift_name ?? '-' }}
                                            @if ($item->shift)
                                                ({{ $item->shift->check_in }} - {{ $item->shift->check_out }})
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($item->date)->format('Y/m/d') }}
                                        </td>

                                        <td class="text-center text-success fw-bold">
                                            {{ $item->check_in_time ?? '-' }}
                                        </td>
                                        <td class="text-center text-success fw-bold">
                                            {{ $item->check_out_time ?? '-' }}
                                        </td>
                                        {{-- <td class="text-center text-danger fw-semibold">
                                            {{ $item->late_reason ? 'Terlambat ' . $item->late_reason : '-' }}
                                        </td> --}}

                                        {{-- <td class="text-center">
                                            @if ($item->check_in_location)
                                                <a href="https://www.google.com/maps?q={{ $item->check_in_location }}"
                                                    class="btn btn-primary btn-sm" target="_blank"
                                                    rel="noopener noreferrer">
                                                    Lihat
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $item->check_in_distance ?? '0' }}
                                                    Meter</small>
                                            @else
                                                -
                                            @endif
                                        </td> --}}

                                        <td class="text-center">
                                            @if ($item->check_in_photo)
                                                <img src="{{ asset('storage/absensi/' . $item->check_in_photo) }}"
                                                    alt="Foto Masuk" class="img--absen rounded shadow-sm"
                                                    style="cursor:pointer;aspect-ratio:1/1;object-fit:cover;width:6rem;"
                                                    onclick="openModal(this.src)">
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($item->check_out_photo)
                                                <img src="{{ asset('storage/absensi/' . $item->check_out_photo) }}"
                                                    alt="Foto Masuk" class="img--absen rounded shadow-sm"
                                                    style="cursor:pointer;aspect-ratio:1/1;object-fit:cover;width:6rem;"
                                                    onclick="openModal(this.src)">
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($item->desc_attendance == 'MASUK')
                                                <span class="badge bg-success text-white">Masuk</span>
                                            @elseif ($item->desc_attendance == 'LEMBUR')
                                                <span class="badge bg-danger text-white">Lembur</span>
                                            @else
                                                <span class="badge bg-secondary text-white">-</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if (in_array(Auth::user()->role_name, ['Finance', 'Scheduler', 'Supervisor']))
                                                <button type="button" class="btn btn-warning btn-icon-square-sm"
                                                    onclick="location.href='{{ route('attendance.edit', $item->id) }}'"
                                                    title="Edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>

                                                <form action="{{ route('attendance.destroy', $item->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-icon-square-sm"
                                                        title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted"> - </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-3">Belum ada data absensi</td>
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
