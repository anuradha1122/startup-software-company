<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        {{ $project->project_number }}
        Project Team
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
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }
    </style>
</head>

<body>

<h1>Project Team & Assignments</h1>

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
        <th>Person</th>
        <th>Role</th>
        <th>Department</th>
        <th>Allocation</th>
        <th>Status</th>
        <th>Period</th>
    </tr>
    </thead>

    <tbody>
    @foreach($project->assignments as $assignment)
        <tr>
            <td>
                {{ $assignment->user->name }}
            </td>

            <td>
                {{ $assignment->role }}
            </td>

            <td>
                {{ $assignment
                    ->user
                    ->resourceProfile
                    ?->department }}
            </td>

            <td>
                {{ $assignment->allocation_percent }}%
            </td>

            <td>
                {{ $assignment->status }}
            </td>

            <td>
                {{ $assignment
                    ->start_date
                    ?->format('Y-m-d') }}
                -
                {{ $assignment
                    ->end_date
                    ?->format('Y-m-d') }}
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