<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #111827;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 6px;
        }

        th {
            background: #f3f4f6;
        }

        .right {
            text-align: right;
        }
    </style>
</head>
<body>

<h1>Payslip</h1>

<p>
    Employee:
    {{ $entry->employee->employee_number }}
    —
    {{ $entry->employee->user->name }}
</p>

<p>
    Period:
    {{ $entry->period->name }}
</p>

<p>
    Payment Date:
    {{ $entry->period->payment_date->format('Y-m-d') }}
</p>

<table>
    <tbody>
        <tr>
            <th>Basic Salary</th>
            <td class="right">
                {{ $entry->currency }}
                {{ number_format((float) $entry->basic_salary, 2) }}
            </td>
        </tr>

        <tr>
            <th>Allowances</th>
            <td class="right">
                {{ $entry->currency }}
                {{ number_format((float) $entry->allowances, 2) }}
            </td>
        </tr>

        <tr>
            <th>Overtime</th>
            <td class="right">
                {{ $entry->currency }}
                {{ number_format((float) $entry->overtime_amount, 2) }}
            </td>
        </tr>

        <tr>
            <th>Gross Pay</th>
            <td class="right">
                {{ $entry->currency }}
                {{ number_format((float) $entry->gross_pay, 2) }}
            </td>
        </tr>

        <tr>
            <th>Deductions</th>
            <td class="right">
                {{ $entry->currency }}
                {{ number_format((float) $entry->deductions, 2) }}
            </td>
        </tr>

        <tr>
            <th>Net Pay</th>
            <td class="right">
                <strong>
                    {{ $entry->currency }}
                    {{ number_format((float) $entry->net_pay, 2) }}
                </strong>
            </td>
        </tr>
    </tbody>
</table>

@if($entry->adjustments->isNotEmpty())
    <h2>Adjustments</h2>

    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>

        <tbody>
            @foreach($entry->adjustments as $adjustment)
                <tr>
                    <td>{{ $adjustment->type }}</td>
                    <td>{{ $adjustment->label }}</td>
                    <td class="right">
                        {{ $entry->currency }}
                        {{ number_format((float) $adjustment->amount, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

</body>
</html>