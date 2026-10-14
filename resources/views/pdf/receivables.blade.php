<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Receivables</title>

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
    </style>
</head>

<body>

<h1>Accounts Receivable</h1>

<table>
    <thead>
    <tr>
        <th>Invoice</th>
        <th>Client</th>
        <th>Due</th>
        <th>Status</th>
        <th>Currency</th>
        <th>Total</th>
        <th>Paid</th>
        <th>Outstanding</th>
    </tr>
    </thead>

    <tbody>
    @foreach($invoices as $invoice)
        <tr>
            <td>
                {{ $invoice->invoice_number }}
            </td>

            <td>
                {{ $invoice->client->name }}
            </td>

            <td>
                {{ $invoice->due_date->format('Y-m-d') }}
            </td>

            <td>
                {{ $invoice->status }}
            </td>

            <td>
                {{ $invoice->currency }}
            </td>

            <td>
                {{ $invoice->total_amount }}
            </td>

            <td>
                {{ $invoice->amount_paid }}
            </td>

            <td>
                {{ $invoice->balance_due }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>