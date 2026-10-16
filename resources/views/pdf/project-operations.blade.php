<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Project Operations</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 7px;
            color: #111827;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 4px;
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

<h1>Project Operations Report</h1>

<p>
    Generated:
    {{ $generatedAt->format('Y-m-d H:i') }}
</p>

<table>
    <thead>
    <tr>
        <th>Project</th>
        <th>Client</th>
        <th>Status</th>
        <th>Curr.</th>
        <th>Team</th>
        <th>Open Tasks</th>
        <th>Approvals</th>
        <th>Invoiced</th>
        <th>Collected</th>
        <th>Cost</th>
        <th>Profit</th>
        <th>Margin %</th>
        <th>Receivable</th>
    </tr>
    </thead>

    <tbody>
    @foreach($rows as $row)
        <tr>
            <td>
                {{ $row['project_number'] }}
                <br>
                {{ $row['project_name'] }}
            </td>

            <td>
                {{ $row['client_name'] }}
            </td>

            <td>
                {{ $row['project_status'] }}
            </td>

            <td>
                {{ $row['currency'] }}
            </td>

            <td class="right">
                {{ $row['active_team_count'] }}
            </td>

            <td class="right">
                {{ $row['open_task_count'] }}
            </td>

            <td class="right">
                {{ $row['pending_client_approval_count'] }}
            </td>

            <td class="right">
                {{ number_format(
                    (float) $row['invoiced_revenue'],
                    2
                ) }}
            </td>

            <td class="right">
                {{ number_format(
                    (float) $row['collected_cash'],
                    2
                ) }}
            </td>

            <td class="right">
                {{ number_format(
                    (float) $row['actual_cost'],
                    2
                ) }}
            </td>

            <td class="right">
                {{ number_format(
                    (float) $row['gross_profit'],
                    2
                ) }}
            </td>

            <td class="right">
                {{ $row['gross_margin_percent'] !== null
                    ? number_format(
                        (float) $row['gross_margin_percent'],
                        2
                    )
                    : 'N/A'
                }}
            </td>

            <td class="right">
                {{ number_format(
                    (float) $row['outstanding_receivable'],
                    2
                ) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<p>
    This is an internal operational report.
    Profitability values are not client-facing.
</p>

</body>
</html>