<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        {{ $order->purchase_order_number }}
    </title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }

        h1,
        h2,
        p {
            margin: 0;
        }

        .muted {
            color: #6b7280;
        }

        .section {
            margin-top: 20px;
        }

        .meta {
            width: 100%;
            border-collapse: collapse;
        }

        .meta td {
            padding: 5px 0;
            vertical-align: top;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items th,
        .items td {
            padding: 7px;
            border: 1px solid #d1d5db;
        }

        .items th {
            background: #f3f4f6;
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .totals {
            width: 42%;
            margin-left: auto;
            margin-top: 15px;
            border-collapse: collapse;
        }

        .totals td {
            padding: 5px;
        }
    </style>
</head>

<body>
    <h1>Purchase Order</h1>

    <p>
        {{ $order->purchase_order_number }}
    </p>

    <p class="muted">
        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
    </p>

    <div class="section">
        <table class="meta">
            <tr>
                <td>
                    <strong>Supplier</strong>
                </td>

                <td>
                    {{ $order->supplier?->name ?? '-' }}
                </td>

                <td>
                    <strong>Order Date</strong>
                </td>

                <td>
                    {{ $order->order_date?->format('Y-m-d') }}
                </td>
            </tr>

            <tr>
                <td>
                    <strong>Project</strong>
                </td>

                <td>
                    {{ $order->project?->name ?? '-' }}
                </td>

                <td>
                    <strong>Expected Delivery</strong>
                </td>

                <td>
                    {{ $order->expected_delivery_date?->format('Y-m-d') ?? '-' }}
                </td>
            </tr>

            <tr>
                <td>
                    <strong>Supplier Reference</strong>
                </td>

                <td>
                    {{ $order->supplier_reference ?? '-' }}
                </td>

                <td>
                    <strong>Currency</strong>
                </td>

                <td>
                    {{ $order->currency }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Items</h2>

        <table class="items">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Unit Cost</th>
                    <th>Line Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>
                            {{ $item->description }}
                        </td>

                        <td>
                            {{ $item->quantity }}
                        </td>

                        <td>
                            {{ $item->unit }}
                        </td>

                        <td>
                            {{ $order->currency }}
                            {{ $item->unit_cost }}
                        </td>

                        <td>
                            {{ $order->currency }}
                            {{ $item->line_total }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <td>Subtotal</td>

                <td class="right">
                    {{ $order->currency }}
                    {{ $order->subtotal }}
                </td>
            </tr>

            <tr>
                <td>Tax</td>

                <td class="right">
                    {{ $order->currency }}
                    {{ $order->tax_amount }}
                </td>
            </tr>

            <tr>
                <td>
                    <strong>Total</strong>
                </td>

                <td class="right">
                    <strong>
                        {{ $order->currency }}
                        {{ $order->total }}
                    </strong>
                </td>
            </tr>
        </table>
    </div>

    @if ($order->notes)
        <div class="section">
            <h2>Notes</h2>

            <p>
                {{ $order->notes }}
            </p>
        </div>
    @endif
</body>
</html>