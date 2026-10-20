<?php

use App\Models\ProcurementRequest;
use App\Models\User;
use App\Services\Procurement\ProcurementRequestService;
use Database\Seeders\CompanySettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        CompanySettingSeeder::class
    );
});

it('creates procurement request with items and exact estimated total', function (): void {
    $user =
        User::factory()->create();

    $request =
        app(
            ProcurementRequestService::class
        )->create(
            [
                'project_id' => null,

                'title' => 'Development equipment',

                'business_reason' => 'Equipment required for the engineering team.',

                'priority' => 'high',

                'currency' => 'LKR',

                'items' => [
                    [
                        'description' => 'Laptop',

                        'quantity' => '2.00',

                        'unit' => 'unit',

                        'estimated_unit_cost' => '250000.00',
                    ],
                    [
                        'description' => 'USB-C Dock',

                        'quantity' => '3.00',

                        'unit' => 'unit',

                        'estimated_unit_cost' => '25000.00',
                    ],
                ],
            ],
            $user
        );

    expect(
        $request
    )->toBeInstanceOf(
        ProcurementRequest::class
    );

    expect(
        $request->request_number
    )->toStartWith('PRQ-');

    expect(
        $request->status
    )->toBe('draft');

    expect(
        $request->currency
    )->toBe('LKR');

    expect(
        $request->estimated_total
    )->toBe('575000.00');

    expect(
        $request->items
    )->toHaveCount(2);

    expect(
        $request
            ->items
            ->first()
            ->estimated_total
    )->toBe('500000.00');

    $this->assertDatabaseHas(
        'procurement_requests',
        [
            'id' => $request->id,

            'requested_by' => $user->id,

            'status' => 'draft',
        ]
    );
});
