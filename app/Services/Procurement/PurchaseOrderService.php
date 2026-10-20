<?php

namespace App\Services\Procurement;

use App\Models\ProcurementRequest;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use App\Services\DocumentNumberService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseOrderService
{
    public function __construct(
        private readonly DocumentNumberService $numbers,
        private readonly ProcurementMoneyService $money,
        private readonly ProcurementQuantityService $quantities,
    ) {}

    public function createFromRequest(
        ProcurementRequest $request,
        Supplier $supplier,
        array $data,
        User $actor
    ): PurchaseOrder {
        return DB::transaction(
            function () use (
                $request,
                $supplier,
                $data,
                $actor
            ): PurchaseOrder {
                $request =
                    ProcurementRequest::query()
                        ->with('items')
                        ->lockForUpdate()
                        ->findOrFail(
                            $request->id
                        );

                if (
                    $request->status
                    !== 'approved'
                ) {
                    throw ValidationException::withMessages([
                        'request' => 'Only approved procurement requests may create purchase orders.',
                    ]);
                }

                if (
                    strtoupper(
                        $data['currency']
                    )
                    !== strtoupper(
                        $request->currency
                    )
                ) {
                    throw ValidationException::withMessages([
                        'currency' => 'Purchase order currency must match the approved request.',
                    ]);
                }

                if (
                    PurchaseOrder::query()
                        ->where(
                            'procurement_request_id',
                            $request->id
                        )
                        ->where(
                            'status',
                            '!=',
                            'cancelled'
                        )
                        ->exists()
                ) {
                    throw ValidationException::withMessages([
                        'request' => 'An active purchase order already exists for this procurement request.',
                    ]);
                }

                $submittedItems =
                    collect(
                        $data['items']
                    )->keyBy(
                        'procurement_request_item_id'
                    );

                if (
                    $submittedItems->count()
                    !== $request->items->count()
                ) {
                    throw ValidationException::withMessages([
                        'items' => 'Every approved procurement item must be included exactly once.',
                    ]);
                }

                $subtotalMinor = 0;

                $order =
                    PurchaseOrder::create([
                        'purchase_order_number' => $this->numbers->next(
                            'purchase_order'
                        ),

                        'procurement_request_id' => $request->id,

                        'supplier_id' => $supplier->id,

                        'project_id' => $request->project_id,

                        'currency' => strtoupper(
                            $request->currency
                        ),

                        'order_date' => $data['order_date'],

                        'expected_delivery_date' => $data[
                                'expected_delivery_date'
                            ]
                            ?? null,

                        'status' => 'draft',

                        'subtotal' => '0.00',

                        'tax_amount' => '0.00',

                        'total' => '0.00',

                        'supplier_reference' => $data[
                                'supplier_reference'
                            ]
                            ?? null,

                        'notes' => $data['notes']
                            ?? null,

                        'created_by' => $actor->id,
                    ]);

                foreach (
                    $request->items as $requestItem
                ) {
                    $submitted =
                        $submittedItems->get(
                            $requestItem->id
                        );

                    if (! $submitted) {
                        throw ValidationException::withMessages([
                            'items' => 'Invalid purchase-order item mapping.',
                        ]);
                    }

                    $quantity =
                        $this->quantities
                            ->toHundredths(
                                (string) $requestItem
                                    ->quantity
                            );

                    $unitMinor =
                        $this->money
                            ->toMinor(
                                $submitted[
                                    'unit_cost'
                                ]
                            );

                    $lineMinor =
                        intdiv(
                            ($quantity * $unitMinor) + 50,
                            100
                        );

                    $subtotalMinor +=
                        $lineMinor;

                    $order
                        ->items()
                        ->create([
                            'procurement_request_item_id' => $requestItem->id,

                            'description' => $requestItem
                                ->description,

                            'quantity' => $requestItem
                                ->quantity,

                            'unit' => $requestItem
                                ->unit,

                            'unit_cost' => $this
                                ->money
                                ->fromMinor(
                                    $unitMinor
                                ),

                            'line_total' => $this
                                ->money
                                ->fromMinor(
                                    $lineMinor
                                ),

                            'received_quantity' => '0.00',

                            'asset_trackable' => (bool) (
                                $submitted[
                                    'asset_trackable'
                                ]
                                ?? false
                            ),
                        ]);
                }

                $taxMinor =
                    $this->money
                        ->toMinor(
                            $data['tax_amount']
                            ?? '0.00'
                        );

                $order->update([
                    'subtotal' => $this
                        ->money
                        ->fromMinor(
                            $subtotalMinor
                        ),

                    'tax_amount' => $this
                        ->money
                        ->fromMinor(
                            $taxMinor
                        ),

                    'total' => $this
                        ->money
                        ->fromMinor(
                            $subtotalMinor
                            + $taxMinor
                        ),
                ]);

                return $order
                    ->refresh()
                    ->load('items');
            }
        );
    }
}
