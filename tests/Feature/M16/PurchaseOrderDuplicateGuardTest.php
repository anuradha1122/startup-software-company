<?php

use App\Models\ProcurementRequest;
use App\Models\ProcurementRequestItem;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Services\Procurement\PurchaseOrderService;
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

it('prevents duplicate active purchase order for same procurement request', function (): void {
    $actor =
        User::factory()->create();

    $supplier =
        createM16Supplier(
            $actor
        );

    $request =
        ProcurementRequest::create([
            'request_number' => 'PRQ-000001',

            'title' => 'Equipment',

            'business_reason' => 'Required equipment.',

            'priority' => 'medium',

            'status' => 'approved',

            'currency' => 'LKR',

            'estimated_total' => '100000.00',

            'requested_by' => $actor->id,

            'approved_at' => now(),
        ]);

    $requestItem =
        ProcurementRequestItem::create([
            'procurement_request_id' => $request->id,

            'description' => 'Monitor',

            'quantity' => '1.00',

            'unit' => 'unit',

            'estimated_unit_cost' => '100000.00',

            'estimated_total' => '100000.00',
        ]);

    PurchaseOrder::create([
        'purchase_order_number' => 'PO-EXISTING',

        'procurement_request_id' => $request->id,

        'supplier_id' => $supplier->id,

        'currency' => 'LKR',

        'order_date' => '2026-10-20',

        'status' => 'issued',

        'subtotal' => '100000.00',

        'tax_amount' => '0.00',

        'total' => '100000.00',

        'created_by' => $actor->id,
    ]);

    expect(
        fn () => app(
            PurchaseOrderService::class
        )->createFromRequest(
            $request,
            $supplier,
            [
                'currency' => 'LKR',

                'order_date' => '2026-10-20',

                'tax_amount' => '0.00',

                'items' => [
                    [
                        'procurement_request_item_id' => $requestItem->id,

                        'unit_cost' => '100000.00',

                        'asset_trackable' => false,
                    ],
                ],
            ],
            $actor
        )
    )->toThrow(
        ValidationException::class
    );

    expect(
        PurchaseOrder::query()
            ->where(
                'procurement_request_id',
                $request->id
            )
            ->count()
    )->toBe(1);
});
