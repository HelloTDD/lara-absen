@extends('layouts.app')
@section('page-title', in_array(Auth::user()->role_name, ['Finance', 'Scheduler', 'Supervisor']) ? 'Panel Gaji Bulanan Karyawan' : 'Laporan Gaji Bulanan')
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
                                    Laporan Gaji Bulanan
                                </h3>
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
                                <table class="table table-striped table-hover" id="datatable_1">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Nama Karyawan</th>
                                            <th class="text-center">Gaji Pokok</th>
                                            <th class="text-center">Tunjangan</th>
                                            <th class="text-center">Bonus</th>
                                            <th class="text-center">THR</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Month</th>
                                            <th class="text-center">Year</th>
                                            <th class="text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php($no = 1)
                                        @foreach ($data as $item)
                                        <tr>
                                            <td class="text-center">{{ $no }}</td>
                                            <td class="text-center">{{ $item->user?->name }}</td>
                                            <td class="text-center">Rp {{ number_format($item->salary_basic) }}</td>
                                            <td class="text-center">Rp {{ number_format($item->salary_allowance) }}</td>
                                            <td class="text-center">Rp {{ number_format($item->salary_bonus) }}</td>
                                            <td class="text-center">Rp {{ number_format($item->salary_holiday) }}</td>
                                            <td class="text-center">Rp {{ number_format($item->salary_total) }}</td>
                                            <td class="text-center">{{ $month[$item->month] }}</td>
                                            <td class="text-center">{{ $item->year }}</td>
                                            <td class="text-end">
                                                <form action="{{ route('profile.slip.gaji') }}" method="post">
                                                    @csrf
                                                    <button type="submit" name="id_salaries"
                                                        class="btn btn-primary btn-sm" value="{{ $item->id }}">
                                                        <i class="ti ti-cloud-download"></i> Download
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @php($no++)
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection