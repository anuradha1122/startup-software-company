<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        {{ $payment->payment_number }}
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

<h1>Payment Receipt</h1>

<p>
    Receipt:
    <strong>
        {{ $payment->payment_number }}
    </strong>
</p>

<p>
    Client:
    {{ $payment->client->name }}
</p>

<p>
    Payment Date:
    {{ $payment->payment_date->format('Y-m-d') }}
</p>

<p>
    Method:
    {{ $payment->method }}
</p>

<p>
    Amount:
    <strong>
        {{ $payment->currency }}
        {{ number_format(
            (float) $payment->amount,
            2
        ) }}
    </strong>
</p>

@if($payment->reference_number)
    <p>
        Reference:
        {{ $payment->reference_number }}
    </p>
@endif

@if($payment->allocations->isNotEmpty())
    <h3>Allocated Invoices</h3>

    <table>
        <thead>
        <tr>
            <th>Invoice</th>
            <th>Amount</th>
        </tr>
        </thead>

        <tbody>
        @foreach($payment->allocations as $allocation)
            <tr>
                <td>
                    {{ $allocation->invoice->invoice_number }}
                </td>

                <td>
                    {{ $payment->currency }}
                    {{ number_format(
                        (float) $allocation->amount,
                        2
                    ) }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

</body>
</html>