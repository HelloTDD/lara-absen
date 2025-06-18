@extends('layouts.app')
@section('page-title', 'Daftar Bagian')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                {{-- <div class="align-self-center"> --}}
                    <div class="justify-content-between d-flex">
                        {{-- <div class="align-self-center"> --}}
                            <div>
                                <h3 class="card-title">Daftar Bagian</h3>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary btn-md" data-bs-toggle="modal"
                                    data-bs-target="#exampleModalLarge"><i data-feather="plus-square"
                                        class="align-self-center icon-xs me-2"></i>Tambah Data Bagian</button>
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
                                    <form action="{{ route('role.store') }}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="mb-3 col-lg-6">
                                                <label for="role_name">Nama Bagian</label>
                                                <input class="form-control" type="text" name="role_name" required>
                                            </div>
                                            <div class="mb-3 col-lg-6">
                                                <label for="description">Deskripsi</label>
                                                <input class="form-control" type="text" name="description" required>
                                            </div>
                                            <div class="mb-3 col-lg-6">
                                                <label for="job_description">Job deskripsi</label>
                                                {{-- <textarea name="job_description" id="job_description" cols="30" rows="10" required></textarea> --}}
                                            </div>
                                            <button id="rowAdder" type="button"
                                                class="btn btn-dark">
                                                <span class="bi bi-plus-square-dotted">
                                                </span> ADD
                                            </button>
                                            <div class="col-lg-12">
                                                <div id="row">
                                                    <div class="input-group m-3">
                                                        <div class="input-group-prepend">
                                                            <button class="btn btn-danger"
                                                                id="DeleteRow" type="button">
                                                                <i class="bi bi-trash"></i>
                                                                Delete
                                                            </button>
                                                        </div>
                                                        <input type="text"
                                                            class="form-control m-input" name="job_description[]" placeholder="description">
                                                    </div>
                                                </div>

                                                <div id="newinput"></div>
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
                                    <th>Deskripsi</th>
                                    <th>Job Deskripsi</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($no = 1)
                                @foreach($role as $item)
                                <tr>
                                    <td>{{ $no }}</td>
                                    <td>{{ $item->role_name }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td>{{ $item->job_description ?? 'Deskripsi belum dibuat' }}</td>
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
                                                    <a class="dropdown-item" href="{{ url('/role/edit') }}/{{ $item->id }}" >Edit</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('role.delete', ['id' => $item->id]) }}">Delete</a>
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
                                                    <label for="role_name">Nama Bagian</label>
                                                    <input class="form-control" type="text" name="role_name" id="role_name_edit" required>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="description">Deskripsi</label>
                                                    <input class="form-control" type="text" name="description" id="description_edit" required>
                                                </div>
                                                <div class="mb-3 col-lg-6">
                                                    <label for="job_description">Job Deskripsi</label>

                                                    <button id="rowAdder" type="button"
                                                        class="btn btn-dark">
                                                        <span class="bi bi-plus-square-dotted">
                                                        </span> ADD
                                                    </button>
                                                    <div class="col-lg-12">
                                                        <div id="row">
                                                            <div class="input-group m-3">
                                                                <div class="input-group-prepend">
                                                                    <button class="btn btn-danger"
                                                                        id="DeleteRow" type="button">
                                                                        <i class="bi bi-trash"></i>
                                                                        Delete
                                                                    </button>
                                                                </div>
                                                                <input type="text"
                                                                    class="form-control m-input" name="job_description[]" placeholder="description">
                                                            </div>
                                                        </div>

                                                        <div id="newinput"></div>
                                                    </div>
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
        <script type="text/javascript">
            $("#rowAdder").click(function() {
                newRowAdd =
                    '<div id="row"> <div class="input-group m-3">' +
                    '<div class="input-group-prepend">' +
                    '<button class="btn btn-danger" id="DeleteRow" type="button">' +
                    '<i class="bi bi-trash"></i> Delete</button> </div>' +
                    '<input type="text" class="form-control m-input" name="job_description[]" placeholder="description"> </div> </div>';

                $('#newinput').append(newRowAdd);
            });

            $("body").on("click", "#DeleteRow", function() {
                $(this).parents("#row").remove();
            })
        </script>
            {{-- <script>
                function openModalEdit(id, role_name, description, job_description) {
                    $('#modalEdits').modal('show');
                    $('#role_name_edit').val(role_name);
                    $('#description_edit').val(description);
                    $('#job_description_edit').val(job_description);
                    $('form[action]').attr('action', `/role/update/${id}`);
                }
            </script> --}}
        @endpush
