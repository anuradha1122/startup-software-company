<?php

namespace App\Services\Procurement;

use App\Models\PurchaseOrder;
use App\Models\User;
use App\Notifications\PurchaseOrderIssuedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseOrderWorkflowService
{
    public function issue(
        PurchaseOrder $order,
        User $actor
    ): PurchaseOrder {
        $result =
            DB::transaction(
                function () use (
                    $order,
                    $actor
                ): PurchaseOrder {
                    $locked =
                        PurchaseOrder::query()
                            ->lockForUpdate()
                            ->findOrFail(
                                $order->id
                            );

                    if (
                        $locked->status
                        !== 'draft'
                    ) {
                        throw ValidationException::withMessages([
                            'status' => 'Only draft purchase orders may be issued.',
                        ]);
                    }

                    if (
                        ! $locked
                            ->items()
                            ->exists()
                    ) {
                        throw ValidationException::withMessages([
                            'items' => 'Purchase order requires at least one item.',
                        ]);
                    }

                    $locked->update([
                        'status' => 'issued',

                        'issued_by' => $actor->id,

                        'issued_at' => now(),
                    ]);

                    if (
                        $locked
                            ->procurement_request_id
                    ) {
                        $locked
                            ->request()
                            ->update([
                                'status' => 'ordered',
                            ]);
                    }

                    return $locked->refresh();
                }
            );

        DB::afterCommit(
            fn () => $actor->notify(
                new PurchaseOrderIssuedNotification(
                    $result
                )
            )
        );

        return $result;
    }

    public function cancel(
        PurchaseOrder $order,
        User $actor
    ): PurchaseOrder {
        return DB::transaction(
            function () use (
                $order,
                $actor
            ): PurchaseOrder {
                $locked =
                    PurchaseOrder::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $order->id
                        );

                if (
                    ! in_array(
                        $locked->status,
                        [
                            'draft',
                            'issued',
                        ],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'status' => 'This purchase order cannot be cancelled.',
                    ]);
                }

                if (
                    $locked
                        ->receipts()
                        ->exists()
                ) {
                    throw ValidationException::withMessages([
                        'status' => 'A purchase order with goods receipts cannot be cancelled.',
                    ]);
                }

                $locked->update([
                    'status' => 'cancelled',

                    'cancelled_by' => $actor->id,

                    'cancelled_at' => now(),
                ]);

                if (
                    $locked
                        ->procurement_request_id
                ) {
                    $locked
                        ->request()
                        ->update([
                            'status' => 'approved',
                        ]);
                }

                return $locked->refresh();
            }
        );
    }
}
