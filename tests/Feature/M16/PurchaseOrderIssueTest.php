<?php

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\User;
use App\Notifications\PurchaseOrderIssuedNotification;
use App\Services\Procurement\PurchaseOrderWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

require_once __DIR__.'/Helpers.php';

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();
});

it('issues draft purchase order containing items', function (): void {
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

            'status' => 'draft',

            'subtotal' => '100000.00',

            'tax_amount' => '0.00',

            'total' => '100000.00',

            'created_by' => $actor->id,
        ]);

    PurchaseOrderItem::create([
        'purchase_order_id' => $order->id,

        'description' => 'Monitor',

        'quantity' => '1.00',

        'unit' => 'unit',

        'unit_cost' => '100000.00',

        'line_total' => '100000.00',

        'received_quantity' => '0.00',
    ]);

    $result =
        app(
            PurchaseOrderWorkflowService::class
        )->issue(
            $order,
            $actor
        );

    expect(
        $result->status
    )->toBe('issued');

    expect(
        $result->issued_by
    )->toBe(
        $actor->id
    );

    expect(
        $result->issued_at
    )->not->toBeNull();

    Notification::assertSentTo(
        $actor,
        PurchaseOrderIssuedNotification::class
    );
});
