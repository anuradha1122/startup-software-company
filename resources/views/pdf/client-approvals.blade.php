<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        {{ $project->project_number }}
        Client Approvals
    </title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
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

<h1>Client Approval Register</h1>

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
        <th>Approval</th>
        <th>Type</th>
        <th>Title</th>
        <th>Status</th>
        <th>Requested</th>
        <th>Responded By</th>
        <th>Responded</th>
    </tr>
    </thead>

    <tbody>
    @foreach($approvals as $approval)
        <tr>
            <td>
                {{ $approval->approval_number }}
            </td>

            <td>
                {{ $approval->type }}
            </td>

            <td>
                {{ $approval->title }}
            </td>

            <td>
                {{ $approval->status }}
            </td>

            <td>
                {{ $approval->requested_at?->format('Y-m-d H:i') }}
            </td>

            <td>
                {{ $approval->responder?->name }}
            </td>

            <td>
                {{ $approval->responded_at?->format('Y-m-d H:i') }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>