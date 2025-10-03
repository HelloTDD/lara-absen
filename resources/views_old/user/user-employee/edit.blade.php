@extends('layouts.app')
@section('page-title', 'Edit karyawan')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                {{-- <div class="align-self-center"> --}}
                    <div class="justify-content-between d-flex">
                        {{-- <div class="align-self-center"> --}}
                            <div>
                                <h3 class="card-title">Edit Karyawan</h3>
                            </div>
                        </div>
                        {{-- </div> --}}
                </div>
                <div class="card-body">
                                        <form action="/user-employee/update/{{ $user->id }}" method="post">
                                            @csrf
                                            @method('PUT')
                                            <div class="row">
                                                <div class="mb-3 col-lg-6">
                                                    <label for="name">Nama Karyawan</label>
                                                    <input class="form-control" type="text" name="name" id="name" value="{{ $user->name }}" required>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="username">Username</label>
                                                    <input class="form-control" style="background-color:aliceblue" type="text" name="username" id="username" value="{{ $user->username }}" readonly required>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="email">Email</label>
                                                    <input class="form-control" type="email" name="email" id="email" value="{{ $user->email }}" required>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="password">password</label>
                                                    <p class="form-text">Kosongkan jika tidak ingin mengubah password</p>
                                                    <input class="form-control" type="password" name="password" id="password">
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="role_id">Bagian</label>
                                                    <select name="role_id" id="role_id" class="form-select" required>
                                                        <option value="">Pilih Bagian</option>
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->id }}" {{ $role->id == $user->role_id ? 'selected' : '' }}>{{ $role->role_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="phone">Telepon</label>
                                                    <input class="form-control" type="text" id="phone" name="phone" value="{{ $user->phone }}" required>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="birth_date">Tanggal Lahir</label>
                                                    <input class="form-control" type="date" id="birth_date" name="birth_date" value="{{ $user->birth_date }}" required>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="gender">Jenis Kelamin</label>
                                                    <select name="gender" id="gender" class="form-select" required>
                                                        <option value="">Pilih Jenis Kelamin</option>
                                                        <option value="Laki-laki" {{ $user->gender == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                                        <option value="Perempuan" {{ $user->gender == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="address">Alamat</label>
                                                    <textarea name="address" id="address" class="form-control" cols="30" rows="10" required>
                                                        {{ $user->address }}
                                                    </textarea>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="date_joined">Tanggal Masuk</label>
                                                    <input class="form-control" type="date" name="date_joined" id="date_joined" value="{{ $user->date_joined }}" required>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="date_joined">Tanggal Keluar</label>
                                                    <input class="form-control" type="date" name="date_leave" id="date_leave" value="{{ $user->date_leave }}">
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="is_admin">Sebagai Admin</label>
                                                    <input class="form-check-input" type="checkbox" name="is_admin" id="is_admin" {{ $user->is_admin == 1 ? 'checked' : '' }}>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="leave">Leave</label>
                                                    <input class="form-control" type="number" name="leave" id="leave" value="{{ $user->leave }}">
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-success">Update</button>
                                            <a href="/user-employee" class="btn btn-secondary">Kembali</a>
                                        </form>
                </div>
            </div>
        </div>

        @endsection
