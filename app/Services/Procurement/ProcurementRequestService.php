<?php

namespace App\Services\Procurement;

use App\Models\ProcurementRequest;
use App\Models\User;
use App\Notifications\ProcurementRequestSubmittedNotification;
use App\Services\DocumentNumberService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class ProcurementRequestService
{
    public function __construct(
        private readonly DocumentNumberService $numbers,
        private readonly ProcurementMoneyService $money,
        private readonly ProcurementQuantityService $quantities,
    ) {}

    public function create(
        array $data,
        User $actor
    ): ProcurementRequest {
        return DB::transaction(
            function () use ($data, $actor): ProcurementRequest {
                $totalMinor = 0;

                $request =
                    ProcurementRequest::create([
                        'request_number' => $this->numbers->next(
                            'procurement_request'
                        ),

                        'project_id' => $data['project_id']
                            ?? null,

                        'title' => $data['title'],

                        'business_reason' => $data['business_reason'],

                        'priority' => $data['priority'],

                        'status' => 'draft',

                        'currency' => strtoupper(
                            $data['currency']
                        ),

                        'estimated_total' => '0.00',

                        'requested_by' => $actor->id,
                    ]);

                foreach (
                    $data['items'] as $item
                ) {
                    $quantity =
                        $this->quantities
                            ->toHundredths(
                                $item['quantity']
                            );

                    $unitMinor =
                        $this->money
                            ->toMinor(
                                $item[
                                    'estimated_unit_cost'
                                ]
                            );

                    $lineMinor =
                        intdiv(
                            ($quantity * $unitMinor) + 50,
                            100
                        );

                    $totalMinor += $lineMinor;

                    $request
                        ->items()
                        ->create([
                            'description' => $item['description'],

                            'quantity' => $this
                                ->quantities
                                ->fromHundredths(
                                    $quantity
                                ),

                            'unit' => $item['unit']
                                ?? 'unit',

                            'estimated_unit_cost' => $this
                                ->money
                                ->fromMinor(
                                    $unitMinor
                                ),

                            'estimated_total' => $this
                                ->money
                                ->fromMinor(
                                    $lineMinor
                                ),

                            'notes' => $item['notes']
                                ?? null,
                        ]);
                }

                $request->update([
                    'estimated_total' => $this
                        ->money
                        ->fromMinor(
                            $totalMinor
                        ),
                ]);

                return $request
                    ->refresh()
                    ->load('items');
            }
        );
    }

    public function submit(
        ProcurementRequest $request,
        User $actor
    ): ProcurementRequest {
        $result =
            DB::transaction(
                function () use ($request, $actor): ProcurementRequest {
                    $locked =
                        ProcurementRequest::query()
                            ->lockForUpdate()
                            ->findOrFail(
                                $request->id
                            );

                    if (
                        $locked->requested_by
                        !== $actor->id
                    ) {
                        throw ValidationException::withMessages([
                            'request' => 'Only the requester may submit this request.',
                        ]);
                    }

                    if (
                        $locked->status
                        !== 'draft'
                    ) {
                        throw ValidationException::withMessages([
                            'status' => 'Only draft requests may be submitted.',
                        ]);
                    }

                    if (
                        ! $locked
                            ->items()
                            ->exists()
                    ) {
                        throw ValidationException::withMessages([
                            'items' => 'At least one procurement item is required.',
                        ]);
                    }

                    $locked->update([
                        'status' => 'submitted',

                        'submitted_at' => now(),
                    ]);

                    return $locked->refresh();
                }
            );

        DB::afterCommit(function () use ($result): void {
            $reviewers =
                User::query()
                    ->permission(
                        'procurement-requests.review'
                    )
                    ->get();

            Notification::send(
                $reviewers,
                new ProcurementRequestSubmittedNotification(
                    $result
                )
            );
        });

        return $result;
    }
}
