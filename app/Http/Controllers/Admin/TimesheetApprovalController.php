<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewTimesheetRequest;
use App\Models\Timesheet;
use App\Models\User;
use App\Notifications\TimesheetApprovedNotification;
use App\Notifications\TimesheetRejectedNotification;
use App\Notifications\TimesheetSubmittedNotification;
use App\Services\Timesheets\ResourceCostRateService;
use App\Services\Timesheets\TimesheetCostingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class TimesheetApprovalController extends Controller
{
    public function submit(
        Request $request,
        Timesheet $timesheet,
        ResourceCostRateService $rates
    ): RedirectResponse {
        Gate::authorize(
            'submit',
            $timesheet
        );

        $timesheet->load([
            'user',
            'entries',
        ]);

        abort_if(
            $timesheet->entries->isEmpty(),
            422,
            'A timesheet must contain at least one entry.'
        );

        foreach (
            $timesheet->entries as $entry
        ) {
            $rates->rateFor(
                $timesheet->user,
                $entry->work_date
            );
        }

        $timesheet->update([
            'status' => 'submitted',

            'submitted_at' => now(),

            'reviewed_by' => null,

            'review_notes' => null,

            'reviewed_at' => null,
        ]);

        $reviewers =
            User::query()
                ->permission(
                    'timesheets.review'
                )
                ->where(
                    'status',
                    'active'
                )
                ->where(
                    'id',
                    '!=',
                    $request->user()->id
                )
                ->get();

        Notification::send(
            $reviewers,
            new TimesheetSubmittedNotification(
                $timesheet
            )
        );

        return back()->with(
            'success',
            'Timesheet submitted.'
        );
    }

    public function review(
        ReviewTimesheetRequest $request,
        Timesheet $timesheet,
        TimesheetCostingService $costing,
        ResourceCostRateService $rates
    ): RedirectResponse {
        Gate::authorize(
            'review',
            $timesheet
        );

        $decision =
            $request->validated(
                'decision'
            );

        if ($decision === 'approved') {
            $costing->cost(
                $timesheet,
                $rates
            );

            $timesheet->update([
                'status' => 'approved',

                'reviewed_by' => $request->user()->id,

                'review_notes' => $request->validated(
                    'review_notes'
                ),

                'reviewed_at' => now(),
            ]);

            $timesheet
                ->user
                ->notify(
                    new TimesheetApprovedNotification(
                        $timesheet
                    )
                );

            return back()->with(
                'success',
                'Timesheet approved.'
            );
        }

        $timesheet->update([
            'status' => 'rejected',

            'reviewed_by' => $request->user()->id,

            'review_notes' => $request->validated(
                'review_notes'
            ),

            'reviewed_at' => now(),
        ]);

        $timesheet
            ->user
            ->notify(
                new TimesheetRejectedNotification(
                    $timesheet
                )
            );

        return back()->with(
            'success',
            'Timesheet returned for changes.'
        );
    }
}
