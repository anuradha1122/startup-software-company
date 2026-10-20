<?php

namespace App\Services\Assets;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssetLifecycleService
{
    public function retire(
        Asset $asset,
        string $reason,
        User $actor
    ): Asset {
        if (blank($reason)) {
            throw ValidationException::withMessages([
                'reason' => 'Retirement reason is required.',
            ]);
        }

        return DB::transaction(
            function () use (
                $asset,
                $reason,
                $actor
            ): Asset {
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
                        ],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'asset' => 'This asset is already retired or disposed.',
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
                        'asset' => 'Assigned assets must be returned before retirement.',
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
                        'asset' => 'Complete or cancel maintenance before retiring this asset.',
                    ]);
                }

                $locked->update([
                    'status' => 'retired',

                    'retired_at' => now(),

                    'retired_by' => $actor->id,

                    'retirement_reason' => $reason,
                ]);

                return $locked->refresh();
            }
        );
    }
}
