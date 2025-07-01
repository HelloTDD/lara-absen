@extends('layouts.app')
@section('page-title', 'Contract Karyawan')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                {{-- <div class="align-self-center"> --}}
                    <div class="justify-content-between d-flex">
                        {{-- <div class="align-self-center"> --}}
                            <div>
                                <h3 class="card-title">Daftar Contract Karyawan</h3>
                            </div>
                            <div>
                                @if (Auth::user()->is_admin)

                                    <button type="button" class="btn btn-primary btn-md" data-bs-toggle="modal"
                                        data-bs-target="#exampleModalLarge"><i data-feather="plus-square"
                                            class="align-self-center icon-xs me-2"></i>Tambah Data Contract</button>

                                    <x-modal id="exampleModalLarge" title="Form Contract" size="lg">
                                        <form action="{{ route('user-contract.store') }}" method="post"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @if (Auth::user()->is_admin == 1)
                                                <div class="mb-3">
                                                    <label>Nama Karyawan</label>
                                                    <select class="form-select" name="user_id" required>
                                                        <option value="">Pilih Karyawan</option>
                                                        @foreach ($users as $user)
                                                            @if ($user->is_admin == 0)
                                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                            <div class="row g-3 mb-3">
                                                <div class="col-lg-6">
                                                    <label>Tanggal Mulai Contract</label>
                                                    <input class="form-control" type="date" name="start_contract_date"
                                                        required>
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>Tanggal Selesai Contract</label>
                                                    <input class="form-control" type="date" name="end_contract_date"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label>Keterangan Contract</label>
                                                <textarea class="form-control" name="desc_constract" required rows="10"
                                                    maxlength="1000"></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label>File Contract</label>
                                                <input class="form-control" type="file" name="file"
                                                    accept=".jpg,.jpeg,.png,.pdf,.docx,.doc" required>
                                            </div>
                                            <button type="submit" class="btn btn-success">Submit</button>
                                        </form>
                                    </x-modal>
                                @endif
                            </div>
                        </div>
                        {{-- </div> --}}
                </div>
                <div class="card-body">
                    <div class="table-responsive">

                        <table class="table table-bordered table-striped" id="datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Karyawan</th>
                                    <th>Tanggal</th>
                                    <th>Disetujui Oleh</th>
                                    <th>File</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($no = 1)
                                @foreach($userContracts as $item)
                                <tr>
                                    <td>{{ $no }}</td>
                                    <td>{{ $item->contracts?->user?->name }}</td>
                                    <td>{{ $item->contracts?->start_contract_date }} ~
                                        {{ $item->contracts?->end_contract_date }}
                                    </td>
                                    <td> {{ $item->contracts->approve_with ?? 'Belum Disetujui'}} </td>
                                    <td> <a href="{{ route('user-contract.download', ['id' => $item->id]) }}"
                                            class="btn btn-info"> <i class="fas fa-download"></i>
                                            Download</a> </td>
                                    <td>
                                        @if (in_array($item->status_contract, ['APPROVE ', 'REVISION', 'RENEWE']))
                                            <span
                                                class="badge rounded-4 bg-success fs-6 m-1">{{ $item->status_contract }}</span>
                                        @elseif ($item->status_contract == 'APPROVE' || $item->status_contract == 'RENEW')
                                            <span class="badge rounded-4 bg-success fs-6 m-1">APPROVE</span>
                                        @elseif ($item->status_contract == 'CANCEL')
                                            <span class="badge rounded-4 bg-danger fs-6 m-1">Reject</span>
                                        @elseif ($item->status_contract == 'REVISION')
                                            <span class="badge rounded-4 bg-info fs-6 m-1">Revision</span>
                                        @else
                                            <span class="badge rounded-4 bg-warning fs-6 m-1">{{ Str::ucfirst($item->status_contract) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="dropstart d-inline-block">
                                            <a class="dropdown-toggle arrow-none" id="dLabel11"
                                                data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                                                aria-expanded="false">
                                                <i class="las la-ellipsis-v font-20 text-muted"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dLabel11">
                                                @if (Auth::user()->is_admin == 1)
                                                    @if (in_array($item->status_contract, ['PENDING', 'REVISION']))
                                                        <a class="dropdown-item"
                                                            href="{{ route('user-contract.status', ['id' => $item->id, 'status' => 'APPROVE']) }}">Approve</a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('user-contract.status', ['id' => $item->id, 'status' => 'CANCEL']) }}">Reject</a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('user-contract.status', ['id' => $item->id, 'status' => 'REVISION']) }}">Revisi</a>
                                                        <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                                            onclick="openModalEdit('{{ $item->contracts?->id }}', '{{ $item->contracts?->start_contract_date }}', '{{ $item->contracts?->end_contract_date }}', '{{ $item->contracts?->desc_contract }}')">Edit</button>
                                                    @elseif (($item->status_contract == 'APPROVE') && todayNow() > $item->contracts?->end_contract_date)
                                                        <a class="dropdown-item"
                                                            href="{{ route('user-contract.status', ['id' => $item->id, 'status' => 'RENEW']) }}">Renew</a>
                                                    @endif
                                                    <a class="dropdown-item"
                                                        href="{{ route('user-contract.delete', ['id' => $item->contracts?->id]) }}">Delete</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @php($no++)
                                @endforeach
                            </tbody>
                        </table>

                        <x-modal id="modalEdits" title="Form Edit Contract" size="lg">
                            <form action="" method="post">
                                @csrf
                                @method('PUT')
                                <div class="row g-3 mb-3">
                                    <div class="col-lg-6">
                                        <label>Tanggal Mulai Contract</label>
                                        <input class="form-control" type="date" name="start_contract_date"
                                            id="start_date" required>
                                    </div>
                                    <div class="col-lg-6">
                                        <label>Tanggal Selesai Contract</label>
                                        <input class="form-control" type="date" name="end_contract_date" id="end_date"
                                            required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label>Keterangan Contract</label>
                                    <textarea class="form-control" name="desc_constract" id="description" required
                                        rows="10" maxlength="1000"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label>File Contract</label>
                                    <input class="form-control" type="file" name="file"
                                        accept=".jpg,.jpeg,.png,.pdf,.docx,.doc">
                                    <span class="text-danger">*kosongkan jika tidak perlu upload file</span>
                                </div>
                                <button type="submit" class="btn btn-success">Submit</button>
                            </form>

                        </x-modal>

                    </div>
                </div>
            </div>
        </div>

        @endsection

        @push('scripts')
            <script src="{{ asset('assets/libs/tinymce/tinymce.min.js') }}"></script>
            <script src="{{ asset('assets/js/pages/form-editor.init.js') }}"></script>
            <script>
                function openModalEdit(id, start_date, end_date, description) {
                    $('#modalEdits').modal('show');
                    $('#start_date').val(start_date);
                    $('#end_date').val(end_date);
                    $('#description').text(description);
                    $('form').attr('action', `/user-contract/update/${id}`);
                }
            </script>
        @endpush
