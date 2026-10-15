<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        {{ $project->project_number }}
        Project Summary
    </title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
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
    </style>
</head>

<body>

<h1>Project Summary</h1>

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

<p>
    Status:
    {{ $project->status }}
</p>

<h2>Milestones</h2>

<table>
    <thead>
    <tr>
        <th>Milestone</th>
        <th>Status</th>
        <th>Due Date</th>
    </tr>
    </thead>

    <tbody>
    @foreach($project->milestones as $milestone)
        <tr>
            <td>
                {{ $milestone->name }}
            </td>

            <td>
                {{ $milestone->status }}
            </td>

            <td>
                {{ $milestone->due_date }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>