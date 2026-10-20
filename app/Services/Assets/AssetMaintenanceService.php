<?php

namespace App\Services\Assets;

use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssetMaintenanceService
{
    public function create(
        Asset $asset,
        array $data,
        User $actor
    ): AssetMaintenance {
        return DB::transaction(
            function () use (
                $asset,
                $data,
                $actor
            ): AssetMaintenance {
                $locked =
                    Asset::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $asset->id
                        );

                if (
                    in_array(
                        $locked->status,
                        [
                            'retired',
                            'disposed',
                            'lost',
                        ],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'asset' => 'This asset cannot enter maintenance.',
                    ]);
                }

                if (
                    $locked
                        ->assignments()
                        ->where(
                            'status',
                            'active'
                        )
                        ->exists()
                ) {
                    throw ValidationException::withMessages([
                        'asset' => 'Assigned assets must be returned before maintenance.',
                    ]);
                }

                if (
                    $locked
                        ->maintenances()
                        ->whereIn(
                            'status',
                            [
                                'scheduled',
                                'in_progress',
                            ]
                        )
                        ->exists()
                ) {
                    throw ValidationException::withMessages([
                        'asset' => 'This asset already has active maintenance.',
                    ]);
                }

                $maintenance =
                    AssetMaintenance::create([
                        'asset_id' => $locked->id,

                        'type' => $data['type'],

                        'status' => 'in_progress',

                        'started_date' => $data['started_date']
                            ?? now()
                                ->toDateString(),

                        'vendor' => $data['vendor']
                            ?? null,

                        'currency' => isset(
                            $data['currency']
                        )
                                ? strtoupper(
                                    $data['currency']
                                )
                                : null,

                        'cost' => $data['cost']
                            ?? null,

                        'description' => $data['description'],

                        'created_by' => $actor->id,
                    ]);

                $locked->update([
                    'status' => 'maintenance',
                ]);

                return $maintenance;
            }
        );
    }

    public function complete(
        AssetMaintenance $maintenance,
        array $data,
        User $actor
    ): AssetMaintenance {
        return DB::transaction(
            function () use (
                $maintenance,
                $data,
                $actor
            ): AssetMaintenance {
                $locked =
                    AssetMaintenance::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $maintenance->id
                        );

                if (
                    ! in_array(
                        $locked->status,
                        [
                            'scheduled',
                            'in_progress',
                        ],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'maintenance' => 'This maintenance record cannot be completed.',
                    ]);
                }

                $asset =
                    Asset::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $locked->asset_id
                        );

                $locked->update([
                    'status' => 'completed',

                    'completed_date' => $data[
                            'completed_date'
                        ],

                    'resolution' => $data['resolution'],

                    'completed_by' => $actor->id,
                ]);

                $asset->update([
                    'status' => 'available',
                ]);

                return $locked->refresh();
            }
        );
    }
}
