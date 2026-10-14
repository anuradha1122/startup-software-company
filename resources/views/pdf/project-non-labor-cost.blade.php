<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        {{ $project->project_number }}
        Non-Labor Cost
    </title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #111827;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 5px;
        }

        th {
            background: #f3f4f6;
        }
    </style>
</head>

<body>

<h1>Approved Non-Labor Project Cost</h1>

<p>
    <strong>
        {{ $project->project_number }}
    </strong>
    —
    {{ $project->name }}
</p>

<table>
    <thead>
    <tr>
        <th>Date</th>
        <th>Expense</th>
        <th>Type</th>
        <th>Supplier</th>
        <th>Title</th>
        <th>Currency</th>
        <th>Amount</th>
    </tr>
    </thead>

    <tbody>
    @foreach($expenses as $expense)
        <tr>
            <td>
                {{ $expense->expense_date->format('Y-m-d') }}
            </td>

            <td>
                {{ $expense->expense_number }}
            </td>

            <td>
                {{ $expense->type }}
            </td>

            <td>
                {{ $expense->supplier?->name }}
            </td>

            <td>
                {{ $expense->title }}
            </td>

            <td>
                {{ $expense->currency }}
            </td>

            <td>
                {{ number_format(
                    (float) $expense->total_amount,
                    2
                ) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<p>
    <strong>
        Total:
        {{ number_format(
            (float) $expenses->sum('total_amount'),
            2
        ) }}
    </strong>
</p>

</body>
</html>