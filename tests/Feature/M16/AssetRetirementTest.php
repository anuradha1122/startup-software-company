<?php

use App\Models\Asset;
use App\Models\User;
use App\Services\Assets\AssetLifecycleService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('retires available asset with reason and actor', function (): void {
    $actor =
        User::factory()->create();

    $asset =
        Asset::create([
            'asset_number' => 'AST-000001',

            'asset_tag' => 'LAP-000001',

            'name' => 'Old Laptop',

            'category' => 'Laptop',

            'status' => 'available',

            'created_by' => $actor->id,
        ]);

    $result =
        app(
            AssetLifecycleService::class
        )->retire(
            $asset,
            'Asset reached end of useful life.',
            $actor
        );

    expect(
        $result->status
    )->toBe('retired');

    expect(
        $result->retired_by
    )->toBe(
        $actor->id
    );

    expect(
        $result->retired_at
    )->not->toBeNull();

    expect(
        $result->retirement_reason
    )->toBe(
        'Asset reached end of useful life.'
    );
});
