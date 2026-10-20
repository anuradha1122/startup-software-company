<?php

namespace App\Notifications;

use App\Models\AssetAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AssetAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public AssetAssignment $assignment
    ) {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return [
            'database',
        ];
    }

    public function toArray(object $notifiable): array
    {
        $this->assignment->loadMissing(
            'asset'
        );

        return [
            'title' => 'Asset assigned',

            'message' => $this->assignment
                ->asset
                ->asset_number
                .' has been assigned to you.',

            'url' => route(
                'admin.assets.show',
                $this->assignment->asset
            ),
        ];
    }
}
