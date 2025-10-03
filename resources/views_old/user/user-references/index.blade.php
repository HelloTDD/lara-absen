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
                                            @if (in_array(Auth::user()->role_name, ['Finance', 'Scheduler', 'Supervisor']))
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

                        <table class="table table-bordered table-striped" id="datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No. Referensi</th>
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
                                        <td>{{ $reference->references_no }}</td>
                                        <td>{{ $reference->user->name }}</td>
                                        <td>{{ $reference->references_date }}</td>
                                        <td>{{ $reference->approve_with ?? '-' }}</td>
                                        <td>
                                        <a href="{{ route('user-references.download', ['id' => $reference->id]) }}"
                                            class="btn btn-info"> <i class="fas fa-download"></i>
                                            Download</a>
                                            <a href="{{ route('user-references.preview', $reference->id) }}" target="_blank" class="btn btn-secondary">Preview</a>

                                        </td>
                                        @if (Auth::user()->is_admin)
                                        <td class="text-end">
                                        <div class="dropstart d-inline-block">
                                            <button class="btn btn-link dropdown-toggle arrow-none p-0" type="button"
                                                id="dropdownMenuButton{{ $reference->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="las la-ellipsis-v font-20 text-muted"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="dropdownMenuButton{{ $reference->id }}">
                                                <li>
                                                    <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                                        onclick="openModalEdit('{{ $reference->id }}', '{{ $reference->user_id }}', '{{ $reference->references_date }}', '{{ $reference->desc_references }}')">Edit</button>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('user-references.delete', ['id' => $reference->id]) }}">Delete</a>
                                                </li>
                                            </ul>
                                        </div>
                                        </td>
                                            @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <x-modal id="modalEdits" title="Form Edit References" size="lg">
                            <form action="" method="post">
                                @csrf
                                @method('PUT')
                                @if (in_array(Auth::user()->role_name, ['Finance', 'Scheduler', 'Supervisor']))
                                                <div class="mb-3">
                                                    <label>Nama Karyawan</label>
                                                    <select class="form-select" name="user_id" id="user_id_edit" required>
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
                                                    <input class="form-control" type="date" name="references_date" id="references_date_edit"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label>Keterangan References</label>
                                                <textarea class="form-control" name="desc_references" id="desc_references_edit" required rows="10"
                                                    maxlength="1000"></textarea>
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
                function openModalEdit(id, user_id, references_date, desc_references) {
                    $('#modalEdits').modal('show');
                    $('#user_id_edit').val(user_id);
                    $('#references_date_edit').val(references_date);
                    $('#desc_references_edit').text(desc_references);
                    $('form').attr('action', `/user-references/update/${id}`);
                }
            </script>
        @endpush
