<?php

use App\Services\Support\SupportSlaService;

it('calculates stricter SLA for urgent critical tickets', function (): void {
    $service =
        app(
            SupportSlaService::class
        );

    $urgent =
        $service->deadlines(
            'urgent',
            'critical'
        );

    $low =
        $service->deadlines(
            'low',
            'minor'
        );

    expect(
        $urgent[
            'first_response_due_at'
        ]->lt(
            $low[
                'first_response_due_at'
            ]
        )
    )->toBeTrue();

    expect(
        $urgent[
            'resolution_due_at'
        ]->lt(
            $low[
                'resolution_due_at'
            ]
        )
    )->toBeTrue();
});
