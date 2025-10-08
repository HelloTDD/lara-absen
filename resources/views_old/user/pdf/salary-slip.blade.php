<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Salary Slip</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .slip-info {
            margin-bottom: 20px;
        }
        .salary-details {
            width: 100%;
            border-collapse: collapse;
        }
        .salary-details th, .salary-details td {
            border: 1px solid #000;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ config('app.name') }}</h2>
        <h3>Salary Slip</h3>
        <p>For the month of {{ date('F Y') }}</p>
    </div>

    <div class="slip-info">
        <p><strong>Employee Name:</strong> {{ $data->user?->name ?? 'N/A' }}</p>
        <p><strong>Employee ID:</strong> {{ $data->user?->id ?? 'N/A' }}</p>
        <p><strong>Department:</strong> {{ $data->user?->role?->role_name ?? 'N/A' }}</p>
    </div>

    <table class="salary-details">
        <tr>
            <td>Gaji Pokok</td>
            <td>{{ number_format($data->salary_basic ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td> Total Tunjangan 
                <table class="salary-details" style="margin-top: 1rem!important">
                    <tr>
                        <th colspan="2">
                            Detail Tunjangan
                        </th>
                    </tr>
                    @foreach ($detail_allowances as $item )
                        <tr>
                            <td>{{ $item->typeAllowance?->name_allowance }}</td>
                            <td>Rp {{ number_format($item->amount) }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
            <td>{{ number_format($data->salary_allowance ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Bonus</td>
            <td>{{ number_format($data->salary_bonus ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>THR</td>
            <td>{{ number_format($data->salary_holiday ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Total</strong></td>
            <td><strong>{{ number_format(($data->salary_total ?? 0)) }}</strong></td>
        </tr>
    </table>

    <div style="margin-top: 20px;">
        <p><strong>Net Salary:</strong> {{ number_format(($data->salary_total?? 0), 2) }}</p>
    </div>

    <div style="margin-top: 50px;">
        <p>Employee Signature: _________________</p>
        <p>Date: {{ date('d/m/Y') }}</p>
    </div>
</body>
</html>