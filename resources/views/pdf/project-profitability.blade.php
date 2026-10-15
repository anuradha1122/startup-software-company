<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        {{ $project->project_number }}
        Profitability
    </title>

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
            padding: 6px;
            border: 1px solid #d1d5db;
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

<h1>Project Profitability Report</h1>

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

<p>
    Currency:
    {{ $financials->currency }}
</p>

<table>
    <tbody>
    <tr>
        <th>Contracted Revenue</th>
        <td class="right">
            {{ number_format(
                $financials->contractedRevenue,
                2
            ) }}
        </td>
    </tr>

    <tr>
        <th>Invoiced Revenue</th>
        <td class="right">
            {{ number_format(
                $financials->invoicedRevenue,
                2
            ) }}
        </td>
    </tr>

    <tr>
        <th>Collected Cash</th>
        <td class="right">
            {{ number_format(
                $financials->collectedCash,
                2
            ) }}
        </td>
    </tr>

    <tr>
        <th>Labor Cost</th>
        <td class="right">
            {{ number_format(
                $financials->laborCost,
                2
            ) }}
        </td>
    </tr>

    <tr>
        <th>Non-Labor Cost</th>
        <td class="right">
            {{ number_format(
                $financials->nonLaborCost,
                2
            ) }}
        </td>
    </tr>

    <tr>
        <th>Actual Cost</th>
        <td class="right">
            {{ number_format(
                $financials->actualCost,
                2
            ) }}
        </td>
    </tr>

    <tr>
        <th>Gross Profit</th>
        <td class="right">
            {{ number_format(
                $financials->grossProfit,
                2
            ) }}
        </td>
    </tr>

    <tr>
        <th>Gross Margin</th>
        <td class="right">
            {{ $financials->grossMarginPercent !== null
                ? number_format(
                    $financials->grossMarginPercent,
                    2
                ).'%'
                : 'N/A'
            }}
        </td>
    </tr>

    <tr>
        <th>Unbilled Revenue</th>
        <td class="right">
            {{ number_format(
                $financials->unbilledRevenue,
                2
            ) }}
        </td>
    </tr>

    <tr>
        <th>Outstanding Receivable</th>
        <td class="right">
            {{ number_format(
                $financials->outstandingReceivable,
                2
            ) }}
        </td>
    </tr>

    <tr>
        <th>Collection %</th>
        <td class="right">
            {{ $financials->collectionPercent !== null
                ? number_format(
                    $financials->collectionPercent,
                    2
                ).'%'
                : 'N/A'
            }}
        </td>
    </tr>
    </tbody>
</table>

@if($financials->currencyMismatchCount() > 0)
    <h3>Currency Integrity Warnings</h3>

    @foreach($financials->currencyMismatches as $warning)
        <p>
            {{ $warning['message'] }}
            Count:
            {{ $warning['count'] }}
        </p>
    @endforeach
@endif

</body>
</html>