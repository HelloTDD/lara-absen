@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>Shift</h1>
        <div class="card">
            <div class="card-header">
                {{-- <div class="align-self-center"> --}}
                    <div class="justify-content-between d-flex">
                        {{-- <div class="align-self-center"> --}}
                            <div>
                                <h3 class="card-title">Daftar Shift</h3>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary btn-md" data-bs-toggle="modal"
                                    data-bs-target="#exampleModalLarge"><i data-feather="plus-square"
                                        class="align-self-center icon-xs me-2"></i>Tambah Data Shift</button>
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
                                    <form action="{{ route('shift.store') }}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="mb-3 col-lg-6">
                                                <label for="salary_basic">Nama Shift</label>
                                                <input class="form-control" type="text" name="shift_name" required>
                                            </div>
                                            <div class="mb-3 col-lg-6">
                                                <label for="salary_basic">Waktu Mulai</label>
                                                <input class="form-control" type="time" name="check_in" required>
                                            </div>
                                            <div class="mb-3 col-lg-6">
                                                <label for="salary_allowance">Waktu Selesai</label>
                                                <input class="form-control" type="time" name="check_out"
                                                    value="0" required>
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
                    <div class="table-responsive">

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam pulang</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($no = 1)
                                @foreach($shift as $item)
                                <tr>
                                    <td>{{ $no }}</td>
                                    <td>{{ $item->shift_name }}</td>
                                    <td>{{ $item->check_in }}</td>
                                    <td>{{ $item->check_out }}</td>
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
                                                    <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                                        onclick="openModalEdit('{{ $item->id }}', '{{ $item->shift_name }}', '{{ $item->check_in }}', '{{ $item->check_out }}')">
                                                        Edit
                                                    </button>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('user-shift.delete', ['id' => $item->id]) }}">Delete</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @php($no++)
                                @endforeach
                            </tbody>
                        </table>

                        <div class="modal fade bd-example-modal-lg" id="modalEdits" tabindex="-1" role="dialog"
                            aria-labelledby="myModalEditsLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg dialog-center" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title m-0" id="myModalEditsLabel">Form Edit</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div><!--end modal-header-->
                                    <div class="modal-body">
                                        <form action="" method="post">
                                            @csrf
                                            @method('PUT')
                                            <div class="row">
                                                <div class="mb-3 col-lg-6">
                                                    <label for="salary_basic">Nama Shift</label>
                                                    <input class="form-control" type="text" name="shift_name" id="shift_name_edit" required>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="salary_basic">Waktu Mulai</label>
                                                    <input class="form-control" type="time" name="check_in" id="check_in_edit" required>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="salary_allowance">Waktu Selesai</label>
                                                    <input class="form-control" type="time" name="check_out" id="check_out_edit" required>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-success">Update</button>
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
                </div>
            </div>
        </div>

        @endsection

        @push('scripts')
            <script>
                function openModalEdit(id, shift_name, check_in, check_out) {
                    $('#modalEdits').modal('show');
                    $('#shift_name_edit').val(shift_name);
                    $('#check_in_edit').val(check_in);
                    $('#check_out_edit').val(check_out);
                    $('form[action]').attr('action', `/shift/update/${id}`);
                }
            </script>
        @endpush
