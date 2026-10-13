<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        {{ $project->project_number }}
        Project Charter
    </title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #111827;
            line-height: 1.5;
        }

        h1 {
            font-size: 20px;
        }

        h2 {
            margin-top: 20px;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 4px;
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
    </style>
</head>

<body>

<h1>Project Charter</h1>

<p>
    <strong>
        {{ $project->project_number }}
    </strong>
    —
    {{ $project->name }}
</p>

<table>
    <tr>
        <th>Client</th>
        <td>{{ $project->client->name }}</td>

        <th>Project Manager</th>
        <td>{{ $project->projectManager->name }}</td>
    </tr>

    <tr>
        <th>Contract</th>
        <td>{{ $project->contract->contract_number }}</td>

        <th>Contract Value</th>
        <td>
            {{ $project->currency }}
            {{ number_format(
                (float) $project->contract_value,
                2
            ) }}
        </td>
    </tr>

    <tr>
        <th>Requirement</th>
        <td>
            {{ $project
                ->requirementVersion
                ->requirement
                ->requirement_number }}
            v{{ $project
                ->requirementVersion
                ->version_number }}
        </td>

        <th>Status</th>
        <td>{{ $project->status }}</td>
    </tr>
</table>

<h2>Business Objective</h2>

<p>
    {!! nl2br(
        e(
            $project
                ->approvedCharter
                ->business_objective
        )
    ) !!}
</p>

<h2>Project Objective</h2>

<p>
    {!! nl2br(
        e(
            $project
                ->approvedCharter
                ->project_objective
        )
    ) !!}
</p>

<h2>Success Criteria</h2>

<ul>
@foreach(
    $project
        ->approvedCharter
        ->success_criteria ?? []
    as $criterion
)
    <li>{{ $criterion }}</li>
@endforeach
</ul>

<h2>Governance Structure</h2>

<p>
    {!! nl2br(
        e(
            $project
                ->approvedCharter
                ->governance_structure
        )
    ) !!}
</p>

<h2>Communication Plan</h2>

<p>
    {!! nl2br(
        e(
            $project
                ->approvedCharter
                ->communication_plan
        )
    ) !!}
</p>

<h2>Escalation Process</h2>

<p>
    {!! nl2br(
        e(
            $project
                ->approvedCharter
                ->escalation_process
        )
    ) !!}
</p>

<h2>Project Phases</h2>

<table>
    <thead>
    <tr>
        <th>Phase</th>
        <th>Planned Start</th>
        <th>Planned End</th>
        <th>Status</th>
    </tr>
    </thead>

    <tbody>
    @foreach($project->phases as $phase)
        <tr>
            <td>{{ $phase->name }}</td>

            <td>
                {{ $phase
                    ->planned_start_date
                    ?->format('Y-m-d') }}
            </td>

            <td>
                {{ $phase
                    ->planned_end_date
                    ?->format('Y-m-d') }}
            </td>

            <td>{{ $phase->status }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2>Key Milestones</h2>

<table>
    <thead>
    <tr>
        <th>Milestone</th>
        <th>Due Date</th>
        <th>Status</th>
    </tr>
    </thead>

    <tbody>
    @foreach(
        $project->milestones
            ->where('client_visible', true)
        as $milestone
    )
        <tr>
            <td>{{ $milestone->name }}</td>

            <td>
                {{ $milestone
                    ->due_date
                    ->format('Y-m-d') }}
            </td>

            <td>
                {{ $milestone->status }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>