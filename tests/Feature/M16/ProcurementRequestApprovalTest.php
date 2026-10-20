<?php

use App\Models\ProcurementRequest;
use App\Models\User;
use App\Notifications\ProcurementRequestReviewedNotification;
use App\Services\Procurement\ProcurementReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();
});

it('approves submitted procurement request', function (): void {
    $requester =
        User::factory()->create();

    $reviewer =
        User::factory()->create();

    $request =
        ProcurementRequest::create([
            'request_number' => 'PRQ-000001',

            'title' => 'Engineering laptops',

            'business_reason' => 'Required for new engineering staff.',

            'priority' => 'high',

            'status' => 'submitted',

            'currency' => 'LKR',

            'estimated_total' => '750000.00',

            'requested_by' => $requester->id,

            'submitted_at' => now(),
        ]);

    $result =
        app(
            ProcurementReviewService::class
        )->review(
            $request,
            $reviewer,
            'approved',
            'Approved for purchasing.'
        );

    expect(
        $result->status
    )->toBe('approved');

    expect(
        $result->reviewed_by
    )->toBe(
        $reviewer->id
    );

    expect(
        $result->approved_at
    )->not->toBeNull();

    expect(
        $result->rejected_at
    )->toBeNull();

    Notification::assertSentTo(
        $requester,
        ProcurementRequestReviewedNotification::class
    );
});
