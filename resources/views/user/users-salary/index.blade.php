@extends('layouts.app')
@section('page-title', 'Daftar Gaji Karyawan')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                {{-- <div class="align-self-center"> --}}
                    <div class="justify-content-between d-flex">
                        {{-- <div class="align-self-center"> --}}
                            <div>
                                <h3 class="card-title">Daftar Gaji Karyawan</h3>
                            </div>

                            @if(Auth::user()->is_admin == 1)
                                <div>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#dataTunjangan"><i data-feather="plus-square"
                                            class="align-self-center icon-xs me-2"></i>Lihat Data Tunjangan</button>

                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#exampleModalLarge"><i data-feather="plus-square"
                                            class="align-self-center icon-xs me-2"></i>Tambah Data Gaji</button>
                                </div>
                            @endif

                        </div>
                        {{-- </div> --}}
                    @if(Auth::user()->is_admin == 1)

                    <x-modal id="dataTunjangan" title="Data Tunjangan Karyawan">
                        <div class="row">
                            <form action="{{ route('allowance.store') }}" method="post" class="d-flex flex-row">
                                @csrf
                                <div class="col-lg-9 col-md-9 col-sm-9">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" name="name"
                                            placeholder="Masukkan nama tunjangan" required>
                                    </div>
                                </div>
                                <div class="col-lg-auto">
                                    <button type="submit" class="btn btn-success mb-3 ms-2">Tambah</button>
                                </div>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Tunjangan</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($no = 1)
                                    @foreach($type_allowance as $allowance)
                                    <tr>
                                        <td>{{ $no }}</td>
                                        <td>{{ $allowance->name_allowance }}</td>
                                        <td><a class="btn btn-danger btn-sm text-white"
                                                href="{{ route('allowance.delete', ['id' => $allowance->id]) }}"> <i
                                                    class="fas fa-times"></i> </a></td>
                                    </tr>
                                    @php($no++)
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-modal>

                    <x-modal id="exampleModalLarge" title="Form Gaji">
                        <form action="{{ route('user-salaries.store') }}" method="post">
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
                                <div class="mb-3 col-lg-6">
                                    <label for="salary_basic">Basic Salary</label>
                                    <input class="form-control" type="number" name="salary_basic" value="0" required>
                                </div>
                                {{-- <div class="mb-3 col-lg-6">
                                    <label for="salary_allowance">Allowance</label>
                                    <input class="form-control" type="number" name="salary_allowance" value="0"
                                        required>
                                </div> --}}
                                <div class="mb-3 col-lg-6">
                                    <label for="salary_bonus">Bonus</label>
                                    <input class="form-control" type="number" name="salary_bonus" value="0" required>
                                </div>
                                <div class="mb-3 col-lg-6">
                                    <label for="salary_holiday">Holiday</label>
                                    <input class="form-control" type="number" name="salary_holiday" value="0" required>
                                </div>
                                <div class="mb-3 col-lg-12">
                                    <label for="salary_allowance">Allowance</label>
                                    <div class="form-check">
                                        @foreach($type_allowance as $allowance)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="salary_allowance[]"
                                                    value="{{ $allowance->id }}" id="allowance_{{ $allowance->id }}">
                                                <label class="form-check-label"
                                                    for="allowance_{{ $allowance->id }}">{{ $allowance->name_allowance }}</label>
                                            </div>
                                            <input type="number" name="allowances[{{ $allowance->id }}]" value=""
                                                class="form-control" id="">
                                        @endforeach
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">Submit</button>
                        </form>
                    </x-modal>
                    @endif
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    @if(Auth::user()->is_admin == 1)
                                        <th>Nama Karyawan</th>
                                    @endif
                                    <th>Gaji Pokok</th>
                                    <th>Tunjangan</th>
                                    <th>Bonus</th>
                                    <th>THR</th>
                                    <th>Total</th>
                                    <th>Bank Name</th>
                                    <th>Account Number</th>
                                    <th>Bank Account Name</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($no = 1)
                                @foreach($salary as $item)
                                <tr>
                                    <td>{{ $no }}</td>
                                    <td>{{ $item->user?->name }}</td>
                                    <td>Rp {{ number_format($item->salary_basic) }}</td>
                                    <td>Rp {{ number_format($item->salary_allowance) }}</td>
                                    <td>Rp {{ number_format($item->salary_bonus) }}</td>
                                    <td>Rp {{ number_format($item->salary_holiday) }}</td>
                                    <td>Rp {{ number_format($item->salary_total) }}</td>
                                    <td>{{$item->user?->user_bank?->bank_name }}</td>
                                    <td>{{ $item->user?->user_bank?->account_number }}</td>
                                    <td>{{ $item->user?->user_bank?->account_name }}</td>
                                    <td class="text-end">
                                        @if(Auth::user()->is_admin == 1)
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
                                                            modal-bs-target="#modalEdits"
                                                            onclick='openModalEdit(@json($item->id), @json($item->user_id), @json($item->salary_basic), @json($item->salary_bonus), @json($item->salary_holiday), @json($item->month), @json($item->year), @json($item->user->allowances), @json($type_allowance))'>
                                                            Edit
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('user-salaries.delete', ['id' => $item->id]) }}">Delete</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            @endif
                                        </td>
                                </tr>
                                @php($no++)
                                @endforeach
                            </tbody>
                        </table>

                        @if(Auth::user()->is_admin == 1)
                            <x-modal id="modalEdits" title="Edit Form Gaji">
                                <form action="" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="mb-3">
                                            <label for="user_id_edit">User</label>
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
                                        <div class="mb-3 col-lg-6">
                                            <label for="salary_basic_edit">Basic Salary</label>
                                            <input class="form-control" type="number" name="salary_basic"
                                                id="salary_basic_edit" value="0" required>
                                        </div>
                                        <div class="mb-3 col-lg-6">
                                            <label for="salary_bonus_edit">Bonus</label>
                                            <input class="form-control" type="number" name="salary_bonus"
                                                id="salary_bonus_edit" value="0" required>
                                        </div>
                                        <div class="mb-3 col-lg-6">
                                            <label for="salary_holiday_edit">Holiday</label>
                                            <input class="form-control" type="number" name="salary_holiday"
                                                id="salary_holiday_edit" value="0" required>
                                        </div>

                                        <div class="mb-3 col-lg-12">
                                            <label>Allowance</label>
                                            <div id="allowance-container">
                                                <!-- Allowance inputs will be dynamically added here -->
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success">Update</button>
                                </form>
                            </x-modal>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @endsection

        @push('scripts')
            <script>
                function openModalEdit(id, user_id, salary_basic, salary_bonus, salary_holiday, month, year, salary_allowances, type_allowance) {
                    $('#modalEdits').modal('show');

                    $('#user_id_edit').val(user_id);
                    $('#salary_basic_edit').val(salary_basic);
                    $('#salary_bonus_edit').val(salary_bonus);
                    $('#salary_holiday_edit').val(salary_holiday);
                    $('#month_edit').val(month);
                    $('#year_edit').val(year);
                    $('form[action]').attr('action', `/user-salaries/update/${id}`);

                    // Kosongkan allowance container dulu
                    const $container = $('#allowance-container');
                    $container.empty();

                    // Loop allowance dinamis
                    let isChecked = '';

                    type_allowance.forEach((allowanceType) => {
                        const existingAllowance = salary_allowances.find(sa => sa.id === allowanceType.id);
                        const isChecked = existingAllowance?.pivot?.type_allowance_id === allowanceType.id ? 'checked' : '';
                        const $wrapper = $('<div>', { class: 'mb-2' });

                        const checkboxHtml = `
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        name="salary_allowance[]" value="${allowanceType.id}"
                                        id="allowance_${allowanceType.id}" ${isChecked}>
                                    <label class="form-check-label"
                                        for="allowance_${allowanceType.id}">${allowanceType.name_allowance}</label>
                                </div>
                            `;

                        const inputHtml = `
                                <input type="number" name="allowances[${allowanceType.id}]" value="${existingAllowance?.pivot?.amount || ''}" class="form-control">
                            `;

                        $wrapper.append(checkboxHtml);
                        $wrapper.append(inputHtml);
                        $container.append($wrapper);
                    })
                }
            </script>
        @endpush
