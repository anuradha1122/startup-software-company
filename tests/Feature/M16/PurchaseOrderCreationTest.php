<?php

use App\Models\ProcurementRequest;
use App\Models\ProcurementRequestItem;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Services\Procurement\PurchaseOrderService;
use Database\Seeders\CompanySettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

require_once __DIR__.'/Helpers.php';

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        CompanySettingSeeder::class
    );
});

it('creates purchase order from approved procurement request', function (): void {
    $actor =
        User::factory()->create();

    $request =
        ProcurementRequest::create([
            'request_number' => 'PRQ-000001',

            'title' => 'Development equipment',

            'business_reason' => 'New engineering staff.',

            'priority' => 'high',

            'status' => 'approved',

            'currency' => 'LKR',

            'estimated_total' => '500000.00',

            'requested_by' => $actor->id,

            'approved_at' => now(),
        ]);

    $requestItem =
        ProcurementRequestItem::create([
            'procurement_request_id' => $request->id,

            'description' => 'Laptop',

            'quantity' => '2.00',

            'unit' => 'unit',

            'estimated_unit_cost' => '250000.00',

            'estimated_total' => '500000.00',
        ]);

    $supplier =
        createM16Supplier(
            $actor
        );

    $order =
        app(
            PurchaseOrderService::class
        )->createFromRequest(
            $request,
            $supplier,
            [
                'currency' => 'LKR',

                'order_date' => '2026-10-20',

                'expected_delivery_date' => '2026-10-30',

                'tax_amount' => '50000.00',

                'supplier_reference' => 'SUP-Q-001',

                'items' => [
                    [
                        'procurement_request_item_id' => $requestItem->id,

                        'unit_cost' => '250000.00',

                        'asset_trackable' => true,
                    ],
                ],
            ],
            $actor
        );

    expect(
        $order
    )->toBeInstanceOf(
        PurchaseOrder::class
    );

    expect(
        $order->purchase_order_number
    )->toStartWith('PO-');

    expect(
        $order->status
    )->toBe('draft');

    expect(
        $order->subtotal
    )->toBe('500000.00');

    expect(
        $order->tax_amount
    )->toBe('50000.00');

    expect(
        $order->total
    )->toBe('550000.00');

    expect(
        $order->supplier_id
    )->toBe(
        $supplier->id
    );

    expect(
        $order->items
    )->toHaveCount(1);

    expect(
        $order
            ->items
            ->first()
            ->unit_cost
    )->toBe('250000.00');

    expect(
        $order
            ->items
            ->first()
            ->asset_trackable
    )->toBeTrue();
});
