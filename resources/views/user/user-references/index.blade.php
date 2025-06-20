@extends('layouts.app')
@section('page-title', 'Surat Referensi Karyawan')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                {{-- <div class="align-self-center"> --}}
                    <div class="justify-content-between d-flex">
                        {{-- <div class="align-self-center"> --}}
                            <div>
                                <h3 class="card-title">Daftar Surat Referensi Karyawan</h3>
                            </div>
                            <div>
                                @if (Auth::user()->is_admin)

                                    <button type="button" class="btn btn-primary btn-md" data-bs-toggle="modal"
                                        data-bs-target="#exampleModalLarge"><i data-feather="plus-square"
                                            class="align-self-center icon-xs me-2"></i>Tambah Data Surat Referensi</button>

                                    <x-modal id="exampleModalLarge" title="Form References" size="lg">
                                        <form action="{{ route('user-references.store') }}" method="post"
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
                                                    <label>Tanggal References</label>
                                                    <input class="form-control" type="date" name="references_date"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label>Keterangan References</label>
                                                <textarea class="form-control" name="desc_references" required rows="10"
                                                    maxlength="1000"></textarea>
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

                        <table class="table table-bordered table-striped">
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
                                @foreach ($userReferences as $reference)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $reference->user->name }}</td>
                                        <td>{{ $reference->references_date }}</td>
                                        <td>{{ $reference->approve_with ?? '-' }}</td>
                                        <td>
                                        <a href="{{ route('user-references.download', ['id' => $reference->id]) }}"
                                            class="btn btn-info"> <i class="fas fa-download"></i>
                                            Download</a>
                                        </td>
                                        <td>
                                            @if (Auth::user()->is_admin)
                                                <button class="btn btn-warning btn-sm"
                                                    onclick="openModalEdit('{{ $reference->id }}', '{{ $reference->start_contract_date }}', '{{ $reference->end_contract_date }}', '{{ $reference->desc_constract }}')">Edit</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <x-modal id="modalEdits" title="Form Edit References" size="lg">
                            <form action="" method="post">
                                @csrf
                                @method('PUT')
                                <div class="row g-3 mb-3">
                                    <div class="col-lg-6">
                                        <label>Tanggal Mulai References</label>
                                        <input class="form-control" type="date" name="start_contract_date"
                                            id="start_date" required>
                                    </div>
                                    <div class="col-lg-6">
                                        <label>Tanggal Selesai References</label>
                                        <input class="form-control" type="date" name="end_contract_date" id="end_date"
                                            required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label>Keterangan References</label>
                                    <textarea class="form-control" name="desc_constract" id="description" required
                                        rows="10" maxlength="1000"></textarea>
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
