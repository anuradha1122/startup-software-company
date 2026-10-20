<?php

use App\Models\ProcurementRequest;
use App\Models\ProcurementRequestItem;
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

it('prevents purchase order creation from unapproved procurement request', function (): void {
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

            'status' => 'submitted',

            'currency' => 'LKR',

            'estimated_total' => '100000.00',

            'requested_by' => $actor->id,

            'submitted_at' => now(),
        ]);

    ProcurementRequestItem::create([
        'procurement_request_id' => $request->id,

        'description' => 'Monitor',

        'quantity' => '1.00',

        'unit' => 'unit',

        'estimated_unit_cost' => '100000.00',

        'estimated_total' => '100000.00',
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
                        'procurement_request_item_id' => $request
                            ->items()
                            ->firstOrFail()
                            ->id,

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
        $request
            ->purchaseOrders()
            ->count()
    )->toBe(0);
});
