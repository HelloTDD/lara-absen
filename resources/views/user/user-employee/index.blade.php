@extends('layouts.app')
@section('page-title', 'Daftar karyawan')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                {{-- <div class="align-self-center"> --}}
                    <div class="justify-content-between d-flex">
                        {{-- <div class="align-self-center"> --}}
                            <div>
                                <h3 class="card-title">Daftar Karyawan</h3>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary btn-md" data-bs-toggle="modal"
                                    data-bs-target="#exampleModalLarge"><i data-feather="plus-square"
                                        class="align-self-center icon-xs me-2"></i>Tambah Data Karyawan</button>
                            </div>
                        </div>
                        {{-- </div> --}}
                    <div class="modal fade bd-example-modal-lg" id="exampleModalLarge" tabindex="-1" role="dialog"
                        aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title m-0" id="myLargeModalLabel">Form Tambah</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div><!--end modal-header-->
                                <div class="modal-body">
                                    <form action="{{ route('user-employee.store') }}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="mb-3 col-lg-6">
                                                <label for="name">Nama Karyawan</label>
                                                <input class="form-control" type="text" name="name" required>
                                            </div>
                                            <div class="mb-3 col-lg-6">
                                                <label for="username">Username</label>
                                                <input class="form-control" type="text" name="username" required>
                                            </div>
                                            <div class="mb-3 col-lg-6">
                                                <label for="email">Email</label>
                                                <input class="form-control" type="email" name="email" required>
                                            </div>
                                            <div class="mb-3 col-lg-6">
                                                <label for="password">password</label>
                                                <input class="form-control" type="password" name="password" required>
                                            </div>
                                            <div class="mb-3 col-lg-6">
                                                <label for="role_id">Bagian</label>
                                                <select name="role_id" class="form-select" required>
                                                    <option value="">Pilih Bagian</option>
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3 col-lg-6">
                                                <label for="phone">Telepon</label>
                                                <input class="form-control" type="text" name="phone"
                                                    placeholder="62xxxxx" required>
                                            </div>
                                            <div class="mb-3 col-lg-6">
                                                <label for="birth_date">Tanggal Lahir</label>
                                                <input class="form-control" type="date" name="birth_date" required>
                                            </div>
                                            <div class="mb-3 col-lg-6">
                                                <label for="gender">Jenis Kelamin</label>
                                                <select name="gender" class="form-select" required>
                                                    <option value="">Pilih Jenis Kelamin</option>
                                                    <option value="Laki-laki">Laki-laki</option>
                                                    <option value="Perempuan">Perempuan</option>
                                                </select>
                                            </div>
                                            <div class="mb-3 col-lg-6">
                                                <label for="address">Alamat</label>
                                                <textarea name="address" class="form-control" cols="30" rows="10"
                                                    required></textarea>
                                            </div>
                                            <div class="mb-3 col-lg-6">
                                                <label for="date_joined">Tanggal Masuk</label>
                                                <input class="form-control" type="date" name="date_joined" required>
                                            </div>
                                            <div class="mb-3 col-lg-6">
                                                <label for="is_admin">Sebagai Admin</label>
                                                <input class="form-check-input" type="checkbox" name="is_admin">
                                            </div>
                                            <div class="mb-3 col-lg-6">
                                                <label for="leave">Leave</label>
                                                <input class="form-control" type="number" name="leave">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-success">Submit</button>
                                    </form>

                                </div><!--end modal-body-->
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-de-secondary btn-sm"
                                        data-bs-dismiss="modal">Close</button>
                                </div><!--end modal-footer-->
                            </div><!--end modal-content-->
                        </div><!--end modal-dialog-->
                    </div><!--end modal-->
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="table-responsive">

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Bagian</th>
                                    <th>Alamat</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Telepon</th>
                                    <th>Masuk</th>
                                    <th>Keluar</th>
                                    <th>Admin</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($no = 1)
                                @foreach($users as $item)
                                <tr>
                                    <td>{{ $no }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->username }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->role->role_name }}</td>
                                    <td>{{ $item->address }}</td>
                                    <td>{{ $item->gender }}</td>
                                    <td>{{ $item->phone }}</td>
                                    <td>{{ $item->date_joined }}</td>
                                    <td>{{ $item->date_leave }}</td>
                                    <td>{{ $item->is_admin == 1 ? 'Ya' : 'Tidak' }}</td>
                                    <td class="text-end">
                                        <div class="dropstart d-inline-block">
                                            <button class="btn btn-link dropdown-toggle arrow-none p-0" type="button"
                                                id="dropdownMenuButton{{ $item->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="las la-ellipsis-v font-20 text-muted"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="dropdownMenuButton{{ $item->id }}">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ url('/user-employee/edit') }}/{{ $item->id }}">Edit</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('user-employee.delete', ['id' => $item->id]) }}">Delete</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @php($no++)
                                @endforeach
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>

        @endsection