@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>Cuti Karyawan</h1>
        <div class="card">
            <div class="card-header">
                {{-- <div class="align-self-center"> --}}
                    <div class="justify-content-between d-flex">
                        {{-- <div class="align-self-center"> --}}
                            <div>
                                <h3 class="card-title">Daftar Cuti Karyawan</h3>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary btn-md" data-bs-toggle="modal"
                                    data-bs-target="#exampleModalLarge"><i data-feather="plus-square"
                                        class="align-self-center icon-xs me-2"></i>Tambah Data Cuti</button>
                            </div>
                        </div>
                        {{-- </div> --}}
                    <div class="modal fade bd-example-modal-lg" id="exampleModalLarge" tabindex="-1" role="dialog"
                        aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg dialog-center" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title m-0" id="myLargeModalLabel">Form Cuti</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div><!--end modal-header-->
                                <div class="modal-body">
                                    <form action="" method="post">
                                        @csrf
                                        <div class="row g-3 mb-3">
                                            <div class="col-lg-6">
                                                <label>Tanggal Mulai Cuti</label>
                                                <input class="form-control" type="date" name="start_date" required>
                                            </div>
                                            <div class="col-lg-6">
                                                <label>Tanggal Selesai Cuti</label>
                                                <input class="form-control" type="date" name="end_date" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label>Keterangan Cuti</label>
                                            <textarea class="form-control" name="description" required rows="10"
                                                maxlength="1000"></textarea>
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
                                    <th>Nama Karyawan</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($no = 1)
                                @foreach($leaves as $leave)
                                <tr>
                                    <td>{{ $no }}</td>
                                    <td>{{ $leave->user?->name }}</td>
                                    <td>{{ $leave->leave_date_start }} ~ {{ $leave->leave_date_end }}</td>
                                    <td>
                                        @if ($leave->status == 'approved')
                                            <span class="badge rounded-4 bg-success fs-6 m-1">Approve</span>
                                        @elseif ($leave->status == 'rejected')
                                            <span class="badge rounded-4 bg-danger fs-6 m-1">Reject</span>
                                        @else
                                            <span class="badge rounded-4 bg-warning fs-6 m-1">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown d-inline-block">
                                            <a class="dropdown-toggle arrow-none" id="dLabel11"
                                                data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                                                aria-expanded="false">
                                                <i class="las la-ellipsis-v font-20 text-muted"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dLabel11">
                                                @if ($leave->status == 'pending' && Auth::user()->is_admin == 1)
                                                    <a class="dropdown-item"
                                                        href="{{ route('user-leave.approve', ['id' => $leave->id]) }}">Approve</a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('user-leave.reject', ['id' => $leave->id]) }}">Reject</a>
                                                @endif
                                                <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                                    onclick="openModalEdit('{{ $leave->id }}', '{{ $leave->leave_date_start }}', '{{ $leave->leave_date_end }}', '{{ $leave->desc_leave }}')">Edit</button>
                                                {{-- <a class="dropdown-item"
                                                    href="{{ route('user-leave.edit', ['id' => $leave->id]) }}">Edit</a>
                                                --}}
                                                <a class="dropdown-item"
                                                    href="{{ route('user-leave.delete', ['id' => $leave->id]) }}">Delete</a>
                                            </div>
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
                                        <h6 class="modal-title m-0" id="myModalEditsLabel">Form Cuti</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div><!--end modal-header-->
                                    <div class="modal-body">
                                        <form action="" method="post">
                                            @csrf
                                            @method('PUT')
                                            <div class="row g-3 mb-3">
                                                <div class="col-lg-6">
                                                    <label>Tanggal Mulai Cuti</label>
                                                    <input class="form-control" type="date" name="start_date"
                                                        id="start_date" required>
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>Tanggal Selesai Cuti</label>
                                                    <input class="form-control" type="date" name="end_date"
                                                        id="end_date" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label>Keterangan Cuti</label>
                                                <textarea class="form-control" name="description" id="description"
                                                    required rows="10" maxlength="1000"></textarea>
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
                    {{ $leaves->links() }}
                </div>
            </div>
        </div>

        @endsection

        @push('scripts')
            <script>
                function openModalEdit(id, start_date, end_date, description) {
                    $('#modalEdits').modal('show');
                    $('#start_date').val(start_date);
                    $('#end_date').val(end_date);
                    $('#description').val(description);
                    $('form').attr('action', `/user-leave/update/${id}`);
                }
            </script>
        @endpush