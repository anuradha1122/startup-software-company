<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Finance Portfolio</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #111827;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 4px;
            border: 1px solid #d1d5db;
        }

        th {
            background: #f3f4f6;
        }
    </style>
</head>

<body>

<h1>Project Finance Portfolio</h1>

@foreach($byCurrency as $summary)
    <h3>
        {{ $summary['currency'] }}
    </h3>

    <p>
        Contracted:
        {{ number_format(
            $summary['contracted_revenue'],
            2
        ) }}
        |
        Invoiced:
        {{ number_format(
            $summary['invoiced_revenue'],
            2
        ) }}
        |
        Collected:
        {{ number_format(
            $summary['collected_cash'],
            2
        ) }}
        |
        Cost:
        {{ number_format(
            $summary['actual_cost'],
            2
        ) }}
        |
        Gross Profit:
        {{ number_format(
            $summary['gross_profit'],
            2
        ) }}
    </p>
@endforeach

<table>
    <thead>
    <tr>
        <th>Project</th>
        <th>Client</th>
        <th>Currency</th>
        <th>Contract</th>
        <th>Invoiced</th>
        <th>Collected</th>
        <th>Cost</th>
        <th>Gross Profit</th>
        <th>Margin %</th>
        <th>Receivable</th>
    </tr>
    </thead>

    <tbody>
    @foreach($projects as $row)
        <tr>
            <td>
                {{ $row['project']->project_number }}
            </td>

            <td>
                {{ $row['project']->client->name }}
            </td>

            <td>
                {{ $row['financials']['currency'] }}
            </td>

            <td>
                {{ $row['financials']['contracted_revenue'] }}
            </td>

            <td>
                {{ $row['financials']['invoiced_revenue'] }}
            </td>

            <td>
                {{ $row['financials']['collected_cash'] }}
            </td>

            <td>
                {{ $row['financials']['actual_cost'] }}
            </td>

            <td>
                {{ $row['financials']['gross_profit'] }}
            </td>

            <td>
                {{ $row['financials']['gross_margin_percent'] }}
            </td>

            <td>
                {{ $row['financials']['outstanding_receivable'] }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>