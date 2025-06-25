@extends('layouts.app')
@section('page-title', 'Shift Karyawan')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                    <form action="{{ route('user-shift.filter') }}" method="post">
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
                        <a href="{{ route('user-shift.reset') }}" class="btn btn-secondary">Reset</a>
                        <a href="{{ route('user-shift.print') }}" class="btn btn-primary">Print</a>
                        <a href="{{ route('user-shift.export') }}" class="btn btn-primary">Export</a>
                    </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                {{-- <div class="align-self-center"> --}}
                    <div class="justify-content-between d-flex">
                        {{-- <div class="align-self-center"> --}}
                            <div>
                                <h3 class="card-title">Daftar Shift Karyawan</h3>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary btn-md" data-bs-toggle="modal"
                                    data-bs-target="#exampleModalLarge"><i data-feather="plus-square"
                                        class="align-self-center icon-xs me-2"></i>Tambah Data Shift</button>
                            </div>
                        </div>
                        {{-- </div> --}}
                    <x-modal id="exampleModalLarge" title="Form Shift">
                        <form action="{{ route('user-shift.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="mb-3">
                                    <label for="user_id">User</label>
                                    <select class="form-control" name="user_id" required>
                                        @if (count($users) == 0)
                                            <option value="">No users available</option>
                                        @else
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="user_id">Shift</label>
                                    <select class="form-control" name="shift_id" required>
                                        @if (count($shift) == 0)
                                            <option value="">No shift available</option>
                                        @else
                                            @foreach($shift as $row)
                                                <option value="{{ $row->id }}">{{ $row->shift_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3 col-lg-6">
                                    <label for="salary_basic">Tanggal Mulai</label>
                                    <input class="form-control" type="date" name="start_date_shift" required>
                                </div>
                                <div class="mb-3 col-lg-6">
                                    <label for="salary_allowance">Tanggal Selesai</label>
                                    <input class="form-control" type="date" name="end_date_shift" value="0" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </form>

                    </x-modal>
                </div>
                <div class="card-body">
                    <div class="table-responsive">

                        <table class="table table-bordered table-striped" id="datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Karyawan</th>
                                    <th>Shift</th>
                                    <th>Mulai</th>
                                    <th>Selesai</th>
                                    <th>MASUK</th>
                                    <th>PULANG</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($no = 1)
                                @foreach($usershift as $item)
                                <tr>
                                    <td>{{ $no }}</td>
                                    <td>{{ $item->user?->name }}</td>
                                    <td>{{ $item->shift->shift_name }}</td>
                                    <td>{{ $item->start_date_shift }}</td>
                                    <td>{{ $item->end_date_shift }}</td>
                                    <td>{{ $item->shift->check_in }}</td>
                                    <td>{{ $item->shift->check_out }}</td>
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
                                                        onclick="openModalEdit('{{ $item->id }}', '{{ $item->user_id }}', '{{ $item->shift }}', '{{ $item->start_date_shift }}', '{{ $item->end_date_shift }}', '{{ $item->salary_holiday }}')">
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

                        <x-modal id="modalEdits" title="Form Edit Shift Karyawan">
                            <form action="" method="post">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="mb-3">
                                        <label for="user_id">User</label>
                                        <select class="form-control" name="user_id" id="user_id_edit" required>
                                            @if (count($users) == 0)
                                                <option value="">No users available</option>
                                            @else
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="user_id">Shift</label>
                                        <select class="form-control" name="shift_id" name="shift_id_edit" required>
                                            @if (count($shift) == 0)
                                                <option value="">No shift available</option>
                                            @else
                                                @foreach($shift as $row)
                                                    <option value="{{ $row->id }}">{{ $row->shift_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="mb-3 col-lg-6">
                                        <label for="salary_basic">Tanggal Mulai</label>
                                        <input class="form-control" type="date" name="start_date_shift"
                                            id="start_date_shift_edit" required>
                                    </div>
                                    <div class="mb-3 col-lg-6">
                                        <label for="salary_allowance">Tanggal Selesai</label>
                                        <input class="form-control" type="date" name="end_date_shift"
                                            id="end_date_shift_edit" value="0" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">Update</button>
                            </form>
                        </x-modal>
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
                function openModalEdit(id, user_id, shift_id, start_date_shift, end_date_shift) {
                    $('#modalEdits').modal('show');
                    $('#user_id_edit').val(user_id);
                    $('#shift_id_edit').val(shift_id);
                    $('#start_date_shift_edit').val(start_date_shift);
                    $('#end_date_shift_edit').val(end_date_shift);
                    $('form[action]').attr('action', `/user-shift/update/${id}`);
                }
            </script>
        @endpush
