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
                                <h3 class="card-title">Detail Karyawan</h3>
                            </div>
                        </div>
                        {{-- </div> --}}
                </div>
                <div class="card-body">
                                        {{-- <form action="/user-employee/update/{{ $user->id }}" method="post">
                                            @csrf
                                            @method('PUT') --}}
                                            <div class="row">
                                                <div class="mb-3 col-lg-6">
                                                    <label for="name">Nama Karyawan</label>
                                                    <input class="form-control" type="text"  style="background-color:aliceblue" name="name" id="name" value="{{ $user->name }}" required readonly>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="username">Username</label>
                                                    <input class="form-control" style="background-color:aliceblue" type="text"  name="username" id="username" value="{{ $user->username }}" required readonly>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="email">Email</label>
                                                    <input class="form-control" type="email"  style="background-color:aliceblue" name="email" id="email" value="{{ $user->email }}" required readonly>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="role_id">Bagian</label>
                                                    <input class="form-control" type="text"  style="background-color:aliceblue" name="role_id" id="role_id" value="{{ $user->role->role_name }}" readonly>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="phone">Telepon</label>
                                                    <input class="form-control" type="text" id="phone"  style="background-color:aliceblue" name="phone" value="{{ $user->phone }}" required readonly>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="birth_date">Tanggal Lahir</label>
                                                    <input class="form-control" type="date" id="birth_date"  style="background-color:aliceblue" name="birth_date" value="{{ $user->birth_date }}" required readonly>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="gender">Jenis Kelamin</label>
                                                    <input class="form-control" type="text" id="gender"  style="background-color:aliceblue" name="gender" value="{{ $user->gender }}" required readonly>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="date_joined">Tanggal Masuk</label>
                                                    <input class="form-control" type="date"  style="background-color:aliceblue" name="date_joined" id="date_joined" value="{{ $user->date_joined }}" required readonly>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="date_joined">Tanggal Keluar</label>
                                                    <input class="form-control" type="date"  style="background-color:aliceblue" name="date_leave" id="date_leave" value="{{ $user->date_leave }}">
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="leave">Sisa Cuti</label>
                                                    <input class="form-control" type="number"  style="background-color:aliceblue" name="leave" id="leave" value="{{ $user->leave }}">
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="address">Alamat</label>
                                                    <textarea  style="background-color:aliceblue" name="address" id="address" class="form-control" cols="30" rows="10" required readonly>
                                                        {{ $user->address }}
                                                    </textarea>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <p class="form-text">Detail bank</p>
                                                    <label for="leave">Bank Name</label>
                                                    <input class="form-control mb-1" type="text"  style="background-color:aliceblue" name="bank_name" id="bank_name" value="{{ $user->user_bank->bank_name ?? '' }}" placeholder="Bank Name">
                                                    <label for="leave">Bank Account Number</label>
                                                    <input class="form-control mb-1" type="text"  style="background-color:aliceblue" name="account_number" id="account_number" value="{{ $user->user_bank->account_number ?? '' }}"  placeholder="Bank Account Number">
                                                    <label for="leave">Bank Account Name</label>
                                                    <input class="form-control mb-1" type="text"  style="background-color:aliceblue" name="account_name" id="account_name" value="{{ $user->user_bank->account_name   ?? '' }}"  placeholder="Bank Account Name">
                                                </div>
                                            </div>
                                            {{-- <button type="submit" class="btn btn-success">Update</button> --}}
                                            <a href="/user-employee" class="btn btn-secondary">Kembali</a>
                                        {{-- </form> --}}
                </div>
            </div>
        </div>

        @endsection
