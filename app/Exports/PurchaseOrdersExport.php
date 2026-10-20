<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PurchaseOrdersExport implements FromQuery, WithHeadings, WithMapping
{
    public function query(): Builder
    {
        return PurchaseOrder::query()
            ->with([
                'supplier:id,name',
                'project:id,name',
                'request:id,request_number',
            ])
            ->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'Purchase Order',
            'Supplier',
            'Procurement Request',
            'Project',
            'Currency',
            'Subtotal',
            'Tax',
            'Total',
            'Order Date',
            'Expected Delivery',
            'Status',
        ];
    }

    public function map(
        mixed $order
    ): array {
        return [
            $order->purchase_order_number,
            $order->supplier?->name,
            $order->request?->request_number,
            $order->project?->name,
            $order->currency,
            $order->subtotal,
            $order->tax_amount,
            $order->total,
            $order->order_date?->format(
                'Y-m-d'
            ),
            $order->expected_delivery_date?->format(
                'Y-m-d'
            ),
            $order->status,
        ];
    }
}
