<?php

use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Services\Procurement\PurchaseOrderWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

require_once __DIR__.'/Helpers.php';

uses(RefreshDatabase::class);

it('prevents cancellation of purchase order after goods receipt exists', function (): void {
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

    GoodsReceipt::create([
        'receipt_number' => 'GRN-000001',

        'purchase_order_id' => $order->id,

        'received_date' => '2026-10-20',

        'received_by' => $actor->id,
    ]);

    expect(
        fn () => app(
            PurchaseOrderWorkflowService::class
        )->cancel(
            $order,
            $actor
        )
    )->toThrow(
        ValidationException::class
    );

    expect(
        $order
            ->fresh()
            ->status
    )->toBe('issued');
});
