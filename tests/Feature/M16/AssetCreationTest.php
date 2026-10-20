<?php

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates company asset with unique identity', function (): void {
    $user =
        User::factory()->create();

    $asset =
        Asset::create([
            'asset_number' => 'AST-000001',

            'asset_tag' => 'LAP-000001',

            'name' => 'Development Laptop',

            'category' => 'Laptop',

            'brand' => 'Dell',

            'model' => 'Latitude',

            'serial_number' => 'SN-M16-001',

            'currency' => 'LKR',

            'purchase_cost' => '350000.00',

            'purchase_date' => '2026-10-20',

            'status' => 'available',

            'location' => 'Head Office',

            'created_by' => $user->id,
        ]);

    expect(
        $asset->public_id
    )->not->toBeNull();

    expect(
        $asset->asset_number
    )->toBe('AST-000001');

    expect(
        $asset->asset_tag
    )->toBe('LAP-000001');

    expect(
        $asset->status
    )->toBe('available');

    expect(
        $asset->purchase_cost
    )->toBe('350000.00');

    $this->assertDatabaseHas(
        'assets',
        [
            'asset_tag' => 'LAP-000001',

            'status' => 'available',
        ]
    );
});
