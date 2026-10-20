<?php

namespace App\Services\Assets;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\User;
use App\Notifications\AssetAssignedNotification;
use App\Notifications\AssetReturnedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssetAssignmentService
{
    public function assign(
        Asset $asset,
        User $recipient,
        array $data,
        User $actor
    ): AssetAssignment {
        $assignment =
            DB::transaction(
                function () use (
                    $asset,
                    $recipient,
                    $data,
                    $actor
                ): AssetAssignment {
                    $locked =
                        Asset::query()
                            ->lockForUpdate()
                            ->findOrFail(
                                $asset->id
                            );

                    if (
                        $locked->status
                        !== 'available'
                    ) {
                        throw ValidationException::withMessages([
                            'asset' => 'Only available assets may be assigned.',
                        ]);
                    }

                    if (
                        AssetAssignment::query()
                            ->where(
                                'asset_id',
                                $locked->id
                            )
                            ->where(
                                'status',
                                'active'
                            )
                            ->exists()
                    ) {
                        throw ValidationException::withMessages([
                            'asset' => 'This asset already has an active assignment.',
                        ]);
                    }

                    $assignment =
                        AssetAssignment::create([
                            'asset_id' => $locked->id,

                            'user_id' => $recipient->id,

                            'project_id' => $data[
                                    'project_id'
                                ]
                                ?? null,

                            'assigned_date' => $data[
                                    'assigned_date'
                                ],

                            'expected_return_date' => $data[
                                    'expected_return_date'
                                ]
                                ?? null,

                            'status' => 'active',

                            'assignment_notes' => $data[
                                    'assignment_notes'
                                ]
                                ?? null,

                            'assigned_by' => $actor->id,
                        ]);

                    $locked->update([
                        'status' => 'assigned',

                        'project_id' => $data['project_id']
                            ?? $locked
                                ->project_id,

                        'location' => $data['location']
                            ?? $locked
                                ->location,
                    ]);

                    return $assignment;
                }
            );

        DB::afterCommit(
            fn () => $recipient->notify(
                new AssetAssignedNotification(
                    $assignment
                )
            )
        );

        return $assignment;
    }

    public function returnAsset(
        AssetAssignment $assignment,
        array $data,
        User $actor
    ): AssetAssignment {
        $result =
            DB::transaction(
                function () use (
                    $assignment,
                    $data,
                    $actor
                ): AssetAssignment {
                    $locked =
                        AssetAssignment::query()
                            ->lockForUpdate()
                            ->findOrFail(
                                $assignment->id
                            );

                    if (
                        $locked->status
                        !== 'active'
                    ) {
                        throw ValidationException::withMessages([
                            'assignment' => 'Only active asset assignments may be returned.',
                        ]);
                    }

                    $asset =
                        Asset::query()
                            ->lockForUpdate()
                            ->findOrFail(
                                $locked
                                    ->asset_id
                            );

                    $locked->update([
                        'status' => 'returned',

                        'returned_date' => $data[
                                'returned_date'
                            ],

                        'return_notes' => $data[
                                'return_notes'
                            ]
                            ?? null,

                        'returned_by' => $actor->id,
                    ]);

                    $asset->update([
                        'status' => 'available',

                        'location' => $data['location']
                            ?? $asset
                                ->location,
                    ]);

                    return $locked->refresh();
                }
            );

        DB::afterCommit(
            fn () => $result
                ->user
                ->notify(
                    new AssetReturnedNotification(
                        $result
                    )
                )
        );

        return $result;
    }
}
