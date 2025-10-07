@extends('layouts.app')
@section('page-title', 'Panel Draft Gaji Bulanan Karyawan')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    {{-- <div class="align-self-center"> --}}
                    <div class="justify-content-between d-flex">
                        {{-- <div class="align-self-center"> --}}
                        <div>
                            <h3 class="card-title">
                                {{ 'Panel Draft Gaji Bulanan Karyawan'}}
                            </h3>
                        </div>

                        @if (in_array(Auth::user()->role_name, ['Finance', 'Supervisor']))
                            <div>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#addDraft"><i data-feather="plus-square"
                                        class="align-self-center icon-xs me-2"></i>Tambah Draft</button>
                            </div>
                        @endif

                        <x-modal id="addDraft" title="Form Tambah Draft">
                            <form action="{{ route('finance.monthly.salary.store') }}" method="post" class="row">
                                @csrf
                                <div class="mb-3 col-lg-12">
                                    <label for="">User</label>
                                    <select name="salary_ids" class="form-control" id="salary_ids">
                                        @foreach ($users as $item)
                                            @if (isset($item->salary?->salary_total))
                                                <option value="{{ $item->salary?->id }}">
                                                    {{ $item->name . '-' . $item->role?->role_name . ' (Rp ' . number_format($item->salary?->salary_total) . ')' }}
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
                        <table class="table table-bordered table-striped" id="datatable">
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
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $no }}</td>
                                        <td>{{ $item->user?->name }}</td>
                                        <td>Rp {{ number_format($item->salary_basic) }}</td>
                                        <td>Rp {{ number_format($item->salary_allowance) }}</td>
                                        <td>Rp {{ number_format($item->salary_bonus) }}</td>
                                        <td>Rp {{ number_format($item->salary_holiday) }}</td>
                                        <td>Rp {{ number_format($item->salary_total) }}</td>
                                        <td>{{ $month[$item->month] }}</td>
                                        <td>{{ $item->year }}</td>
                                        <td class="text-end">
                                            @if (in_array(Auth::user()->role_name, ['Finance', 'Scheduler', 'Supervisor']))
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
                                                                onclick='openModalEdit(
                                                                    @json($item->id),
                                                                    @json($item->user_id),
                                                                    @json($item->salary_basic),
                                                                    @json($item->salary_bonus),
                                                                    @json($item->salary_holiday),
                                                                    @json($item->salary_total),
                                                                    @json($item->month),
                                                                    @json($item->year),
                                                                    @json($item->type_allowances ?? $item->user?->allowances ?? []),
                                                                    @json($type_allowance)
                                                                )'>
                                                                Edit
                                                            </button>

                                                        </li>
                                                        <li>
                                                            <form action="{{ route('finance.monthly.salary.destroy', $item->id) }}" method="post">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item">Delete</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @else
                                                <form action="{{ route('profile.slip.gaji') }}" method="post">
                                                    @csrf
                                                    <button type="submit" name="id_salaries" class="btn btn-primary btn-sm"
                                                        value="{{ $item->id }}">
                                                        <i class="ti ti-cloud-download"></i> Download
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @php($no++)
                                @endforeach
                            </tbody>
                        </table>
                        
                        @if (in_array(Auth::user()->role_name, ['Finance', 'Supervisor']))
                            <x-modal id="modalEdits" title="Edit Form Gaji">
                                <form action="" method="post" id="formEditSalary">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="mb-3">
                                            <label for="user_id_edit">User</label>
                                            <select class="form-control" name="user_id" id="user_id_edit" required readonly>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3 col-lg-6">
                                            <label for="salary_basic_edit">Basic Salary</label>
                                            <input class="form-control" type="number" name="salary_basic" id="salary_basic_edit">
                                        </div>
                                        <div class="mb-3 col-lg-6">
                                            <label for="salary_bonus_edit">Bonus</label>
                                            <input class="form-control" type="number" name="salary_bonus" id="salary_bonus_edit">
                                        </div>
                                        <div class="mb-3 col-lg-6">
                                            <label for="salary_holiday_edit">Holiday</label>
                                            <input class="form-control" type="number" name="salary_holiday" id="salary_holiday_edit">
                                        </div>
                                        <div class="mb-3 col-lg-12">
                                            <label>Allowance</label>
                                            <div id="allowance-container">
                                                <!-- Allowance inputs injected by JS -->
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success">Update</button>
                                </form>
                            </x-modal>
                        @endif
                    </div>

                    @if ((count($data) > 0) && in_array(Auth::user()->role_name, ['Finance', 'Supervisor']))
                        <form action="{{ route('finance.monthly.salary.publish') }}" method="post">
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
function openModalEdit(
    id,
    user_id,
    salary_basic,
    salary_bonus,
    salary_holiday,
    salary_total,
    month,
    year,
    salary_allowances,
    type_allowance
) {
    $('#modalEdits').modal('show');

    $('#user_id_edit').val(user_id);
    $('#salary_basic_edit').val(salary_basic);
    $('#salary_bonus_edit').val(salary_bonus);
    $('#salary_holiday_edit').val(salary_holiday);

    if ($('#salary_total_edit').length) {
        $('#salary_total_edit').val(salary_total);
    }

    $('#formEditSalary').attr('action', `/monthly-salary/${id}`);

    const $container = $('#allowance-container');
    $container.empty();

    type_allowance.forEach((allowanceType) => {
        const existingAllowance = (salary_allowances || []).find(sa => sa.id === allowanceType.id);
        const isChecked = existingAllowance ? 'checked' : '';
        const amount = existingAllowance?.pivot?.amount ?? existingAllowance?.amount ?? '';

        const $wrapper = $('<div>', { class: 'mb-2' });

        const checkboxHtml = `
            <div class="form-check">
                <input class="form-check-input" type="checkbox"
                    name="salary_allowance[]" value="${allowanceType.id}"
                    id="allowance_${allowanceType.id}" ${isChecked}>
                <label class="form-check-label" for="allowance_${allowanceType.id}">
                    ${allowanceType.name_allowance}
                </label>
            </div>
        `;

        const inputHtml = `
            <input type="number" name="allowances[${allowanceType.id}]" value="${amount}" class="form-control mt-1">
        `;

        $wrapper.append(checkboxHtml);
        $wrapper.append(inputHtml);
        $container.append($wrapper);
    });
}
</script>
@endpush


