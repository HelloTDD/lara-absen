@extends('layouts.app')
@section('page-title', 'Edit Bagian')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    {{-- <div class="align-self-center"> --}}
                        <div class="justify-content-between d-flex">
                            {{-- <div class="align-self-center"> --}}
                                <div>
                                    <h3 class="card-title">Edit Bagian</h3>
                                </div>
                            </div>
                            {{-- </div> --}}
                    </div>
                    <div class="card-body">
                        <form action="/role/update/{{ $role->id }}" method="post">
                            {{-- <form action="" method="post"> --}}
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="mb-3 col-lg-6">
                                        <label for="role_name">Nama Bagian</label>
                                        <input class="form-control" type="text" name="role_name" id="role_name"
                                            value="{{ $role->role_name }}" required>
                                    </div>
                                    <div class="mb-3 col-lg-6">
                                        <label for="description">Deskripsi</label>
                                        <input class="form-control" type="text" name="description" id="description"
                                            value="{{ $role->description }}" required>
                                    </div>
                                    <div class="mb-3 col-lg-6">
                                        <label for="job_description">Job Deskripsi</label>
                                        <button id="rowAdder" type="button" class="btn btn-dark">
                                            <span class="bi bi-plus-square-dotted">
                                            </span> ADD
                                        </button>
                                        @if (isset($job_description))
                                            @php
                                                $job_description = json_decode($role->job_description, true);
                                                foreach ($job_description as $row) {
                                                    echo '

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
                                                                        class="form-control m-input" name="job_description[]" placeholder="description" value="' . $row . '">
                                                                </div>
                                                            </div>

                                                            <div id="newinput"></div>
                                                        </div>

                                                                    ';
                                                    }

                                            @endphp
                                        @endif
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">Update</button>
                            </form>
                    </div>
                </div>
            </div>

@endsection
        @push('scripts')
            <script type="text/javascript">
                $("#rowAdder").click(function () {
                    newRowAdd =
                        '<div id="row"> <div class="input-group m-3">' +
                        '<div class="input-group-prepend">' +
                        '<button class="btn btn-danger" id="DeleteRow" type="button">' +
                        '<i class="bi bi-trash"></i> Delete</button> </div>' +
                        '<input type="text" class="form-control m-input" name="job_description[]" placeholder="description"> </div> </div>';

                    $('#newinput').append(newRowAdd);
                });

                $("body").on("click", "#DeleteRow", function () {
                    $(this).parents("#row").remove();
                })
            </script>
        @endpush