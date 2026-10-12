<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $quotation->quotation_number }}</title>

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

        th, td {
            border: 1px solid #d1d5db;
            padding: 6px;
        }

        th {
            background: #f3f4f6;
        }

        .right {
            text-align: right;
        }
    </style>
</head>

<body>

<h1>Quotation</h1>

<p>
    <strong>
        {{ $quotation->quotation_number }}
    </strong>
</p>

<p>
    Client:
    {{ $quotation->client->name }}
</p>

<p>
    Valid until:
    {{ $quotation->valid_until->format('Y-m-d') }}
</p>

<table>
    <thead>
    <tr>
        <th>Module</th>
        <th>Description</th>
        <th>Hours</th>
        <th>Amount</th>
    </tr>
    </thead>

    <tbody>
    @foreach($quotation->estimate->lines as $line)
        <tr>
            <td>{{ $line->module_name }}</td>
            <td>{{ $line->description }}</td>

            <td class="right">
                {{ $line->estimated_hours }}
            </td>

            <td class="right">
                {{ number_format(
                    (float) $line->revenue_amount,
                    2
                ) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<br>

<table>
    <tr>
        <th>Subtotal</th>
        <td class="right">
            {{ $quotation->currency }}
            {{ number_format(
                (float) $quotation->subtotal,
                2
            ) }}
        </td>
    </tr>

    <tr>
        <th>
            Discount
            ({{ $quotation->discount_percent }}%)
        </th>

        <td class="right">
            {{ number_format(
                (float) $quotation->discount_amount,
                2
            ) }}
        </td>
    </tr>

    <tr>
        <th>
            Tax
            ({{ $quotation->tax_percent }}%)
        </th>

        <td class="right">
            {{ number_format(
                (float) $quotation->tax_amount,
                2
            ) }}
        </td>
    </tr>

    <tr>
        <th>Total</th>

        <td class="right">
            <strong>
                {{ $quotation->currency }}
                {{ number_format(
                    (float) $quotation->total_amount,
                    2
                ) }}
            </strong>
        </td>
    </tr>
</table>

<h2>Payment Schedule</h2>
<p>
    {!! nl2br(e($quotation->payment_schedule)) !!}
</p>

<h2>Terms & Conditions</h2>
<p>
    {!! nl2br(e($quotation->terms_and_conditions)) !!}
</p>

</body>
</html>