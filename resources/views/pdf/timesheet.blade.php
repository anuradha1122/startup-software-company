<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Timesheet</title>

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
            padding: 6px;
        }

        th {
            background: #f3f4f6;
        }
    </style>
</head>

<body>

<h1>Weekly Timesheet</h1>

<p>
    <strong>
        {{ $timesheet->user->name }}
    </strong>
</p>

<p>
    Week:
    {{ $timesheet->week_start->format('Y-m-d') }}
    -
    {{ $timesheet->week_end->format('Y-m-d') }}
</p>

<p>
    Status:
    {{ $timesheet->status }}
</p>

<table>
    <thead>
    <tr>
        <th>Date</th>
        <th>Project</th>
        <th>Task</th>
        <th>Description</th>
        <th>Hours</th>
        <th>Billable</th>
    </tr>
    </thead>

    <tbody>
    @foreach($timesheet->entries as $entry)
        <tr>
            <td>
                {{ $entry->work_date->format('Y-m-d') }}
            </td>

            <td>
                {{ $entry->project->project_number }}
            </td>

            <td>
                {{ $entry->task->task_number }}
            </td>

            <td>
                {{ $entry->description }}
            </td>

            <td>
                {{ $entry->hours }}
            </td>

            <td>
                {{ $entry->billable ? 'Yes' : 'No' }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<p>
    <strong>
        Total Hours:
        {{ $timesheet->totalHours() }}
    </strong>
</p>

</body>
</html>