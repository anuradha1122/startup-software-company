<?php

use App\Models\ProcurementRequest;
use App\Models\User;
use App\Services\Procurement\ProcurementReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('prevents requester from approving own procurement request', function (): void {
    $user =
        User::factory()->create();

    $request =
        ProcurementRequest::create([
            'request_number' => 'PRQ-000001',

            'title' => 'Development laptops',

            'business_reason' => 'Required for new development staff.',

            'priority' => 'high',

            'status' => 'submitted',

            'currency' => 'LKR',

            'estimated_total' => '500000.00',

            'requested_by' => $user->id,

            'submitted_at' => now(),
        ]);

    expect(
        fn () => app(
            ProcurementReviewService::class
        )->review(
            $request,
            $user,
            'approved',
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

    expect(
        $request
            ->fresh()
            ->reviewed_by
    )->toBeNull();
});
