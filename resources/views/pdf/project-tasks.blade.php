<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        {{ $project->project_number }}
        Task Report
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
            padding: 5px;
            border: 1px solid #d1d5db;
        }

        th {
            background: #f3f4f6;
        }
    </style>
</head>

<body>

<h1>Project Task Report</h1>

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
        <th>Task</th>
        <th>Title</th>
        <th>Type</th>
        <th>Priority</th>
        <th>Status</th>
        <th>Assignee</th>
        <th>Phase</th>
        <th>Milestone</th>
        <th>Hours</th>
        <th>Due</th>
    </tr>
    </thead>

    <tbody>
    @foreach($project->tasks as $task)
        <tr>
            <td>
                {{ $task->task_number }}
            </td>

            <td>
                {{ $task->title }}
            </td>

            <td>
                {{ $task->type }}
            </td>

            <td>
                {{ $task->priority }}
            </td>

            <td>
                {{ $task->workflowStatus->name }}
            </td>

            <td>
                {{ $task->assignee?->name }}
            </td>

            <td>
                {{ $task->phase?->name }}
            </td>

            <td>
                {{ $task->milestone?->name }}
            </td>

            <td>
                {{ $task->estimated_hours }}
            </td>

            <td>
                {{ $task->due_date?->format('Y-m-d') }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<p>
    Generated:
    {{ $generatedAt->format('Y-m-d H:i:s') }}

    by
    {{ $generatedBy->name }}
</p>

</body>
</html>