@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <h1 class="mb-4">Edit Absensi</h1>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('attendance.update', $absensi->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="user_name" class="form-label">Nama</label>
                        <input type="text" class="form-control" value="{{ $absensi->user->name }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="date" value="{{ $absensi->date }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="shift_id" class="form-label">Shift</label>
                        <select name="shift_id" class="form-control" required>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ $absensi->shift_id == $shift->id ? 'selected' : '' }}>
                                    {{ $shift->shift_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="check_in_time" class="form-label">Jam Masuk</label>
                        <input type="time" step="1" name="check_in_time" class="form-control" value="{{ $absensi->check_in_time }}">
                    </div>

                    <div class="mb-3">
                        <label for="check_out_time" class="form-label">Jam Pulang</label>
                        <input type="time" step="1" name="check_out_time" class="form-control" value="{{ $absensi->check_out_time }}">
                    </div>

                    <div class="mb-3">
                        <label for="check_in_photo" class="form-label">Foto Masuk</label><br>
                        @if ($absensi->check_in_photo)
                            <img src="{{ asset('storage/absensi/' . $absensi->check_in_photo) }}" alt="Foto Masuk" class="mb-2 rounded" style="max-width: 200px;">
                        @endif
                        <input type="file" name="check_in_photo" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="check_out_photo" class="form-label">Foto Pulang</label><br>
                        @if ($absensi->check_out_photo)
                            <img src="{{ asset('storage/absensi/' . $absensi->check_out_photo) }}" alt="Foto Pulang" class="mb-2 rounded" style="max-width: 200px;">
                        @endif
                        <input type="file" name="check_out_photo" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="desc_attendance" class="form-label">Keterangan Absen</label>
                        <select name="desc_attendance" class="form-control">
                            <option value="MASUK" {{ $absensi->status == 'MASUK' ? 'selected' : '' }}>MASUK</option>
                            <option value="PULANG" {{ $absensi->status == 'PULANG' ? 'selected' : '' }}>PULANG</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('attendance.list') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
