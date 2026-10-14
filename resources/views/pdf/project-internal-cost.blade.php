<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        {{ $project->project_number }}
        Internal Cost
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

<h1>Project Internal Cost Report</h1>

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
        <th>Date</th>
        <th>Person</th>
        <th>Task</th>
        <th>Hours</th>
        <th>Rate</th>
        <th>Currency</th>
        <th>Cost</th>
    </tr>
    </thead>

    <tbody>
    @foreach($entries as $entry)
        <tr>
            <td>
                {{ $entry->work_date->format('Y-m-d') }}
            </td>

            <td>
                {{ $entry->timesheet->user->name }}
            </td>

            <td>
                {{ $entry->task->task_number }}
            </td>

            <td>
                {{ $entry->hours }}
            </td>

            <td>
                {{ number_format(
                    (float) $entry->hourly_cost_rate_snapshot,
                    2
                ) }}
            </td>

            <td>
                {{ $entry->cost_currency }}
            </td>

            <td>
                {{ number_format(
                    (float) $entry->internal_cost_amount,
                    2
                ) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<p>
    <strong>
        Total Hours:
        {{ number_format(
            (float) $entries->sum('hours'),
            2
        ) }}
    </strong>
</p>

<p>
    <strong>
        Total Internal Cost:
        {{ number_format(
            (float) $entries->sum('internal_cost_amount'),
            2
        ) }}
    </strong>
</p>

</body>
</html>