<?php

use App\Models\ProcurementRequest;
use App\Models\User;
use App\Services\Procurement\ProcurementReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();
});

it('requires review notes when rejecting procurement request', function (): void {
    $requester =
        User::factory()->create();

    $reviewer =
        User::factory()->create();

    $request =
        ProcurementRequest::create([
            'request_number' => 'PRQ-000001',

            'title' => 'Office equipment',

            'business_reason' => 'Replace damaged equipment.',

            'priority' => 'medium',

            'status' => 'submitted',

            'currency' => 'LKR',

            'estimated_total' => '100000.00',

            'requested_by' => $requester->id,

            'submitted_at' => now(),
        ]);

    expect(
        fn () => app(
            ProcurementReviewService::class
        )->review(
            $request,
            $reviewer,
            'rejected',
            null
        )
    )->toThrow(
        ValidationException::class
    );

    expect(
        $request
            ->fresh()
            ->status
    )->toBe('submitted');
});

it('rejects procurement request with review notes', function (): void {
    $requester =
        User::factory()->create();

    $reviewer =
        User::factory()->create();

    $request =
        ProcurementRequest::create([
            'request_number' => 'PRQ-000002',

            'title' => 'Additional monitors',

            'business_reason' => 'Additional equipment request.',

            'priority' => 'low',

            'status' => 'submitted',

            'currency' => 'LKR',

            'estimated_total' => '150000.00',

            'requested_by' => $requester->id,

            'submitted_at' => now(),
        ]);

    $result =
        app(
            ProcurementReviewService::class
        )->review(
            $request,
            $reviewer,
            'rejected',
            'Budget is not available.'
        );

    expect(
        $result->status
    )->toBe('rejected');

    expect(
        $result->review_notes
    )->toBe(
        'Budget is not available.'
    );

    expect(
        $result->reviewed_by
    )->toBe(
        $reviewer->id
    );

    expect(
        $result->rejected_at
    )->not->toBeNull();
});
