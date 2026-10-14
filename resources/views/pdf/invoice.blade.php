<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>{{ $invoice->invoice_number }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
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

        .right {
            text-align: right;
        }
    </style>
</head>

<body>

<h1>Invoice</h1>

<p>
    <strong>{{ $invoice->invoice_number }}</strong>
</p>

<p>
    Invoice Date:
    {{ $invoice->invoice_date->format('Y-m-d') }}
</p>

<p>
    Due Date:
    {{ $invoice->due_date->format('Y-m-d') }}
</p>

<h3>Bill To</h3>

<p>
    {{ $invoice->billing_name }}<br>

    @if($invoice->billing_email)
        {{ $invoice->billing_email }}<br>
    @endif

    {!! nl2br(e($invoice->billing_address)) !!}
</p>

<table>
    <thead>
    <tr>
        <th>Description</th>
        <th class="right">Qty</th>
        <th class="right">Unit Price</th>
        <th class="right">Tax %</th>
        <th class="right">Total</th>
    </tr>
    </thead>

    <tbody>
    @foreach($invoice->lines as $line)
        <tr>
            <td>{{ $line->description }}</td>

            <td class="right">
                {{ $line->quantity }}
            </td>

            <td class="right">
                {{ number_format(
                    (float) $line->unit_price,
                    2
                ) }}
            </td>

            <td class="right">
                {{ $line->tax_rate }}
            </td>

            <td class="right">
                {{ number_format(
                    (float) $line->total_amount,
                    2
                ) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<p class="right">
    Subtotal:
    {{ $invoice->currency }}
    {{ number_format(
        (float) $invoice->subtotal,
        2
    ) }}
</p>

<p class="right">
    Tax:
    {{ $invoice->currency }}
    {{ number_format(
        (float) $invoice->tax_amount,
        2
    ) }}
</p>

<p class="right">
    <strong>
        Total:
        {{ $invoice->currency }}
        {{ number_format(
            (float) $invoice->total_amount,
            2
        ) }}
    </strong>
</p>

@if($invoice->payment_instructions)
    <h3>Payment Instructions</h3>

    <p>
        {!! nl2br(
            e($invoice->payment_instructions)
        ) !!}
    </p>
@endif

@if($invoice->notes)
    <h3>Notes</h3>
    <p>{{ $invoice->notes }}</p>
@endif

</body>
</html>