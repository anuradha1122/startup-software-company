<?php

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\User;
use App\Services\Assets\AssetAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();
});

it('prevents asset from having multiple active assignments', function (): void {
    $actor =
        User::factory()->create();

    $firstUser =
        User::factory()->create();

    $secondUser =
        User::factory()->create();

    $asset =
        Asset::create([
            'asset_number' => 'AST-000001',

            'asset_tag' => 'LAP-000001',

            'name' => 'Development Laptop',

            'category' => 'Laptop',

            'status' => 'assigned',

            'created_by' => $actor->id,
        ]);

    AssetAssignment::create([
        'asset_id' => $asset->id,

        'user_id' => $firstUser->id,

        'assigned_date' => '2026-10-20',

        'status' => 'active',

        'assigned_by' => $actor->id,
    ]);

    expect(
        fn () => app(
            AssetAssignmentService::class
        )->assign(
            $asset,
            $secondUser,
            [
                'assigned_date' => '2026-10-20',
            ],
            $actor
        )
    )->toThrow(
        ValidationException::class
    );

    expect(
        AssetAssignment::query()
            ->where(
                'asset_id',
                $asset->id
            )
            ->where(
                'status',
                'active'
            )
            ->count()
    )->toBe(1);
});
