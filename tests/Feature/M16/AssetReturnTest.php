<?php

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\User;
use App\Notifications\AssetReturnedNotification;
use App\Services\Assets\AssetAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();
});

it('returns assigned asset and preserves assignment history', function (): void {
    $actor =
        User::factory()->create();

    $recipient =
        User::factory()->create();

    $asset =
        Asset::create([
            'asset_number' => 'AST-000001',

            'asset_tag' => 'LAP-000001',

            'name' => 'Development Laptop',

            'category' => 'Laptop',

            'status' => 'assigned',

            'location' => 'Remote',

            'created_by' => $actor->id,
        ]);

    $assignment =
        AssetAssignment::create([
            'asset_id' => $asset->id,

            'user_id' => $recipient->id,

            'assigned_date' => '2026-10-01',

            'status' => 'active',

            'assigned_by' => $actor->id,
        ]);

    $result =
        app(
            AssetAssignmentService::class
        )->returnAsset(
            $assignment,
            [
                'returned_date' => '2026-10-20',

                'location' => 'Head Office',

                'return_notes' => 'Returned in good condition.',
            ],
            $actor
        );

    expect(
        $result->status
    )->toBe('returned');

    expect(
        $result->returned_date
    )->not->toBeNull();

    expect(
        $result->returned_by
    )->toBe(
        $actor->id
    );

    expect(
        $asset
            ->fresh()
            ->status
    )->toBe('available');

    expect(
        $asset
            ->fresh()
            ->location
    )->toBe('Head Office');

    expect(
        AssetAssignment::query()
            ->where(
                'asset_id',
                $asset->id
            )
            ->count()
    )->toBe(1);

    Notification::assertSentTo(
        $recipient,
        AssetReturnedNotification::class
    );
});
