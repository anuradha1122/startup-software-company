<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $proposal->proposal_number }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #111827;
        }

        h1, h2 {
            margin-bottom: 6px;
        }

        h2 {
            margin-top: 20px;
            border-bottom: 1px solid #d1d5db;
        }

        .meta {
            color: #6b7280;
        }
    </style>
</head>

<body>

<h1>Software Proposal</h1>

<p>
    <strong>
        {{ $proposal->proposal_number }}
    </strong>
</p>

<p>
    Client:
    {{ $proposal->client->name }}
</p>

<p>
    {{ $proposal->title }}
</p>

<div class="meta">
    Valid until:
    {{ $proposal->valid_until?->format('Y-m-d') }}
</div>

<h2>Executive Summary</h2>
<p>
    {!! nl2br(e($proposal->executive_summary)) !!}
</p>

<h2>Solution Overview</h2>
<p>
    {!! nl2br(e($proposal->solution_overview)) !!}
</p>

<h2>Delivery Approach</h2>
<p>
    {!! nl2br(e($proposal->delivery_approach)) !!}
</p>

<h2>Commercial Summary</h2>

<p>
    Currency:
    {{ $proposal->estimate->currency }}
</p>

<p>
    Estimated Commercial Value:
    {{ number_format(
        (float) $proposal->estimate->estimated_revenue,
        2
    ) }}
</p>

@if($proposal->commercial_notes)
    <h2>Commercial Notes</h2>

    <p>
        {!! nl2br(e($proposal->commercial_notes)) !!}
    </p>
@endif

</body>
</html>