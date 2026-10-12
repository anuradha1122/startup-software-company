<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $contract->contract_number }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            line-height: 1.5;
        }

        h2 {
            margin-top: 20px;
        }
    </style>
</head>

<body>

<h1>Software Services Contract</h1>

<p>
    Contract:
    <strong>
        {{ $contract->contract_number }}
    </strong>
</p>

<p>
    Client:
    {{ $contract->client->name }}
</p>

<p>
    {{ $contract->title }}
</p>

<h2>Commercial Value</h2>

<p>
    {{ $contract->currency }}
    {{ number_format(
        (float) $contract->contract_value,
        2
    ) }}
</p>

<h2>Contract Period</h2>

<p>
    {{ $contract->start_date?->format('Y-m-d') }}
    to
    {{ $contract->end_date?->format('Y-m-d') }}
</p>

<h2>Payment Terms</h2>

<p>
    {{ $contract->payment_terms_days }}
    days
</p>

<p>
    {!! nl2br(e($contract->payment_schedule)) !!}
</p>

<h2>Special Terms</h2>

<p>
    {!! nl2br(e($contract->special_terms)) !!}
</p>

<h2>Termination</h2>

<p>
    {!! nl2br(e($contract->termination_terms)) !!}
</p>

<h2>Reference Documents</h2>

<p>
    Requirement:
    {{ $contract
        ->requirementVersion
        ->requirement
        ->requirement_number }}

    Version:
    {{ $contract
        ->requirementVersion
        ->version_number }}
</p>

<p>
    Quotation:
    {{ $contract->quotation->quotation_number }}
</p>

</body>
</html>