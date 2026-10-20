<?php

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\User;
use App\Services\Procurement\GoodsReceiptService;
use Database\Seeders\CompanySettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

require_once __DIR__.'/Helpers.php';

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        CompanySettingSeeder::class
    );
});

it('receives all ordered goods and closes purchase order receipt state', function (): void {
    $actor =
        User::factory()->create();

    $supplier =
        createM16Supplier(
            $actor
        );

    $order =
        PurchaseOrder::create([
            'purchase_order_number' => 'PO-000001',

            'supplier_id' => $supplier->id,

            'currency' => 'LKR',

            'order_date' => '2026-10-20',

            'status' => 'issued',

            'subtotal' => '100000.00',

            'tax_amount' => '0.00',

            'total' => '100000.00',

            'created_by' => $actor->id,

            'issued_by' => $actor->id,

            'issued_at' => now(),
        ]);

    $item =
        PurchaseOrderItem::create([
            'purchase_order_id' => $order->id,

            'description' => 'Monitor',

            'quantity' => '2.00',

            'unit' => 'unit',

            'unit_cost' => '50000.00',

            'line_total' => '100000.00',

            'received_quantity' => '0.00',

            'asset_trackable' => false,
        ]);

    $receipt =
        app(
            GoodsReceiptService::class
        )->receive(
            $order,
            [
                'received_date' => '2026-10-22',

                'delivery_reference' => 'DEL-001',

                'notes' => 'Full delivery received.',

                'items' => [
                    [
                        'purchase_order_item_id' => $item->id,

                        'quantity_received' => '2.00',

                        'quantity_rejected' => '0.00',

                        'condition_notes' => 'Received in good condition.',
                    ],
                ],
            ],
            $actor
        );

    expect(
        $receipt->receipt_number
    )->toStartWith('GRN-');

    expect(
        $receipt->items
    )->toHaveCount(1);

    expect(
        $receipt
            ->items
            ->first()
            ->quantity_received
    )->toBe('2.00');

    expect(
        $receipt
            ->items
            ->first()
            ->quantity_rejected
    )->toBe('0.00');

    expect(
        $item
            ->fresh()
            ->received_quantity
    )->toBe('2.00');

    expect(
        $order
            ->fresh()
            ->status
    )->toBe('received');

    $this->assertDatabaseHas(
        'goods_receipts',
        [
            'id' => $receipt->id,

            'purchase_order_id' => $order->id,

            'received_by' => $actor->id,
        ]
    );

    $this->assertDatabaseHas(
        'goods_receipt_items',
        [
            'goods_receipt_id' => $receipt->id,

            'purchase_order_item_id' => $item->id,

            'quantity_received' => '2.00',
        ]
    );
});
