<?php

namespace App\Services\Procurement;

use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\User;
use App\Services\DocumentNumberService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GoodsReceiptService
{
    public function __construct(
        private readonly DocumentNumberService $numbers,
        private readonly ProcurementQuantityService $quantities,
    ) {}

    public function receive(
        PurchaseOrder $order,
        array $data,
        User $actor
    ): GoodsReceipt {
        return DB::transaction(
            function () use (
                $order,
                $data,
                $actor
            ): GoodsReceipt {
                $order =
                    PurchaseOrder::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $order->id
                        );

                if (
                    ! in_array(
                        $order->status,
                        [
                            'issued',
                            'partially_received',
                        ],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'status' => 'Only issued purchase orders may receive goods.',
                    ]);
                }

                $receipt =
                    GoodsReceipt::create([
                        'receipt_number' => $this->numbers->next(
                            'goods_receipt'
                        ),

                        'purchase_order_id' => $order->id,

                        'received_date' => $data['received_date'],

                        'delivery_reference' => $data[
                                'delivery_reference'
                            ]
                            ?? null,

                        'notes' => $data['notes']
                            ?? null,

                        'received_by' => $actor->id,
                    ]);

                foreach (
                    $data['items'] as $submitted
                ) {
                    $item =
                        PurchaseOrderItem::query()
                            ->where(
                                'purchase_order_id',
                                $order->id
                            )
                            ->lockForUpdate()
                            ->findOrFail(
                                $submitted[
                                    'purchase_order_item_id'
                                ]
                            );

                    $current =
                        $this->quantities
                            ->toHundredths(
                                (string) $item
                                    ->received_quantity
                            );

                    $received =
                        $this->quantities
                            ->toHundredths(
                                $submitted[
                                    'quantity_received'
                                ]
                            );

                    $rejected =
                        $this->quantities
                            ->toHundredths(
                                $submitted[
                                    'quantity_rejected'
                                ]
                                ?? '0.00'
                            );

                    if (
                        $received === 0
                        && $rejected === 0
                    ) {
                        continue;
                    }

                    $ordered =
                        $this->quantities
                            ->toHundredths(
                                (string) $item
                                    ->quantity
                            );

                    $newReceived =
                        $current
                        + $received;

                    if (
                        $newReceived
                        > $ordered
                    ) {
                        throw ValidationException::withMessages([
                            'items' => "Received quantity exceeds ordered quantity for {$item->description}.",
                        ]);
                    }

                    $receipt
                        ->items()
                        ->create([
                            'purchase_order_item_id' => $item->id,

                            'quantity_received' => $this
                                ->quantities
                                ->fromHundredths(
                                    $received
                                ),

                            'quantity_rejected' => $this
                                ->quantities
                                ->fromHundredths(
                                    $rejected
                                ),

                            'condition_notes' => $submitted[
                                    'condition_notes'
                                ]
                                ?? null,
                        ]);

                    $item->update([
                        'received_quantity' => $this
                            ->quantities
                            ->fromHundredths(
                                $newReceived
                            ),
                    ]);
                }

                if (
                    ! $receipt
                        ->items()
                        ->exists()
                ) {
                    throw ValidationException::withMessages([
                        'items' => 'At least one received or rejected quantity is required.',
                    ]);
                }

                $order->refresh();

                $allReceived =
                    ! $order
                        ->items()
                        ->whereColumn(
                            'received_quantity',
                            '<',
                            'quantity'
                        )
                        ->exists();

                $status =
                    $allReceived
                        ? 'received'
                        : 'partially_received';

                $order->update([
                    'status' => $status,
                ]);

                if (
                    $order
                        ->procurement_request_id
                ) {
                    $order
                        ->request()
                        ->update([
                            'status' => $status,
                        ]);
                }

                return $receipt
                    ->load([
                        'items',
                        'receiver',
                    ]);
            }
        );
    }
}
