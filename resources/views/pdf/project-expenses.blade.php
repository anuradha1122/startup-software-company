<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        {{ $project->project_number }}
        Expenses
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

<h1>Project Expense Report</h1>

<p>
    <strong>
        {{ $project->project_number }}
    </strong>
    —
    {{ $project->name }}
</p>

<p>
    Client:
    {{ $project->client->name }}
</p>

<table>
    <thead>
    <tr>
        <th>Expense</th>
        <th>Date</th>
        <th>Type</th>
        <th>Title</th>
        <th>Supplier</th>
        <th>Currency</th>
        <th>Total</th>
        <th>Status</th>
    </tr>
    </thead>

    <tbody>
    @foreach($project->expenses as $expense)
        <tr>
            <td>
                {{ $expense->expense_number }}
            </td>

            <td>
                {{ $expense->expense_date->format('Y-m-d') }}
            </td>

            <td>
                {{ $expense->type }}
            </td>

            <td>
                {{ $expense->title }}
            </td>

            <td>
                {{ $expense->supplier?->name }}
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

            <td>
                {{ $expense->status }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>