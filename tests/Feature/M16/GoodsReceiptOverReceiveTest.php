<?php

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\User;
use App\Services\Procurement\GoodsReceiptService;
use Database\Seeders\CompanySettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

require_once __DIR__.'/Helpers.php';

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        CompanySettingSeeder::class
    );
});

it('prevents received quantity from exceeding ordered quantity', function (): void {
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

            'status' => 'partially_received',

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

            'description' => 'Keyboard',

            'quantity' => '10.00',

            'unit' => 'unit',

            'unit_cost' => '10000.00',

            'line_total' => '100000.00',

            'received_quantity' => '8.00',

            'asset_trackable' => false,
        ]);

    expect(
        fn () => app(
            GoodsReceiptService::class
        )->receive(
            $order,
            [
                'received_date' => '2026-10-22',

                'delivery_reference' => 'DEL-OVER-001',

                'items' => [
                    [
                        'purchase_order_item_id' => $item->id,

                        'quantity_received' => '3.00',

                        'quantity_rejected' => '0.00',

                        'condition_notes' => 'Attempted over-receipt.',
                    ],
                ],
            ],
            $actor
        )
    )->toThrow(
        ValidationException::class
    );

    expect(
        $item
            ->fresh()
            ->received_quantity
    )->toBe('8.00');

    expect(
        $order
            ->fresh()
            ->status
    )->toBe(
        'partially_received'
    );

    expect(
        $order
            ->receipts()
            ->count()
    )->toBe(0);
});
