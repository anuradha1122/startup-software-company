<?php

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\User;
use App\Services\Assets\AssetLifecycleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('prevents retirement of asset with active assignment', function (): void {
    $actor =
        User::factory()->create();

    $recipient =
        User::factory()->create();

    $asset =
        Asset::create([
            'asset_number' => 'AST-000001',

            'asset_tag' => 'LAP-000001',

            'name' => 'Assigned Laptop',

            'category' => 'Laptop',

            'status' => 'assigned',

            'created_by' => $actor->id,
        ]);

    AssetAssignment::create([
        'asset_id' => $asset->id,

        'user_id' => $recipient->id,

        'assigned_date' => '2026-10-01',

        'status' => 'active',

        'assigned_by' => $actor->id,
    ]);

    expect(
        fn () => app(
            AssetLifecycleService::class
        )->retire(
            $asset,
            'Attempt retirement.',
            $actor
        )
    )->toThrow(
        ValidationException::class
    );

    expect(
        $asset
            ->fresh()
            ->status
    )->toBe('assigned');

    expect(
        $asset
            ->fresh()
            ->retired_at
    )->toBeNull();
});
