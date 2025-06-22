@extends('layouts.app')
@section('page-title', Auth::user()->is_admin == 1 ? 'Panel Gaji Bulanan Karyawan' : 'Laporan Gaji Bulanan')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                {{-- <div class="align-self-center"> --}}
                    <div class="justify-content-between d-flex">
                        {{-- <div class="align-self-center"> --}}
                            <div>
                                <h3 class="card-title"> {{ Auth::user()->is_admin == 1 ? 'Panel Gaji Bulanan Karyawan' : 'Laporan Gaji Bulanan' }} </h3>
                            </div>

                            @if (Auth::user()->is_admin == 1);

                                <div>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#addDraft"><i data-feather="plus-square"
                                            class="align-self-center icon-xs me-2"></i>Tambah Draft</button>
                                </div>
                            @endif

                            <x-modal id="addDraft" title="Form Tambah Draft">
                                <form action="{{ route('monthly.salary.store') }}" method="post" class="row">
                                    @csrf
                                    <div class="mb-3 col-lg-12">
                                        <label for="">User</label>
                                        <select name="salary_ids" class="form-control" id="salary_ids">
                                            @foreach ($users as $item)

                                                @if(isset($item->salary?->salary_total))
                                                    <option value="{{ $item->salary?->id }}">
                                                        {{ $item->name . "-" . $item->role?->role_name . " (Rp " . number_format($item->salary?->salary_total) . ")" }}
                                                    </option>
                                                @endif

                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 col-lg-12">
                                        <label for="">Bulan</label>
                                        <select class="form-control" name="month" id="month">
                                            @foreach ($month as $key => $item)
                                                <option value="{{ $key }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 col-lg-12">
                                        <label for="">Tahun</label>
                                        <select class="form-control" name="year" id="year">
                                            @foreach ($year as $item)
                                                <option value="{{ $item }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-success col-lg-3 col-md-12">Buat
                                        Draft!</button>
                                </form>
                            </x-modal>

                        </div>
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
                                        <th>Nama Karyawan</th>
                                        <th>Gaji Pokok</th>
                                        <th>Tunjangan</th>
                                        <th>Bonus</th>
                                        <th>THR</th>
                                        <th>Total</th>
                                        <th>Month</th>
                                        <th>Year</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($no = 1)
                                    @foreach($data as $item)
                                    <tr>
                                        <td>{{ $no }}</td>
                                        <td>{{ $item->user_salary?->user?->name }}</td>
                                        <td>Rp {{ number_format($item->user_salary?->salary_basic) }}</td>
                                        <td>Rp {{ number_format($item->user_salary?->salary_allowance) }}</td>
                                        <td>Rp {{ number_format($item->user_salary?->salary_bonus) }}</td>
                                        <td>Rp {{ number_format($item->user_salary?->salary_holiday) }}</td>
                                        <td>Rp {{ number_format($item->user_salary?->salary_total) }}</td>
                                        <td> {{ $month[$item->month] }}</td>
                                        <td> {{ $item->year }}</td>
                                        <td class="text-end">
                                            @if(Auth::user()->is_admin == 1)
                                                <div class="dropstart d-inline-block">
                                                    <button class="btn btn-link dropdown-toggle arrow-none p-0"
                                                        type="button" id="dropdownMenuButton{{ $item->id }}"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="las la-ellipsis-v font-20 text-muted"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end"
                                                        aria-labelledby="dropdownMenuButton{{ $item->id }}">
                                                        <li>
                                                            <button class="dropdown-item" type="button"
                                                                data-bs-toggle="modal" modal-bs-target="#modalEdits"
                                                                onclick='openModalEdit(@json($item->user_salary?->id), @json($item->user_salary?->user_id), @json($item->user_salary?->salary_basic), @json($item->user_salary?->salary_bonus), @json($item->user_salary?->salary_holiday), @json($item->user_salary?->month), @json($item->user_salary?->year), @json($item->user_salary?->user->allowances), @json($type_allowance))'>
                                                                Edit
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('user-salaries.delete', ['id' => $item->id]) }}">Delete</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @else
                                                <form action="{{ route('profile.slip.gaji') }}" method="post">
                                                    @csrf
                                                    <button type="submit" name="id_salaries" class="btn btn-primary btn-sm"
                                                        value="{{ $item->user_salary?->id }}"> <i
                                                            class="ti ti-cloud-download"></i> Download </button>
                                                </form>
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

                        @if (Auth::user()->is_admin == 1);

                            <form action="{{ route('monthly.salary.publish') }}" method="post">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm">Publish Gaji Bulanan Karyawan</button>
                            </form>
                        @endif
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