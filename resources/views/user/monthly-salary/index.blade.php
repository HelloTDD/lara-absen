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