<?php

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\User;
use App\Notifications\AssetAssignedNotification;
use App\Services\Assets\AssetAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();
});

it('assigns available asset to user', function (): void {
    $actor =
        User::factory()->create();

    $recipient =
        User::factory()->create();

    $asset =
        Asset::create([
            'asset_number' => 'AST-000001',

            'asset_tag' => 'LAP-000001',

            'name' => 'Laptop',

            'category' => 'Laptop',

            'status' => 'available',

            'created_by' => $actor->id,
        ]);

    $assignment =
        app(
            AssetAssignmentService::class
        )->assign(
            $asset,
            $recipient,
            [
                'assigned_date' => '2026-10-20',

                'expected_return_date' => '2027-10-20',

                'location' => 'Remote',

                'assignment_notes' => 'Assigned for development work.',
            ],
            $actor
        );

    expect(
        $assignment
    )->toBeInstanceOf(
        AssetAssignment::class
    );

    expect(
        $assignment->status
    )->toBe('active');

    expect(
        $assignment->user_id
    )->toBe(
        $recipient->id
    );

    expect(
        $asset
            ->fresh()
            ->status
    )->toBe('assigned');

    expect(
        $asset
            ->fresh()
            ->location
    )->toBe('Remote');

    Notification::assertSentTo(
        $recipient,
        AssetAssignedNotification::class
    );
});
