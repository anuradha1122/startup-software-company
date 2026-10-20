<?php

namespace App\Services\Assets;

use App\Models\Asset;
use App\Models\PurchaseOrderItem;
use App\Models\User;
use App\Services\DocumentNumberService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssetService
{
    public function __construct(
        private readonly DocumentNumberService $numbers
    ) {}

    public function create(
        array $data,
        User $actor
    ): Asset {
        return DB::transaction(
            function () use (
                $data,
                $actor
            ): Asset {
                $purchaseOrderItem = null;

                if (
                    ! empty(
                        $data[
                            'purchase_order_item_id'
                        ]
                    )
                ) {
                    $purchaseOrderItem =
                        PurchaseOrderItem::query()
                            ->findOrFail(
                                $data[
                                    'purchase_order_item_id'
                                ]
                            );

                    if (
                        ! $purchaseOrderItem
                            ->asset_trackable
                    ) {
                        throw ValidationException::withMessages([
                            'purchase_order_item_id' => 'This purchase-order item is not marked as asset trackable.',
                        ]);
                    }

                    if (
                        (string) $purchaseOrderItem
                            ->received_quantity
                        === '0.00'
                    ) {
                        throw ValidationException::withMessages([
                            'purchase_order_item_id' => 'Assets can only be registered after goods have been received.',
                        ]);
                    }
                }

                return Asset::create([
                    'asset_number' => $this->numbers->next(
                        'asset'
                    ),

                    'asset_tag' => $data['asset_tag'],

                    'name' => $data['name'],

                    'category' => $data['category'],

                    'brand' => $data['brand']
                        ?? null,

                    'model' => $data['model']
                        ?? null,

                    'serial_number' => $data['serial_number']
                        ?? null,

                    'supplier_id' => $data['supplier_id']
                        ?? $purchaseOrderItem
                            ?->order
                            ?->supplier_id,

                    'purchase_order_item_id' => $purchaseOrderItem
                        ?->id,

                    'project_id' => $data['project_id']
                        ?? $purchaseOrderItem
                            ?->order
                            ?->project_id,

                    'currency' => $data['currency']
                        ?? $purchaseOrderItem
                            ?->order
                            ?->currency,

                    'purchase_cost' => $data['purchase_cost']
                        ?? $purchaseOrderItem
                            ?->unit_cost,

                    'purchase_date' => $data['purchase_date']
                        ?? null,

                    'warranty_expires_at' => $data[
                            'warranty_expires_at'
                        ]
                        ?? null,

                    'status' => 'available',

                    'location' => $data['location']
                        ?? null,

                    'notes' => $data['notes']
                        ?? null,

                    'created_by' => $actor->id,
                ]);
            }
        );
    }
}
