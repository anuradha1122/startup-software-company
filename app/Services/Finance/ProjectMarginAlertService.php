<?php

namespace App\Services\Finance;

use App\Models\Project;
use App\Models\ProjectFinanceSetting;
use App\Models\User;
use App\Notifications\ProjectMarginAlertNotification;
use Illuminate\Support\Facades\Notification;

class ProjectMarginAlertService
{
    public function __construct(
        private readonly ProjectProfitabilityService $profitability
    ) {}

    public function check(
        Project $project
    ): bool {
        $result =
            $this->profitability
                ->calculate(
                    $project
                );

        if (
            $result
                ->currencyMismatchCount()
            > 0
        ) {
            return false;
        }

        $setting =
            ProjectFinanceSetting::firstOrCreate(
                [
                    'project_id' => $project->id,
                ],
                [
                    'target_margin_percent' => 30,

                    'warning_margin_percent' => 20,

                    'alerts_enabled' => true,
                ]
            );

        if (! $setting->alerts_enabled) {
            return false;
        }

        if (
            $result->grossMarginPercent
            === null
        ) {
            return false;
        }

        if (
            $result->grossMarginPercent
            >= (float) $setting
                ->warning_margin_percent
        ) {
            return false;
        }

        if (
            $setting
                ->last_margin_alerted_at
                ?->isToday()
        ) {
            return false;
        }

        $recipients =
            User::query()
                ->permission(
                    'finance.alerts.receive'
                )
                ->where(
                    'status',
                    'active'
                )
                ->get();

        if (
            $project->projectManager
            && $project
                ->projectManager
                ->status === 'active'
            && $project
                ->projectManager
                ->can(
                    'project-profitability.view'
                )
        ) {
            $recipients =
                $recipients
                    ->push(
                        $project
                            ->projectManager
                    )
                    ->unique('id')
                    ->values();
        }

        Notification::send(
            $recipients,
            new ProjectMarginAlertNotification(
                $project,
                $result,
                (float) $setting
                    ->warning_margin_percent
            )
        );

        $setting->update([
            'last_margin_alerted_at' => now(),
        ]);

        return true;
    }
}
